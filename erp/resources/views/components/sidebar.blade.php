<div id="erp-sidebar-overlay" class="fixed inset-0 z-40 hidden bg-slate-950/40 lg:hidden" aria-hidden="true"></div>

<aside id="erp-sidebar" class="fixed inset-y-0 left-0 z-50 w-64 -translate-x-full border-r border-slate-800 bg-slate-950 text-white transition-transform duration-200 lg:translate-x-0" aria-label="Sidebar">
    <div class="flex h-full flex-col">
        <div class="flex h-16 items-center gap-3 border-b border-slate-800 px-5">
            <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-indigo-600 shadow-sm shadow-indigo-950/40">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>
            <div class="min-w-0">
                <p class="truncate text-sm font-bold tracking-tight">ERP System</p>
                <p class="truncate text-xs text-slate-500">Business management</p>
            </div>
        </div>

        <nav class="flex-1 overflow-y-auto px-3 py-5" aria-label="Main navigation">
            <p class="px-3 pb-2 text-[10px] font-semibold uppercase tracking-[0.18em] text-slate-500">Workspace</p>
            <ul class="space-y-1">
                @php
                    $items = [
                        ['route'=>'dashboard','label'=>'Dashboard','path'=>'dashboard'],
                        ['route'=>'products.index','label'=>'Products','path'=>'products.*'],
                        ['route'=>'categories.index','label'=>'Categories','path'=>'categories.*'],
                        ['route'=>'stock.index','label'=>'Stock','path'=>'stock.*'],
                        ['route'=>'customers.index','label'=>'Customers','path'=>'customers.*'],
                        ['route'=>'sales.index','label'=>'Sales','path'=>'sales.*'],
                        ['route'=>'debts.index','label'=>'Debts','path'=>'debts.*'],
                        ['route'=>'quotes.index','label'=>'Quotes','path'=>'quotes.*'],
                    ];
                @endphp
                @foreach($items as $item)
                    @php $active = request()->routeIs($item['path']); @endphp
                    <li>
                        <a href="{{ route($item['route']) }}" class="group flex min-h-11 items-center gap-3 rounded-lg px-3 text-sm font-medium transition {{ $active ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                            <span class="flex h-8 w-8 items-center justify-center rounded-md {{ $active ? 'bg-white/10' : 'bg-slate-900 group-hover:bg-slate-800' }}">
                                @switch($item['label'])
                                    @case('Dashboard')
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 13h8V3H3v10Zm10 8h8V11h-8v10ZM3 21h8v-6H3v6Zm10-18v6h8V3h-8Z"/></svg>
                                        @break
                                    @case('Products')
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m20 7.5-8-4.5-8 4.5v9l8 4.5 8-4.5v-9Zm0 0-8 4.5m0 0L4 7.5M12 12v9"/></svg>
                                        @break
                                    @case('Categories')
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4h6v6H4V4Zm10 0h6v6h-6V4ZM4 14h6v6H4v-6Zm10 0h6v6h-6v-6Z"/></svg>
                                        @break
                                    @case('Stock')
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7h16M4 12h16M4 17h16M7 4v16"/></svg>
                                        @break
                                    @case('Customers')
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19a4 4 0 0 0-8 0m4-6a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm8 6a4 4 0 0 0-3.2-3.9M17 11a3 3 0 1 0-2.5-4.5"/></svg>
                                        @break
                                    @case('Sales')
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5h16v14H4V5Zm4 4h8M8 13h5M15 17h2"/></svg>
                                        @break
                                    @case('Debts')
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8V6m0 8v2m9-4a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                                        @break
                                    @default
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.586l6.414 6.414V19a2 2 0 0 1-2 2Z"/></svg>
                                @endswitch
                            </span>
                            <span class="flex-1">{{ $item['label'] }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </nav>

        <div class="border-t border-slate-800 p-3">
            @auth
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="group flex min-h-11 w-full items-center gap-3 rounded-lg px-3 text-sm font-semibold text-slate-400 transition hover:bg-rose-500/10 hover:text-rose-300">
                        <span class="flex h-8 w-8 items-center justify-center rounded-md bg-slate-900 group-hover:bg-rose-500/10">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m17 16 4-4m0 0-4-4m4 4H7m6 4v1a3 3 0 0 1-3 3H6a3 3 0 0 1-3-3V7a3 3 0 0 1 3-3h4a3 3 0 0 1 3 3v1"/></svg>
                        </span>
                        Sign out
                    </button>
                </form>
            @endauth
        </div>
    </div>
</aside>

<button id="erp-mobile-menu" type="button" class="fixed left-4 top-4 z-30 inline-flex h-10 w-10 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-700 shadow-sm lg:hidden" aria-label="Open navigation" aria-controls="erp-sidebar" aria-expanded="false">
    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
</button>

<script>
    (() => {
        const menu = document.getElementById('erp-mobile-menu');
        const sidebar = document.getElementById('erp-sidebar');
        const overlay = document.getElementById('erp-sidebar-overlay');
        if (!menu || !sidebar || !overlay) return;

        const close = () => {
            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
            menu.setAttribute('aria-expanded', 'false');
        };
        menu.addEventListener('click', () => {
            sidebar.classList.remove('-translate-x-full');
            overlay.classList.remove('hidden');
            menu.setAttribute('aria-expanded', 'true');
        });
        overlay.addEventListener('click', close);
        sidebar.querySelectorAll('a').forEach(link => link.addEventListener('click', () => {
            if (window.innerWidth < 1024) close();
        }));
    })();
</script>
