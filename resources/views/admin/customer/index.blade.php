<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Customer</h2>
            <a href="{{ route('admin.customers.create') }}"
                class="inline-flex items-center rounded-lg bg-gray-900 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-800">
                + Tambah Customer
            </a>
        </div>
    </x-slot>

    <div class="space-y-4">
        @if (session('success'))
            <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Table --}}
        <div class="bg-white shadow sm:rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr class="text-left">
                            <th class="px-4 py-3 font-semibold text-gray-700">Nama</th>
                            <th class="px-4 py-3 font-semibold text-gray-700">Email</th>
                            <th class="px-4 py-3 font-semibold text-gray-700">Phone</th>
                            <th class="px-4 py-3 font-semibold text-gray-700">Status</th>
                            <th class="px-4 py-3 font-semibold text-gray-700">Requests</th>
                            <th class="px-4 py-3 font-semibold text-gray-700 w-[220px]">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200">
                        @forelse($customers as $customer)
                            <tr>
                                <td class="px-4 py-3">
                                    <div class="font-semibold text-gray-900">{{ $customer->name }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="text-gray-900">{{ $customer->email }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="text-gray-600">{{ $customer->phone ?? '-' }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <form action="{{ route('admin.customers.toggle-status', $customer) }}"
                                        method="POST" class="inline">
                                        @csrf
                                        <button type="submit"
                                            class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $customer->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $customer->is_active ? 'Aktif' : 'Nonaktif' }}
                                        </button>
                                    </form>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="text-sm text-gray-600">
                                        <div>Requests: {{ $customer->access_requests_count }}</div>
                                        <div>Accesses: {{ $customer->video_accesses_count }}</div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-wrap gap-2">
                                        <a href="{{ route('admin.customers.show', $customer) }}"
                                            class="rounded-lg bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-700 hover:bg-blue-100">
                                            Detail
                                        </a>
                                        <a href="{{ route('admin.customers.edit', $customer) }}"
                                            class="rounded-lg bg-indigo-50 px-3 py-1.5 text-xs font-semibold text-indigo-700 hover:bg-indigo-100">
                                            Edit
                                        </a>
                                        <form action="{{ route('admin.customers.destroy', $customer) }}" method="POST"
                                            class="inline"
                                            onsubmit="return confirm('Yakin ingin menghapus customer ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="rounded-lg bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-700 hover:bg-red-100">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                    Belum ada data customer.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if ($customers->hasPages())
                <div class="border-t border-gray-200 px-4 py-3">
                    {{ $customers->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
