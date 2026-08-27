<x-layout>
    <div class="max-w-5xl mx-auto space-y-6">
        <div class="flex justify-between items-center">
            <x-page-heading>New Purchase</x-page-heading>
            <a href="{{ route('purchases.index') }}" class="text-sm text-slate-500 hover:text-slate-800 transition font-medium">
                ← Back to Purchases
            </a>
        </div>

        <x-forms.form action="{{ route('purchases.store') }}" method="POST" id="purchase-form" class="space-y-6" novalidate>
            <x-panel class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <x-forms.select name="supplier_id" label="Supplier *" id="supplier_id" required>
                        <option value="">Select Supplier</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                {{ $supplier->name }}
                            </option>
                        @endforeach
                    </x-forms.select>
                    <p data-validation-error="supplier_id" class="text-rose-500 text-xs mt-1 hidden"></p>
                </div>

                <div>
                    <x-forms.input type="date" name="purchase_date" label="Purchase Date *" id="purchase_date" value="{{ old('purchase_date', date('Y-m-d')) }}" required />
                    <p data-validation-error="purchase_date" class="text-rose-500 text-xs mt-1 hidden"></p>
                </div>
            </x-panel>

            <!-- Dynamic Product Selector -->
            <x-panel>
                <h2 class="text-sm font-bold text-slate-800 mb-4">Products</h2>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs" id="items-table">
                        <thead>
                            <tr class="border-b border-slate-200 text-slate-500 font-semibold">
                                <th class="pb-3 w-5/12">Product</th>
                                <th class="pb-3 w-2/12">Qty</th>
                                <th class="pb-3 w-2/12">Unit Cost (DT)</th>
                                <th class="pb-3 w-2/12">Total</th>
                                <th class="pb-3 w-1/12 text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody id="items-container" class="divide-y divide-slate-100">
                            <!-- Rows inserted dynamically -->
                        </tbody>
                    </table>
                </div>

                <x-forms.divider />

                <div class="flex justify-between items-center pt-2">
                    <button type="button" id="add-item-btn" class="px-4 py-2 bg-indigo-50 text-indigo-600 rounded-xl font-semibold text-xs hover:bg-indigo-100 transition">
                        + Add Product
                    </button>
                    <div class="text-right">
                        <span class="text-xs text-slate-500 font-medium">Grand Total: </span>
                        <span id="grand-total" class="text-lg font-bold text-indigo-600">0.000 DT</span>
                    </div>
                </div>
            </x-panel>

            <x-panel>
                <x-forms.field label="Notes / Remarks" name="notes">
                    <textarea name="notes" id="notes" rows="2" class="w-full rounded-xl border-slate-200 text-sm focus:ring-indigo-500" placeholder="Optional purchase notes...">{{ old('notes') }}</textarea>
                </x-forms.field>
                <p data-validation-error="notes" class="text-rose-500 text-xs mt-1 hidden"></p>
            </x-panel>

            <div class="flex justify-end gap-3">
                <a href="{{ route('purchases.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 font-semibold text-xs hover:bg-slate-50">
                    Cancel
                </a>
                <x-forms.button>Confirm Purchase</x-forms.button>
            </div>
        </x-forms.form>
    </div>

    <script>
        const products = @json($products);
        let itemIndex = 0;

        function createRow() {
            const container = document.getElementById('items-container');
            const tr = document.createElement('tr');
            tr.className = 'item-row';
            tr.dataset.index = itemIndex;

            let productOptions = `<option value="">Select Product</option>`;
            products.forEach(p => {
                productOptions += `<option value="${p.id}" data-price="${p.purchase_price}">${p.name} (Stock: ${p.stock})</option>`;
            });

            tr.innerHTML = `
                <td class="py-3 pr-2 align-top">
                    <select name="items[${itemIndex}][product_id]" required class="product-select w-full rounded-xl border-slate-200 text-xs py-2 focus:ring-indigo-500">
                        ${productOptions}
                    </select>
                    <p data-validation-error="items[${itemIndex}][product_id]" class="text-rose-500 text-xs mt-1 hidden"></p>
                </td>
                <td class="py-3 pr-2 align-top">
                    <input type="number" name="items[${itemIndex}][quantity]" value="1" min="1" step="1" required class="qty-input w-full rounded-xl border-slate-200 text-xs py-2 focus:ring-indigo-500">
                    <p data-validation-error="items[${itemIndex}][quantity]" class="text-rose-500 text-xs mt-1 hidden"></p>
                </td>
                <td class="py-3 pr-2 align-top">
                    <input type="number" step="0.001" name="items[${itemIndex}][unit_price]" value="0.000" min="0" required class="price-input w-full rounded-xl border-slate-200 text-xs py-2 focus:ring-indigo-500">
                    <p data-validation-error="items[${itemIndex}][unit_price]" class="text-rose-500 text-xs mt-1 hidden"></p>
                </td>
                <td class="py-3 pr-2 align-top pt-5 font-semibold text-slate-700 line-total">
                    0.000 DT
                </td>
                <td class="py-3 text-center align-top pt-4">
                    <button type="button" class="remove-btn text-rose-500 hover:text-rose-700 font-bold text-base">&times;</button>
                </td>
            `;

            container.appendChild(tr);

            const select = tr.querySelector('.product-select');
            const qty = tr.querySelector('.qty-input');
            const price = tr.querySelector('.price-input');
            const removeBtn = tr.querySelector('.remove-btn');

            select.addEventListener('change', (e) => {
                const selected = e.target.options[e.target.selectedIndex];
                const defaultPrice = selected.dataset.price || 0;
                price.value = parseFloat(defaultPrice).toFixed(3);
                recalculate();
                
                if (typeof validateField === 'function') {
                    validateField(select);
                    validateField(price);
                }
            });

            qty.addEventListener('input', () => {
                recalculate();
                if (typeof validateField === 'function') validateField(qty);
            });

            price.addEventListener('input', () => {
                recalculate();
                if (typeof validateField === 'function') validateField(price);
            });

            removeBtn.addEventListener('click', () => {
                tr.remove();
                recalculate();
            });

            itemIndex++;
        }

        function recalculate() {
            let grandTotal = 0;
            document.querySelectorAll('.item-row').forEach(row => {
                const q = parseFloat(row.querySelector('.qty-input').value) || 0;
                const p = parseFloat(row.querySelector('.price-input').value) || 0;
                const total = q * p;
                row.querySelector('.line-total').textContent = total.toFixed(3) + ' DT';
                grandTotal += total;
            });
            document.getElementById('grand-total').textContent = grandTotal.toFixed(3) + ' DT';
        }

        document.getElementById('add-item-btn').addEventListener('click', () => createRow());
        createRow();
    </script>
</x-layout>