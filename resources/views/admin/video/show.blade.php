<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Detail Video</h2>
            <a href="{{ route('admin.videos.index') }}"
                class="inline-flex items-center rounded-lg bg-gray-100 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-200">
                ← Kembali
            </a>
        </div>
    </x-slot>

    <div class="space-y-4">
        {{-- Video Preview --}}
        <div class="bg-white shadow sm:rounded-lg overflow-hidden">
            @if ($video->thumbnail)
                <img src="{{ asset('storage/' . $video->thumbnail) }}" alt="{{ $video->title }}"
                    class="w-full aspect-video object-cover">
            @else
                <div class="aspect-video bg-gray-900 flex items-center justify-center">
                    <svg class="w-24 h-24 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                        <path
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" />
                    </svg>
                </div>
            @endif
        </div>

        {{-- Info Video --}}
        <div class="bg-white shadow sm:rounded-lg">
            <div class="p-6 space-y-4">
                <div class="flex items-center justify-between border-b pb-4">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900">{{ $video->title }}</h3>
                        <p class="text-sm text-gray-500 mt-1">Slug: {{ $video->slug }}</p>
                    </div>
                    <span
                        class="inline-flex items-center rounded-full px-3 py-1.5 text-sm font-semibold {{ $video->is_active ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700' }}">
                        {{ $video->is_active ? 'Aktif' : 'Tidak Aktif' }}
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Tipe Video --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Tipe Video</label>
                        <p class="mt-1 text-base text-gray-900">
                            @if ($video->isExternal())
                                <span class="inline-flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                    </svg>
                                    URL Eksternal
                                </span>
                            @else
                                <span class="inline-flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                    </svg>
                                    File Upload
                                </span>
                            @endif
                        </p>
                    </div>

                    {{-- Durasi --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Durasi</label>
                        <p class="mt-1 text-base text-gray-900">{{ $video->duration_formatted }}</p>
                    </div>

                    {{-- URL atau Path --}}
                    <div class="md:col-span-2">
                        <label
                            class="block text-sm font-medium text-gray-500">{{ $video->isExternal() ? 'URL Video' : 'Path File' }}</label>
                        <p class="mt-1 text-base text-gray-900 break-all">
                            {{ $video->isExternal() ? $video->external_url : $video->storage_path }}
                        </p>
                        @if ($video->isExternal() && $video->is_external_secured)
                            <span class="inline-flex items-center mt-1 text-sm text-amber-600">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                                URL Dilindungi
                            </span>
                        @endif
                    </div>

                    {{-- Tanggal Upload --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Dibuat</label>
                        <p class="mt-1 text-base text-gray-900">{{ $video->created_at->format('d M Y, H:i') }}</p>
                    </div>

                    {{-- Update Terakhir --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Update Terakhir</label>
                        <p class="mt-1 text-base text-gray-900">{{ $video->updated_at->format('d M Y, H:i') }}</p>
                    </div>

                    {{-- Deskripsi --}}
                    @if ($video->description)
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-500">Deskripsi</label>
                            <p class="mt-1 text-base text-gray-900">{{ $video->description }}</p>
                        </div>
                    @endif
                </div>

                {{-- Statistik Access Requests --}}
                <div class="border-t pt-4 mt-4">
                    <h4 class="text-lg font-semibold text-gray-900 mb-3">Statistik Akses</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-blue-50 rounded-lg p-4">
                            <p class="text-sm text-blue-600 font-medium">Total Request</p>
                            <p class="text-2xl font-bold text-blue-900">{{ $video->accessRequests->count() }}</p>
                        </div>
                        <div class="bg-yellow-50 rounded-lg p-4">
                            <p class="text-sm text-yellow-600 font-medium">Pending</p>
                            <p class="text-2xl font-bold text-yellow-900">
                                {{ $video->accessRequests->where('status', 'pending')->count() }}</p>
                        </div>
                        <div class="bg-green-50 rounded-lg p-4">
                            <p class="text-sm text-green-600 font-medium">Approved</p>
                            <p class="text-2xl font-bold text-green-900">
                                {{ $video->accessRequests->where('status', 'approved')->count() }}</p>
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex flex-wrap gap-3 pt-4 border-t">
                    <a href="{{ route('admin.videos.edit', $video) }}"
                        class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
                        Edit Video
                    </a>
                    <a href="{{ route('videos.show', $video) }}" target="_blank"
                        class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                        Lihat di Frontend
                    </a>
                    <form action="{{ route('admin.videos.destroy', $video) }}" method="POST" class="inline"
                        onsubmit="return confirm('Yakin ingin menghapus video ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700">
                            Hapus Video
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
