<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $video->title }}</h2>
            <a href="{{ route('home') }}"
                class="inline-flex items-center rounded-lg bg-gray-100 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-200">
                ← Kembali
            </a>
        </div>
    </x-slot>

    <div class="space-y-6">
        {{-- Video Thumbnail --}}
        <div class="bg-white shadow sm:rounded-lg overflow-hidden">
            <div class="aspect-video bg-gray-100">
                @if ($video->thumbnail)
                    <img src="{{ asset('storage/' . $video->thumbnail) }}" alt="{{ $video->title }}"
                        class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                        <svg class="w-24 h-24 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" />
                        </svg>
                    </div>
                @endif
            </div>
        </div>

        {{-- Video Info --}}
        <div class="bg-white shadow sm:rounded-lg">
            <div class="p-6">
                <div class="flex items-center gap-4 text-sm text-gray-500 mb-4">
                    <span>Durasi: {{ $video->duration_formatted }}</span>
                    <span>•</span>
                    <span>{{ $video->created_at->format('d M Y') }}</span>
                </div>

                @if ($video->description)
                    <p class="text-gray-700 leading-relaxed mb-6">
                        {{ $video->description }}
                    </p>
                @endif

                @auth
                    @if ($hasActiveAccess)
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                            <p class="text-green-700 font-semibold mb-2">✓ Anda memiliki akses aktif ke video ini</p>
                            <p class="text-sm text-green-600">Sisa waktu: {{ $activeAccess->remaining_minutes }} menit</p>
                        </div>
                        <a href="{{ route('customer.videos.watch', $video) }}"
                            class="inline-flex items-center rounded-lg bg-blue-600 px-6 py-3 text-sm font-semibold text-white hover:bg-blue-700">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" />
                            </svg>
                            Tonton Sekarang
                        </a>
                    @elseif($hasPendingRequest)
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <p class="text-yellow-700 font-semibold">⏳ Request akses Anda sedang diproses</p>
                            <p class="text-sm text-yellow-600 mt-1">Silakan tunggu approval dari admin</p>
                        </div>
                    @else
                        <a href="{{ route('customer.access-requests.create', ['video_id' => $video->id]) }}"
                            class="inline-flex items-center rounded-lg bg-blue-600 px-6 py-3 text-sm font-semibold text-white hover:bg-blue-700">
                            Request Akses Video
                        </a>
                    @endif
                @else
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <p class="text-blue-800 mb-3">Login untuk request akses video ini</p>
                        <a href="{{ route('login') }}"
                            class="inline-flex items-center rounded-lg bg-blue-600 px-6 py-3 text-sm font-semibold text-white hover:bg-blue-700">
                            Login Sekarang
                        </a>
                    </div>
                @endauth
            </div>
        </div>
    </div>
</x-app-layout>
