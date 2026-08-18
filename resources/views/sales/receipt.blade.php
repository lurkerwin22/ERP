<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt #REC-{{ str_pad($sale->id, 6, '0', STR_PAD_LEFT) }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background-color: white !important;
                padding: 0 !important;
            }
            .receipt-panel {
                box-shadow: none !important;
                border: none !important;
                max-width: 100% !important;
                padding: 0 !important;
            }
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen py-8 text-gray-800">

    <!-- Action Bar (Hidden when printing) -->
    <div class="max-w-md mx-auto mb-6 flex justify-between items-center no-print px-4">
        <a href="{{ route('sales.show', $sale) }}" 
           class="inline-flex items-center text-sm font-medium text-gray-600 hover:text-gray-900 transition">
            ← Back to Sale
        </a>
        
        <!-- Reusing your custom Form Button component -->
        <x-forms.button type="button" onclick="window.print()">
            🖨️ Print Receipt
        </x-forms.button>
    </div>

    <!-- Printable Container using your custom Panel component -->
    <x-panel class="receipt-panel max-w-md mx-auto bg-white p-6 rounded-lg shadow-md border border-gray-200">
        <!-- Receipt Header -->
        <div class="text-center pb-4 border-b border-dashed border-gray-300">
            <h1 class="text-xl font-bold uppercase tracking-wide text-gray-900">Payment Receipt</h1>
            <p class="text-xs text-gray-500 uppercase mt-1">Your Company Name</p>
            <p class="text-xs text-gray-400">Phone: +216 XX XXX XXX | Email: info@company.com</p>
        </div>

        <!-- Receipt Info -->
        <div class="py-4 text-xs space-y-1.5 border-b border-dashed border-gray-300">
            <div class="flex justify-between">
                <span class="text-gray-500">Receipt No:</span>
                <span class="font-semibold text-gray-900">#REC-{{ str_pad($sale->id, 6, '0', STR_PAD_LEFT) }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Date:</span>
                <span class="font-medium text-gray-800">{{ $sale->created_at ? $sale->created_at->format('d/m/Y H:i') : now()->format('d/m/Y H:i') }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Customer:</span>
                <span class="font-medium text-gray-800">{{ $sale->customer->name ?? 'Guest / General Customer' }}</span>
            </div>
        </div>

        <!-- Purchased Products Table -->
        <div class="py-4 border-b border-dashed border-gray-300">
            <table class="w-full text-xs text-left">
                <thead>
                    <tr class="text-gray-500 uppercase border-b border-gray-200">
                        <th class="pb-2">Product</th>
                        <th class="pb-2 text-center">Qty</th>
                        <th class="pb-2 text-right">Price</th>
                        <th class="pb-2 text-right">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($sale->saleItems as $item)
                        <tr>
                            <td class="py-2 font-medium text-gray-800">{{ $item->product->name ?? 'Product #'.$item->product_id }}</td>
                            <td class="py-2 text-center text-gray-600">{{ $item->quantity }}</td>
                            <td class="py-2 text-right text-gray-600">{{ number_format($item->unit_price, 2) }} TND</td>
                            <td class="py-2 text-right font-medium text-gray-800">{{ number_format($item->subtotal ?? ($item->quantity * $item->unit_price), 2) }} TND</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Payment Breakdown -->
        <div class="py-4 space-y-2 text-xs border-b border-dashed border-gray-300">
            <div class="flex justify-between text-sm font-semibold text-gray-900">
                <span>Total Amount:</span>
                <span>{{ number_format($sale->total, 2) }} TND</span>
            </div>
            <div class="flex justify-between text-gray-600">
                <span>Amount Paid:</span>
                <span class="text-green-600 font-medium">{{ number_format($sale->paid ?? $sale->total, 2) }} TND</span>
            </div>
            <div class="flex justify-between text-gray-600">
                <span>Remaining Balance:</span>
                <span class="{{ ($sale->remaining ?? 0) > 0 ? 'text-red-600 font-medium' : 'text-gray-800' }}">
                    {{ number_format($sale->remaining ?? 0, 2) }} TND
                </span>
            </div>
        </div>

        <!-- Footer -->
        <div class="pt-4 text-center">
            <p class="text-xs font-medium text-gray-600">Thank you for your business!</p>
            <p class="text-[10px] text-gray-400 mt-1">Please keep this receipt for your records.</p>
        </div>
    </x-panel>

</body>
</html>