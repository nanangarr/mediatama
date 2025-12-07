<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Manajemen Request Akses</h2>
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

        {{-- Filter --}}
        <div class="bg-white shadow sm:rounded-lg p-4">
            <form method="GET" action="{{ route('admin.access-requests.index') }}" class="flex flex-wrap gap-4">
                <div class="flex-1 min-w-[200px]">
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" id="status"
                        class="block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Semua Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved
                        </option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected
                        </option>
                    </select>
                </div>
                <div class="flex-1 min-w-[200px]">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Cari
                        Customer/Video</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}"
                        placeholder="Nama customer atau video..."
                        class="block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit"
                        class="rounded-lg bg-gray-900 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-800">
                        Filter
                    </button>
                    <a href="{{ route('admin.access-requests.index') }}"
                        class="rounded-lg bg-gray-100 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-200">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        {{-- Table --}}
        <div class="bg-white shadow sm:rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Video
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Durasi Request</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($accessRequests as $request)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $request->customer->name }}
                                        </div>
                                        <div class="text-sm text-gray-500">{{ $request->customer->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    @if ($request->video->thumbnail)
                                        <img src="{{ asset('storage/' . $request->video->thumbnail) }}"
                                            alt="{{ $request->video->title }}"
                                            class="w-16 h-16 object-cover rounded mr-3">
                                    @endif
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $request->video->title }}
                                        </div>
                                        <div class="text-sm text-gray-500">Durasi:
                                            {{ $request->video->duration_formatted }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $request->requested_minutes }} menit
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if ($request->status === 'pending')
                                    <span
                                        class="inline-flex items-center rounded-full bg-yellow-50 px-2 py-1 text-xs font-semibold text-yellow-700">Pending</span>
                                @elseif($request->status === 'approved')
                                    <span
                                        class="inline-flex items-center rounded-full bg-green-50 px-2 py-1 text-xs font-semibold text-green-700">Approved</span>
                                @else
                                    <span
                                        class="inline-flex items-center rounded-full bg-red-50 px-2 py-1 text-xs font-semibold text-red-700">Rejected</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $request->created_at->format('d M Y, H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                @if ($request->isPending())
                                    <button
                                        onclick="openApproveModal({{ $request->id }}, '{{ $request->customer->name }}', '{{ $request->video->title }}', {{ $request->requested_minutes }}, {{ $request->video->duration_sec }})"
                                        class="text-green-600 hover:text-green-900 mr-3">
                                        Approve
                                    </button>
                                    <button
                                        onclick="openRejectModal({{ $request->id }}, '{{ $request->customer->name }}', '{{ $request->video->title }}')"
                                        class="text-red-600 hover:text-red-900">
                                        Reject
                                    </button>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-sm text-gray-500">
                                Tidak ada request akses
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="bg-white shadow sm:rounded-lg p-4">
            {{ $accessRequests->links() }}
        </div>
    </div>

    {{-- Approve Modal --}}
    <div id="approveModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Approve Request Akses</h3>
                <form id="approveForm" method="POST">
                    @csrf
                    <div class="mb-4">
                        <p class="text-sm text-gray-600 mb-2">Customer: <span id="approveCustomerName"
                                class="font-semibold"></span></p>
                        <p class="text-sm text-gray-600 mb-2">Video: <span id="approveVideoTitle"
                                class="font-semibold"></span></p>
                        <p class="text-sm text-gray-600 mb-4">Request: <span id="approveRequestedMinutes"
                                class="font-semibold"></span> menit</p>
                    </div>
                    <div class="mb-4">
                        <label for="approved_minutes" class="block text-sm font-medium text-gray-700 mb-1">Durasi
                            Disetujui (menit)</label>
                        <input type="number" name="approved_minutes" id="approved_minutes" required min="1"
                            step="1"
                            class="block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        <p class="mt-1 text-xs text-gray-500">Min: durasi video (<span id="minDuration"></span> detik)
                        </p>
                    </div>
                    <div class="mb-4">
                        <label for="grace_minutes" class="block text-sm font-medium text-gray-700 mb-1">Grace Period
                            (menit, opsional)</label>
                        <input type="number" name="grace_minutes" id="grace_minutes" min="0" value="0"
                            step="1"
                            class="block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        <p class="mt-1 text-xs text-gray-500">Waktu tambahan setelah durasi berakhir</p>
                    </div>
                    <div class="flex gap-2">
                        <button type="submit"
                            class="flex-1 rounded-lg bg-green-600 px-4 py-2 text-sm font-semibold text-white hover:bg-green-700">
                            Approve
                        </button>
                        <button type="button" onclick="closeApproveModal()"
                            class="flex-1 rounded-lg bg-gray-100 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-200">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Reject Modal --}}
    <div id="rejectModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Reject Request Akses</h3>
                <form id="rejectForm" method="POST">
                    @csrf
                    <div class="mb-4">
                        <p class="text-sm text-gray-600 mb-2">Customer: <span id="rejectCustomerName"
                                class="font-semibold"></span></p>
                        <p class="text-sm text-gray-600 mb-4">Video: <span id="rejectVideoTitle"
                                class="font-semibold"></span></p>
                    </div>
                    <div class="mb-4">
                        <label for="reason" class="block text-sm font-medium text-gray-700 mb-1">Alasan
                            Penolakan</label>
                        <textarea name="reason" id="reason" rows="3" required
                            class="block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="Jelaskan alasan penolakan..."></textarea>
                    </div>
                    <div class="flex gap-2">
                        <button type="submit"
                            class="flex-1 rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700">
                            Reject
                        </button>
                        <button type="button" onclick="closeRejectModal()"
                            class="flex-1 rounded-lg bg-gray-100 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-200">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openApproveModal(requestId, customerName, videoTitle, requestedMinutes, videoDurationSec) {
            document.getElementById('approveModal').classList.remove('hidden');
            document.getElementById('approveForm').action = `/admin/access-requests/${requestId}/approve`;
            document.getElementById('approveCustomerName').textContent = customerName;
            document.getElementById('approveVideoTitle').textContent = videoTitle;
            document.getElementById('approveRequestedMinutes').textContent = requestedMinutes;
            document.getElementById('minDuration').textContent = videoDurationSec;
            document.getElementById('approved_minutes').value = requestedMinutes;
            document.getElementById('approved_minutes').min = Math.ceil(videoDurationSec / 60);
        }

        function closeApproveModal() {
            document.getElementById('approveModal').classList.add('hidden');
        }

        function openRejectModal(requestId, customerName, videoTitle) {
            document.getElementById('rejectModal').classList.remove('hidden');
            document.getElementById('rejectForm').action = `/admin/access-requests/${requestId}/reject`;
            document.getElementById('rejectCustomerName').textContent = customerName;
            document.getElementById('rejectVideoTitle').textContent = videoTitle;
        }

        function closeRejectModal() {
            document.getElementById('rejectModal').classList.add('hidden');
        }

        // Close modal on outside click
        window.onclick = function(event) {
            const approveModal = document.getElementById('approveModal');
            const rejectModal = document.getElementById('rejectModal');
            if (event.target == approveModal) {
                closeApproveModal();
            }
            if (event.target == rejectModal) {
                closeRejectModal();
            }
        }
    </script>
</x-app-layout>
