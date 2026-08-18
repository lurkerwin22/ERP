<header class="sticky top-0 z-20 border-b border-slate-200 bg-white/95 backdrop-blur">
    <div class="flex h-16 items-center gap-3 px-4 sm:px-6 lg:px-8">
        <div class="w-12 lg:hidden" aria-hidden="true"></div>

        <form action="{{ request()->routeIs('categories.*') ? route('categories.index') : route('products.index') }}" method="GET" class="relative min-w-0 flex-1">
            <label for="global-search" class="sr-only">Search</label>
            <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-4.35-4.35m2.35-5.65a8 8 0 1 1-16 0 8 8 0 0 1 16 0Z"/>
            </svg>
            <input id="global-search" name="search" type="search" value="{{ request('search') }}" placeholder="{{ request()->routeIs('categories.*') ? 'Search categories…' : 'Search products…' }}" class="erp-input h-10 pl-9 pr-3 sm:max-w-xl">
        </form>

        @auth
            <div class="relative shrink-0">
                <button id="erp-user-menu-button" type="button" class="flex items-center gap-2 rounded-lg p-1.5 text-left transition hover:bg-slate-50" aria-expanded="false" aria-haspopup="true">
                    <span class="flex h-9 w-9 items-center justify-center rounded-full bg-indigo-50 text-sm font-bold text-indigo-700 ring-1 ring-indigo-100">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                    <span class="hidden max-w-40 md:block">
                        <span class="block truncate text-sm font-semibold text-slate-900">{{ auth()->user()->name }}</span>
                        <span class="block truncate text-xs text-slate-500">{{ auth()->user()->email }}</span>
                    </span>
                    <svg class="hidden h-4 w-4 text-slate-400 md:block" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m7 10 5 5 5-5"/></svg>
                </button>

                <div id="erp-user-menu" class="absolute right-0 mt-2 hidden w-56 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-lg" role="menu">
                    <div class="border-b border-slate-100 px-4 py-3">
                        <p class="text-xs font-medium text-slate-500">Signed in as</p>
                        <p class="mt-1 truncate text-sm font-semibold text-slate-900">{{ auth()->user()->email }}</p>
                    </div>
                    <div class="p-1">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="flex w-full items-center rounded-lg px-3 py-2 text-left text-sm font-medium text-rose-600 hover:bg-rose-50" role="menuitem">Sign out</button>
                        </form>
                    </div>
                </div>
            </div>
        @endauth
    </div>
</header>

<script>
    (() => {
        const button = document.getElementById('erp-user-menu-button');
        const menu = document.getElementById('erp-user-menu');
        if (!button || !menu) return;
        button.addEventListener('click', (event) => {
            event.stopPropagation();
            const open = button.getAttribute('aria-expanded') === 'true';
            button.setAttribute('aria-expanded', String(!open));
            menu.classList.toggle('hidden', open);
        });
        document.addEventListener('click', () => {
            button.setAttribute('aria-expanded', 'false');
            menu.classList.add('hidden');
        });
    })();
</script>
