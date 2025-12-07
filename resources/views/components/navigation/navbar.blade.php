<header class="fixed inset-x-0 top-0 z-50 border-b border-white/10 bg-[#0f0f0f]/90 backdrop-blur">
    <div class="mx-auto flex h-16 w-full max-w-7xl items-center justify-between gap-3 px-4">
        {{-- Left: Logo --}}
        <div class="flex items-center gap-3">
            <button type="button" id="openSidebarBtn"
                class="grid h-10 w-10 place-items-center rounded-xl hover:bg-white/10 md:hidden"
                aria-label="Open sidebar">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2">
                    <path d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>

            <a href="/" class="flex items-center gap-2">
                <img src="/logo.jpg" alt="Logo" class="w-[150px] object-cover" onerror="this.style.display='none';">
            </a>
        </div>

        {{-- Middle: Search --}}
        <div class="hidden flex-1 items-center justify-center md:flex">
            <div class="flex w-full max-w-xl items-center">
                <div class="flex w-full items-center rounded-full bg-white/10 px-1">
                    <input type="text" placeholder="Cari video…"
                        class="w-full bg-transparent px-4 py-2.5 text-sm text-white placeholder:text-white/50
               border-0 ring-0 shadow-none outline-none appearance-none
               focus:border-0 focus:ring-0 focus:shadow-none focus:outline-none" />

                    <button type="button"
                        class="grid h-9 w-9 place-items-center rounded-full bg-transparent
               hover:bg-white/10 border-0 ring-0 shadow-none outline-none
               focus:outline-none focus:ring-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2">
                            <path d="M21 21l-4.3-4.3" />
                            <circle cx="11" cy="11" r="7" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        {{-- Right: Auth --}}
        <div class="flex items-center gap-2">
            @if (Route::has('login'))
                @auth
                    <a href="{{ url('/dashboard') }}"
                        class="rounded-full bg-white px-4 py-2 text-sm font-semibold text-black hover:bg-white/90">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}"
                        class="rounded-full px-4 py-2 text-sm font-semibold text-white/90 ring-1 ring-white/15 hover:bg-white/10">
                        Log in
                    </a>

                    @if (Route::has('register'))
                        <a href="{{ route('register') }}"
                            class="rounded-full bg-white px-4 py-2 text-sm font-semibold text-black hover:bg-white/90">
                            Register
                        </a>
                    @endif
                @endauth
            @endif
        </div>
    </div>
</header>
