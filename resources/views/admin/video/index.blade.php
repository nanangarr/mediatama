<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Video</h2>
            <a href="{{ route('admin.videos.create') }}"
                class="inline-flex items-center rounded-lg bg-gray-900 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-800">
                + Tambah Video
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

        {{-- Search + Filter --}}
        <div class="bg-white shadow sm:rounded-lg p-4">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex-1">
                    <input type="text" placeholder="Cari judul video atau customer..."
                        class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" />
                </div>
                <div class="flex items-center gap-2">
                    <select class="rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Semua status</option>
                        <option value="draft">Draft</option>
                        <option value="processing">Processing</option>
                        <option value="completed">Selesai</option>
                        <option value="published">Published</option>
                    </select>
                    <button
                        class="rounded-lg bg-gray-100 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-200">
                        Terapkan
                    </button>
                </div>
            </div>
        </div>

        {{-- Table --}}
        <div class="bg-white shadow sm:rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr class="text-left">
                            <th class="px-4 py-3 font-semibold text-gray-700">Video</th>
                            <th class="px-4 py-3 font-semibold text-gray-700">Durasi</th>
                            <th class="px-4 py-3 font-semibold text-gray-700">Status</th>
                            <th class="px-4 py-3 font-semibold text-gray-700">Tanggal</th>
                            <th class="px-4 py-3 font-semibold text-gray-700 w-[180px]">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200">
                        @forelse($videos as $video)
                            <tr>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="flex-shrink-0 w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center overflow-hidden">
                                            @if ($video->thumbnail)
                                                <img src="{{ asset('storage/' . $video->thumbnail) }}"
                                                    alt="{{ $video->title }}" class="w-full h-full object-cover">
                                            @else
                                                <svg class="w-8 h-8 text-gray-400" fill="currentColor"
                                                    viewBox="0 0 20 20">
                                                    <path
                                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" />
                                                </svg>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="font-semibold text-gray-900">{{ $video->title }}</div>
                                            <div class="text-xs text-gray-500">
                                                @if ($video->isExternal())
                                                    <span class="text-blue-600">External URL</span>
                                                @else
                                                    <span>Local File</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-gray-700">
                                    {{ $video->duration_formatted ?? '-' }}
                                </td>
                                <td class="px-4 py-3">
                                    <span
                                        class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold {{ $video->is_active ? 'bg-green-50 text-green-700' : 'bg-gray-50 text-gray-700' }}">
                                        {{ $video->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-gray-700">{{ $video->created_at->format('d M Y') }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-wrap gap-2">
                                        <a href="{{ route('admin.videos.show', $video) }}"
                                            class="rounded-lg bg-gray-100 px-3 py-1.5 text-xs font-semibold text-gray-700 hover:bg-gray-200">
                                            Detail
                                        </a>
                                        <a href="{{ route('admin.videos.edit', $video) }}"
                                            class="rounded-lg bg-indigo-50 px-3 py-1.5 text-xs font-semibold text-indigo-700 hover:bg-indigo-100">
                                            Edit
                                        </a>
                                        <form action="{{ route('admin.videos.destroy', $video) }}" method="POST"
                                            class="inline"
                                            onsubmit="return confirm('Yakin ingin menghapus video ini?')">
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
                                <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                    Belum ada data video.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if ($videos->hasPages())
                <div class="border-t border-gray-200 px-4 py-3">
                    {{ $videos->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
