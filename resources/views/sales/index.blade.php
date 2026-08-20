<x-layout>
    <div x-data="{ filtersOpen: {{ request()->hasAny(['status', 'payment_status', 'customer_id', 'from_date', 'to_date', 'min_total', 'max_total', 'sort']) ? 'true' : 'false' }} }">
        
        <!-- Header -->
        <div class="mb-6 flex justify-between items-center pb-4 border-b">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Sales Dashboard</h1>
                <p class="text-sm text-gray-500">Manage transaction histories, issue payments, and filter orders</p>
            </div>
            <a href="{{ route('sales.create') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-lg text-sm shadow-sm transition">
                + New Sale
            </a>
        </div>

        <!-- Phase 7: Summary KPI Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
                <span class="text-xs text-gray-500 uppercase font-semibold">Total Completed Sales</span>
                <p class="text-2xl font-extrabold text-gray-900 mt-1">{{ $stats['total_sales'] }}</p>
            </div>
            <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
                <span class="text-xs text-gray-500 uppercase font-semibold">Fully Paid</span>
                <p class="text-2xl font-extrabold text-green-600 mt-1">{{ $stats['paid_sales'] }}</p>
            </div>
            <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
                <span class="text-xs text-gray-500 uppercase font-semibold">Unpaid / Partial</span>
                <p class="text-2xl font-extrabold text-yellow-600 mt-1">{{ $stats['unpaid_sales'] }}</p>
            </div>
            <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
                <span class="text-xs text-gray-500 uppercase font-semibold">Outstanding Balance</span>
                <p class="text-2xl font-extrabold text-red-600 mt-1">{{ number_format($stats['outstanding_amount'], 2) }} TND</p>
            </div>
        </div>

        <!-- Phase 3: Search Bar & Filters Trigger -->
        <form method="GET" action="{{ route('sales.index') }}" class="mb-6">
            <div class="flex flex-wrap items-center gap-3">
                <div class="flex-1 min-w-[240px]">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search sale ID or customer..." class="w-full border-gray-300 rounded-lg text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500 px-4 py-2">
                </div>
                <button type="submit" class="px-4 py-2 bg-gray-900 hover:bg-black text-white font-bold rounded-lg text-sm shadow-sm">
                    Search
                </button>
                <button type="button" @click="filtersOpen = !filtersOpen" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 font-bold rounded-lg text-sm shadow-sm hover:bg-gray-50 flex items-center gap-2">
                    <span>Filters</span>
                    <span class="text-xs bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded-full" x-show="filtersOpen">Open</span>
                </button>
                @if(request()->query())
                    <a href="{{ route('sales.index') }}" class="text-xs text-red-600 hover:underline font-semibold">Clear all</a>
                @endif
            </div>

            <!-- Phase 3 & 4: Collapsible Filters Panel -->
            <div x-show="filtersOpen" x-collapse class="mt-4 bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
                <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4">
                    
                    <!-- 1. Sale Status -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Status</label>
                        <select name="status" class="w-full border-gray-300 rounded-lg text-sm">
                            <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>All</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>

                    <!-- 2. Payment Status -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Payment</label>
                        <select name="payment_status" class="w-full border-gray-300 rounded-lg text-sm">
                            <option value="" {{ request('payment_status') == '' ? 'selected' : '' }}>All</option>
                            <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Paid</option>
                            <option value="partial" {{ request('payment_status') == 'partial' ? 'selected' : '' }}>Partial</option>
                            <option value="unpaid" {{ request('payment_status') == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                        </select>
                    </div>

                    <!-- 3. Customer -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Customer</label>
                        <select name="customer_id" class="w-full border-gray-300 rounded-lg text-sm">
                            <option value="all">All Customers</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}" {{ request('customer_id') == $customer->id ? 'selected' : '' }}>
                                    {{ $customer->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- 4. Date Range -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">From Date</label>
                        <input type="date" name="from_date" value="{{ request('from_date') }}" class="w-full border-gray-300 rounded-lg text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">To Date</label>
                        <input type="date" name="to_date" value="{{ request('to_date') }}" class="w-full border-gray-300 rounded-lg text-sm">
                    </div>

                    <!-- 5. Sorting -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Sorting</label>
                        <select name="sort" class="w-full border-gray-300 rounded-lg text-sm">
                            <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest first</option>
                            <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest first</option>
                            <option value="total_low_high" {{ request('sort') == 'total_low_high' ? 'selected' : '' }}>Total low → high</option>
                            <option value="total_high_low" {{ request('sort') == 'total_high_low' ? 'selected' : '' }}>Total high → low</option>
                            <option value="id_asc" {{ request('sort') == 'id_asc' ? 'selected' : '' }}>Sale ID ascending</option>
                            <option value="id_desc" {{ request('sort') == 'id_desc' ? 'selected' : '' }}>Sale ID descending</option>
                        </select>
                    </div>

                </div>

                <!-- Min/Max total optional row -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4 pt-4 border-t">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Min Total (TND)</label>
                        <input type="number" step="0.01" name="min_total" value="{{ request('min_total') }}" placeholder="0.00" class="w-full border-gray-300 rounded-lg text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Max Total (TND)</label>
                        <input type="number" step="0.01" name="max_total" value="{{ request('max_total') }}" placeholder="1000.00" class="w-full border-gray-300 rounded-lg text-sm">
                    </div>
                </div>

                <div class="mt-4 flex justify-end gap-2">
                    <a href="{{ route('sales.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-lg text-sm">Reset</a>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-lg text-sm">Apply Filters</button>
                </div>
            </div>
        </form>

        <!-- Phase 5: Active Filters Badges -->
        @if(request()->hasAny(['search', 'status', 'payment_status', 'customer_id', 'from_date', 'to_date', 'min_total', 'max_total']))
            <div class="mb-4 flex flex-wrap items-center gap-2">
                <span class="text-xs text-gray-500 font-semibold">Active Filters:</span>
                
                @if(request('search'))
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">
                        Query: "{{ request('search') }}"
                    </span>
                @endif

                @if(request('payment_status'))
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800 uppercase">
                        Payment: {{ request('payment_status') }}
                    </span>
                @endif

                @if(request('status') && request('status') !== 'all')
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-800 uppercase">
                        Status: {{ request('status') }}
                    </span>
                @endif

                @if(request('from_date') || request('to_date'))
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                        Date: {{ request('from_date', '...') }} to {{ request('to_date', '...') }}
                    </span>
                @endif
            </div>
        @endif

        <!-- Sales Data Table -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            @if($sales->isEmpty())
                <div class="p-8 text-center text-gray-500">
                    No sales matched your specific filter criteria.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-3.5 text-xs font-bold text-gray-600 uppercase">Sale Ref</th>
                                <th class="px-6 py-3.5 text-xs font-bold text-gray-600 uppercase">Customer</th>
                                <th class="px-6 py-3.5 text-xs font-bold text-gray-600 uppercase">Date</th>
                                <th class="px-6 py-3.5 text-xs font-bold text-gray-600 uppercase text-right">Total</th>
                                <th class="px-6 py-3.5 text-xs font-bold text-gray-600 uppercase text-right">Remaining</th>
                                <th class="px-6 py-3.5 text-xs font-bold text-gray-600 uppercase text-center">Status</th>
                                <th class="px-6 py-3.5 text-xs font-bold text-gray-600 uppercase text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 text-sm">
                            @foreach($sales as $sale)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 font-bold text-indigo-600">
                                        <a href="{{ route('sales.show', $sale) }}">#{{ $sale->id }}</a>
                                    </td>
                                    <td class="px-6 py-4 font-semibold text-gray-900">
                                        {{ $sale->customer?->name ?? $sale->customer_name ?? 'Walk-in Customer' }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-600">
                                        {{ $sale->created_at->format('d/m/Y') }}
                                    </td>
                                    <td class="px-6 py-4 text-right font-medium text-gray-900">
                                        {{ number_format($sale->total, 2) }} TND
                                    </td>
                                    <td class="px-6 py-4 text-right font-bold {{ $sale->remaining_balance > 0 ? 'text-red-600' : 'text-green-600' }}">
                                        {{ number_format($sale->remaining_balance, 2) }} TND
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if($sale->remaining_balance <= 0)
                                            <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">PAID</span>
                                        @elseif($sale->amount_paid > 0)
                                            <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">PARTIAL</span>
                                        @else
                                            <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">UNPAID</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('sales.show', $sale) }}" class="px-3 py-1.5 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg shadow-sm">
                                            View
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <!-- Phase 6: Pagination with Preserved Filters -->
        @if($sales->hasPages())
            <div class="mt-6">
                {{ $sales->links() }}
            </div>
        @endif

    </div>
</x-layout>