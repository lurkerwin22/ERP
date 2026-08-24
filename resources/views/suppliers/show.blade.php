<x-layout>
    <div class="max-w-7xl mx-auto py-6 space-y-6">
        <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm flex justify-between items-start">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $supplier->name }}</h1>
                <p class="text-sm text-gray-500 mt-1">{{ $supplier->email ?? 'No email' }} • {{ $supplier->phone ?? 'No phone' }}</p>
                @if($supplier->address)<p class="text-xs text-gray-400 mt-1">{{ $supplier->address }}</p>@endif
            </div>
            <a href="{{ route('suppliers.edit', $supplier) }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium">
                Edit Supplier
            </a>
        </div>

        <h2 class="text-lg font-bold text-gray-900">Products Supplied ({{ $supplier->products->count() }})</h2>

        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Purchase Price</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Selling Price</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stock</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($supplier->products as $product)
                        <tr>
                            <td class="px-6 py-4 text-sm font-semibold text-gray-900">{{ $product->name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $product->category->name ?? 'Uncategorized' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $product->purchase_price !== null ? number_format($product->purchase_price, 2) . ' DT' : '—' }}</td>
                            <td class="px-6 py-4 text-sm font-semibold text-gray-900">{{ number_format($product->price, 2) }} DT</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $product->stock }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-6 text-center text-sm text-gray-500">No products linked to this supplier yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layout>