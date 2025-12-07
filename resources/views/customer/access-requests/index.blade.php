<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Request Akses Saya</h2>
    </x-slot>

    <div class="space-y-4">
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        {{-- Statistics --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white shadow sm:rounded-lg p-6">
                <div class="text-sm font-medium text-gray-500">Total Request</div>
                <div class="mt-2 text-3xl font-bold text-gray-900">{{ $accessRequests->total() }}</div>
            </div>
            <div class="bg-white shadow sm:rounded-lg p-6">
                <div class="text-sm font-medium text-gray-500">Pending</div>
                <div class="mt-2 text-3xl font-bold text-yellow-600">
                    {{ $accessRequests->where('status', 'pending')->count() }}</div>
            </div>
            <div class="bg-white shadow sm:rounded-lg p-6">
                <div class="text-sm font-medium text-gray-500">Approved</div>
                <div class="mt-2 text-3xl font-bold text-green-600">
                    {{ $accessRequests->where('status', 'approved')->count() }}</div>
            </div>
        </div>

        {{-- Requests List --}}
        <div class="bg-white shadow sm:rounded-lg">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Riwayat Request</h3>
                    <a href="{{ route('home') }}"
                        class="rounded-lg bg-gray-900 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-800">
                        Request Video Baru
                    </a>
                </div>

                <div class="space-y-4">
                    @forelse($accessRequests as $request)
                        @php
                            $canWatch =
                                $request->status === 'approved' &&
                                $request->videoAccess &&
                                $request->videoAccess->isActive();
                            $watchUrl = $canWatch ? route('customer.videos.watch', $request->video) : null;
                        @endphp

                        <div class="border border-gray-200 rounded-lg p-4 {{ $canWatch ? 'hover:bg-gray-50 cursor-pointer transition-colors' : '' }}"
                            @if ($canWatch) onclick="window.location.href='{{ $watchUrl }}'" @endif>
                            <div class="flex items-start gap-4">
                                @if ($request->video->thumbnail)
                                    <img src="{{ asset('storage/' . $request->video->thumbnail) }}"
                                        alt="{{ $request->video->title }}"
                                        class="w-32 h-32 object-cover rounded-lg flex-shrink-0">
                                @else
                                    <div
                                        class="w-32 h-32 bg-gray-200 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <svg class="w-12 h-12 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" />
                                        </svg>
                                    </div>
                                @endif

                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start justify-between">
                                        <div>
                                            <h4 class="text-lg font-semibold text-gray-900">
                                                {{ $request->video->title }}
                                            </h4>
                                            <p class="text-sm text-gray-500 mt-1">Request:
                                                {{ $request->requested_minutes }} menit | Video:
                                                {{ $request->video->duration_formatted }}</p>
                                        </div>
                                        @if ($request->status === 'pending')
                                            <span
                                                class="inline-flex items-center rounded-full bg-yellow-50 px-3 py-1 text-sm font-semibold text-yellow-700">Pending</span>
                                        @elseif($request->status === 'approved')
                                            <span
                                                class="inline-flex items-center rounded-full bg-green-50 px-3 py-1 text-sm font-semibold text-green-700">Approved</span>
                                        @else
                                            <span
                                                class="inline-flex items-center rounded-full bg-red-50 px-3 py-1 text-sm font-semibold text-red-700">Rejected</span>
                                        @endif
                                    </div>

                                    <div class="mt-2 text-sm text-gray-600">
                                        <p>Request pada: {{ $request->created_at->format('d M Y, H:i') }}</p>

                                        @if ($request->status === 'approved')
                                            @if ($request->videoAccess)
                                                <p class="text-green-600 font-medium mt-1">
                                                    Disetujui: {{ $request->videoAccess->approved_minutes }} menit
                                                    @if ($request->videoAccess->grace_minutes > 0)
                                                        (+ {{ $request->videoAccess->grace_minutes }} menit grace)
                                                    @endif
                                                </p>
                                                @if ($request->videoAccess->isActive())
                                                    <p class="text-blue-600 mt-1">Sisa waktu:
                                                        {{ $request->videoAccess->remaining_minutes }} menit</p>
                                                @else
                                                    <p class="text-gray-500 mt-1">Akses sudah berakhir</p>
                                                @endif
                                            @endif
                                        @elseif($request->status === 'rejected' && $request->rejection_reason)
                                            <p class="text-red-600 mt-1">Alasan: {{ $request->rejection_reason }}</p>
                                        @endif
                                    </div>

                                    <div class="mt-3 flex gap-2">
                                        @if ($canWatch)
                                            <a href="{{ $watchUrl }}"
                                                class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700"
                                                onclick="event.stopPropagation()">
                                                🎬 Tonton Video
                                            </a>
                                        @endif

                                        @if ($request->isPending())
                                            <form action="{{ route('customer.access-requests.cancel', $request) }}"
                                                method="POST"
                                                onsubmit="return confirm('Yakin ingin membatalkan request ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="rounded-lg bg-red-100 px-4 py-2 text-sm font-semibold text-red-700 hover:bg-red-200">
                                                    Batalkan Request
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12 text-gray-500">
                            <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <p class="text-lg font-medium">Belum ada request akses</p>
                            <p class="text-sm text-gray-400 mt-1">Mulai request akses ke video yang Anda inginkan</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Pagination --}}
        <div class="bg-white shadow sm:rounded-lg p-4">
            {{ $accessRequests->links() }}
        </div>
    </div>
</x-app-layout>
