<!-- Mobile Drawer Toggle Button -->
<button data-drawer-target="default-sidebar" data-drawer-toggle="default-sidebar"
        aria-controls="default-sidebar" type="button"
        class="text-gray-600 bg-transparent hover:bg-slate-100 focus:ring-2 focus:ring-slate-200 font-medium rounded-lg text-sm p-2.5 ms-3 mt-3 focus:outline-none inline-flex sm:hidden transition">
    <span class="sr-only">Open sidebar</span>
    <svg class="w-6 h-6" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
    </svg>
</button>

<aside id="default-sidebar"
       class="fixed top-0 left-0 z-40 w-64 h-screen transition-transform -translate-x-full sm:translate-x-0"
       aria-label="Sidebar">

    <div class="h-full px-4 py-5 overflow-y-auto bg-slate-900 border-e border-slate-800 flex flex-col justify-between shadow-sm">

        <div>
            <!-- App Logo / Header -->
            <div class="flex items-center gap-3 px-3 pb-6 mb-2 border-b border-slate-800/80">
                <div class="w-9 h-9 rounded-xl bg-indigo-600 flex items-center justify-center text-white font-bold shadow-md shadow-indigo-500/20">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <div class="flex flex-col">
                    <span class="text-base font-bold text-white tracking-tight leading-none">ERP System</span>
                    <span class="text-xs font-medium text-slate-400 mt-1">Management Portal</span>
                </div>
            </div>

            <!-- Navigation Links -->
            <ul class="space-y-1 font-medium">

                <!-- Dashboard -->
                <li>
                    <a href="{{ route('dashboard') }}"
                       class="flex items-center px-3 py-2.5 text-sm rounded-xl transition-all duration-150 {{ request()->routeIs('dashboard') ? 'bg-indigo-600 text-white font-semibold shadow-lg shadow-indigo-600/30' : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' }} group">
                        <svg class="shrink-0 w-5 h-5 transition-colors {{ request()->routeIs('dashboard') ? 'text-white' : 'text-slate-400 group-hover:text-slate-200' }}"
                             aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 13h8V3H3v10Zm10 8h8V11h-8v10ZM3 21h8v-6H3v6Zm10-18v6h8V3h-8Z"/>
                        </svg>
                        <span class="ms-3">Dashboard</span>
                    </a>
                </li>

                <!-- Products -->
                <li>
                    <a href="{{ route('products.index') }}"
                       class="flex items-center px-3 py-2.5 text-sm rounded-xl transition-all duration-150 {{ request()->routeIs('products.*') ? 'bg-indigo-600 text-white font-semibold shadow-lg shadow-indigo-600/30' : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' }} group">
                        <svg class="shrink-0 w-5 h-5 transition-colors {{ request()->routeIs('products.*') ? 'text-white' : 'text-slate-400 group-hover:text-slate-200' }}"
                             aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7.5 12 3 4 7.5m16 0v9L12 21l-8-4.5v-9m16 0-8 4.5m0 0L4 7.5M12 12v9"/>
                        </svg>
                        <span class="flex-1 ms-3 whitespace-nowrap">Products</span>
                    </a>
                </li>

                <!-- Categories -->
                <li>
                    <a href="{{ route('categories.index') }}"
                       class="flex items-center px-3 py-2.5 text-sm rounded-xl transition-all duration-150 {{ request()->routeIs('categories.*') ? 'bg-indigo-600 text-white font-semibold shadow-lg shadow-indigo-600/30' : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' }} group">
                        <svg class="shrink-0 w-5 h-5 transition-colors {{ request()->routeIs('categories.*') ? 'text-white' : 'text-slate-400 group-hover:text-slate-200' }}"
                             aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4h6v6H4V4Zm10 0h6v6h-6V4ZM4 14h6v6H4v-6Zm10 0h6v6h-6v-6Z"/>
                        </svg>
                        <span class="flex-1 ms-3 whitespace-nowrap">Categories</span>
                    </a>
                </li>
                <!-- Suppliers -->
                <li>
                    <a href="{{ route('suppliers.index') }}"
                       class="flex items-center px-3 py-2.5 text-sm rounded-xl transition-all duration-150 {{ request()->routeIs('suppliers.*') ? 'bg-indigo-600 text-white font-semibold shadow-lg shadow-indigo-600/30' : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' }} group">
                        <svg class="shrink-0 w-5 h-5 transition-colors {{ request()->routeIs('suppliers.*') ? 'text-white' : 'text-slate-400 group-hover:text-slate-200' }}"
                             aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0v-5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v5m-6 0h6"/>
                        </svg>
                        <span class="flex-1 ms-3 whitespace-nowrap">Suppliers</span>
                    </a>
                </li>
                <!-- Purchases -->
                <li>
                    <a href="{{ route('purchases.index') }}"
                    class="flex items-center px-3 py-2.5 text-sm rounded-xl transition-all duration-150 {{ request()->routeIs('purchases.*') ? 'bg-indigo-600 text-white font-semibold shadow-lg shadow-indigo-600/30' : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' }} group">
                        <svg class="shrink-0 w-5 h-5 transition-colors {{ request()->routeIs('purchases.*') ? 'text-white' : 'text-slate-400 group-hover:text-slate-200' }}"
                            aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <span class="flex-1 ms-3 whitespace-nowrap">Purchases</span>
                    </a>
                </li>
                <!-- Stock -->
                <li>
                    <a href="{{ route('stock.index') }}"
                       class="flex items-center px-3 py-2.5 text-sm rounded-xl transition-all duration-150 {{ request()->routeIs('stock.*') ? 'bg-indigo-600 text-white font-semibold shadow-lg shadow-indigo-600/30' : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' }} group">
                        <svg class="shrink-0 w-5 h-5 transition-colors {{ request()->routeIs('stock.*') ? 'text-white' : 'text-slate-400 group-hover:text-slate-200' }}"
                             aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7h16M4 12h16M4 17h16M7 4v16"/>
                        </svg>
                        <span class="flex-1 ms-3 whitespace-nowrap">Stock</span>
                    </a>
                </li>

                <!-- Customers -->
                <li>
                    <a href="{{ route('customers.index') }}"
                       class="flex items-center px-3 py-2.5 text-sm rounded-xl transition-all duration-150 {{ request()->routeIs('customers.*') ? 'bg-indigo-600 text-white font-semibold shadow-lg shadow-indigo-600/30' : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' }} group">
                        <svg class="shrink-0 w-5 h-5 transition-colors {{ request()->routeIs('customers.*') ? 'text-white' : 'text-slate-400 group-hover:text-slate-200' }}"
                             aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19a4 4 0 0 0-8 0"/>
                            <circle cx="11" cy="9" r="4" stroke="currentColor" stroke-width="2"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 12a3 3 0 1 0-2.5-4.5M17 19h4"/>
                        </svg>
                        <span class="flex-1 ms-3 whitespace-nowrap">Customers</span>
                    </a>
                </li>

                <!-- Sales -->
                <li>
                    <a href="{{ route('sales.index') }}"
                       class="flex items-center px-3 py-2.5 text-sm rounded-xl transition-all duration-150 {{ request()->routeIs('sales.*') ? 'bg-indigo-600 text-white font-semibold shadow-lg shadow-indigo-600/30' : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' }} group">
                        <svg class="shrink-0 w-5 h-5 transition-colors {{ request()->routeIs('sales.*') ? 'text-white' : 'text-slate-400 group-hover:text-slate-200' }}"
                             aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5h16v14H4V5Z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9h8M8 13h5M15 17h2"/>
                        </svg>
                        <span class="flex-1 ms-3 whitespace-nowrap">Sales</span>
                    </a>
                </li>

                <!-- Debts -->
                <li>
                    <a href="{{ route('debts.index') }}"
                       class="flex items-center px-3 py-2.5 text-sm rounded-xl transition-all duration-150 {{ request()->routeIs('debts.*') ? 'bg-red-600 text-white font-semibold shadow-lg shadow-red-600/30' : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' }} group">
                        <svg class="shrink-0 w-5 h-5 transition-colors {{ request()->routeIs('debts.*') ? 'text-white' : 'text-slate-400 group-hover:text-red-400' }}"
                             aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V6m0 8v2m0-10e-5c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                        </svg>
                        <span class="flex-1 ms-3 whitespace-nowrap">Debts</span>
                    </a>
                </li>

                <!-- Quotes -->
                <li>
                    <a href="{{ route('quotes.index') }}"
                       class="flex items-center px-3 py-2.5 text-sm rounded-xl transition-all duration-150 {{ request()->routeIs('quotes.*') ? 'bg-indigo-600 text-white font-semibold shadow-lg shadow-indigo-600/30' : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' }} group">
                        <svg class="shrink-0 w-5 h-5 transition-colors {{ request()->routeIs('quotes.*') ? 'text-white' : 'text-slate-400 group-hover:text-slate-200' }}"
                             aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.586a1 1 0 0 1 .707.293l5.414 5.414a1 1 0 0 1 .293.707V19a2 2 0 0 1-2 2Z"/>
                        </svg>
                        <span class="flex-1 ms-3 whitespace-nowrap">Quotes</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('ai.index') }}" 
                    class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('ai.*') ? 'bg-indigo-600 text-white' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800' }}">
                        <span>🤖</span>
                        <span>Assistant IA</span>
                    </a>
                </li>

            </ul>
        </div>

        <!-- Logout Section -->
        <div class="pt-4 border-t border-slate-800">
            @auth
                @if (Route::has('logout'))
                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                       
                        <button type="submit" class="w-full flex items-center justify-center gap-2 px-3 py-2.5 text-sm font-semibold text-rose-400 hover:text-rose-300 hover:bg-rose-500/10 rounded-xl transition-all duration-150">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            Log Out
                        </button>
                    </form>
                @endif
            @endauth
        </div>

    </div>
</aside>