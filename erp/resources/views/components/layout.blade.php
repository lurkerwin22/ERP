<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'ERP System' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full antialiased font-sans text-slate-900">
    @auth
        <div class="min-h-screen bg-slate-50">
            <x-sidebar />
            <div class="lg:pl-64">
                <x-topbar />
                <main class="min-h-[calc(100vh-4rem)] px-4 py-6 sm:px-6 lg:px-8">
                    <div class="mx-auto w-full max-w-7xl">
                        @if (session('success'))
                            <x-alert type="success" :message="session('success')" />
                        @endif
                        @if (session('error'))
                            <x-alert type="error" :message="session('error')" />
                        @endif
                        {{ $slot }}
                    </div>
                </main>
            </div>
        </div>
    @else
        <main class="min-h-screen bg-slate-50 px-4 py-8 sm:px-6">
            <div class="mx-auto flex min-h-[calc(100vh-4rem)] w-full max-w-md items-center justify-center">
                <div class="w-full">
                    <div class="mb-6 flex items-center justify-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-600 text-white shadow-sm">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        </div>
                        <div>
                            <p class="text-lg font-bold tracking-tight text-slate-950">ERP System</p>
                            <p class="text-xs text-slate-500">Business management</p>
                        </div>
                    </div>
                    {{ $slot }}
                </div>
            </div>
        </main>
    @endauth
</body>
</html>
