<x-layout>
    <div class="max-w-4xl mx-auto space-y-6">
        <div class="flex justify-between items-center">
            <div>
                <x-page-heading>Purchase #{{ $purchase->id }}</x-page-heading>
                <p class="text-xs text-slate-500">Date: {{ $purchase->purchase_date->format('d/m/Y') }}</p>
            </div>
            <a href="{{ route('purchases.index') }}" class="text-sm text-slate-500 hover:text-slate-800 transition font-medium">
                ← Back to Purchases
            </a>
        </div>

        <x-panel class="grid grid-cols-2 gap-4 text-xs">
            <div>
                <span class="text-slate-400 block font-semibold">Supplier</span>
                <span class="text-sm font-bold text-slate-800">{{ $purchase->supplier->name }}</span>
                @if($purchase->supplier->phone)
                    <p class="text-slate-500 mt-0.5">{{ $purchase->supplier->phone }}</p>
                @endif
            </div>
            <div>
                <span class="text-slate-400 block font-semibold">Status</span>
                <span class="inline-block mt-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700 uppercase">
                    {{ $purchase->status }}
                </span>
            </div>
        </x-panel>

        <x-panel class="p-0 overflow-hidden">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 font-semibold border-b border-slate-100">
                    <tr>
                        <th class="p-4">Product</th>
                        <th class="p-4 text-center">Qty</th>
                        <th class="p-4 text-right">Unit Price</th>
                        <th class="p-4 text-right">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @foreach($purchase->items as $item)
                        <tr>
                            <td class="p-4 font-semibold">{{ $item->product->name }}</td>
                            <td class="p-4 text-center">{{ $item->quantity }}</td>
                            <td class="p-4 text-right">{{ number_format($item->unit_price, 3) }} DT</td>
                            <td class="p-4 text-right font-bold">{{ number_format($item->total, 3) }} DT</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-slate-50/50 font-bold text-slate-800 border-t border-slate-100">
                    <tr>
                        <td colspan="3" class="p-4 text-right text-xs">Grand Total:</td>
                        <td class="p-4 text-right text-sm text-indigo-600">{{ number_format($purchase->total, 3) }} DT</td>
                    </tr>
                </tfoot>
            </table>
        </x-panel>

        @if($purchase->notes)
            <x-panel class="text-xs">
                <span class="font-bold text-slate-700 block mb-1">Notes:</span>
                <p class="text-slate-600">{{ $purchase->notes }}</p>
            </x-panel>
        @endif
    </div>
</x-layout>