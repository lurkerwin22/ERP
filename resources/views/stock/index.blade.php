<x-layout>
    <x-page-heading>Stock Overview</x-page-heading>

    <!-- Active Search Filter Banner -->
    @if(request('search'))
        <div class="mb-4 flex items-center justify-between rounded-lg bg-gray-100 p-4 border border-gray-200">
            <p class="text-sm text-gray-700">
                Showing results for: <span class="font-semibold text-gray-900">"{{ request('search') }}"</span>
            </p>
            <a href="{{ route('stock.index') }}" 
               class="text-sm font-medium text-indigo-600 hover:text-indigo-800 hover:underline">
                Clear search
            </a>
        </div>
    @endif

    <x-panel class="overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <!-- Helper PHP Logic for Toggle Direction -->
                    @php
                        function getSortUrl($column) {
                            $currentSort = request('sort', 'name');
                            $currentDir = request('direction', 'asc');
                            $newDir = ($currentSort === $column && $currentDir === 'asc') ? 'desc' : 'asc';
                            
                            return request()->fullUrlWithQuery([
                                'sort' => $column,
                                'direction' => $newDir
                            ]);
                        }

                        function renderSortIcon($column) {
                            $currentSort = request('sort', 'name');
                            $currentDir = request('direction', 'asc');

                            if ($currentSort !== $column) {
                                return '<svg class="w-3.5 h-3.5 text-gray-400 group-hover:text-gray-600 ml-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path></svg>';
                            }

                            return $currentDir === 'asc' 
                                ? '<svg class="w-3.5 h-3.5 text-indigo-600 ml-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>'
                                : '<svg class="w-3.5 h-3.5 text-indigo-600 ml-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>';
                        }
                    @endphp

                    <!-- Product Column Header -->
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <a href="{{ getSortUrl('name') }}" class="group inline-flex items-center hover:text-gray-900">
                            Product {!! renderSortIcon('name') !!}
                        </a>
                    </th>

                    <!-- Stock Column Header -->
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <a href="{{ getSortUrl('stock') }}" class="group inline-flex items-center hover:text-gray-900">
                            Current Stock {!! renderSortIcon('stock') !!}
                        </a>
                    </th>

                    <!-- Alert Threshold Column Header -->
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Alert Threshold
                    </th>

                    <!-- Status Column Header -->
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <a href="{{ getSortUrl('status') }}" class="group inline-flex items-center hover:text-gray-900">
                            Status {!! renderSortIcon('status') !!}
                        </a>
                    </th>

                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($products as $product)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $product->nom ?? $product->name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-800">
                            {{ $product->stock }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $product->seuil_alerte }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @if($product->stock <= 0)
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                    Out of stock
                                </span>
                            @elseif($product->stock <= $product->seuil_alerte)
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    Low stock
                                </span>
                            @else
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Normal
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-3">
                            <a href="{{ route('stock.adjust', $product) }}" class="text-indigo-600 hover:text-indigo-900 font-medium">Manage Stock</a>
                            <span class="text-gray-300">|</span>
                            <a href="{{ route('stock.movements', $product) }}" class="text-gray-600 hover:text-gray-900 font-medium">History</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                            @if(request('search'))
                                No products found matching "<span class="font-semibold">{{ request('search') }}</span>".
                            @else
                                No products found.
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </x-panel>

    <div class="mt-4">
        {{ $products->links() }}
    </div>
</x-layout>