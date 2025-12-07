<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class VideoController extends Controller
{
    public function index()
    {
        $videos = Video::latest()->paginate(10);
        return view('admin.video.index', compact('videos'));
    }

    public function create()
    {
        return view('admin.video.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:150',
            'description' => 'nullable|string',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'video_file' => 'nullable|file|mimes:mp4,mov,avi,wmv|max:512000',
            'external_url' => 'nullable|url|max:255',
            'is_active' => 'boolean',
        ]);

        // Validasi: harus ada salah satu antara video_file atau external_url
        if (!$request->hasFile('video_file') && !$request->external_url) {
            return back()->withErrors(['video_file' => 'Harus mengupload video atau mengisi URL eksternal.'])->withInput();
        }

        if ($request->hasFile('video_file') && $request->external_url) {
            return back()->withErrors(['video_file' => 'Pilih salah satu: upload video atau URL eksternal.'])->withInput();
        }

        $data = [
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'description' => $request->description,
            'is_active' => $request->has('is_active'),
        ];

        // Handle thumbnail upload
        if ($request->hasFile('thumbnail')) {
            $thumbnailPath = $request->file('thumbnail')->store('thumbnails', 'public');
            $data['thumbnail'] = $thumbnailPath;
        }

        // Handle video file upload
        if ($request->hasFile('video_file')) {
            $videoPath = $request->file('video_file')->store('videos', 'public');
            $data['storage_path'] = $videoPath;

            // Get video duration using ffprobe
            $fullPath = storage_path('app/public/' . $videoPath);
            $duration = $this->getVideoDuration($fullPath);
            $data['duration_sec'] = $duration;
        }

        // Handle external URL
        if ($request->external_url) {
            $data['external_url'] = $request->external_url;

            // Check if URL is secured (contains signature or token patterns)
            $isSecured = $this->checkUrlSecurity($request->external_url);
            $data['is_external_secured'] = $isSecured;
            $data['external_security_checked_at'] = now();

            // Try to get duration if possible (optional)
            $duration = $this->getExternalVideoDuration($request->external_url);
            if ($duration) {
                $data['duration_sec'] = $duration;
            }
        }

        Video::create($data);

        return redirect()->route('admin.videos.index')->with('success', 'Video berhasil ditambahkan.');
    }

    public function show(Video $video)
    {
        return view('admin.video.show', compact('video'));
    }

    public function edit(Video $video)
    {
        return view('admin.video.edit', compact('video'));
    }

    public function update(Request $request, Video $video)
    {
        $request->validate([
            'title' => 'required|string|max:150',
            'description' => 'nullable|string',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'video_file' => 'nullable|file|mimes:mp4,mov,avi,wmv|max:512000',
            'external_url' => 'nullable|url|max:255',
            'is_active' => 'boolean',
        ]);

        $data = [
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'description' => $request->description,
            'is_active' => $request->has('is_active'),
        ];

        // Handle thumbnail upload
        if ($request->hasFile('thumbnail')) {
            // Delete old thumbnail
            if ($video->thumbnail) {
                Storage::disk('public')->delete($video->thumbnail);
            }
            $thumbnailPath = $request->file('thumbnail')->store('thumbnails', 'public');
            $data['thumbnail'] = $thumbnailPath;
        }

        // Handle video file upload
        if ($request->hasFile('video_file')) {
            // Delete old video file
            if ($video->storage_path) {
                Storage::disk('public')->delete($video->storage_path);
            }

            $videoPath = $request->file('video_file')->store('videos', 'public');
            $data['storage_path'] = $videoPath;
            $data['external_url'] = null; // Clear external URL if uploading new file

            // Get video duration using ffprobe
            $fullPath = storage_path('app/public/' . $videoPath);
            $duration = $this->getVideoDuration($fullPath);
            $data['duration_sec'] = $duration;
        }

        // Handle external URL
        if ($request->external_url && !$request->hasFile('video_file')) {
            $data['external_url'] = $request->external_url;
            $data['storage_path'] = null; // Clear storage path if using external URL

            // Check if URL is secured
            $isSecured = $this->checkUrlSecurity($request->external_url);
            $data['is_external_secured'] = $isSecured;
            $data['external_security_checked_at'] = now();

            // Try to get duration
            $duration = $this->getExternalVideoDuration($request->external_url);
            if ($duration) {
                $data['duration_sec'] = $duration;
            }
        }

        $video->update($data);

        return redirect()->route('admin.videos.index')->with('success', 'Video berhasil diperbarui.');
    }

    public function destroy(Video $video)
    {
        // Delete files
        if ($video->thumbnail) {
            Storage::disk('public')->delete($video->thumbnail);
        }
        if ($video->storage_path) {
            Storage::disk('public')->delete($video->storage_path);
        }

        $video->delete();

        return redirect()->route('admin.videos.index')->with('success', 'Video berhasil dihapus.');
    }

    /**
     * Get video duration using ffprobe
     */
    private function getVideoDuration(string $filePath): ?int
    {
        try {
            // Get ffprobe path from environment variable
            $ffprobePath = env('FFPROBE_BINARIES', env('FFPROBE_BIN_PATH', 'ffprobe'));

            // Check if ffprobe is available
            $process = new Process([$ffprobePath, '-version']);
            $process->run();

            if (!$process->isSuccessful()) {
                Log::warning('ffprobe not found at: ' . $ffprobePath);
                return null;
            }

            // Get duration using ffprobe
            $process = new Process([
                $ffprobePath,
                '-v',
                'error',
                '-show_entries',
                'format=duration',
                '-of',
                'default=noprint_wrappers=1:nokey=1',
                $filePath
            ]);

            $process->run();

            if ($process->isSuccessful()) {
                $duration = trim($process->getOutput());
                return $duration ? (int) round((float) $duration) : null;
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Error getting video duration: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Check if external URL has security features (signed URL, tokens, etc.)
     */
    private function checkUrlSecurity(string $url): bool
    {
        // Check for common signature/token patterns
        $securityPatterns = [
            '/signature=/i',
            '/token=/i',
            '/key=/i',
            '/auth=/i',
            '/sign=/i',
            '/expires=/i',
            '/sig=/i',
        ];

        foreach ($securityPatterns as $pattern) {
            if (preg_match($pattern, $url)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Try to get duration from external video URL (optional, may not always work)
     */
    private function getExternalVideoDuration(string $url): ?int
    {
        try {
            // Get ffprobe path from environment variable
            $ffprobePath = env('FFPROBE_BINARIES', env('FFPROBE_BIN_PATH', 'ffprobe'));

            // Only try if ffprobe is available
            $process = new Process([$ffprobePath, '-version']);
            $process->run();

            if (!$process->isSuccessful()) {
                return null;
            }

            // Try to get duration from external URL
            $process = new Process([
                $ffprobePath,
                '-v',
                'error',
                '-show_entries',
                'format=duration',
                '-of',
                'default=noprint_wrappers=1:nokey=1',
                $url
            ]);

            $process->setTimeout(30); // 30 second timeout
            $process->run();

            if ($process->isSuccessful()) {
                $duration = trim($process->getOutput());
                return $duration ? (int) round((float) $duration) : null;
            }

            return null;
        } catch (\Exception $e) {
            Log::warning('Could not get external video duration: ' . $e->getMessage());
            return null;
        }
    }
}
