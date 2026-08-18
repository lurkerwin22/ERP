<button data-drawer-target="default-sidebar" data-drawer-toggle="default-sidebar"
        aria-controls="default-sidebar" type="button"
        class="text-heading bg-transparent box-border border border-transparent hover:bg-neutral-secondary-medium focus:ring-4 focus:ring-neutral-tertiary font-medium leading-5 rounded-base ms-3 mt-3 text-sm p-2 focus:outline-none inline-flex sm:hidden">
    <span class="sr-only">Open sidebar</span>

    <svg class="w-6 h-6" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
         width="24" height="24" fill="none" viewBox="0 0 24 24">
        <path stroke="currentColor" stroke-linecap="round" stroke-width="2"
              d="M5 7h14M5 12h14M5 17h10"/>
    </svg>
</button>

<aside id="default-sidebar"
       class="fixed top-0 left-0 z-40 w-64 h-full transition-transform -translate-x-full sm:translate-x-0"
       aria-label="Sidebar">

    <div class="h-full px-3 py-4 overflow-y-auto bg-neutral-primary-soft border-e border-default">

        <ul class="flex flex-col h-full space-y-2 font-medium">

            <!-- Dashboard -->
            <li>
                <a href="/"
                   class="flex items-center px-2 py-1.5 text-body rounded-base hover:bg-neutral-tertiary hover:text-fg-brand group">

                    <svg class="shrink-0 w-5 h-5 transition duration-75 group-hover:text-fg-brand"
                         aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                         width="24" height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round"
                              stroke-linejoin="round" stroke-width="2"
                              d="M3 13h8V3H3v10Zm10 8h8V11h-8v10ZM3 21h8v-6H3v6Zm10-18v6h8V3h-8Z"/>
                    </svg>

                    <span class="ms-3">Dashboard</span>
                </a>
            </li>


            <!-- Product -->
            <li>
                <a href="/products"
                   class="flex items-center px-2 py-1.5 text-body rounded-base hover:bg-neutral-tertiary hover:text-fg-brand group">

                    <svg class="shrink-0 w-5 h-5 transition duration-75 group-hover:text-fg-brand"
                         aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                         width="24" height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round"
                              stroke-linejoin="round" stroke-width="2"
                              d="M20 7.5 12 3 4 7.5m16 0v9L12 21l-8-4.5v-9m16 0-8 4.5m0 0L4 7.5M12 12v9"/>
                    </svg>

                    <span class="flex-1 ms-3 whitespace-nowrap">Product</span>
                </a>
            </li>


            <!-- Categories -->
            <li>
                <a href="/categories"
                   class="flex items-center px-2 py-1.5 text-body rounded-base hover:bg-neutral-tertiary hover:text-fg-brand group">

                    <svg class="shrink-0 w-5 h-5 transition duration-75 group-hover:text-fg-brand"
                         aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                         width="24" height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round"
                              stroke-linejoin="round" stroke-width="2"
                              d="M4 4h6v6H4V4Zm10 0h6v6h-6V4ZM4 14h6v6H4v-6Zm10 0h6v6h-6v-6Z"/>
                    </svg>

                    <span class="flex-1 ms-3 whitespace-nowrap">Categories</span>
                </a>
            </li>


            <!-- Stock -->
            <li>
                <a href="/stock"
                   class="flex items-center px-2 py-1.5 text-body rounded-base hover:bg-neutral-tertiary hover:text-fg-brand group">

                    <svg class="shrink-0 w-5 h-5 transition duration-75 group-hover:text-fg-brand"
                         aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                         width="24" height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round"
                              stroke-linejoin="round" stroke-width="2"
                              d="M4 7h16M4 12h16M4 17h16"/>
                        <path stroke="currentColor" stroke-linecap="round"
                              stroke-linejoin="round" stroke-width="2"
                              d="M7 4v16"/>
                    </svg>

                    <span class="flex-1 ms-3 whitespace-nowrap">Stock</span>
                </a>
            </li>


            <!-- Customers -->
            <li>
                <a href="/customers"
                   class="flex items-center px-2 py-1.5 text-body rounded-base hover:bg-neutral-tertiary hover:text-fg-brand group">

                    <svg class="shrink-0 w-5 h-5 transition duration-75 group-hover:text-fg-brand"
                         aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                         width="24" height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round"
                              stroke-linejoin="round" stroke-width="2"
                              d="M15 19a4 4 0 0 0-8 0"/>
                        <circle cx="11" cy="9" r="4"
                                stroke="currentColor" stroke-width="2"/>
                        <path stroke="currentColor" stroke-linecap="round"
                              stroke-width="2"
                              d="M19 12a3 3 0 1 0-2.5-4.5"/>
                        <path stroke="currentColor" stroke-linecap="round"
                              stroke-width="2"
                              d="M17 19h4"/>
                    </svg>

                    <span class="flex-1 ms-3 whitespace-nowrap">Customers</span>
                </a>
            </li>


            <!-- Sales -->
            <li>
                <a href="/sales"
                   class="flex items-center px-2 py-1.5 text-body rounded-base hover:bg-neutral-tertiary hover:text-fg-brand group">

                    <svg class="shrink-0 w-5 h-5 transition duration-75 group-hover:text-fg-brand"
                         aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                         width="24" height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round"
                              stroke-linejoin="round" stroke-width="2"
                              d="M4 5h16v14H4V5Z"/>
                        <path stroke="currentColor" stroke-linecap="round"
                              stroke-width="2"
                              d="M8 9h8M8 13h5"/>
                        <path stroke="currentColor" stroke-linecap="round"
                              stroke-width="2"
                              d="M15 17h2"/>
                    </svg>

                    <span class="flex-1 ms-3 whitespace-nowrap">Sales</span>
                </a>
            </li>


            <!-- Logout -->
            <li class="mt-auto pt-6 mb-6">
                @auth
                    <div class="font-bold flex">
                        <form method="POST" action="/logout" class="w-full">
                            @csrf
                            @method('DELETE')

                            <x-forms.button class="w-full">
                                Log Out
                            </x-forms.button>
                        </form>
                    </div>
                @endauth
            </li>

        </ul>
    </div>
</aside>