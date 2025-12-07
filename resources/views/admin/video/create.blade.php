<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Tambah Video</h2>
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

        <form action="{{ route('admin.videos.store') }}" method="POST" enctype="multipart/form-data"
            class="p-6 space-y-6">
            @csrf

            {{-- Pilihan Upload atau URL --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Sumber Video</label>
                <div class="flex gap-4">
                    <label class="flex items-center">
                        <input type="radio" name="video_source" value="upload" checked class="mr-2"
                            onchange="toggleVideoSource()">
                        <span>Upload File</span>
                    </label>
                    <label class="flex items-center">
                        <input type="radio" name="video_source" value="url" class="mr-2"
                            onchange="toggleVideoSource()">
                        <span>URL Eksternal</span>
                    </label>
                </div>
            </div>

            {{-- Judul Video --}}
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700">Judul Video</label>
                <input type="text" name="title" id="title" required value="{{ old('title') }}"
                    class="mt-1 block w-full rounded-lg focus:border-indigo-500 focus:ring-indigo-500 @error('title') border-red-500 @enderror"
                    placeholder="Masukkan judul video">
                @error('title')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Deskripsi --}}
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700">Deskripsi</label>
                <textarea name="description" id="description" rows="4"
                    class="mt-1 block w-full rounded-lg focus:border-indigo-500 focus:ring-indigo-500 @error('description') border-red-500 @enderror"
                    placeholder="Deskripsi video...">{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Thumbnail --}}
            <div>
                <label for="thumbnail" class="block text-sm font-medium text-gray-700">Thumbnail</label>
                <input type="file" name="thumbnail" id="thumbnail" accept="image/*"
                    class="mt-1 block w-full text-sm text-gray-900 border rounded-lg cursor-pointer bg-gray-50 focus:outline-none @error('thumbnail') border-red-500 @enderror">
                <p class="mt-1 text-sm text-gray-500">Format: JPG, PNG (Opsional)</p>
                @error('thumbnail')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Upload Video --}}
            <div id="upload_section">
                <label for="video_file" class="block text-sm font-medium text-gray-700">File Video</label>
                <input type="file" name="video_file" id="video_file" accept="video/*"
                    class="mt-1 block w-full text-sm text-gray-900 border rounded-lg cursor-pointer bg-gray-50 focus:outline-none @error('video_file') border-red-500 @enderror">
                <p class="mt-1 text-sm text-gray-500">Format: MP4, MOV, AVI (Max: 500MB)</p>
                @error('video_file')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- URL Eksternal --}}
            <div id="url_section" style="display: none;">
                <label for="external_url" class="block text-sm font-medium text-gray-700">URL Video</label>
                <input type="url" name="external_url" id="external_url" value="{{ old('external_url') }}"
                    class="mt-1 block w-full rounded-lg focus:border-indigo-500 focus:ring-indigo-500 @error('external_url') border-red-500 @enderror"
                    placeholder="https://example.com/video.mp4">
                <p class="mt-1 text-sm text-gray-500">Masukkan URL video eksternal</p>
                @error('external_url')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror

                {{-- <label class="flex items-center mt-2">
                    <input type="checkbox" name="is_external_secured" value="1"
                        {{ old('is_external_secured') ? 'checked' : '' }} class="mr-2">
                    <span class="text-sm text-gray-700">URL dilindungi (memerlukan autentikasi)</span>
                </label> --}}
            </div>

            {{-- Status --}}
            <div>
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" value="1"
                        {{ old('is_active', true) ? 'checked' : '' }} class="mr-2">
                    <span class="text-sm font-medium text-gray-700">Aktifkan Video</span>
                </label>
            </div>

            {{-- Submit --}}
            <div class="flex items-center gap-3 pt-4">
                <button type="submit"
                    class="rounded-lg bg-gray-900 px-6 py-2.5 text-sm font-semibold text-white hover:bg-gray-800">
                    Simpan Video
                </button>
                <a href="{{ route('admin.videos.index') }}"
                    class="rounded-lg bg-gray-100 px-6 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-200">
                    Batal
                </a>
            </div>
        </form>
    </div>

    <script>
        function toggleVideoSource() {
            const source = document.querySelector('input[name="video_source"]:checked').value;
            document.getElementById('upload_section').style.display = source === 'upload' ? 'block' : 'none';
            document.getElementById('url_section').style.display = source === 'url' ? 'block' : 'none';
        }
    </script>
</x-app-layout>
