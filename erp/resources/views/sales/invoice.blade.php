<x-layout>
    <style>
        @media print {
            nav, header, aside, .no-print {
                display: none !important;
            }
            body {
                background: white !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            .invoice-container {
                box-shadow: none !important;
                border: none !important;
                padding: 0 !important;
                max-width: 100% !important;
            }
        }
    </style>

    <div class="mb-6 flex justify-between items-center pb-4 border-b no-print">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Invoice Details</h1>
            <p class="text-sm text-gray-500">Invoice reference: #INV-{{ str_pad($sale->id, 6, '0', STR_PAD_LEFT) }}</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('sales.show', $sale) }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg shadow-sm hover:bg-gray-50 transition">
                &larr; Back to Sale Details
            </a>
            <button onclick="window.print()" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-lg shadow transition flex items-center gap-2">
                🖨️ Print Invoice
            </button>
        </div>
    </div>

    <div class="invoice-container max-w-4xl mx-auto bg-white p-8 md:p-12 rounded-xl shadow-sm border border-gray-200">
        <!-- Header -->
        <div class="flex justify-between items-start border-b pb-8">
            <div>
                <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight">INVOICE</h2>
                <p class="text-sm font-semibold text-indigo-600 mt-1">#INV-{{ str_pad($sale->id, 6, '0', STR_PAD_LEFT) }}</p>
            </div>
            <div class="text-right">
                <h3 class="text-lg font-bold text-gray-800">Your Company Name</h3>
                <p class="text-xs text-gray-500">Monastir, Tunisia</p>
                <p class="text-xs text-gray-500">Contact: support@yourcompany.com</p>
            </div>
        </div>

        <!-- Billed To / Details -->
        <div class="grid grid-cols-2 gap-8 my-8">
            <div>
                <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Billed To</h4>
                <p class="text-base font-bold text-gray-800">
                    {{ $sale->customer_name ?? optional($sale->customer)->name ?? 'Walk-in Customer' }}
                </p>
                @if($sale->customer_phone || optional($sale->customer)->phone)
                    <p class="text-sm text-gray-600">{{ $sale->customer_phone ?? optional($sale->customer)->phone }}</p>
                @endif
                @if($sale->customer_address || optional($sale->customer)->address)
                    <p class="text-sm text-gray-600">{{ $sale->customer_address ?? optional($sale->customer)->address }}</p>
                @endif
            </div>
            <div class="text-right">
                <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Invoice Details</h4>
                <p class="text-sm text-gray-600"><span class="font-semibold text-gray-800">Date:</span> {{ $sale->created_at->format('d/m/Y') }}</p>
                <p class="text-sm text-gray-600">
                    <span class="font-semibold text-gray-800">Payment Status:</span> 
                    <span class="uppercase font-bold {{ $sale->payment_status === 'paid' ? 'text-green-600' : 'text-amber-600' }}">
                        {{ $sale->status === 'cancelled' ? 'CANCELLED' : $sale->payment_status }}
                    </span>
                </p>
            </div>
        </div>

        <!-- Line Items Table -->
        <div class="overflow-x-auto my-6">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b-2 border-gray-200 bg-gray-50">
                        <th class="py-3 px-4 text-xs font-bold text-gray-600 uppercase">Item Description</th>
                        <th class="py-3 px-4 text-xs font-bold text-gray-600 uppercase text-center">Qty</th>
                        <th class="py-3 px-4 text-xs font-bold text-gray-600 uppercase text-right">Unit Price</th>
                        <th class="py-3 px-4 text-xs font-bold text-gray-600 uppercase text-right">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($sale->saleItems as $item)
                        <tr>
                            <td class="py-4 px-4 text-sm font-medium text-gray-900">
                                {{ $item->product_name }}
                            </td>
                            <td class="py-4 px-4 text-sm text-gray-700 text-center">{{ $item->quantity }}</td>
                            <td class="py-4 px-4 text-sm text-gray-700 text-right">{{ number_format($item->unit_price, 2) }} TND</td>
                            <td class="py-4 px-4 text-sm font-bold text-gray-900 text-right">{{ number_format($item->subtotal, 2) }} TND</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Ledger & Totals Section -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 border-t pt-6">
            <!-- Left: Payment History Ledger -->
            <div>
                <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">Payment Log</h4>
                @if($sale->payments->isEmpty())
                    <p class="text-xs text-gray-400 italic">No payments recorded for this invoice yet.</p>
                @else
                    <table class="w-full text-xs border border-gray-200 rounded-lg overflow-hidden">
                        <thead class="bg-gray-50 border-b">
                            <tr>
                                <th class="p-2 text-left text-gray-600">Date</th>
                                <th class="p-2 text-left text-gray-600">Method</th>
                                <th class="p-2 text-right text-gray-600">Amount</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($sale->payments as $payment)
                                <tr>
                                    <td class="p-2 text-gray-700">{{ $payment->paid_at ? $payment->paid_at->format('d/m/Y') : $payment->created_at->format('d/m/Y') }}</td>
                                    <td class="p-2 capitalize text-gray-600">{{ str_replace('_', ' ', $payment->payment_method) }}</td>
                                    <td class="p-2 text-right font-bold text-gray-900">{{ number_format($payment->amount, 2) }} TND</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>

            <!-- Right: Calculations Summary -->
            <div class="space-y-3 bg-gray-50 p-5 rounded-xl border border-gray-200">
                <div class="flex justify-between text-sm text-gray-600">
                    <span>Subtotal / Total:</span>
                    <span class="font-bold text-gray-900">{{ number_format($sale->total, 2) }} TND</span>
                </div>
                <div class="flex justify-between text-sm text-gray-600">
                    <span>Total Paid:</span>
                    <span class="font-bold text-green-600">{{ number_format($sale->amount_paid, 2) }} TND</span>
                </div>
                <div class="flex justify-between text-base font-bold text-gray-900 border-t pt-3">
                    <span>Balance Due:</span>
                    <span class="{{ $sale->remaining_amount > 0 ? 'text-red-600' : 'text-gray-900' }}">
                        {{ number_format($sale->remaining_amount, 2) }} TND
                    </span>
                </div>
            </div>
        </div>

        <div class="mt-12 border-t pt-6 text-center text-xs text-gray-400">
            Thank you for your business!
        </div>
    </div>
</x-layout>