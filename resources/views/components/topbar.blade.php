<div class="flex h-16 w-full items-center justify-between border-b border-gray-200 bg-white px-4 shadow-sm sm:px-6 lg:px-8">

    <!-- Left Side: Expanded Search Bar -->
    <div class="flex flex-1">
        <!-- Updated action attribute -->
        <form action="{{ request()->routeIs('categories.*') ? route('categories.index') : route('products.index') }}" method="GET" class="relative w-full max-w-3xl">
            <label for="search-field" class="sr-only">Search</label>

            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd"
                        d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z"
                        clip-rule="evenodd" />
                </svg>
            </div>

            <input
                id="search-field"
                class="block w-full rounded-md border border-gray-300 bg-gray-50 py-2 pl-10 pr-3 text-gray-900 placeholder-gray-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-600 sm:text-sm"
                placeholder="{{ request()->routeIs('categories.*') ? 'Search categories...' : 'Search products, customers, sales...' }}"
                type="search"
                name="search"
                value="{{ request('search') }}"
            >
        </form>
    </div>


    <!-- Right Side: User Profile -->
    <div class="ml-6 flex items-center">

        @auth
            <div class="relative">

                <!-- Profile Button -->
                <button
                    type="button"
                    class="-m-1.5 flex items-center rounded-md p-1.5 focus:outline-none group"
                    id="user-menu-button"
                    aria-expanded="false"
                    aria-haspopup="true"
                >
                    <span class="sr-only">Open user menu</span>

                    <!-- User Avatar -->
                    @if(auth()->user()->profile_photo)
                        <img src="{{ asset('storage/' . auth()->user()->profile_photo) }}" alt="{{ auth()->user()->name }}" class="h-8 w-8 rounded-full object-cover ring-2 ring-gray-200 group-hover:ring-indigo-500">
                    @else
                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-indigo-100 text-sm font-semibold text-indigo-600 ring-2 ring-gray-200 group-hover:ring-indigo-500">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                    @endif

                    <!-- User Name -->
                    <span class="hidden lg:flex lg:items-center ">
                        <span class="ml-4 text-sm font-semibold leading-6 text-gray-900">
                            {{ auth()->user()->name }}
                        </span>

                        
                    </span>
                </button>


                <!-- Dropdown -->
                <div
                    class="absolute right-0 z-10 mt-2.5 w-56 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black/5 focus:outline-none hidden"
                    role="menu"
                    aria-orientation="vertical"
                    aria-labelledby="user-menu-button"
                >

                    <!-- User Information -->
                    <div class="px-4 py-3 border-b border-gray-100">
                        <p class="text-xs text-gray-500">
                            Signed in as
                        </p>

                        <p class="mt-1 text-sm font-medium text-gray-900 truncate">
                            {{ auth()->user()->email }}
                        </p>
                        <span class="mt-2 inline-flex rounded-full bg-indigo-50 px-2.5 py-1 text-xs font-semibold capitalize text-indigo-700">
                            {{ auth()->user()->role }}
                        </span>
                    </div>

                    <!-- Profile -->
                    <a
                        href="{{ route('profile.edit') }}"
                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50"
                        role="menuitem"
                    >
                        Your Profile
                    </a>

                    <!-- Settings -->
                    <a
                        href="{{ route('profile.edit') }}"
                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50"
                        role="menuitem"
                    >
                        Settings
                    </a>

                    <hr class="my-1 border-gray-100">

                    <!-- Logout -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf

                        <button
                            type="submit"
                            class="block w-full px-4 py-2 text-left text-sm text-red-600 hover:bg-red-50"
                            role="menuitem"
                        >
                            Sign out
                        </button>
                    </form>

                </div>

            </div>
        @endauth

    </div>
</div>