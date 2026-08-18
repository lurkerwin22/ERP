<x-layout>
    <div class="erp-page">
    <!-- Header Block -->
    <div>
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-gray-200">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Sales</h1>
                <p class="text-sm text-gray-500">Track customer orders, sales totals, and order statuses.</p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <!-- Search Form -->
                <form action="{{ route('sales.index') }}" method="GET" class="flex items-center gap-2">
                    <input 
                        type="text" 
                        name="search" 
                        placeholder="Search sale ID or customer..." 
                        value="{{ request('search') }}" 
                        class="erp-input w-60"
                    />
                    <button type="submit" class="erp-btn-secondary">
                        Search
                    </button>
                    @if(request('search'))
                        <a href="{{ route('sales.index') }}" class="text-sm text-gray-500 hover:text-gray-700 underline px-1">
                            Clear
                        </a>
                    @endif
                </form>

                <!-- New Sale Button -->
                <a href="{{ route('sales.create') }}" class="erp-btn-primary whitespace-nowrap">
                    + New Sale
                </a>
            </div>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 font-medium rounded shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->has('cancel'))
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 font-medium rounded shadow-sm">
            {{ $errors->first('cancel') }}
        </div>
    @endif

    <!-- Table Section -->
    <div class="erp-table-wrap">
        <div class="overflow-x-auto w-full">
            <table class="erp-table">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3.5 text-xs font-bold text-gray-600 uppercase tracking-wider">Sale #</th>
                        <th class="px-6 py-3.5 text-xs font-bold text-gray-600 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-3.5 text-xs font-bold text-gray-600 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3.5 text-xs font-bold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3.5 text-xs font-bold text-gray-600 uppercase tracking-wider">Total</th>
                        <th class="px-6 py-3.5 text-right text-xs font-bold text-gray-600 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse($sales as $sale)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">
                                #{{ $sale->id }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-800">
                                {{ $sale->customer_name ?? optional($sale->customer)->name ?? 'Walk-in Customer' }}
                                @if(is_null($sale->customer_id) && $sale->customer_name && $sale->customer_name !== 'Walk-in Customer')
                                    <span class="text-xs text-gray-400 block">(Deleted Customer)</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ \Carbon\Carbon::parse($sale->sale_date)->format('M d, Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @if($sale->status === 'completed')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Completed
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        Cancelled
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">
                                {{ number_format($sale->total, 2) }} TND
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                <a href="{{ route('sales.show', $sale) }}" class="text-indigo-600 hover:text-indigo-900">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-sm text-gray-500 font-medium">
                                No sales recorded yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-6 w-full">
        {{ $sales->links() }}
    </div>
    </div>
</x-layout>