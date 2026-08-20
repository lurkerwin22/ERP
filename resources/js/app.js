// resources/js/app.js
//
// Cleanup notes:
// - This file previously contained THREE overlapping implementations of the
//   AI chat widget (#chat-form / #chat-messages). Two were older/duplicate
//   drafts; one referenced functions that were never defined anywhere in the
//   file (appendUserMessage, showChatSpinner, hideChatSpinner, sendToAiAgent)
//   and would have thrown a ReferenceError the moment a chip button was
//   clicked. Kept the single most complete, working version below.
// - Product filter, POS "Create Sale" cart, and Quote builder sections were
//   each unique and are preserved as-is (just tidied).

document.addEventListener('DOMContentLoaded', function () {
    // =========================================================================
    // 1. AI CHAT WIDGET
    // =========================================================================
    const chatStream = document.getElementById('chat-messages');
    const chatForm = document.getElementById('chat-form');
    const userInput = document.getElementById('user-input');

    if (chatForm && chatStream && userInput) {
        const csrfToken = chatForm.querySelector('input[name="_token"]')?.value
            || document.querySelector('meta[name="csrf-token"]')?.content;

        // Handle quick-reply chip clicks
        document.querySelectorAll('.chip-btn').forEach((button) => {
            button.addEventListener('click', async function () {
                const promptText = this.getAttribute('data-prompt');
                const endpoint = this.getAttribute('data-endpoint');

                appendMessage('user', promptText);

                if (endpoint) {
                    // Direct REST path for canned reports (skips the LLM round-trip)
                    const loaderId = appendLoadingIndicator();
                    try {
                        const response = await fetch(endpoint, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                            },
                        });
                        const reportData = await response.json();
                        removeLoadingIndicator(loaderId);
                        appendMessage('assistant', formatReportHtml(reportData));
                    } catch (err) {
                        removeLoadingIndicator(loaderId);
                        appendMessage('assistant', '⚠️ Erreur lors de la récupération du rapport.');
                    }
                } else {
                    submitChatMessage(promptText);
                }
            });
        });

        // Handle manual form submission
        chatForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const text = userInput.value.trim();
            if (!text) return;

            appendMessage('user', text);
            userInput.value = '';
            submitChatMessage(text);
        });

        async function submitChatMessage(messageText) {
            const endpoint = chatForm.getAttribute('data-endpoint');
            const loaderId = appendLoadingIndicator();

            try {
                const response = await fetch(endpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        messages: [{ role: 'user', content: messageText }],
                    }),
                });

                const data = await response.json();
                removeLoadingIndicator(loaderId);

                const content = data.content || data.message || 'Réponse reçue.';
                appendMessage('assistant', content);
            } catch (err) {
                removeLoadingIndicator(loaderId);
                appendMessage('assistant', "⚠️ Impossible d'interroger le service IA.");
            }
        }

        function appendMessage(role, htmlContent) {
            const wrapper = document.createElement('div');
            wrapper.className = `flex gap-3 text-sm ${role === 'user' ? 'justify-end' : ''}`;

            if (role === 'user') {
                wrapper.innerHTML = `
                    <div class="bg-indigo-600 text-white rounded-2xl rounded-tr-xs px-4 py-3 max-w-xl leading-relaxed shadow-xs">
                        ${escapeHtml(htmlContent)}
                    </div>
                    <div class="w-8 h-8 rounded-lg bg-slate-700 text-slate-300 flex items-center justify-center font-bold text-xs shrink-0 shadow-xs mt-0.5">
                        Vous
                    </div>
                `;
            } else {
                wrapper.innerHTML = `
                    <div class="w-8 h-8 rounded-lg bg-indigo-600 text-white flex items-center justify-center font-bold text-xs shrink-0 shadow-xs mt-0.5">
                        IA
                    </div>
                    <div class="bg-slate-800 text-slate-100 rounded-2xl rounded-tl-xs px-4 py-3 max-w-2xl leading-relaxed border border-slate-700/60 shadow-xs">
                        ${htmlContent}
                    </div>
                `;
            }

            chatStream.appendChild(wrapper);
            chatStream.scrollTop = chatStream.scrollHeight;
        }

        function appendLoadingIndicator() {
            const id = 'loader-' + Date.now();
            const wrapper = document.createElement('div');
            wrapper.id = id;
            wrapper.className = 'flex gap-3 text-sm';
            wrapper.innerHTML = `
                <div class="w-8 h-8 rounded-lg bg-indigo-600 text-white flex items-center justify-center font-bold text-xs shrink-0 shadow-xs mt-0.5">
                    IA
                </div>
                <div class="bg-slate-800 text-slate-300 rounded-2xl rounded-tl-xs px-4 py-3 border border-slate-700/60 shadow-xs flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-indigo-400 animate-bounce"></span>
                    <span class="w-2 h-2 rounded-full bg-indigo-400 animate-bounce [animation-delay:0.2s]"></span>
                    <span class="w-2 h-2 rounded-full bg-indigo-400 animate-bounce [animation-delay:0.4s]"></span>
                </div>
            `;
            chatStream.appendChild(wrapper);
            chatStream.scrollTop = chatStream.scrollHeight;
            return id;
        }

        function removeLoadingIndicator(id) {
            const el = document.getElementById(id);
            if (el) el.remove();
        }

        function formatReportHtml(report) {
            if (report.type === 'stock') {
                const items = report.data.items || report.data;
                if (!Array.isArray(items) || items.length === 0) {
                    return "✅ Aucun produit en rupture ou proche du seuil d'alerte.";
                }
                const list = items.slice(0, 10).map((i) => `
                    <li class="flex justify-between items-center py-1 border-b border-slate-700/50 last:border-0">
                        <span class="font-medium text-slate-200">${escapeHtml(i.name || i.label)}</span>
                        <span class="text-xs px-2 py-0.5 rounded ${i.stock <= 0 ? 'bg-rose-900/60 text-rose-300' : 'bg-amber-900/60 text-amber-300'}">
                            ${i.stock} unités (Seuil: ${i.alert_threshold || '-'})
                        </span>
                    </li>
                `).join('');
                return `<div class="font-semibold text-amber-400 mb-2">${escapeHtml(report.title)}</div><ul class="space-y-1">${list}</ul>`;
            }

            if (report.type === 'sales') {
                const total = Number(report.data.total_sales || report.data.total || 0)
                    .toLocaleString('fr-TN', { minimumFractionDigits: 2 });
                const count = report.data.count || report.data.sales_count || 0;
                return `
                    <div class="font-semibold text-blue-400 mb-2">${escapeHtml(report.title)}</div>
                    <div class="grid grid-cols-2 gap-3 mt-2">
                        <div class="bg-slate-900/60 p-2.5 rounded-lg border border-slate-700/50">
                            <div class="text-xs text-slate-400">Chiffre d'affaires</div>
                            <div class="text-base font-bold text-emerald-400">${total} TND</div>
                        </div>
                        <div class="bg-slate-900/60 p-2.5 rounded-lg border border-slate-700/50">
                            <div class="text-xs text-slate-400">Commandes</div>
                            <div class="text-base font-bold text-slate-200">${count}</div>
                        </div>
                    </div>
                `;
            }

            if (report.type === 'debts') {
                const debts = report.data.debts || report.data;
                if (!Array.isArray(debts) || debts.length === 0) {
                    return '✅ Aucun client débiteur en retard.';
                }
                const list = debts.slice(0, 10).map((d) => `
                    <li class="flex justify-between items-center py-1 border-b border-slate-700/50 last:border-0">
                        <span class="font-medium text-slate-200">${escapeHtml(d.customer_name || d.name)}</span>
                        <span class="font-semibold text-rose-400">${Number(d.balance || d.debt || 0).toFixed(2)} TND</span>
                    </li>
                `).join('');
                return `<div class="font-semibold text-rose-400 mb-2">${escapeHtml(report.title)}</div><ul class="space-y-1">${list}</ul>`;
            }

            return `<pre class="text-xs bg-slate-950 p-2 rounded overflow-x-auto">${escapeHtml(JSON.stringify(report.data, null, 2))}</pre>`;
        }

        function escapeHtml(str) {
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }
    }

    // =========================================================================
    // 2. PRODUCT FILTER FORM VALIDATION
    // =========================================================================
    const filterForm = document.getElementById('product-filter-form');

    if (filterForm) {
        filterForm.addEventListener('submit', (e) => {
            const minInput = document.getElementById('min_price_input');
            const maxInput = document.getElementById('max_price_input');
            const errorEl = document.getElementById('price_error');

            if (!minInput || !maxInput || !errorEl) return;

            const minVal = parseFloat(minInput.value);
            const maxVal = parseFloat(maxInput.value);

            // Reset state
            errorEl.classList.add('hidden');
            errorEl.textContent = '';
            minInput.classList.remove('border-red-500');
            maxInput.classList.remove('border-red-500');

            // Min > Max validation
            if (!isNaN(minVal) && !isNaN(maxVal) && minVal > maxVal) {
                e.preventDefault();
                errorEl.textContent = 'Min price cannot be greater than Max price.';
                errorEl.classList.remove('hidden');
                minInput.classList.add('border-red-500');
                maxInput.classList.add('border-red-500');
                return false;
            }

            // Negative numbers validation
            if ((!isNaN(minVal) && minVal < 0) || (!isNaN(maxVal) && maxVal < 0)) {
                e.preventDefault();
                errorEl.textContent = 'Price cannot be negative.';
                errorEl.classList.remove('hidden');
                return false;
            }
        });
    }

    // =========================================================================
    // 3. POS "CREATE SALE" — PRODUCT SEARCH + CART
    // =========================================================================
    const searchInput = document.getElementById('product-search-input');

    if (searchInput) {
        const searchResults = document.getElementById('search-results-dropdown');
        const cartRows = document.getElementById('cart-rows');
        const emptyCartMsg = document.getElementById('empty-cart-msg');
        const stockWarning = document.getElementById('stock-warning');
        const subtotalEl = document.getElementById('summary-subtotal');
        const remainingEl = document.getElementById('summary-remaining');
        const amountPaidInput = document.getElementById('amount_paid');

        let cart = {};
        let debounceTimer;

        // Live search
        searchInput.addEventListener('input', function () {
            clearTimeout(debounceTimer);
            const query = this.value.trim();

            if (query.length < 1) {
                searchResults.classList.add('hidden');
                return;
            }

            debounceTimer = setTimeout(() => {
                fetch(`/api/products/search?q=${encodeURIComponent(query)}`)
                    .then((res) => res.json())
                    .then((products) => {
                        searchResults.innerHTML = '';

                        if (products.length === 0) {
                            searchResults.innerHTML = `<div class="p-3 text-xs text-gray-500 text-center">No products found</div>`;
                        } else {
                            products.forEach((product) => {
                                const item = document.createElement('div');
                                item.className = 'p-3 hover:bg-indigo-50 cursor-pointer flex justify-between items-center transition';
                                item.innerHTML = `
                                    <div>
                                        <p class="font-bold text-gray-900 text-sm">${product.name}</p>
                                        <p class="text-xs text-gray-500">Stock: ${product.stock}</p>
                                    </div>
                                    <span class="font-extrabold text-indigo-600 text-sm">${parseFloat(product.price).toFixed(2)} TND</span>
                                `;
                                item.addEventListener('click', () => {
                                    addProductToCart(product);
                                    searchResults.classList.add('hidden');
                                    searchInput.value = '';
                                });
                                searchResults.appendChild(item);
                            });
                        }
                        searchResults.classList.remove('hidden');
                    });
            }, 250);
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function (e) {
            if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
                searchResults.classList.add('hidden');
            }
        });

        function addProductToCart(product) {
            stockWarning.classList.add('hidden');

            if (cart[product.id]) {
                if (cart[product.id].quantity + 1 > product.stock) {
                    showWarning(`Insufficient stock. Available: ${product.stock}`);
                    return;
                }
                cart[product.id].quantity++;
            } else {
                if (product.stock < 1) {
                    showWarning('Insufficient stock. Available: 0');
                    return;
                }
                cart[product.id] = {
                    id: product.id,
                    name: product.name,
                    price: parseFloat(product.price),
                    stock: product.stock,
                    quantity: 1,
                };
            }
            renderCart();
        }

        function showWarning(msg) {
            stockWarning.innerText = msg;
            stockWarning.classList.remove('hidden');
        }

        function renderCart() {
            const keys = Object.keys(cart);

            if (keys.length === 0) {
                emptyCartMsg.classList.remove('hidden');
                cartRows.innerHTML = '';
                cartRows.appendChild(emptyCartMsg);
                updateTotals();
                return;
            }

            emptyCartMsg.classList.add('hidden');
            cartRows.innerHTML = '';

            keys.forEach((id) => {
                const item = cart[id];
                const tr = document.createElement('tr');
                tr.className = 'hover:bg-gray-50';
                tr.innerHTML = `
                    <td class="px-4 py-3 font-semibold text-gray-900">
                        ${item.name}
                        <input type="hidden" name="items[${id}][product_id]" value="${item.id}">
                    </td>
                    <td class="px-4 py-3 text-center">
                        <input type="number" min="1" max="${item.stock}" name="items[${id}][quantity]" value="${item.quantity}"
                            class="cart-qty-input w-20 text-center border-gray-300 rounded-md p-1 font-bold text-sm" data-id="${id}">
                    </td>
                    <td class="px-4 py-3 text-right text-gray-600">
                        ${item.price.toFixed(2)}
                        <input type="hidden" name="items[${id}][unit_price]" value="${item.price}">
                    </td>
                    <td class="px-4 py-3 text-right font-bold text-gray-900">
                        ${(item.price * item.quantity).toFixed(2)}
                    </td>
                    <td class="px-4 py-3 text-center">
                        <button type="button" class="remove-item text-red-500 hover:text-red-700 font-bold" data-id="${id}">✕</button>
                    </td>
                `;
                cartRows.appendChild(tr);
            });

            document.querySelectorAll('.cart-qty-input').forEach((input) => {
                input.addEventListener('change', function () {
                    const id = this.dataset.id;
                    let val = parseInt(this.value) || 1;

                    if (val > cart[id].stock) {
                        showWarning(`Insufficient stock. Available: ${cart[id].stock}`);
                        val = cart[id].stock;
                        this.value = val;
                    } else {
                        stockWarning.classList.add('hidden');
                    }

                    cart[id].quantity = val;
                    renderCart();
                });
            });

            document.querySelectorAll('.remove-item').forEach((btn) => {
                btn.addEventListener('click', function () {
                    delete cart[this.dataset.id];
                    renderCart();
                });
            });

            updateTotals();
        }

        function updateTotals() {
            let subtotal = 0;
            Object.values(cart).forEach((item) => {
                subtotal += item.price * item.quantity;
            });

            subtotalEl.innerText = subtotal.toFixed(2);

            const paid = parseFloat(amountPaidInput.value) || 0;
            const remaining = Math.max(0, subtotal - paid);
            remainingEl.innerText = remaining.toFixed(2);
        }

        if (amountPaidInput) {
            amountPaidInput.addEventListener('input', updateTotals);
        }
    }

    // =========================================================================
    // 4. QUOTE BUILDER
    // =========================================================================
    const quoteForm = document.getElementById('quote-form');

    if (quoteForm) {
        const tbody = document.getElementById('items-table-body');
        const addProductBtn = document.getElementById('add-product-btn');
        const grandTotalEl = document.getElementById('grand-total');
        let rowIndex = 0;

        function createRow(product = null) {
            const tr = document.createElement('tr');
            tr.className = 'item-row hover:bg-gray-50/60 transition';

            const productId = product ? product.id : '';
            const productName = product ? product.name : '';
            const price = product ? parseFloat(product.price).toFixed(2) : '0.00';
            const stock = product ? product.stock : 0;

            tr.innerHTML = `
                <td class="py-3 px-4 align-middle relative">
                    <input type="text" class="product-search-input w-full text-xs border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 placeholder-gray-400 py-2 px-3"
                        placeholder="Type product name..." value="${productName}" autocomplete="off" required />
                    <input type="hidden" name="items[${rowIndex}][product_id]" class="product-id-input" value="${productId}" required />

                    <div class="search-results hidden absolute z-50 left-0 right-0 mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-52 overflow-y-auto text-xs divide-y divide-gray-100"></div>
                </td>
                <td class="py-3 px-3 align-middle">
                    <div class="flex flex-col items-center">
                        <input type="number" name="items[${rowIndex}][quantity]" value="1" min="1"
                            class="qty-input w-full text-center text-xs font-medium border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-1.5 px-2" required />
                        <span class="stock-badge block text-[10px] ${stock === 0 ? 'text-red-500 font-bold' : 'text-gray-400 font-normal'} mt-1" data-stock="${stock}">
                            ${productId ? (stock === 0 ? 'Out of Stock' : `Max: ${stock}`) : 'Stock: --'}
                        </span>
                    </div>
                </td>
                <td class="py-3 px-3 align-middle">
                    <input type="number" step="0.01" name="items[${rowIndex}][unit_price]" value="${price}" min="0"
                        class="price-input w-full text-right text-xs border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-1.5 px-2 font-medium" required />
                </td>
                <td class="py-3 px-4 align-middle text-right font-bold text-gray-800 text-xs subtotal-cell">
                    ${price} TND
                </td>
                <td class="py-3 px-2 align-middle text-center">
                    <button type="button" class="remove-row-btn text-gray-400 hover:text-red-600 font-bold text-base transition">&times;</button>
                </td>
            `;

            tbody.appendChild(tr);
            setupRowSearch(tr);
            rowIndex++;
            calculateGrandTotal();
        }

        function setupRowSearch(tr) {
            const rowSearchInput = tr.querySelector('.product-search-input');
            const hiddenIdInput = tr.querySelector('.product-id-input');
            const resultsBox = tr.querySelector('.search-results');
            const priceInput = tr.querySelector('.price-input');
            const stockBadge = tr.querySelector('.stock-badge');
            let debounceTimer;

            rowSearchInput.addEventListener('input', function () {
                clearTimeout(debounceTimer);
                hiddenIdInput.value = '';
                const query = this.value.trim();

                if (query.length < 1) {
                    resultsBox.classList.add('hidden');
                    return;
                }

                debounceTimer = setTimeout(() => {
                    fetch(`/api/products/search?q=${encodeURIComponent(query)}`)
                        .then((res) => res.json())
                        .then((products) => {
                            resultsBox.innerHTML = '';
                            if (products.length === 0) {
                                resultsBox.innerHTML = `<div class="p-3 text-gray-400 text-center italic">No products found</div>`;
                            } else {
                                products.forEach((product) => {
                                    const item = document.createElement('div');
                                    item.className = 'p-3 hover:bg-indigo-50/60 cursor-pointer flex justify-between items-center transition';
                                    item.innerHTML = `
                                        <div>
                                            <p class="font-bold text-gray-800 text-xs">${product.name}</p>
                                            <p class="text-[10px] text-gray-400">Stock: ${product.stock}</p>
                                        </div>
                                        <span class="font-extrabold text-indigo-600 text-xs">${parseFloat(product.price).toFixed(2)} TND</span>
                                    `;
                                    item.addEventListener('click', () => {
                                        rowSearchInput.value = product.name;
                                        hiddenIdInput.value = product.id;
                                        priceInput.value = parseFloat(product.price).toFixed(2);

                                        stockBadge.dataset.stock = product.stock;
                                        stockBadge.textContent = product.stock === 0 ? 'Out of Stock!' : `Max: ${product.stock}`;
                                        stockBadge.className = `stock-badge block text-[10px] text-center ${product.stock === 0 ? 'text-red-500 font-bold' : 'text-gray-400 font-medium'} mt-1`;

                                        resultsBox.classList.add('hidden');
                                        validateRow(tr);
                                    });
                                    resultsBox.appendChild(item);
                                });
                            }
                            resultsBox.classList.remove('hidden');
                        });
                }, 200);
            });

            document.addEventListener('click', (e) => {
                if (!rowSearchInput.contains(e.target) && !resultsBox.contains(e.target)) {
                    resultsBox.classList.add('hidden');
                }
            });
        }

        function validateRow(tr) {
            const qtyInput = tr.querySelector('.qty-input');
            const priceInput = tr.querySelector('.price-input');
            const stockBadge = tr.querySelector('.stock-badge');
            const subtotalCell = tr.querySelector('.subtotal-cell');

            const stock = parseInt(stockBadge.dataset.stock) || 0;
            const currentQty = parseInt(qtyInput.value) || 0;

            if (currentQty > stock && stock > 0) {
                stockBadge.textContent = `Exceeds Stock (${stock})!`;
                stockBadge.className = 'stock-badge block text-[10px] text-center text-red-600 font-bold mt-1';
                qtyInput.classList.add('border-red-400', 'bg-red-50');
            } else if (stock > 0) {
                stockBadge.textContent = `Max: ${stock}`;
                stockBadge.className = 'stock-badge block text-[10px] text-center text-gray-400 font-medium mt-1';
                qtyInput.classList.remove('border-red-400', 'bg-red-50');
            }

            const unitPrice = parseFloat(priceInput.value) || 0;
            const subtotal = currentQty * unitPrice;
            subtotalCell.textContent = subtotal.toFixed(2) + ' TND';

            calculateGrandTotal();
        }

        function calculateGrandTotal() {
            let total = 0;
            tbody.querySelectorAll('.item-row').forEach((tr) => {
                const qty = parseFloat(tr.querySelector('.qty-input').value) || 0;
                const price = parseFloat(tr.querySelector('.price-input').value) || 0;
                total += qty * price;
            });
            grandTotalEl.textContent = total.toFixed(2) + ' TND';
        }

        tbody.addEventListener('input', (e) => {
            const tr = e.target.closest('.item-row');
            if (tr && (e.target.classList.contains('qty-input') || e.target.classList.contains('price-input'))) {
                validateRow(tr);
            }
        });

        tbody.addEventListener('click', (e) => {
            if (e.target.classList.contains('remove-row-btn')) {
                if (tbody.querySelectorAll('.item-row').length > 1) {
                    e.target.closest('.item-row').remove();
                    calculateGrandTotal();
                }
            }
        });

        if (addProductBtn) {
            addProductBtn.addEventListener('click', () => createRow());
        }

        // Initialize the table with one empty item row on load
        createRow();
    }
});