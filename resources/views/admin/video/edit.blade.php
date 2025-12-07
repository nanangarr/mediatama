<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Video</h2>
            <a href="{{ route('admin.videos.index') }}"
                class="inline-flex items-center rounded-lg bg-gray-100 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-200">
                ← Kembali
            </a>
        </div>
    </x-slot>

    <div class="bg-white shadow sm:rounded-lg">
        @if (session('success'))
            <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg" role="alert">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('admin.videos.update', $video) }}" method="POST" enctype="multipart/form-data"
            class="p-6 space-y-6">
            @csrf
            @method('PUT')

            {{-- Judul Video --}}
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700">Judul Video</label>
                <input type="text" name="title" id="title" required value="{{ old('title', $video->title) }}"
                    class="mt-1 block w-full rounded-lg  focus:border-indigo-500 focus:ring-indigo-500 @error('title') border-red-500 @enderror"
                    placeholder="Masukkan judul video">
                @error('title')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Current Video Info --}}
            <div class="bg-gray-50 rounded-lg p-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Video Saat Ini</label>
                <div class="flex items-center gap-4">
                    @if ($video->thumbnail)
                        <img src="{{ asset('storage/' . $video->thumbnail) }}" alt="Thumbnail"
                            class="w-32 h-32 object-cover rounded-lg">
                    @else
                        <div class="flex-shrink-0 w-32 h-32 bg-gray-200 rounded-lg flex items-center justify-center">
                            <svg class="w-12 h-12 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" />
                            </svg>
                        </div>
                    @endif
                    <div class="text-sm text-gray-600">
                        <p><strong>Tipe:</strong> {{ $video->isExternal() ? 'URL Eksternal' : 'File Upload' }}</p>
                        @if ($video->isExternal())
                            <p><strong>URL:</strong> {{ Str::limit($video->external_url, 50) }}</p>
                        @else
                            <p><strong>File:</strong> {{ basename($video->storage_path) }}</p>
                        @endif
                        <p><strong>Durasi:</strong> {{ $video->duration_formatted }}</p>
                    </div>
                </div>
            </div>

            {{-- Upload/URL Options --}}
            @if (!$video->isExternal())
                {{-- Upload Video Baru --}}
                <div>
                    <label for="video_file" class="block text-sm font-medium text-gray-700">Ganti File Video
                        (Opsional)</label>
                    <input type="file" name="video_file" id="video_file" accept="video/*"
                        class="mt-1 block w-full text-sm text-gray-900 border  rounded-lg cursor-pointer bg-gray-50 focus:outline-none @error('video_file') border-red-500 @enderror">
                    <p class="mt-1 text-sm text-gray-500">Kosongkan jika tidak ingin mengganti video</p>
                    @error('video_file')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            @else
                {{-- Edit URL --}}
                <div>
                    <label for="external_url" class="block text-sm font-medium text-gray-700">URL Video</label>
                    <input type="url" name="external_url" id="external_url"
                        value="{{ old('external_url', $video->external_url) }}"
                        class="mt-1 block w-full rounded-lg  focus:border-indigo-500 focus:ring-indigo-500 @error('external_url') border-red-500 @enderror"
                        placeholder="https://example.com/video.mp4">
                    @error('external_url')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror

                    {{-- <label class="flex items-center mt-2">
                        <input type="checkbox" name="is_external_secured" value="1"
                            {{ old('is_external_secured', $video->is_external_secured) ? 'checked' : '' }}
                            class="mr-2">
                        <span class="text-sm text-gray-700">URL dilindungi (memerlukan autentikasi)</span>
                    </label> --}}
                </div>
            @endif

            {{-- Thumbnail --}}
            <div>
                <label for="thumbnail" class="block text-sm font-medium text-gray-700">Ganti Thumbnail
                    (Opsional)</label>
                <input type="file" name="thumbnail" id="thumbnail" accept="image/*"
                    class="mt-1 block w-full text-sm text-gray-900 border  rounded-lg cursor-pointer bg-gray-50 focus:outline-none @error('thumbnail') border-red-500 @enderror">
                @error('thumbnail')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Deskripsi --}}
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700">Deskripsi</label>
                <textarea name="description" id="description" rows="4"
                    class="mt-1 block w-full rounded-lg  focus:border-indigo-500 focus:ring-indigo-500 @error('description') border-red-500 @enderror"
                    placeholder="Deskripsi video...">{{ old('description', $video->description) }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Status --}}
            <div>
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" value="1"
                        {{ old('is_active', $video->is_active) ? 'checked' : '' }} class="mr-2">
                    <span class="text-sm font-medium text-gray-700">Aktifkan Video</span>
                </label>
            </div>

            {{-- Submit --}}
            <div class="flex items-center gap-3 pt-4">
                <button type="submit"
                    class="rounded-lg bg-gray-900 px-6 py-2.5 text-sm font-semibold text-white hover:bg-gray-800">
                    Update Video
                </button>
                <a href="{{ route('admin.videos.index') }}"
                    class="rounded-lg bg-gray-100 px-6 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-200">
                    Batal
                </a>
            </div>
        </form>
    </div>
</x-app-layout>
