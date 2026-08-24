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
        <!-- Sidebar container -->
        <aside class="flex-shrink-0 w-64 bg-gray-900 text-white overflow-y-auto">
            <x-sidebar/>
        </aside>
        @endauth

        <!-- Main content area wrapper -->
        <div class="flex flex-col flex-1 min-w-0 overflow-hidden">
            @auth
            <!-- Sticky/Fixed topbar header -->
            <header class="flex-shrink-0 z-10 bg-white border-b border-gray-200">
                <x-topbar />
            </header>
            @endauth


            <!-- Scrollable page body ($slot) -->
            <main class="flex-1 overflow-y-auto bg-gray-50 p-6 md:p-8">
                <div class="max-w-7xl mx-auto">
                    {{ $slot }}
                </div>
            </main>
            
        </div>
    </div>
</body>
</html>