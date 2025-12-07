@props([
    'allVideos' => [],
    'requestedVideos' => [],
])

<aside id="appSidebar"
    class="fixed left-0 top-16 z-40 hidden h-[calc(100vh-4rem)] w-72 border-r border-white/10 bg-[#0f0f0f]/95 backdrop-blur
           md:block">

    <div class="h-full overflow-y-auto p-4">
        <div class="mb-4">
            <p class="text-xs font-semibold tracking-wider text-white/50">MENU</p>

            <nav class="mt-3 space-y-1">
                <a href="{{ url('/') }}"
                    class="block rounded-xl px-3 py-2 text-sm text-white/80 hover:bg-white/10">
                    All Videos
                    <span class="ml-2 rounded-full bg-white/10 px-2 py-0.5 text-xs text-white/70">
                        {{ count($allVideos) }}
                    </span>
                </a>

                @auth
                    <a href="{{ url('/requests') }}"
                        class="block rounded-xl px-3 py-2 text-sm text-white/80 hover:bg-white/10">
                        Requested Videos
                        <span class="ml-2 rounded-full bg-white/10 px-2 py-0.5 text-xs text-white/70">
                            {{ count($requestedVideos) }}
                        </span>
                    </a>
                @endauth
            </nav>
        </div>

        {{-- <div class="mt-6">
            <p class="text-xs font-semibold tracking-wider text-white/50">LIST (RINGKAS)</p>

            <div class="mt-3">
                <p class="mb-2 text-xs text-white/50">All</p>
                <ul class="space-y-1">
                    @foreach (array_slice($allVideos, 0, 8) as $v)
                        <li class="truncate rounded-lg px-3 py-2 text-xs text-white/70 hover:bg-white/5">
                            {{ $v['title'] ?? '-' }}
                        </li>
                    @endforeach
                </ul>
            </div>

            @auth
                <div class="mt-5">
                    <p class="mb-2 text-xs text-white/50">Requested</p>
                    <ul class="space-y-1">
                        @forelse (array_slice($requestedVideos, 0, 8) as $v)
                            <li class="truncate rounded-lg px-3 py-2 text-xs text-white/70 hover:bg-white/5">
                                {{ $v['title'] ?? '-' }}
                            </li>
                        @empty
                            <li class="rounded-lg px-3 py-2 text-xs text-white/40">
                                Belum ada request.
                            </li>
                        @endforelse
                    </ul>
                </div>
            @endauth
        </div> --}}
    </div>
</aside>

{{-- Mobile overlay + drawer --}}
<div id="sidebarOverlay" class="fixed inset-0 z-40 hidden bg-black/60 md:hidden"></div>

<aside id="appSidebarMobile"
    class="fixed left-0 top-0 z-50 hidden h-full w-80 border-r border-white/10 bg-[#0f0f0f] md:hidden">
    <div class="flex h-16 items-center justify-between border-b border-white/10 px-4">
        <p class="text-sm font-semibold">Menu</p>
        <button type="button" id="closeSidebarBtn"
            class="rounded-lg px-3 py-2 text-sm text-white/80 hover:bg-white/10">
            Tutup
        </button>
    </div>

    <div class="h-[calc(100vh-4rem)] overflow-y-auto p-4">
        {{-- reuse layout desktop (simple) --}}
        <a href="{{ url('/') }}" class="block rounded-xl px-3 py-2 text-sm text-white/80 hover:bg-white/10">
            All Videos <span class="ml-2 rounded-full bg-white/10 px-2 py-0.5 text-xs">{{ count($allVideos) }}</span>
        </a>

        @auth
            <a href="{{ url('/requests') }}" class="mt-2 block rounded-xl px-3 py-2 text-sm text-white/80 hover:bg-white/10">
                Requested Videos <span class="ml-2 rounded-full bg-white/10 px-2 py-0.5 text-xs">{{ count($requestedVideos) }}</span>
            </a>
        @endauth
    </div>
</aside>