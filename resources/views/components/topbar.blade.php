<header class="flex h-16 w-full items-center justify-between border-b border-gray-200 bg-white px-4 shadow-sm sm:px-6 lg:px-8">
  
  <!-- Left Side: Search Bar Component -->
  <div class="flex flex-1 max-w-md">
    <form action="#" method="GET" class="relative w-full">
      <label for="search-field" class="sr-only">Search</label>
      <!-- Search Icon -->
      <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
        <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
          <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
        </svg>
      </div>
      <!-- Search Input -->
      <input 
        id="search-field" 
        class="block h-full w-full rounded-md border-0 py-2 pl-10 pr-3 text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-indigo-600 sm:text-sm bg-gray-50 border border-gray-300" 
        placeholder="Search everything..." 
        type="search" 
        name="search"
      >
    </form>
  </div>

  <!-- Right Side: Profile & Action Buttons -->
  <div class="ml-4 flex items-center md:ml-6 space-x-4">
    
    <!-- Notification Button (Optional but common) -->
    <button type="button" class="relative rounded-full bg-white p-1 text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
      <span class="sr-only">View notifications</span>
      <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
      </svg>
      <!-- Alert Badge -->
      <span class="absolute top-1 right-1 block h-2 w-2 rounded-full bg-red-400 ring-2 ring-white"></span>
    </button>

    <!-- Separator line -->
    <div class="hidden lg:block lg:h-6 lg:w-px lg:bg-gray-200" aria-hidden="true"></div>

    <!-- User Profile Dropdown Component -->
    <div class="relative">
      <!-- Profile Button Trigger -->
      <button type="button" class="-m-1.5 flex items-center p-1.5 focus:outline-none group" id="user-menu-button" aria-expanded="false" aria-haspopup="true">
        <span class="sr-only">Open user menu</span>
        <img class="h-8 w-8 rounded-full bg-gray-50 object-cover ring-2 ring-gray-200 group-hover:ring-indigo-500" src="https://picsum.photos/32" alt="User avatar">
        <span class="hidden lg:flex lg:items-center">
          <span class="ml-4 text-sm font-semibold leading-6 text-gray-900" aria-hidden="true">Tom Cook</span>
          <svg class="ml-2 h-5 w-5 text-gray-400 group-hover:text-gray-600" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
          </svg>
        </span>
      </button>

      <!-- 
        Dropdown Menu Container
        Toggle states (Visible/Hidden) depend on your JavaScript framework (React/Vue state, or AlpineJS).
        Add 'hidden' class to hide, remove it to show.
      -->
      <div class="absolute right-0 z-10 mt-2.5 w-48 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black/5 focus:outline-none hidden" role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button" tabindex="-1">
        <div class="px-4 py-2 border-b border-gray-100">
          <p class="text-xs text-gray-500">Signed in as</p>
          <p class="text-sm font-medium text-gray-900 truncate">tom.cook@example.com</p>
        </div>
        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50" role="menuitem" tabindex="-1">Your Profile</a>
        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50" role="menuitem" tabindex="-1">Settings</a>
        <hr class="border-gray-100 my-1">
        <a href="#" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50" role="menuitem" tabindex="-1">Sign out</a>
      </div>

    </div>
  </div>
</header>
