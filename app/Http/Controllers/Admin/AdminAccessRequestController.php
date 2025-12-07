<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccessRequests;
use App\Models\VideoAccess;
use App\Notifications\AccessRequestApproved;
use App\Notifications\AccessRequestRejected;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AdminAccessRequestController extends Controller
{
    public function index(Request $request)
    {
        $query = AccessRequests::with(['customer', 'video', 'reviewer'])
            ->latest('requested_at');

        // Filter by status
        if ($request->has('status') && in_array($request->status, ['pending', 'approved', 'rejected'])) {
            $query->where('status', $request->status);
        }

        $accessRequests = $query->paginate(15);

        return view('admin.access_request.index', compact('accessRequests'));
    }

    public function show(AccessRequests $accessRequest)
    {
        $accessRequest->load(['customer', 'video', 'reviewer', 'videoAccess']);
        return view('admin.access_request.show', compact('accessRequest'));
    }

    public function approve(Request $request, AccessRequests $accessRequest)
    {
        if (!$accessRequest->isPending()) {
            return back()->withErrors(['error' => 'Request ini sudah diproses sebelumnya.']);
        }

        $request->validate([
            'approved_minutes' => 'required|integer|min:1',
            'grace_minutes' => 'nullable|integer|min:0|max:60',
            'reason' => 'nullable|string|max:255',
        ]);

        $video = $accessRequest->video;
        $approvedMinutes = (int) $request->approved_minutes;
        $graceMinutes = (int) ($request->grace_minutes ?? 0);

        // Validasi: approved_minutes harus >= durasi video (dalam menit)
        $videoDurationMinutes = $video->duration_sec ? ceil($video->duration_sec / 60) : 0;

        if ($approvedMinutes < $videoDurationMinutes) {
            return back()->withErrors([
                'approved_minutes' => "Durasi akses minimal {$videoDurationMinutes} menit (durasi video)."
            ])->withInput();
        }

        // Update access request
        $accessRequest->update([
            'status' => 'approved',
            'reviewer_id' => Auth::id(),
            'reviewed_at' => now(),
            'reason' => $request->reason,
        ]);

        // Create video access
        $startAt = Carbon::now();
        $endAt = $startAt->copy()->addMinutes($approvedMinutes);

        VideoAccess::create([
            'request_id' => $accessRequest->id,
            'customer_id' => $accessRequest->customer_id,
            'video_id' => $accessRequest->video_id,
            'approved_by' => Auth::id(),
            'approved_minutes' => $approvedMinutes,
            'grace_minutes' => $graceMinutes,
            'start_at' => $startAt,
            'end_at' => $endAt,
            'status' => 'active',
            'video_duration_sec_snapshot' => $video->duration_sec ?? 0,
        ]);

        // Send email notification
        try {
            $accessRequest->customer->notify(new AccessRequestApproved($accessRequest, $approvedMinutes, $graceMinutes));
        } catch (\Exception $e) {
            Log::error('Failed to send approval email: ' . $e->getMessage());
        }

        return redirect()->route('admin.access-requests.index')
            ->with('success', 'Request akses berhasil disetujui.');
    }

    public function reject(Request $request, AccessRequests $accessRequest)
    {
        if (!$accessRequest->isPending()) {
            return back()->withErrors(['error' => 'Request ini sudah diproses sebelumnya.']);
        }

        $request->validate([
            'reason' => 'required|string|max:255',
        ]);

        // Update access request
        $accessRequest->update([
            'status' => 'rejected',
            'reviewer_id' => Auth::id(),
            'reviewed_at' => now(),
            'reason' => $request->reason,
        ]);

        // Send email notification
        try {
            $accessRequest->customer->notify(new AccessRequestRejected($accessRequest, $request->reason));
        } catch (\Exception $e) {
            Log::error('Failed to send rejection email: ' . $e->getMessage());
        }

        return redirect()->route('admin.access-requests.index')
            ->with('success', 'Request akses berhasil ditolak.');
    }

    /**
     * Bulk approve for multiple access requests
     */
    public function bulkApprove(Request $request)
    {
        $request->validate([
            'request_ids' => 'required|array',
            'request_ids.*' => 'exists:access_requests,id',
            'approved_minutes' => 'required|integer|min:1',
            'grace_minutes' => 'nullable|integer|min:0|max:60',
        ]);

        $approvedMinutes = (int) $request->approved_minutes;
        $graceMinutes = (int) ($request->grace_minutes ?? 0);
        $successCount = 0;

        foreach ($request->request_ids as $requestId) {
            $accessRequest = AccessRequests::find($requestId);

            if (!$accessRequest || !$accessRequest->isPending()) {
                continue;
            }

            $video = $accessRequest->video;
            $videoDurationMinutes = $video->duration_sec ? ceil($video->duration_sec / 60) : 0;

            // Skip if approved minutes less than video duration
            if ($approvedMinutes < $videoDurationMinutes) {
                continue;
            }

            // Update access request
            $accessRequest->update([
                'status' => 'approved',
                'reviewer_id' => Auth::id(),
                'reviewed_at' => now(),
            ]);

            // Create video access
            $startAt = Carbon::now();
            $endAt = $startAt->copy()->addMinutes($approvedMinutes);

            VideoAccess::create([
                'request_id' => $accessRequest->id,
                'customer_id' => $accessRequest->customer_id,
                'video_id' => $accessRequest->video_id,
                'approved_by' => Auth::id(),
                'approved_minutes' => $approvedMinutes,
                'grace_minutes' => $graceMinutes,
                'start_at' => $startAt,
                'end_at' => $endAt,
                'status' => 'active',
                'video_duration_sec_snapshot' => $video->duration_sec ?? 0,
            ]);

            // Send email notification
            try {
                $accessRequest->customer->notify(new AccessRequestApproved($accessRequest, $approvedMinutes, $graceMinutes));
            } catch (\Exception $e) {
                Log::error('Failed to send approval email: ' . $e->getMessage());
            }

            $successCount++;
        }

        return redirect()->route('admin.access-requests.index')
            ->with('success', "{$successCount} request berhasil disetujui.");
    }
}
