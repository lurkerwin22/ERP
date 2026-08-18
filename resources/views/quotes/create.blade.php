<x-layout>
    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <x-page-heading>Create New Quote</x-page-heading>
                <p class="text-xs text-gray-500 mt-1">Prepare an official quotation for a customer</p>
            </div>
            <a href="{{ route('quotes.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900 transition">
                ← Back to Quotes
            </a>
        </div>

        <!-- Validation Errors -->
        @if ($errors->any())
            <div class="p-4 bg-red-50 border-l-4 border-red-500 text-red-700 text-sm rounded">
                <p class="font-semibold mb-1">Please correct the following errors:</p>
                <ul class="list-disc list-inside space-y-1 text-xs">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('quotes.store') }}" method="POST" id="quote-form">
            @csrf

            <x-panel class="p-6 bg-white border border-gray-200 space-y-6 rounded-xl shadow-sm">
                <!-- Step 1: Customer & Date Selection -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <x-forms.select label="Customer" name="customer_id" id="customer_id" required>
                            @foreach($customers as $customer)
                                @php
                                    $isWalkIn = Str::contains(strtolower($customer->name), ['walk-in', 'guest', 'passing', 'comptoir', 'passager']);
                                    $isSelected = old('customer_id') ? (old('customer_id') == $customer->id) : ($isWalkIn || $loop->first);
                                @endphp
                                <option value="{{ $customer->id }}" {{ $isSelected ? 'selected' : '' }}>
                                    {{ $customer->name }} {{ $customer->phone ? '('.$customer->phone.')' : '' }}
                                </option>
                            @endforeach
                        </x-forms.select>
                    </div>

                    <div>
                        <x-forms.input label="Quote Date" type="date" name="date" id="date" value="{{ old('date', now()->format('Y-m-d')) }}" required />
                    </div>
                </div>

                <x-forms.divider />

                <!-- Step 2: Products & Quantities -->
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider">Quote Items</h3>
                            <p class="text-xs text-gray-500">Select items and quantities. Prices populate automatically.</p>
                        </div>
                        <button type="button" id="add-product-btn" 
                                class="inline-flex items-center px-3 py-1.5 text-xs font-semibold text-indigo-700 bg-indigo-50 border border-indigo-200 rounded-md hover:bg-indigo-100 transition shadow-sm">
                            + Add Product
                        </button>
                    </div>

                    <div class="overflow-x-auto border border-gray-200 rounded-lg">
                        <table class="w-full text-sm text-left">
                            <thead class="text-xs text-gray-600 uppercase bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="py-3 px-4">Product</th>
                                    <th class="py-3 px-3 w-32 text-center">Qty</th>
                                    <th class="py-3 px-3 w-36 text-right">Unit Price (TND)</th>
                                    <th class="py-3 px-3 w-36 text-right">Subtotal (TND)</th>
                                    <th class="py-3 px-2 w-10 text-center"></th>
                                </tr>
                            </thead>
                            <tbody id="items-table-body" class="divide-y divide-gray-100 bg-white">
                                <!-- Dynamic Rows -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <x-forms.divider />

                <!-- Step 3: Total Calculation & Action Buttons -->
                <div class="flex flex-col md:flex-row justify-between items-center pt-2">
                    <div class="text-xs text-gray-500 mb-4 md:mb-0">
                        💡 <span class="font-medium text-gray-700">Note:</span> Generating a quote does <span class="font-semibold text-gray-800">not</span> deduct item stock.
                    </div>

                    <div class="flex items-center space-x-6">
                        <div class="text-right">
                            <span class="text-xs text-gray-500 uppercase block font-medium">Grand Total</span>
                            <span id="grand-total" class="text-2xl font-bold text-indigo-600">0.00 TND</span>
                        </div>

                        <x-forms.button type="submit" id="submit-btn">
                            Save Quote Draft
                        </x-forms.button>
                    </div>
                </div>
            </x-panel>
        </form>
    </div>

    <!-- Cleaned & Optimized JavaScript -->
    <script>
        const productsData = @json($products);
        const tbody = document.getElementById('items-table-body');
        let rowIndex = 0;

        // Render options list once
        const productOptionsHtml = `
            <option value="" data-price="0" data-stock="0">-- Choose Product --</option>
            ${productsData.map(p => `
                <option value="${p.id}" data-price="${p.price}" data-stock="${p.stock}">
                    ${p.name} (In Stock: ${p.stock})
                </option>
            `).join('')}
        `;

        function createRow(selectedProductId = '', qty = 1, price = '') {
            const tr = document.createElement('tr');
            tr.className = 'item-row';
            tr.innerHTML = `
                <td class="py-2.5 px-4">
                    <select name="items[${rowIndex}][product_id]" class="product-select w-full border-gray-300 rounded-md text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        ${productOptionsHtml}
                    </select>
                </td>
                <td class="py-2.5 px-3">
                    <div class="relative">
                        <input type="number" name="items[${rowIndex}][quantity]" value="${qty}" min="1" 
                               class="qty-input w-full text-center border-gray-300 rounded-md text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required />
                        <span class="stock-badge block text-[10px] text-center text-gray-400 mt-0.5">Stock: --</span>
                    </div>
                </td>
                <td class="py-2.5 px-3">
                    <input type="number" step="0.01" name="items[${rowIndex}][unit_price]" value="${price}" min="0" 
                           class="price-input w-full text-right border-gray-300 rounded-md text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required />
                </td>
                <td class="py-2.5 px-3 text-right font-semibold text-gray-800 subtotal-cell">
                    0.00 TND
                </td>
                <td class="py-2.5 px-2 text-center">
                    <button type="button" class="remove-row-btn text-gray-400 hover:text-red-600 font-bold text-lg transition">&times;</button>
                </td>
            `;

            tbody.appendChild(tr);

            if (selectedProductId) {
                const select = tr.querySelector('.product-select');
                select.value = selectedProductId;
            }

            rowIndex++;
            validateRow(tr);
        }

        // Single function to validate, format badge, and update subtotal for a row
        function validateRow(tr) {
            const select = tr.querySelector('.product-select');
            const qtyInput = tr.querySelector('.qty-input');
            const priceInput = tr.querySelector('.price-input');
            const stockBadge = tr.querySelector('.stock-badge');
            const subtotalCell = tr.querySelector('.subtotal-cell');

            const option = select.options[select.selectedIndex];
            const stock = parseInt(option.dataset.stock) || 0;
            const price = parseFloat(option.dataset.price) || 0;
            const currentQty = parseInt(qtyInput.value) || 0;

            // Update badge & input warning states
            if (!select.value) {
                stockBadge.textContent = "Stock: --";
                stockBadge.className = "stock-badge block text-[10px] text-center text-gray-400 mt-0.5";
                qtyInput.classList.remove('border-red-500', 'bg-red-50');
            } else if (stock === 0) {
                stockBadge.textContent = "Out of Stock!";
                stockBadge.className = "stock-badge block text-[10px] text-center text-red-500 font-bold mt-0.5";
                qtyInput.classList.add('border-red-500', 'bg-red-50');
            } else if (currentQty > stock) {
                stockBadge.textContent = `Exceeds Stock (${stock})!`;
                stockBadge.className = "stock-badge block text-[10px] text-center text-red-600 font-bold mt-0.5";
                qtyInput.classList.add('border-red-500', 'bg-red-50');
            } else {
                stockBadge.textContent = `Max: ${stock}`;
                stockBadge.className = "stock-badge block text-[10px] text-center text-gray-500 font-medium mt-0.5";
                qtyInput.classList.remove('border-red-500', 'bg-red-50');
            }

            // Subtotal
            const unitPrice = parseFloat(priceInput.value) || 0;
            const subtotal = currentQty * unitPrice;
            subtotalCell.textContent = subtotal.toFixed(2) + ' TND';

            calculateGrandTotal();
        }

        function calculateGrandTotal() {
            let total = 0;
            tbody.querySelectorAll('.item-row').forEach(tr => {
                const qty = parseFloat(tr.querySelector('.qty-input').value) || 0;
                const price = parseFloat(tr.querySelector('.price-input').value) || 0;
                total += (qty * price);
            });
            document.getElementById('grand-total').textContent = total.toFixed(2) + ' TND';
        }

        // EVENT DELEGATION FOR TABLE INPUTS & CHANGES
        tbody.addEventListener('change', (e) => {
            const tr = e.target.closest('.item-row');
            if (!tr) return;

            // Auto-populate price if product dropdown changed
            if (e.target.classList.contains('product-select')) {
                const option = e.target.options[e.target.selectedIndex];
                const price = parseFloat(option.dataset.price) || 0;
                if (price > 0) {
                    tr.querySelector('.price-input').value = price.toFixed(2);
                }
            }
            validateRow(tr);
        });

        tbody.addEventListener('input', (e) => {
            const tr = e.target.closest('.item-row');
            if (tr) validateRow(tr);
        });

        tbody.addEventListener('click', (e) => {
            if (e.target.classList.contains('remove-row-btn')) {
                if (tbody.querySelectorAll('.item-row').length > 1) {
                    e.target.closest('.item-row').remove();
                    calculateGrandTotal();
                }
            }
        });

        // Form Submission Validation Guard
        document.getElementById('quote-form').addEventListener('submit', (e) => {
            const invalidRow = Array.from(tbody.querySelectorAll('.item-row')).find(tr => {
                const qtyInput = tr.querySelector('.qty-input');
                return qtyInput.classList.contains('border-red-500');
            });

            if (invalidRow) {
                e.preventDefault();
                alert('Please fix item stock quantities before saving the quote.');
            }
        });

        document.getElementById('add-product-btn').addEventListener('click', () => createRow());
        document.addEventListener('DOMContentLoaded', () => createRow());
    </script>
</x-layout>