<header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-4 sm:px-6 lg:px-8">
    <div class="flex items-center gap-2 md:hidden">
        <button @click="sidebarOpen = !sidebarOpen"
            class="inline-flex items-center justify-center p-2 rounded-md text-gray-600 hover:bg-gray-100">
            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
        <span class="font-semibold text-gray-900">{{ config('app.name', 'Laravel') }}</span>
    </div>

    <div class="ml-auto">
        @auth
            <x-dropdown align="right" width="48">
                <x-slot name="trigger">
                    <button
                        class="inline-flex items-center gap-2 px-3 py-2 rounded-md text-sm font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-100 focus:outline-none transition">
                        <div class="truncate max-w-[180px]">{{ Auth::user()->name }}</div>
                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>
                </x-slot>

                <x-slot name="content">
                    <x-dropdown-link :href="route('profile.edit')">
                        {{ __('Profile') }}
                    </x-dropdown-link>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                            {{ __('Log Out') }}
                        </x-dropdown-link>
                    </form>
                </x-slot>
            </x-dropdown>
        @else
            <div class="flex items-center gap-2">
                <a href="{{ route('login') }}"
                    class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-100">
                    Login
                </a>
                @if (Route::has('register'))
                    <a href="{{ route('register') }}"
                        class="inline-flex items-center px-4 py-2 rounded-lg bg-blue-600 text-sm font-semibold text-white hover:bg-blue-700">
                        Register
                    </a>
                @endif
            </div>
        @endauth
    </div>
</header>
