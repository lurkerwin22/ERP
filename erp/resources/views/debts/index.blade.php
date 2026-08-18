<x-layout>
    <div class="erp-page">
        <x-page-heading description="Overview of unpaid and partially paid customer balances">Outstanding Debts</x-page-heading>

    <!-- KPI Summary Cards -->
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        <div class="erp-stat">
            <span class="text-xs text-gray-500 uppercase font-semibold">Total Outstanding Debt</span>
            <p class="text-3xl font-extrabold text-3xl font-extrabold text-rose-600 mt-1">{{ number_format($totalDebt, 2) }} TND</p>
        </div>

        <div class="erp-stat">
            <span class="text-xs text-gray-500 uppercase font-semibold">Sales with Unpaid Balances</span>
            <p class="text-3xl font-extrabold text-indigo-600 mt-1">{{ $totalSalesWithDebt }}</p>
        </div>
    </div>

    <!-- Debts Table -->
    <div class="erp-table-wrap">
        @if($sales->isEmpty())
            <div class="p-8 text-center text-gray-500">
                🎉 No outstanding debts found! All completed sales are fully paid.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="erp-table">
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
                            <tr>
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
                                    {{ number_format($sale->remaining_amount, 2) }} TND
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($sale->payment_status === 'partial')
                                        <x-badge type="partial">PARTIAL</x-badge>
                                    @else
                                        <x-badge type="unpaid">UNPAID</x-badge>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('sales.show', $sale) }}" class="erp-btn-primary min-h-9 px-3 py-1.5 text-xs">
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
    </div>
</x-layout>