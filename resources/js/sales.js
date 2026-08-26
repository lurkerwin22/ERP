document.addEventListener('DOMContentLoaded', function () {

    const searchInput = document.getElementById(
        'product-search-input'
    );

    if (!searchInput) {
        return;
    }

     // =========================================================================
    // 2. POS "CREATE SALE" — PRODUCT SEARCH + CART
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


})