<x-layout>
    <x-page-heading>Stock Overview</x-page-heading>

    <!-- Stock Status Filter Tags -->
    <div class="mb-6 flex flex-wrap items-center gap-2">
        <span class="text-sm font-medium text-gray-700 mr-2">Filter Status:</span>

        <!-- All Statuses -->
        <a href="{{ request()->fullUrlWithQuery(['status' => null]) }}"
           class="px-3 py-1.5 rounded-full text-xs font-semibold transition-colors border {{ !request('status') ? 'bg-gray-900 text-white border-gray-900' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50' }}">
            All
        </a>

        <!-- Normal Tag -->
        <a href="{{ request()->fullUrlWithQuery(['status' => 'normal']) }}"
           class="px-3 py-1.5 rounded-full text-xs font-semibold transition-colors border {{ request('status') === 'normal' ? 'bg-green-700 text-white border-green-700 ring-2 ring-green-300' : 'bg-green-50 text-green-800 border-green-200 hover:bg-green-100' }}">
            Normal
        </a>

        <!-- Low Stock Tag -->
        <a href="{{ request()->fullUrlWithQuery(['status' => 'low_stock']) }}"
           class="px-3 py-1.5 rounded-full text-xs font-semibold transition-colors border {{ request('status') === 'low_stock' ? 'bg-yellow-600 text-white border-yellow-600 ring-2 ring-yellow-300' : 'bg-yellow-50 text-yellow-800 border-yellow-200 hover:bg-yellow-100' }}">
            Low Stock
        </a>

        <!-- Out of Stock Tag -->
        <a href="{{ request()->fullUrlWithQuery(['status' => 'out_of_stock']) }}"
           class="px-3 py-1.5 rounded-full text-xs font-semibold transition-colors border {{ request('status') === 'out_of_stock' ? 'bg-red-700 text-white border-red-700 ring-2 ring-red-300' : 'bg-red-50 text-red-800 border-red-200 hover:bg-red-100' }}">
            Out of Stock
        </a>

        <!-- Clear All Filters Button (Shows if search or status filter is active) -->
        @if(request('status') || request('search'))
            <a href="{{ route('stock.index') }}" 
               class="ml-auto inline-flex items-center text-xs font-medium text-red-600 hover:text-red-800 hover:underline">
                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                Clear Filters
            </a>
        @endif
    </div>


    <x-panel class="overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
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

                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <a href="{{ getSortUrl('name') }}" class="group inline-flex items-center hover:text-gray-900">
                            Product {!! renderSortIcon('name') !!}
                        </a>
                    </th>

                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <a href="{{ getSortUrl('stock') }}" class="group inline-flex items-center hover:text-gray-900">
                            Current Stock {!! renderSortIcon('stock') !!}
                        </a>
                    </th>

                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Alert Threshold
                    </th>

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
                            {{ $product->name ?? $product->name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-800">
                            {{ $product->stock }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $product->alert_threshold }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @if($product->stock <= 0)
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                    Out of stock
                                </span>
                            @elseif($product->stock <= $product->alert_threshold)
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
                            No products matching the selected criteria.
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