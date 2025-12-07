<?php

namespace App\Http\Controllers\Customer;

use App\Models\Video;
use App\Models\VideoAccess;
use Illuminate\Http\Request;
use App\Models\AccessRequests;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Notifications\AccessRequestCreated;

class AccessRequestController extends Controller
{
    /**
     * Show customer's access requests
     */
    public function index()
    {
        $accessRequests = AccessRequests::where('customer_id', Auth::id())
            ->with(['video', 'reviewer', 'videoAccess'])
            ->latest('requested_at')
            ->paginate(10);

        return view('customer.access-requests.index', compact('accessRequests'));
    }

    /**
     * Show form to create new access request
     */
    public function create(Request $request)
    {
        $video = Video::findOrFail($request->video_id);

        if (!$video->is_active) {
            abort(404, 'Video tidak ditemukan.');
        }

        // Check if user already has active access
        $activeAccess = VideoAccess::where('customer_id', Auth::id())
            ->where('video_id', $video->id)
            ->where('status', 'active')
            ->whereDate('start_at', '<=', now())
            ->latest()
            ->first();

        if ($activeAccess) {
            $activeAccess->checkAndUpdateExpiration();

            if ($activeAccess->isActive()) {
                return redirect()->route('videos.show', $video)
                    ->with('info', 'Anda sudah memiliki akses aktif ke video ini.');
            }
        }

        // Check if user has pending request
        $hasPendingRequest = AccessRequests::where('customer_id', Auth::id())
            ->where('video_id', $video->id)
            ->where('status', 'pending')
            ->exists();

        if ($hasPendingRequest) {
            return redirect()->route('videos.show', $video)
                ->with('info', 'Anda sudah memiliki request yang sedang diproses untuk video ini.');
        }

        return view('customer.access-requests.create', compact('video'));
    }

    /**
     * Store new access request
     */
    public function store(Request $request)
    {
        $request->validate([
            'video_id' => 'required|exists:videos,id',
            'requested_minutes' => 'nullable|integer|min:1',
            'notes' => 'nullable|string|max:1000',
        ]);

        $video = Video::findOrFail($request->video_id);

        if (!$video->is_active) {
            return back()->with('error', 'Video tidak ditemukan.');
        }

        // Check if user already has active access
        $activeAccess = VideoAccess::where('customer_id', Auth::id())
            ->where('video_id', $video->id)
            ->where('status', 'active')
            ->whereDate('start_at', '<=', now())
            ->latest()
            ->first();

        if ($activeAccess) {
            $activeAccess->checkAndUpdateExpiration();

            if ($activeAccess->isActive()) {
                return redirect()->route('videos.show', $video)
                    ->with('info', 'Anda sudah memiliki akses aktif ke video ini.');
            }
        }

        // Check if user has pending request
        $hasPendingRequest = AccessRequests::where('customer_id', Auth::id())
            ->where('video_id', $video->id)
            ->where('status', 'pending')
            ->exists();

        if ($hasPendingRequest) {
            return redirect()->route('videos.show', $video)
                ->with('info', 'Anda sudah memiliki request yang sedang diproses untuk video ini.');
        }

        // If no requested_minutes, default to video duration in minutes
        $requestedMinutes = $request->requested_minutes;

        if (!$requestedMinutes && $video->duration_sec) {
            $requestedMinutes = (int) ceil($video->duration_sec / 60);
        }

        // Create access request
        $accessRequest = AccessRequests::create([
            'customer_id' => Auth::id(),
            'video_id' => $video->id,
            'requested_minutes' => $requestedMinutes,
            'notes' => $request->notes,
            'status' => 'pending',
            'requested_at' => now(),
        ]);

        // Send email notification to customer (optional)
        try {
            $user = Auth::user();
            if ($user) {
                $user->notify(new AccessRequestCreated($accessRequest));
            }
        } catch (\Exception $e) {
            Log::error('Failed to send access request created email: ' . $e->getMessage());
        }

        return redirect()->route('videos.show', $video)
            ->with('success', 'Request akses video berhasil diajukan. Silakan tunggu persetujuan dari admin.');
    }

    /**
     * Show specific access request detail
     */
    public function show(AccessRequests $accessRequest)
    {
        // Make sure the request belongs to the authenticated user
        if ($accessRequest->customer_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $accessRequest->load(['video', 'reviewer', 'videoAccess']);

        return view('customer.access-requests.show', compact('accessRequest'));
    }

    /**
     * Cancel pending access request
     */
    public function cancel(AccessRequests $accessRequest)
    {
        // Make sure the request belongs to the authenticated user
        if ($accessRequest->customer_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        if (!$accessRequest->isPending()) {
            return back()->with('error', 'Hanya request yang masih pending yang bisa dibatalkan.');
        }

        $accessRequest->update([
            'status' => 'rejected',
            'reviewer_id' => Auth::id(),
            'reviewed_at' => now(),
            'reason' => 'Dibatalkan oleh customer',
        ]);

        return redirect()->route('customer.access-requests.index')
            ->with('success', 'Request akses berhasil dibatalkan.');
    }

    /**
     * Request new access after expiration
     */
    public function requestAgain(Video $video)
    {
        return redirect()->route('customer.access-requests.create', ['video_id' => $video->id]);
    }
}
