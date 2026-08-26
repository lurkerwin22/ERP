<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ERP System</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full antialiased font-sans text-gray-900">
    <!-- Main wrapper spanning full screen height -->
    <div class="flex h-screen overflow-hidden">
        @auth
        <!-- Sidebar container (the sidebar component handles its own mobile drawer/toggle) -->
        <aside class="flex-shrink-0 sm:w-64 bg-gray-900 text-white overflow-y-auto">
            <x-sidebar/>
        </aside>
        @endauth

        <!-- Main content area wrapper -->
        <div class="flex flex-col flex-1 min-w-0 overflow-hidden">
            @auth
            <!-- Sticky/Fixed topbar header -->
            <header class="flex-shrink-0 z-10 bg-white">
                <x-topbar />
            </header>
            @endauth


            <!-- Scrollable page body ($slot) -->
            <main class="flex-1 overflow-y-auto {{ auth()->check() ? 'bg-gray-50 p-6 md:p-8' : '' }}">
                <div class="{{ auth()->check() ? 'max-w-7xl mx-auto' : '' }}">

                    @if (session('success'))
                        <div class="mb-6 flex items-start gap-3 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800" role="alert">
                            <svg class="w-5 h-5 flex-shrink-0 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75l2.25 2.25 4.5-4.5m4.5 2.25a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="flex-1">{{ session('success') }}</span>
                            <button type="button" onclick="this.closest('[role=alert]').remove()" class="text-green-500 hover:text-green-700">&times;</button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="mb-6 flex items-start gap-3 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert">
                            <svg class="w-5 h-5 flex-shrink-0 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m0 3.75h.008v.008H12V16.5zm9-4.5a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="flex-1">{{ session('error') }}</span>
                            <button type="button" onclick="this.closest('[role=alert]').remove()" class="text-red-500 hover:text-red-700">&times;</button>
                        </div>
                    @endif

                    {{ $slot }}
                </div>
            </main>
            
        </div>
    </div>
</body>
</html>