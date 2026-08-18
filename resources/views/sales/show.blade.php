<x-layout>
    <!-- Header -->
    <div class="mb-6 flex justify-between items-center pb-4 border-b">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Sale Details #{{ $sale->id }}</h1>
            <p class="text-sm text-gray-500">Recorded on {{ $sale->created_at->format('d/m/Y H:i') }}</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('sales.index') }}"
               class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg shadow-sm hover:bg-gray-50">
                &larr; Back to Sales
            </a>

            <a href="{{ route('sales.invoice', $sale) }}"
               class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg shadow-sm hover:bg-indigo-700 flex items-center gap-2">
                📄 View / Print Invoice
            </a>
            
            <a href="{{ route('sales.receipt', $sale) }}" 
               class="inline-flex items-center px-4 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-sm font-medium rounded-md transition border border-indigo-200">
                🧾 Receipt
            </a>

            @if($sale->status === 'completed')
                <form method="POST"
                      action="{{ route('sales.cancel', $sale) }}"
                      onsubmit="return confirm('Are you sure you want to cancel this sale and restore stock?');">
                    @csrf
                    @method('PATCH')

                    <button type="submit"
                            class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg shadow-sm hover:bg-red-700">
                        ↩ Cancel & Restock
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Financial KPI Summary Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
            <span class="text-xs text-gray-500 uppercase font-semibold">Total Amount</span>
            <p class="text-2xl font-black text-gray-900 mt-1">{{ number_format($sale->total_amount, 2) }} TND</p>
        </div>

        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
            <span class="text-xs text-gray-500 uppercase font-semibold">Amount Paid</span>
            <p class="text-2xl font-black text-green-600 mt-1">{{ number_format($sale->amount_paid, 2) }} TND</p>
        </div>

        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
            <span class="text-xs text-gray-500 uppercase font-semibold">Remaining Balance</span>
            <p class="text-2xl font-black text-red-600 mt-1">{{ number_format($sale->remaining_balance, 2) }} TND</p>
        </div>

        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
            <span class="text-xs text-gray-500 uppercase font-semibold">Payment Status</span>
            <div class="mt-2">
                @if($sale->status === 'cancelled')
                    <span class="px-3 py-1 text-xs font-bold uppercase rounded-full bg-gray-200 text-gray-800">CANCELLED</span>
                @elseif($sale->payment_status === 'paid')
                    <span class="px-3 py-1 text-xs font-bold uppercase rounded-full bg-green-100 text-green-800">PAID</span>
                @elseif($sale->payment_status === 'partial')
                    <span class="px-3 py-1 text-xs font-bold uppercase rounded-full bg-yellow-100 text-yellow-800">PARTIAL</span>
                @else
                    <span class="px-3 py-1 text-xs font-bold uppercase rounded-full bg-red-100 text-red-800">UNPAID</span>
                @endif
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column (Items + Payment History) -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Purchased Items Table -->
            <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Purchased Items</h2>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-4 py-3 text-xs font-bold text-gray-600 uppercase">Product</th>
                                <th class="px-4 py-3 text-xs font-bold text-gray-600 uppercase">Unit Price</th>
                                <th class="px-4 py-3 text-xs font-bold text-gray-600 uppercase text-center">Qty</th>
                                <th class="px-4 py-3 text-right text-xs font-bold text-gray-600 uppercase">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($sale->saleItems as $item)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm font-semibold text-gray-900">
                                        {{ $item->product_name }}
                                        @if(is_null($item->product_id))
                                            <span class="ml-1 text-xs font-normal text-gray-400">(Archived Product)</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600">
                                        {{ number_format($item->unit_price, 2) }} TND
                                    </td>
                                    <td class="px-4 py-3 text-sm text-center font-medium">
                                        {{ $item->quantity }}
                                    </td>
                                    <td class="px-4 py-3 text-right text-sm font-bold text-gray-900">
                                        {{ number_format($item->subtotal, 2) }} TND
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Payment History Table -->
            <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Payment History</h2>

                @if($sale->payments->isEmpty())
                    <p class="text-gray-500 text-sm">No payments recorded for this sale yet.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-4 py-3 text-xs font-bold text-gray-600 uppercase">Date</th>
                                    <th class="px-4 py-3 text-xs font-bold text-gray-600 uppercase">Method</th>
                                    <th class="px-4 py-3 text-xs font-bold text-gray-600 uppercase">Amount</th>
                                    <th class="px-4 py-3 text-right text-xs font-bold text-gray-600 uppercase">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 text-sm">
                                @foreach($sale->payments as $payment)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 font-medium text-gray-900">
                                            {{ $payment->paid_at ? $payment->paid_at->format('d/m/Y H:i') : $payment->created_at->format('d/m/Y H:i') }}
                                        </td>
                                        <td class="px-4 py-3 capitalize text-gray-600">
                                            {{ str_replace('_', ' ', $payment->payment_method) }}
                                        </td>
                                        <td class="px-4 py-3 font-bold text-gray-900">
                                            {{ number_format($payment->amount, 2) }} TND
                                        </td>
                                        <td class="px-4 py-3 text-right">
                                            @if($sale->status !== 'cancelled')
                                                <form action="{{ route('payments.destroy', $payment) }}" method="POST" onsubmit="return confirm('Are you sure you want to reverse this payment?');" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-xs font-semibold text-red-600 hover:text-red-800">
                                                        Reverse
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        <!-- Right Column (Customer Sidebar + Add Payment Form) -->
        <div class="space-y-6">
            <!-- Customer & Sale Notes -->
            <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm space-y-4">
                <h2 class="text-lg font-bold text-gray-900 border-b pb-2">Customer & Status</h2>      
                <div>
                    <label class="text-xs text-gray-500 uppercase font-semibold">Customer</label>
                    <p class="text-sm font-bold text-gray-900 mt-1">
                        {{ $sale->customer_name ?? optional($sale->customer)->name ?? 'Walk-in Customer' }}
                        @if(is_null($sale->customer_id) && $sale->customer_name && $sale->customer_name !== 'Walk-in Customer')
                            <span class="text-xs font-normal text-gray-400 block">(Deleted Customer)</span>
                        @endif
                    </p>
                    @if($sale->customer_phone || optional($sale->customer)->phone)
                        <p class="text-xs text-gray-500">{{ $sale->customer_phone ?? optional($sale->customer)->phone }}</p>
                    @endif
                </div>

                <div>
                    <label class="text-xs text-gray-500 uppercase font-semibold">Sale Status</label>
                    <div class="mt-1">
                        @if($sale->status === 'completed')
                            <span class="px-2.5 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full">Completed</span>
                        @else
                            <span class="px-2.5 py-1 text-xs font-semibold text-red-800 bg-red-100 rounded-full">Cancelled</span>
                        @endif
                    </div>
                </div>

                @if($sale->notes)
                    <div>
                        <label class="text-xs text-gray-500 uppercase font-semibold">Notes</label>
                        <p class="text-sm text-gray-600 bg-gray-50 p-3 rounded-lg border mt-1">{{ $sale->notes }}</p>
                    </div>
                @endif
            </div>

            <!-- Add Payment Form Box -->
            <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Add Payment</h2>

                @if($sale->status === 'cancelled')
                    <div class="p-4 bg-gray-100 text-gray-600 rounded-lg text-sm border border-gray-200">
                        This sale is <strong>cancelled</strong>. No payments can be added.
                    </div>
                @elseif($sale->payment_status === 'paid')
                    <div class="p-4 bg-green-50 text-green-700 rounded-lg text-sm border border-green-200">
                        🎉 This sale is <strong>fully paid</strong>! No further payment needed.
                    </div>
                @else
                    <form action="{{ route('sales.payments.store', $sale) }}" method="POST" class="space-y-4">
                        @csrf

                        <!-- Amount -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Amount (TND)</label>
                            <input type="number" step="0.01" min="0.01" max="{{ $sale->remaining_balance }}" name="amount" value="{{ old('amount', $sale->remaining_balance) }}" required class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                            <span class="text-xs text-gray-500 mt-1 block">Max: {{ number_format($sale->remaining_balance, 2) }} TND</span>
                            @error('amount') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Payment Method -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Payment Method</label>
                            <select name="payment_method" required class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                <option value="cash">Cash</option>
                                <option value="card">Card</option>
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="check">Check</option>
                            </select>
                            @error('payment_method') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Date -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Payment Date</label>
                            <input type="datetime-local" name="paid_at" value="{{ old('paid_at', now()->format('Y-m-d\TH:i')) }}" required class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                            @error('paid_at') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 px-4 rounded-lg shadow-sm transition text-sm">
                            ➕ Save Payment
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</x-layout>