<x-layout>
    <div class="erp-page">
        <x-page-heading description="Manage your catalog, pricing, categories, and stock levels." action="{{ route('products.create') }}" action-label="+ Add product">Products</x-page-heading>

        @if(request('search'))
            <div class="flex items-center justify-between gap-4 rounded-xl border border-indigo-100 bg-indigo-50 px-4 py-3 text-sm text-indigo-800">
                <p>Showing results for <strong>"{{ request('search') }}"</strong></p>
                <a href="{{ route('products.index') }}" class="font-semibold hover:underline">Clear</a>
            </div>
        @endif

        @if($category)
            <div class="flex items-center justify-between gap-4 rounded-xl border border-indigo-100 bg-indigo-50 px-4 py-3">
                <div>
                    <p class="text-sm font-semibold text-indigo-900">Category: {{ $category->name }}</p>
                    <p class="text-xs text-indigo-700">Create a product directly in this category.</p>
                </div>
                <a href="{{ route('products.create', ['category' => $category->id]) }}" class="erp-btn-primary shrink-0">+ Add product</a>
            </div>
        @endif

        <div class="grid gap-4">
            @forelse ($products as $product)
                <x-product-card :product="$product" />
            @empty
                <x-panel>
                    <x-empty-state title="No products found" description="Create your first product or adjust your search filters.">
                        <a href="{{ route('products.create') }}" class="erp-btn-primary">+ Create product</a>
                    </x-empty-state>
                </x-panel>
            @endforelse
        </div>

        @if(method_exists($products, 'links'))
            <div>{{ $products->links() }}</div>
        @endif
    </div>
</x-layout>
