<aside class="hidden md:flex md:w-64 md:flex-col bg-white border-r border-gray-200">
    <div class="h-16 flex items-center gap-3 px-4 border-b border-gray-200">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
            <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
            <span class="font-semibold text-gray-900">{{ config('app.name', 'Laravel') }}</span>
        </a>
    </div>

    <div class="flex-1 px-3 py-4">
        <div class="space-y-1">
            @if (auth()->check() && auth()->user()->hasRole('admin'))
                <a href="{{ route('admin.customers.index') }}"
                    class="block rounded-lg px-3 py-2 text-sm font-medium
                   {{ request()->routeIs('admin.customers.index') ? 'bg-gray-100 text-gray-900' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                    Dashboard
                </a>
            @else
                <a href="{{ route('home') }}"
                    class="block rounded-lg px-3 py-2 text-sm font-medium
                   {{ request()->routeIs('home') ? 'bg-gray-100 text-gray-900' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                    Dashboard
                </a>
            @endif

            @if (auth()->check() && auth()->user()->hasRole('admin'))
                <a href="{{ route('admin.customers.index') }}"
                    class="block rounded-lg px-3 py-2 text-sm font-medium
                   {{ request()->routeIs('admin.customers.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                    Customer
                </a>

                <a href="{{ route('admin.videos.index') }}"
                    class="block rounded-lg px-3 py-2 text-sm font-medium
                   {{ request()->routeIs('admin.videos.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                    Video
                </a>

                <a href="{{ route('admin.access-requests.index') }}"
                    class="block rounded-lg px-3 py-2 text-sm font-medium
                   {{ request()->routeIs('admin.access-requests.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                    Request Akses
                </a>
            @endif

            @if (auth()->check() && auth()->user()->hasRole('customer'))
                <a href="{{ route('customer.videos.index') }}"
                    class="block rounded-lg px-3 py-2 text-sm font-medium
                   {{ request()->routeIs('customer.videos.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                    My Videos
                </a>

                <a href="{{ route('customer.access-requests.index') }}"
                    class="block rounded-lg px-3 py-2 text-sm font-medium
                   {{ request()->routeIs('customer.access-requests.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                    My Requests
                </a>
            @endif
        </div>
    </div>

    <div class="border-t border-gray-200 p-3">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                class="w-full rounded-lg px-3 py-2 text-left text-sm font-medium text-red-600 hover:bg-red-50">
                Logout
            </button>
        </form>
    </div>
</aside>

{{-- Mobile drawer sidebar --}}
<div x-show="sidebarOpen" x-cloak class="md:hidden">
    <div class="fixed inset-0 z-40">
        <div class="absolute inset-0 bg-black/40" @click="sidebarOpen=false"></div>

        <div class="absolute left-0 top-0 h-full w-72 bg-white border-r border-gray-200 flex flex-col">
            <div class="h-16 flex items-center justify-between px-4 border-b border-gray-200">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                    <x-application-logo class="block h-8 w-auto fill-current text-gray-800" />
                    <span class="font-semibold text-gray-900">{{ config('app.name', 'Laravel') }}</span>
                </a>
                <button @click="sidebarOpen=false" class="p-2 rounded-md hover:bg-gray-100 text-gray-600">✕</button>
            </div>

            <div class="flex-1 px-3 py-4 space-y-1">
                @if (auth()->check() && auth()->user()->hasRole('admin'))
                    <a href="{{ route('admin.customers.index') }}"
                        class="block rounded-lg px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-gray-900">Dashboard</a>
                @else
                    <a href="{{ route('home') }}"
                        class="block rounded-lg px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-gray-900">Dashboard</a>
                @endif

                @if (auth()->check() && auth()->user()->hasRole('admin'))
                    <a href="{{ route('admin.customers.index') }}"
                        class="block rounded-lg px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-gray-900">Customer</a>
                    <a href="{{ route('admin.videos.index') }}"
                        class="block rounded-lg px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-gray-900">Video</a>
                    <a href="{{ route('admin.access-requests.index') }}"
                        class="block rounded-lg px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-gray-900">Request
                        Akses</a>
                @endif

                @if (auth()->check() && auth()->user()->hasRole('customer'))
                    <a href="{{ route('customer.videos.index') }}"
                        class="block rounded-lg px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-gray-900">My
                        Videos</a>
                    <a href="{{ route('customer.access-requests.index') }}"
                        class="block rounded-lg px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-gray-900">My
                        Requests</a>
                @endif
            </div>

            <div class="border-t border-gray-200 p-3">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="w-full rounded-lg px-3 py-2 text-left text-sm font-medium text-red-600 hover:bg-red-50">
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
