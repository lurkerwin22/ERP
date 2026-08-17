<x-layout>
    <!-- Embedded Print Styles (Hides layout headers, sidebars, and buttons when printing) -->
    <style>
        @media print {
            /* Hide application navigation bars, headers, and action buttons */
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

    <!-- Top Action Toolbar -->
    <div class="mb-6 flex justify-between items-center pb-4 border-b no-print">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Invoice Details</h1>
            <p class="text-sm text-gray-500">Invoice reference: #{{ $vente->id }}</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('ventes.show', $vente) }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg shadow-sm hover:bg-gray-50 transition">
                &larr; Back to Sale Details
            </a>
            <button onclick="window.print()" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-lg shadow transition flex items-center gap-2">
                🖨️ Print Invoice
            </button>
        </div>
    </div>

    <!-- Printable Invoice Document Box -->
    <div class="invoice-container max-w-4xl mx-auto bg-white p-8 md:p-12 rounded-xl shadow-sm border border-gray-200">
        
        <!-- Document Header -->
        <div class="flex justify-between items-start border-b pb-8">
            <div>
                <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight">INVOICE</h2>
                <p class="text-sm font-semibold text-indigo-600 mt-1">#INV-{{ str_pad($vente->id, 6, '0', STR_PAD_LEFT) }}</p>
            </div>
            <div class="text-right">
                <h3 class="text-lg font-bold text-gray-800">Your Company Name</h3>
                <p class="text-xs text-gray-500">Monastir, Tunisia</p>
                <p class="text-xs text-gray-500">Contact: support@yourcompany.com</p>
            </div>
        </div>

        <!-- Meta Information -->
        <div class="grid grid-cols-2 gap-8 my-8">
            <div>
                <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Billed To</h4>
                <p class="text-base font-bold text-gray-800">{{ $vente->client->nom ?? 'Walk-in Customer' }}</p>
                @if($vente->client)
                    <p class="text-sm text-gray-600">{{ $vente->client->telephone }}</p>
                    <p class="text-sm text-gray-600">{{ $vente->client->adresse ?? '' }}</p>
                @endif
            </div>
            <div class="text-right">
                <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Invoice Details</h4>
                <p class="text-sm text-gray-600"><span class="font-semibold text-gray-800">Date:</span> {{ $vente->created_at->format('d/m/Y') }}</p>
                <p class="text-sm text-gray-600"><span class="font-semibold text-gray-800">Status:</span> {{ ucfirst($vente->statut) }}</p>
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
                    @foreach($vente->ligneVentes as $item)
                        <tr>
                            <td class="py-4 px-4 text-sm font-medium text-gray-900">
                                {{ $item->nom_produit }}
                            </td>
                            <td class="py-4 px-4 text-sm text-gray-700 text-center">{{ $item->quantite }}</td>
                            <td class="py-4 px-4 text-sm text-gray-700 text-right">{{ number_format($item->prix_unitaire, 2) }} TND</td>
                            <td class="py-4 px-4 text-sm font-bold text-gray-900 text-right">{{ number_format($item->sous_total, 2) }} TND</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Totals Section -->
        <div class="flex justify-end border-t pt-6">
            <div class="w-full md:w-1/2 space-y-2">
                <div class="flex justify-between text-sm text-gray-600">
                    <span>Subtotal:</span>
                    <span class="font-semibold">{{ number_format($vente->total, 2) }} TND</span>
                </div>
                <div class="flex justify-between text-lg font-bold text-gray-900 border-t pt-2">
                    <span>Total Due:</span>
                    <span class="text-indigo-600">{{ number_format($vente->total, 2) }} TND</span>
                </div>
            </div>
        </div>

        <!-- Document Footer -->
        <div class="mt-12 border-t pt-6 text-center text-xs text-gray-400">
            Thank you for your business!
        </div>
    </div>
</x-layout>