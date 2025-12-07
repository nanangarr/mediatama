<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased">
    <div x-data="{ sidebarOpen:false }" class="min-h-screen bg-gray-100">
        <div class="flex min-h-screen">
            {{-- Sidebar --}}
            @include('layouts.navigation') {{-- nanti file ini isi sidebar saja --}}

            {{-- Main area --}}
            <div class="flex-1 min-w-0 flex flex-col">
                {{-- Topbar --}}
                @include('layouts.topbar') {{-- kita buat file baru topbar, profil kanan atas --}}

                {{-- Page Heading --}}
                @isset($header)
                    <div class="bg-white border-b border-gray-200">
                        <div class="px-4 sm:px-6 lg:px-8 py-5">
                            {{ $header }}
                        </div>
                    </div>
                @endisset

                {{-- Content --}}
                <main class="flex-1 px-4 sm:px-6 lg:px-8 py-6">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </div>
</body>
</html>