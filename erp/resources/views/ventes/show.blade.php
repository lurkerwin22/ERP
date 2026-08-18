<x-layout>
    <div class="mb-6 flex justify-between items-center pb-4 border-b">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Sale Details #{{ $vente->id }}</h1>
            <p class="text-sm text-gray-500">Recorded on {{ $vente->created_at->format('d/m/Y H:i') }}</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('ventes.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg shadow-sm hover:bg-gray-50">
                &larr; Back to Sales
            </a>
            <a href="{{ route('ventes.invoice', $vente) }}" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg shadow-sm hover:bg-indigo-700 flex items-center gap-2">
                📄 View / Print Invoice
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Line Items Table -->
        <div class="lg:col-span-2 space-y-6">
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
                            @foreach($vente->ligneVentes as $item)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm font-semibold text-gray-900">
                                        <!-- Snapshot Name -->
                                        {{ $item->nom_produit }}
                                        @if(is_null($item->product_id))
                                            <span class="ml-1 text-xs font-normal text-gray-400">(Archived Product)</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600">
                                        {{ number_format($item->prix_unitaire, 2) }} TND
                                    </td>
                                    <td class="px-4 py-3 text-sm text-center font-medium">
                                        {{ $item->quantite }}
                                    </td>
                                    <td class="px-4 py-3 text-right text-sm font-bold text-gray-900">
                                        {{ number_format($item->sous_total, 2) }} TND
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Order Summary & Customer Sidebar -->
        <div class="space-y-6">
            <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm space-y-4">
            <h2 class="text-lg font-bold text-gray-900 border-b pb-2">Customer & Status</h2>      
        <div>
            <label class="text-xs text-gray-500 uppercase font-semibold">Customer</label>
            <p class="text-sm font-bold text-gray-900 mt-1">
                {{ $vente->client_nom ?? optional($vente->client)->nom ?? 'Walk-in Customer' }}
                @if(is_null($vente->client_id) && $vente->client_nom && $vente->client_nom !== 'Walk-in Customer')
                    <span class="text-xs font-normal text-gray-400 block">(Deleted Customer)</span>
                @endif
            </p>
            @if($vente->client_telephone || optional($vente->client)->telephone)
                <p class="text-xs text-gray-500">{{ $vente->client_telephone ?? optional($vente->client)->telephone }}</p>
            @endif
        </div>

                <div>
                    <label class="text-xs text-gray-500 uppercase font-semibold">Status</label>
                    <div class="mt-1">
                        @if($vente->statut === 'completee')
                            <span class="px-2.5 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full">Completed</span>
                        @else
                            <span class="px-2.5 py-1 text-xs font-semibold text-red-800 bg-red-100 rounded-full">Cancelled</span>
                        @endif
                    </div>
                </div>

                @if($vente->notes)
                    <div>
                        <label class="text-xs text-gray-500 uppercase font-semibold">Notes</label>
                        <p class="text-sm text-gray-600 bg-gray-50 p-3 rounded-lg border text-xs mt-1">{{ $vente->notes }}</p>
                    </div>
                @endif

                <div class="pt-4 border-t border-gray-200">
                    <span class="text-xs font-semibold text-gray-500 uppercase">Total Amount</span>
                    <p class="text-3xl font-extrabold text-indigo-600 mt-1">{{ number_format($vente->total, 2) }} TND</p>
                </div>
            </div>
        </div>
    </div>
</x-layout>