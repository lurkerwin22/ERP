<x-layout>
    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <div>
                <x-page-heading>Purchases</x-page-heading>
                <p class="text-xs text-slate-500 mt-0.5">Manage stock orders & supplier receipts</p>
            </div>
            <a href="{{ route('purchases.create') }}" class="px-4 py-2.5 bg-indigo-600 text-white font-semibold text-xs rounded-xl shadow-lg shadow-indigo-600/30 hover:bg-indigo-700 transition">
                + New Purchase
            </a>
        </div>

        <!-- Filters -->
        <x-panel>
            <form method="GET" action="{{ route('purchases.index') }}" class="flex flex-wrap gap-3 items-end">
                <div>
                    {{-- Pass the label attribute directly to x-forms.select --}}
                    <x-forms.select name="supplier_id" label="Supplier">
                        <option value="">All Suppliers</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                {{ $supplier->name }}
                            </option>
                        @endforeach
                    </x-forms.select>
                </div>

                <div>
                    <x-forms.label name="from_date" label="From Date" />
                    <x-forms.input type="date" name="from_date" value="{{ request('from_date') }}" />
                </div>

                <div>
                    <x-forms.label name="to_date" label="To Date" />
                    <x-forms.input type="date" name="to_date" value="{{ request('to_date') }}" />
                </div>

                <div class="flex gap-2">
                    <x-forms.button>Filter</x-forms.button>
                    <a href="{{ route('purchases.index') }}" class="px-3 py-2 text-xs text-slate-500 hover:text-slate-800 transition">Reset</a>
                </div>
            </form>
        </x-panel>

        <!-- Purchases List -->
        <x-panel class="p-0 overflow-hidden">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 font-semibold border-b border-slate-100">
                    <tr>
                        <th class="p-4">ID</th>
                        <th class="p-4">Date</th>
                        <th class="p-4">Supplier</th>
                        <th class="p-4">Total</th>
                        <th class="p-4">Status</th>
                        <th class="p-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($purchases as $purchase)
                        <tr class="hover:bg-slate-50/50">
                            <td class="p-4 font-bold">#{{ $purchase->id }}</td>
                            <td class="p-4">{{ $purchase->purchase_date->format('d/m/Y') }}</td>
                            <td class="p-4 font-medium">{{ $purchase->supplier->name ?? 'N/A' }}</td>
                            <td class="p-4 font-bold text-indigo-600">{{ number_format($purchase->total, 3) }} DT</td>
                            <td class="p-4">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700 uppercase">
                                    {{ $purchase->status }}
                                </span>
                            </td>
                            <td class="p-4 text-right">
                                <a href="{{ route('purchases.show', $purchase) }}" class="px-3 py-1.5 bg-slate-100 text-slate-600 rounded-lg font-semibold hover:bg-slate-200 transition">
                                    View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-6 text-center text-slate-400">No purchases found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="p-4 border-t border-slate-100">
                {{ $purchases->links() }}
            </div>
        </x-panel>
    </div>
</x-layout>