<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Detail Customer</h2>
            <div class="flex gap-2">
                <a href="{{ route('admin.customers.edit', $customer) }}"
                    class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
                    Edit Customer
                </a>
                <a href="{{ route('admin.customers.index') }}"
                    class="inline-flex items-center rounded-lg bg-gray-100 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-200">
                    ← Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-4">
        @if (session('success'))
            <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        {{-- Customer Info --}}
        <div class="bg-white shadow sm:rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Informasi Customer</h3>
            </div>
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Nama</label>
                        <p class="mt-1 text-base text-gray-900">{{ $customer->name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Email</label>
                        <p class="mt-1 text-base text-gray-900">{{ $customer->email }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Nomor Telepon</label>
                        <p class="mt-1 text-base text-gray-900">{{ $customer->phone ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Status</label>
                        <form action="{{ route('admin.customers.toggle-status', $customer) }}" method="POST"
                            class="inline">
                            @csrf
                            <button type="submit"
                                class="mt-1 rounded-full px-3 py-1 text-sm font-semibold {{ $customer->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $customer->is_active ? 'Aktif' : 'Nonaktif' }}
                            </button>
                        </form>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-500">Alamat</label>
                        <p class="mt-1 text-base text-gray-900">{{ $customer->address ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Terdaftar Sejak</label>
                        <p class="mt-1 text-base text-gray-900">{{ $customer->created_at->format('d M Y, H:i') }}</p>
                    </div>
                </div>

                <div class="pt-4 border-t flex gap-3">
                    <form action="{{ route('admin.customers.send-password-reset', $customer) }}" method="POST"
                        class="inline">
                        @csrf
                        <button type="submit"
                            class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                            Kirim Reset Password
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Statistics --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white shadow sm:rounded-lg p-6">
                <div class="text-sm font-medium text-gray-500">Total Requests</div>
                <div class="mt-2 text-3xl font-bold text-gray-900">{{ $stats['total_requests'] }}</div>
            </div>
            <div class="bg-white shadow sm:rounded-lg p-6">
                <div class="text-sm font-medium text-gray-500">Pending</div>
                <div class="mt-2 text-3xl font-bold text-yellow-600">{{ $stats['pending_requests'] }}</div>
            </div>
            <div class="bg-white shadow sm:rounded-lg p-6">
                <div class="text-sm font-medium text-gray-500">Approved</div>
                <div class="mt-2 text-3xl font-bold text-green-600">{{ $stats['approved_requests'] }}</div>
            </div>
            <div class="bg-white shadow sm:rounded-lg p-6">
                <div class="text-sm font-medium text-gray-500">Active Accesses</div>
                <div class="mt-2 text-3xl font-bold text-blue-600">{{ $stats['active_accesses'] }}</div>
            </div>
        </div>

        {{-- Access Requests History --}}
        <div class="bg-white shadow sm:rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Riwayat Access Requests</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr class="text-left">
                            <th class="px-4 py-3 font-semibold text-gray-700">Video</th>
                            <th class="px-4 py-3 font-semibold text-gray-700">Requested At</th>
                            <th class="px-4 py-3 font-semibold text-gray-700">Status</th>
                            <th class="px-4 py-3 font-semibold text-gray-700">Reviewed At</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($customer->accessRequests as $request)
                            <tr>
                                <td class="px-4 py-3">
                                    <div class="font-medium text-gray-900">{{ $request->video->title }}</div>
                                </td>
                                <td class="px-4 py-3 text-gray-600">
                                    {{ $request->requested_at->format('d M Y, H:i') }}
                                </td>
                                <td class="px-4 py-3">
                                    <span
                                        class="rounded-full px-2.5 py-1 text-xs font-semibold 
                                        @if ($request->status === 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($request->status === 'approved') bg-green-100 text-green-800
                                        @else bg-red-100 text-red-800 @endif">
                                        {{ ucfirst($request->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-gray-600">
                                    {{ $request->reviewed_at ? $request->reviewed_at->format('d M Y, H:i') : '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-gray-500">
                                    Belum ada riwayat access request.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Video Accesses --}}
        <div class="bg-white shadow sm:rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Video Accesses</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr class="text-left">
                            <th class="px-4 py-3 font-semibold text-gray-700">Video</th>
                            <th class="px-4 py-3 font-semibold text-gray-700">Start</th>
                            <th class="px-4 py-3 font-semibold text-gray-700">End</th>
                            <th class="px-4 py-3 font-semibold text-gray-700">Duration</th>
                            <th class="px-4 py-3 font-semibold text-gray-700">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($customer->videoAccesses as $access)
                            <tr>
                                <td class="px-4 py-3">
                                    <div class="font-medium text-gray-900">{{ $access->video->title }}</div>
                                </td>
                                <td class="px-4 py-3 text-gray-600">
                                    {{ $access->start_at->format('d M Y, H:i') }}
                                </td>
                                <td class="px-4 py-3 text-gray-600">
                                    {{ $access->end_at->format('d M Y, H:i') }}
                                </td>
                                <td class="px-4 py-3 text-gray-600">
                                    {{ $access->approved_minutes }} menit
                                    @if ($access->grace_minutes > 0)
                                        <span class="text-xs text-gray-500">(+{{ $access->grace_minutes }}
                                            grace)</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <span
                                        class="rounded-full px-2.5 py-1 text-xs font-semibold 
                                        @if ($access->status === 'active') bg-green-100 text-green-800
                                        @elseif($access->status === 'expired') bg-gray-100 text-gray-800
                                        @else bg-red-100 text-red-800 @endif">
                                        {{ ucfirst($access->status) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                    Belum ada video access.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
