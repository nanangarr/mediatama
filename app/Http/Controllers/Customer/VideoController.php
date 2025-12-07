<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Video;
use App\Models\VideoAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VideoController extends Controller
{
    /**
     * Display listing of videos (public access - no login required)
     */
    public function index()
    {
        $videos = Video::where('is_active', true)
            ->latest()
            ->paginate(12);

        return view('customer.videos.index', compact('videos'));
    }

    /**
     * Display customer's videos with active access
     */
    public function myVideos()
    {
        $user = Auth::user();

        // Get active video accesses
        $activeAccesses = VideoAccess::where('customer_id', $user->id)
            ->where('status', 'active')
            ->whereDate('start_at', '<=', now())
            ->with('video')
            ->latest()
            ->get()
            ->filter(function ($access) {
                $access->checkAndUpdateExpiration();
                return $access->isActive();
            });

        $activeVideos = $activeAccesses->map(function ($access) {
            return [
                'video' => $access->video,
                'access' => $access,
            ];
        });

        return view('customer.index', compact('activeVideos'));
    }

    /**
     * Show video detail (public access - no login required)
     */
    public function show(Video $video)
    {
        if (!$video->is_active) {
            abort(404, 'Video tidak ditemukan.');
        }

        $video->load(['accessRequests']);

        // Check if user is logged in and has access
        $hasAccess = false;
        $activeAccess = null;
        $remainingMinutes = 0;

        if (Auth::check()) {
            // Get active access for this user and video
            $activeAccess = VideoAccess::where('customer_id', Auth::id())
                ->where('video_id', $video->id)
                ->where('status', 'active')
                ->whereDate('start_at', '<=', now())
                ->latest()
                ->first();

            if ($activeAccess) {
                // Check and update expiration on-access
                $activeAccess->checkAndUpdateExpiration();

                if ($activeAccess->isActive()) {
                    $hasAccess = true;
                    $remainingMinutes = $activeAccess->remaining_minutes;
                } else {
                    $activeAccess = null;
                }
            }

            // Check if user has pending request
            $hasPendingRequest = $video->accessRequests()
                ->where('customer_id', Auth::id())
                ->where('status', 'pending')
                ->exists();

            $hasActiveAccess = $activeAccess !== null;
        } else {
            $hasPendingRequest = false;
            $hasActiveAccess = false;
        }

        return view('customer.videos.show', compact(
            'video',
            'hasActiveAccess',
            'activeAccess',
            'hasPendingRequest'
        ));
    }

    /**
     * Watch video (requires authentication and active access)
     */
    public function watch(Video $video)
    {
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'Silakan login untuk menonton video.');
        }

        if (!$video->is_active) {
            abort(404, 'Video tidak ditemukan.');
        }

        // Get active access for this user and video
        $activeAccess = VideoAccess::where('customer_id', Auth::id())
            ->where('video_id', $video->id)
            ->where('status', 'active')
            ->whereDate('start_at', '<=', now())
            ->latest()
            ->first();

        if (!$activeAccess) {
            return redirect()->route('customer.videos.show', $video)
                ->with('error', 'Anda tidak memiliki akses ke video ini. Silakan ajukan request akses terlebih dahulu.');
        }

        // Check and update expiration on-access
        $activeAccess->checkAndUpdateExpiration();

        if (!$activeAccess->isActive()) {
            return redirect()->route('customer.videos.show', $video)
                ->with('error', 'Akses Anda telah berakhir. Silakan ajukan request akses baru.');
        }

        // Reload to get fresh data
        $activeAccess->refresh();
        $remainingMinutes = $activeAccess->remaining_minutes;

        return view('customer.videos.watch', compact('video', 'activeAccess', 'remainingMinutes'));
    }

    /**
     * Get video source for player (requires authentication and active access)
     */
    public function stream(Video $video)
    {
        if (!Auth::check()) {
            abort(403, 'Unauthorized');
        }

        if (!$video->is_active) {
            abort(404, 'Video tidak ditemukan.');
        }

        // Get active access for this user and video
        $activeAccess = VideoAccess::where('customer_id', Auth::id())
            ->where('video_id', $video->id)
            ->where('status', 'active')
            ->whereDate('start_at', '<=', now())
            ->latest()
            ->first();

        if (!$activeAccess) {
            abort(403, 'Anda tidak memiliki akses ke video ini.');
        }

        // Check and update expiration on-access
        $activeAccess->checkAndUpdateExpiration();

        if (!$activeAccess->isActive()) {
            abort(403, 'Akses Anda telah berakhir.');
        }

        // If external URL, redirect to it
        if ($video->isExternal()) {
            return redirect($video->external_url);
        }

        // If local file, stream it
        $path = storage_path('app/public/' . $video->storage_path);

        if (!file_exists($path)) {
            abort(404, 'File video tidak ditemukan.');
        }

        $file = fopen($path, 'rb');
        $size = filesize($path);
        $length = $size;
        $start = 0;
        $end = $size - 1;

        header('Content-Type: video/mp4');
        header('Accept-Ranges: bytes');

        if (isset($_SERVER['HTTP_RANGE'])) {
            $c_start = $start;
            $c_end = $end;

            list(, $range) = explode('=', $_SERVER['HTTP_RANGE'], 2);
            if (strpos($range, ',') !== false) {
                header('HTTP/1.1 416 Requested Range Not Satisfiable');
                header("Content-Range: bytes $start-$end/$size");
                exit;
            }

            if ($range == '-') {
                $c_start = $size - substr($range, 1);
            } else {
                $range = explode('-', $range);
                $c_start = $range[0];
                $c_end = (isset($range[1]) && is_numeric($range[1])) ? $range[1] : $size;
            }

            $c_end = ($c_end > $end) ? $end : $c_end;
            if ($c_start > $c_end || $c_start > $size - 1 || $c_end >= $size) {
                header('HTTP/1.1 416 Requested Range Not Satisfiable');
                header("Content-Range: bytes $start-$end/$size");
                exit;
            }

            $start = $c_start;
            $end = $c_end;
            $length = $end - $start + 1;

            fseek($file, $start);
            header('HTTP/1.1 206 Partial Content');
        }

        header("Content-Range: bytes $start-$end/$size");
        header("Content-Length: $length");

        $buffer = 1024 * 8;
        while (!feof($file) && ($p = ftell($file)) <= $end) {
            if ($p + $buffer > $end) {
                $buffer = $end - $p + 1;
            }
            set_time_limit(0);
            echo fread($file, $buffer);
            flush();
        }

        fclose($file);
        exit;
    }
}
