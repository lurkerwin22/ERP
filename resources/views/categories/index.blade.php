<x-layout>
    <div class="flex justify-between items-center mb-6">
        <x-page-heading>Categories</x-page-heading>

        <a href="/categories/create" 
        class="inline-flex whitespace-nowrap px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg shadow-sm transition-colors text-sm">
            + New Category
        </a>
    </div>

    <!-- 3-Column Responsive Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse ($categories as $categorie)
            <x-category-card :categorie="$categorie" />
        @empty
            <div class="col-span-full text-center py-12 bg-white rounded-lg border border-gray-200 text-gray-500">
                No categories found.
            </div>
        @endforelse
    </div>

    <!-- Pagination Links (if paginated) -->
    @if(method_exists($categories, 'links'))
        <div class="mt-6">
            {{ $categories->links() }}
        </div>
    @endif
</x-layout>