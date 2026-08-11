<x-layout>
    <div class="space-y-4 w-full bg-blue-800/90 p-2 flex items-center justify-center text-white mb-4 border rounded-lg hover:bg-blue-800 transition-colors">
        <a href="/products/create" class="w-full px-auto py-2 text-center">Create a Product</a>
    </div>
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