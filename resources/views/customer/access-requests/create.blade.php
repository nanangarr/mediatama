<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Request Akses Video</h2>
            <a href="{{ route('videos.show', $video) }}"
                class="inline-flex items-center rounded-lg bg-gray-100 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-200">
                ← Kembali
            </a>
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto">
        @if (session('error'))
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        {{-- Video Info --}}
        <div class="bg-white shadow sm:rounded-lg mb-6">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Video yang Direquest</h3>
                <div class="flex items-start gap-4">
                    @if ($video->thumbnail)
                        <img src="{{ asset('storage/' . $video->thumbnail) }}" alt="{{ $video->title }}"
                            class="w-48 h-auto object-cover rounded-lg">
                    @else
                        <div class="w-48 h-32 bg-gray-200 rounded-lg flex items-center justify-center">
                            <svg class="w-12 h-12 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" />
                            </svg>
                        </div>
                    @endif
                    <div class="flex-1">
                        <h4 class="text-xl font-bold text-gray-900">{{ $video->title }}</h4>
                        @if ($video->description)
                            <p class="text-gray-600 mt-2">{{ $video->description }}</p>
                        @endif
                        <div class="mt-3 text-sm text-gray-500">
                            <p><strong>Durasi Video:</strong> {{ $video->duration_formatted }}
                                ({{ $video->duration_sec }} detik)</p>
                            <p><strong>Tipe:</strong> {{ $video->isExternal() ? 'URL Eksternal' : 'File Upload' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Request Form --}}
        <div class="bg-white shadow sm:rounded-lg">
            <form action="{{ route('customer.access-requests.store') }}" method="POST" class="p-6 space-y-6">
                @csrf
                <input type="hidden" name="video_id" value="{{ $video->id }}">

                <div>
                    <label for="requested_minutes" class="block text-sm font-medium text-gray-700">Durasi Akses yang
                        Diminta (menit)</label>
                    <input type="number" name="requested_minutes" id="requested_minutes"
                        value="{{ old('requested_minutes', ceil($video->duration_sec / 60)) }}"
                        min="{{ ceil($video->duration_sec / 60) }}" required
                        class="mt-1 block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 @error('requested_minutes') border-red-500 @enderror">
                    <p class="mt-1 text-sm text-gray-500">
                        Minimal: {{ ceil($video->duration_sec / 60) }} menit (sesuai durasi video)
                    </p>
                    @error('requested_minutes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700">Catatan (Opsional)</label>
                    <textarea name="notes" id="notes" rows="4"
                        class="mt-1 block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 @error('notes') border-red-500 @enderror"
                        placeholder="Tambahkan catatan untuk admin...">{{ old('notes') }}</textarea>
                    <p class="mt-1 text-sm text-gray-500">
                        Jelaskan alasan Anda membutuhkan akses ke video ini
                    </p>
                    @error('notes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <h4 class="text-sm font-semibold text-blue-900 mb-2">Informasi Penting:</h4>
                    <ul class="text-sm text-blue-800 space-y-1 list-disc list-inside">
                        <li>Request Anda akan direview oleh admin</li>
                        <li>Durasi akses akan ditentukan oleh admin berdasarkan request Anda</li>
                        <li>Setelah disetujui, Anda akan menerima notifikasi via email</li>
                        <li>Akses video memiliki batas waktu tertentu</li>
                    </ul>
                </div>

                <div class="flex items-center gap-3 pt-4">
                    <button type="submit"
                        class="rounded-lg bg-gray-900 px-6 py-2.5 text-sm font-semibold text-white hover:bg-gray-800">
                        Kirim Request
                    </button>
                    <a href="{{ route('videos.show', $video) }}"
                        class="rounded-lg bg-gray-100 px-6 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-200">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
