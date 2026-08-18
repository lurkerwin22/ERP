<x-layout>
    <div class="mb-6 flex justify-between items-center pb-4 border-b">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Create New Sale</h1>
            <p class="text-sm text-gray-500">Select customer and add products to the order.</p>
        </div>
        <a href="{{ route('ventes.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900">&larr; Back to Sales</a>
    </div>

    @if($errors->has('stock'))
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 font-medium rounded shadow-sm">
            {{ $errors->first('stock') }}
        </div>
    @endif

    <form action="{{ route('ventes.store') }}" method="POST" id="sale-form">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Left Column: Order Items -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">Select Products</h2>

                    <!-- Product Picker Bar -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-6 p-4 bg-gray-50 rounded-lg border border-gray-100">
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Product</label>
                            <select id="product-select" class="w-full text-sm border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">-- Choose a product --</option>
                                @foreach($produits as $produit)
                                    <option value="{{ $produit->id }}" 
                                            data-name="{{ $produit->name ?? $produit->nom }}" 
                                            data-price="{{ $produit->prix }}" 
                                            data-stock="{{ $produit->stock }}">
                                        {{ $produit->name ?? $produit->nom }} ({{ number_format($produit->prix, 2) }} TND | Stock: {{ $produit->stock }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex items-end">
                            <button type="button" id="add-item-btn" class="w-full py-2 px-4 bg-indigo-600 hover:bg-indigo-700 text-white font-medium text-sm rounded-lg transition-colors">
                                + Add Product
                            </button>
                        </div>
                    </div>

                    <!-- Cart Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse" id="cart-table">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-4 py-3 text-xs font-bold text-gray-600 uppercase">Product</th>
                                    <th class="px-4 py-3 text-xs font-bold text-gray-600 uppercase">Price</th>
                                    <th class="px-4 py-3 text-xs font-bold text-gray-600 uppercase w-28">Qty</th>
                                    <th class="px-4 py-3 text-right text-xs font-bold text-gray-600 uppercase">Subtotal</th>
                                    <th class="px-4 py-3 text-center text-xs font-bold text-gray-600 uppercase">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200" id="cart-body">
                                <tr id="empty-row">
                                    <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-400 font-medium">
                                        No products added to order yet.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Right Column: Customer & Details -->
            <div class="space-y-6">
                <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm space-y-4">
                    <h2 class="text-lg font-bold text-gray-900 border-b pb-2">Customer & Checkout</h2>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Customer</label>
                        <select name="client_id" class="w-full text-sm border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Walk-in Customer (General)</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                                    {{ $client->nom }} ({{ $client->telephone }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Notes</label>
                        <textarea name="notes" rows="3" class="w-full text-sm border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" placeholder="Optional notes...">{{ old('notes') }}</textarea>
                    </div>

                    <!-- Total Section -->
                    <div class="pt-4 border-t border-gray-200">
                        <span class="text-xs font-semibold text-gray-500 uppercase">Total Amount</span>
                        <p class="text-3xl font-extrabold text-indigo-600 mt-1"><span id="grand-total">0.00</span> TND</p>
                    </div>

                    <button type="submit" class="w-full py-3 px-4 bg-green-600 hover:bg-green-700 text-white font-bold text-sm rounded-lg shadow transition-colors">
                        Confirm Sale
                    </button>
                </div>
            </div>

        </div>
    </form>

    <!-- Dynamic Cart Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const productSelect = document.getElementById('product-select');
            const addItemBtn = document.getElementById('add-item-btn');
            const cartBody = document.getElementById('cart-body');
            const emptyRow = document.getElementById('empty-row');
            const grandTotalEl = document.getElementById('grand-total');

            let itemIndex = 0;

            addItemBtn.addEventListener('click', function () {
                const selectedOption = productSelect.options[productSelect.selectedIndex];
                const productId = selectedOption.value;

                if (!productId) {
                    alert('Please select a product first.');
                    return;
                }

                const productName = selectedOption.dataset.name;
                const price = parseFloat(selectedOption.dataset.price);
                const stock = parseInt(selectedOption.dataset.stock);

                // Prevent duplicate entries
                if (document.querySelector(`input[value="${productId}"]`)) {
                    alert('Product is already in the list.');
                    return;
                }

                if (emptyRow) {
                    emptyRow.style.display = 'none';
                }

                const row = document.createElement('tr');
                row.className = 'hover:bg-gray-50 transition-colors';
                row.dataset.price = price;
                row.innerHTML = `
                    <td class="px-4 py-3 text-sm font-semibold text-gray-900">
                        ${productName}
                        <input type="hidden" name="items[${itemIndex}][product_id]" value="${productId}">
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-600">${price.toFixed(2)} TND</td>
                    <td class="px-4 py-3 text-sm">
                        <input type="number" name="items[${itemIndex}][quantite]" value="1" min="1" max="${stock}" 
                            class="qty-input w-20 px-2 py-1 border border-gray-300 rounded text-sm text-center focus:ring-indigo-500 focus:border-indigo-500">
                    </td>
                    <td class="px-4 py-3 text-right text-sm font-bold text-gray-900 subtotal-cell">${price.toFixed(2)} TND</td>
                    <td class="px-4 py-3 text-center">
                        <button type="button" class="remove-btn text-red-600 hover:text-red-800 font-bold text-sm">&times;</button>
                    </td>
                `;

                cartBody.appendChild(row);
                itemIndex++;

                // Attach recalculation event listeners
                const qtyInput = row.querySelector('.qty-input');
                qtyInput.addEventListener('input', function () {
                    let qty = parseInt(this.value) || 1;
                    if (qty > stock) {
                        alert(`Maximum available stock is ${stock}`);
                        this.value = stock;
                        qty = stock;
                    }
                    const subtotal = qty * price;
                    row.querySelector('.subtotal-cell').textContent = subtotal.toFixed(2) + ' TND';
                    calculateTotal();
                });

                row.querySelector('.remove-btn').addEventListener('click', function () {
                    row.remove();
                    calculateTotal();
                    if (cartBody.querySelectorAll('tr').length === 1) {
                        emptyRow.style.display = '';
                    }
                });

                calculateTotal();
                productSelect.value = '';
            });

            function calculateTotal() {
                let total = 0;
                document.querySelectorAll('#cart-body tr:not(#empty-row)').forEach(row => {
                    const price = parseFloat(row.dataset.price);
                    const qty = parseInt(row.querySelector('.qty-input').value) || 0;
                    total += price * qty;
                });
                grandTotalEl.textContent = total.toFixed(2);
            }
        });
    </script>
</x-layout>