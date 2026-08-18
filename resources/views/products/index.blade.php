<x-layout>
    <!-- Active Search Filter Banner -->
    @if(request('search'))
        <div class="mb-4 flex items-center justify-between rounded-lg bg-gray-100 p-4 border border-gray-200">
            <p class="text-sm text-gray-700">
                Showing results for: <span class="font-semibold text-gray-900">"{{ request('search') }}"</span>
            </p>
            <a href="{{ route('products.index') }}" 
               class="text-sm font-medium text-indigo-600 hover:text-indigo-800 hover:underline">
                Clear search
            </a>
        </div>
    @endif
    @if($category)
    <div class="space-y-4 w-full bg-blue-800/90 p-2 flex items-center justify-center text-white mb-4 border rounded-lg hover:bg-blue-800 transition-colors">
            <a href="{{ route('products.create', ['category' => $category->id]) }}"
               class="w-full px-auto py-2 text-center">
                Create a Product
            </a>
        </div>
    @endif
    <div class="space-y-4 w-full">
        @foreach ($products as $product)
            <x-product-card :product="$product" />
        @endforeach
    </div>
    <!-- Pagination Navigation Links -->
    <div class="mt-6">
        {{ $products->links() }}
    </div>
</x-layout>