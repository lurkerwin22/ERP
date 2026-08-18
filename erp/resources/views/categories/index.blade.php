<x-layout>
    <div class="erp-page">
        <x-page-heading description="Organize products into clear groups for faster browsing and stock management." action="{{ route('categories.create') }}" action-label="+ New category">Categories</x-page-heading>

        @if(request('search'))
            <div class="flex items-center justify-between gap-4 rounded-xl border border-indigo-100 bg-indigo-50 px-4 py-3 text-sm text-indigo-800">
                <p>Showing categories for <strong>"{{ request('search') }}"</strong></p>
                <a href="{{ route('categories.index') }}" class="font-semibold hover:underline">Clear</a>
            </div>
        @endif

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
            @forelse ($categories as $category)
                <x-category-card :category="$category" />
            @empty
                <div class="col-span-full erp-card">
                    <x-empty-state title="No categories found" description="Create a category to keep your product catalog organized.">
                        <a href="{{ route('categories.create') }}" class="erp-btn-primary">+ Create category</a>
                    </x-empty-state>
                </div>
            @endforelse
        </div>

        @if(method_exists($categories, 'links'))
            <div>{{ $categories->links() }}</div>
        @endif
    </div>
</x-layout>
