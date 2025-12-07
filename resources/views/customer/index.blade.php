<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Video Saya</h2>
    </x-slot>

    <div class="space-y-4">
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        {{-- Quick Stats --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-white shadow sm:rounded-lg p-6">
                <div class="text-sm font-medium text-gray-500">Video dengan Akses Aktif</div>
                <div class="mt-2 text-3xl font-bold text-blue-600">{{ $activeVideos->count() }}</div>
            </div>
            <div class="bg-white shadow sm:rounded-lg p-6">
                <div class="text-sm font-medium text-gray-500">Request Pending</div>
                <div class="mt-2 text-3xl font-bold text-yellow-600">
                    {{ auth()->user()->accessRequests()->where('status', 'pending')->count() }}</div>
            </div>
        </div>

        {{-- Active Videos --}}
        <div class="bg-white shadow sm:rounded-lg">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Video yang Dapat Ditonton</h3>
                    <div class="flex gap-2">
                        <a href="{{ route('customer.access-requests.index') }}"
                            class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                            Lihat Request Saya
                        </a>
                        <span class="text-gray-300">|</span>
                        <a href="{{ route('home') }}"
                            class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                            Jelajahi Video
                        </a>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse($activeVideos as $accessData)
                        @php
                            $video = $accessData['video'];
                            $access = $accessData['access'];
                        @endphp
                        <div
                            class="border border-gray-200 rounded-lg overflow-hidden hover:shadow-lg transition-shadow">
                            <div class="aspect-video bg-gray-100 relative">
                                @if ($video->thumbnail)
                                    <img src="{{ asset('storage/' . $video->thumbnail) }}" alt="{{ $video->title }}"
                                        class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                                        <svg class="w-12 h-12 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
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
                            <div class="p-4">
                                <h4 class="font-semibold text-gray-900 mb-2 line-clamp-2">{{ $video->title }}</h4>
                                <div class="text-sm text-gray-600 space-y-1 mb-3">
                                    <p class="flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Sisa: {{ $access->remaining_minutes }} menit
                                    </p>
                                    @if ($access->remaining_minutes <= 10)
                                        <p class="text-yellow-600 font-medium">Segera berakhir!</p>
                                    @endif
                                </div>
                                <a href="{{ route('customer.videos.watch', $video) }}"
                                    class="block w-full text-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                                    Tonton Sekarang
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full text-center py-12 text-gray-500">
                            <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                            <p class="text-lg font-medium">Belum ada video dengan akses aktif</p>
                            <p class="text-sm text-gray-400 mt-1">Request akses ke video yang ingin Anda tonton</p>
                            <a href="{{ route('home') }}"
                                class="inline-block mt-4 rounded-lg bg-gray-900 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-800">
                                Jelajahi Video
                            </a>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
