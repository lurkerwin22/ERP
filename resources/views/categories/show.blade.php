<x-layout>
    <div class="max-w-7xl mx-auto py-6 space-y-6">

        <!-- Header -->
        <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm flex justify-between items-start">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $category->name }}</h1>

                <p class="text-sm text-gray-500 mt-1">
                    {{ $category->description ?? 'No description provided.' }}
                </p>
            </div>

            <div class="flex gap-3">
                <x-forms.link-button href="{{ route('categories.index') }}">
                    Back to Categories
                </x-forms.link-button>

                <a href="{{ route('categories.edit', $category) }}"
                   class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium">
                    Edit Category
                </a>
            </div>
        </div>

        <h2 class="text-lg font-bold text-gray-900">
            Products in this Category ({{ $category->products->count() }})
        </h2>

        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Purchase Price</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Selling Price</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stock</th>
                    </tr>
                </thead>

                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($category->products as $product)
                        <tr>
                            <td class="px-6 py-4 text-sm font-semibold text-gray-900">
                                <a href="{{ route('products.edit', $product) }}" class="hover:text-indigo-600">
                                    {{ $product->name }}
                                </a>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $product->purchase_price !== null ? number_format($product->purchase_price, 2) . ' DT' : '—' }}
                            </td>
                            <td class="px-6 py-4 text-sm font-semibold text-gray-900">
                                {{ number_format($product->price, 2) }} DT
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ rtrim(rtrim(number_format($product->stock, 2), '0'), '.') }} {{ $product->unit ?? '' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-6 text-center text-sm text-gray-500">
                                No products linked to this category yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</x-layout>
