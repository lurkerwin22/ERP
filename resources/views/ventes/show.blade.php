<x-layout>
    <div class="mb-6 flex justify-between items-center pb-4 border-b">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Sale #{{ $vente->id }}</h1>
            <p class="text-sm text-gray-500">Date: {{ \Carbon\Carbon::parse($vente->date_vente)->format('F d, Y - H:i') }}</p>
        </div>
        <div class="flex items-center gap-3">
            @if($vente->statut === 'completee')
                <form action="{{ route('ventes.cancel', $vente) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this sale? Stock will be restored.');">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-md shadow-sm transition-colors">
                        Cancel Sale & Restore Stock
                    </button>
                </form>
            @endif
            <a href="{{ route('ventes.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900">&larr; Back to Sales</a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Customer & Sale Summary -->
        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm space-y-4 h-fit">
            <h2 class="text-lg font-bold text-gray-900 border-b pb-2">Summary</h2>
            <div>
                <span class="text-xs font-semibold text-gray-500 uppercase">Customer</span>
                <p class="text-base font-medium text-gray-900 mt-0.5">
                    {{ $vente->client ? $vente->client->nom : 'Walk-in Customer' }}
                </p>
                @if($vente->client)
                    <p class="text-xs text-gray-500">{{ $vente->client->email }} | {{ $vente->client->telephone }}</p>
                @endif
            </div>

            <div>
                <span class="text-xs font-semibold text-gray-500 uppercase">Status</span>
                <div class="mt-1">
                    @if($vente->statut === 'completee')
                        <span class="px-2.5 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">Completed</span>
                    @else
                        <span class="px-2.5 py-1 text-xs font-medium bg-red-100 text-red-800 rounded-full">Cancelled</span>
                    @endif
                </div>
            </div>

            <div class="pt-2 border-t">
                <span class="text-xs font-semibold text-gray-500 uppercase">Total Amount</span>
                <p class="text-2xl font-extrabold text-indigo-600 mt-1">{{ number_format($vente->total, 2) }} TND</p>
            </div>
        </div>

        <!-- Sale Items List -->
        <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-bold text-gray-900">Purchased Items</h2>
            </div>
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-xs font-bold text-gray-600 uppercase">Product</th>
                        <th class="px-6 py-3 text-xs font-bold text-gray-600 uppercase">Unit Price</th>
                        <th class="px-6 py-3 text-xs font-bold text-gray-600 uppercase">Quantity</th>
                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-600 uppercase">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($vente->ligneVentes as $item)
                        <tr>
                            <td class="px-6 py-4 text-sm font-semibold text-gray-900">
                                {{ $item->produit->name }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ number_format($item->prix_unitaire, 2) }} TND
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $item->quantite }}
                            </td>
                            <td class="px-6 py-4 text-right text-sm font-bold text-gray-900">
                                {{ number_format($item->sous_total, 2) }} TND
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-layout>