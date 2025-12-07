<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Customer</h2>
            <a href="{{ route('admin.customers.index') }}"
                class="inline-flex items-center rounded-lg bg-gray-100 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-200">
                ← Kembali
            </a>
        </div>
    </x-slot>

    <div class="bg-white shadow sm:rounded-lg">
        <form action="{{ route('admin.customers.update', $customer) }}" method="POST" class="p-6 space-y-6">
            @csrf
            @method('PATCH')

            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Nama --}}
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Nama Customer *</label>
                <input type="text" name="name" id="name" required value="{{ old('name', $customer->name) }}"
                    class="mt-1 block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                    placeholder="Masukkan nama customer">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Email --}}
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email *</label>
                <input type="email" name="email" id="email" required
                    value="{{ old('email', $customer->email) }}"
                    class="mt-1 block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                    placeholder="customer@example.com">
                @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Phone --}}
            <div>
                <label for="phone" class="block text-sm font-medium text-gray-700">Nomor Telepon</label>
                <input type="text" name="phone" id="phone" value="{{ old('phone', $customer->phone) }}"
                    class="mt-1 block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                    placeholder="081234567890">
                @error('phone')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Address --}}
            <div>
                <label for="address" class="block text-sm font-medium text-gray-700">Alamat</label>
                <textarea name="address" id="address" rows="3"
                    class="mt-1 block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                    placeholder="Alamat lengkap customer">{{ old('address', $customer->address) }}</textarea>
                @error('address')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Password --}}
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Password Baru</label>
                <input type="password" name="password" id="password"
                    class="mt-1 block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                    placeholder="Kosongkan jika tidak ingin mengubah password">
                @error('password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Password Confirmation --}}
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Konfirmasi Password
                    Baru</label>
                <input type="password" name="password_confirmation" id="password_confirmation"
                    class="mt-1 block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                    placeholder="Konfirmasi password baru">
            </div>

            {{-- Submit --}}
            <div class="flex items-center gap-3 pt-4">
                <button type="submit"
                    class="rounded-lg bg-gray-900 px-6 py-2.5 text-sm font-semibold text-white hover:bg-gray-800">
                    Update Customer
                </button>
                <a href="{{ route('admin.customers.index') }}"
                    class="rounded-lg bg-gray-100 px-6 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-200">
                    Batal
                </a>
            </div>
        </form>
    </div>
</x-app-layout>
