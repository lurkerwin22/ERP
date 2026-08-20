<x-layout>
    <div class="mb-6 flex justify-between items-center pb-4 border-b">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Outstanding Debts</h1>
            <p class="text-sm text-gray-500">Overview of unpaid and partially paid customer balances</p>
        </div>
    </div>

    <!-- KPI Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
            <span class="text-xs text-gray-500 uppercase font-semibold">Total Outstanding Debt</span>
            <p class="text-3xl font-extrabold text-red-600 mt-1">{{ number_format($totalDebt, 2) }} TND</p>
        </div>

        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
            <span class="text-xs text-gray-500 uppercase font-semibold">Sales with Unpaid Balances</span>
            <p class="text-3xl font-extrabold text-indigo-600 mt-1">{{ $totalSalesWithDebt }}</p>
        </div>
    </div>

    <!-- Debts Table -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        @if($sales->isEmpty())
            <div class="p-8 text-center text-gray-500">
                🎉 No outstanding debts found! All completed sales are fully paid.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3.5 text-xs font-bold text-gray-600 uppercase">Sale Ref</th>
                            <th class="px-6 py-3.5 text-xs font-bold text-gray-600 uppercase">Customer</th>
                            <th class="px-6 py-3.5 text-xs font-bold text-gray-600 uppercase">Sale Date</th>
                            <th class="px-6 py-3.5 text-xs font-bold text-gray-600 uppercase text-right">Total</th>
                            <th class="px-6 py-3.5 text-xs font-bold text-gray-600 uppercase text-right">Paid</th>
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
                                    @if($sale->customer)
                                        <a href="{{ route('customers.show', $sale->customer) }}" class="hover:underline text-gray-900">
                                            {{ $sale->customer->name }}
                                        </a>
                                    @else
                                        {{ $sale->customer_name ?? 'Walk-in Customer' }}
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-gray-600">
                                    {{ $sale->created_at->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 text-right font-medium text-gray-900">
                                    {{ number_format($sale->total, 2) }} TND
                                </td>
                                <td class="px-6 py-4 text-right font-semibold text-green-600">
                                    {{ number_format($sale->amount_paid, 2) }} TND
                                </td>
                                <td class="px-6 py-4 text-right font-bold text-red-600">
                                   {{ number_format($sale->remaining_balance, 2) }} TND
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($sale->payment_status === 'partial')
                                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">PARTIAL</span>
                                    @else
                                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">UNPAID</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('sales.show', $sale) }}" class="px-3 py-1.5 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg shadow-sm transition">
                                        Collect Payment
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
    <!-- Pagination -->
    @if($sales->hasPages())
        <div class="mt-6">
            {{ $sales->links() }}
        </div>
    @endif
</x-layout>