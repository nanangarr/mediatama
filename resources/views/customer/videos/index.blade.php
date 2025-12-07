<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            @auth
                Semua Video
            @else
                Selamat Datang di Mediatama
            @endauth
        </h2>
    </x-slot>

    <div class="space-y-4">
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @guest
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <p class="text-blue-800 text-sm">
                    <strong>Info:</strong> Login untuk bisa request akses video.
                    <a href="{{ route('login') }}" class="underline font-semibold hover:text-blue-900">Login sekarang</a>
                </p>
            </div>
        @endguest

        {{-- Video List --}}
        <div class="bg-white shadow sm:rounded-lg">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">
                        @auth
                            Jelajahi Video
                        @else
                            Koleksi Video Kami
                        @endauth
                    </h3>
                    @auth
                        <div class="flex gap-2">
                            <a href="{{ route('customer.videos.index') }}"
                                class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                                Video Saya
                            </a>
                            <span class="text-gray-300">|</span>
                            <a href="{{ route('customer.access-requests.index') }}"
                                class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                                My Requests
                            </a>
                        </div>
                    @endauth
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse($videos as $video)
                        <div
                            class="border border-gray-200 rounded-lg overflow-hidden hover:shadow-lg transition-shadow">
                            <a href="{{ route('videos.show', $video) }}" class="block">
                                <div class="aspect-video bg-gray-100 relative">
                                    @if ($video->thumbnail)
                                        <img src="{{ asset('storage/' . $video->thumbnail) }}" alt="{{ $video->title }}"
                                            class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                                            <svg class="w-12 h-12 text-gray-400" fill="currentColor"
                                                viewBox="0 0 20 20">
                                                <path
                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" />
                                            </svg>
                                        </div>
                                    @endif
                                    <div
                                        class="absolute top-2 right-2 bg-black/70 text-white px-2 py-1 rounded text-xs font-medium">
                                        {{ $video->duration_formatted }}
                                    </div>
                                </div>
                            </a>

                            <div class="p-4">
                                <a href="{{ route('videos.show', $video) }}">
                                    <h4 class="font-semibold text-gray-900 mb-2 line-clamp-2 hover:text-blue-600">
                                        {{ $video->title }}
                                    </h4>
                                </a>

                                @if ($video->description)
                                    <p class="text-sm text-gray-600 mb-3 line-clamp-2">
                                        {{ $video->description }}
                                    </p>
                                @endif

                                <div class="flex gap-2">
                                    <a href="{{ route('videos.show', $video) }}"
                                        class="flex-1 text-center rounded-lg bg-gray-100 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-200">
                                        Lihat Detail
                                    </a>
                                    @auth
                                        <a href="{{ route('customer.access-requests.create', ['video_id' => $video->id]) }}"
                                            class="flex-1 text-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                                            Request Akses
                                        </a>
                                    @else
                                        <a href="{{ route('login') }}"
                                            class="flex-1 text-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                                            Login
                                        </a>
                                    @endauth
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full py-12 text-center text-gray-500">
                            <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                            <p class="text-lg">Belum ada video tersedia</p>
                        </div>
                    @endforelse
                </div>

                @if ($videos->hasPages())
                    <div class="mt-6">
                        {{ $videos->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
