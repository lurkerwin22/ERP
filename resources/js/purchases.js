document.addEventListener('DOMContentLoaded', () => {
    // --- State Management ---
    let purchaseItems = [];

    // --- DOM Elements ---
    const supplierSelect = document.getElementById('supplier_id');
    const productSelect = document.getElementById('product_select');
    const itemsTableBody = document.getElementById('purchase_items_body');
    const purchaseForm = document.getElementById('purchase_form');
    const submitBtn = document.getElementById('submit_btn');

    // Summary elements
    const subtotalEl = document.getElementById('summary_subtotal');
    const taxEl = document.getElementById('summary_tax');
    const grandTotalEl = document.getElementById('summary_total');

    // --- Core Calculation Helpers ---
    function calculateLine(quantity, price, taxRate) {
        const qty = parseFloat(quantity) || 0;
        const unitPrice = parseFloat(price) || 0;
        const tax = parseFloat(taxRate) || 0;

        const subtotal = qty * unitPrice;
        const taxAmount = subtotal * (tax / 100);
        const total = subtotal + taxAmount;

        return { subtotal, taxAmount, total };
    }

    function calculateTotals() {
        let grandSubtotal = 0;
        let grandTax = 0;

        purchaseItems.forEach(item => {
            const line = calculateLine(item.quantity, item.unit_price, item.tax_rate);
            grandSubtotal += line.subtotal;
            grandTax += line.taxAmount;
        });

        const grandTotal = grandSubtotal + grandTax;

        if (subtotalEl) subtotalEl.textContent = grandSubtotal.toFixed(2);
        if (taxEl) taxEl.textContent = grandTax.toFixed(2);
        if (grandTotalEl) grandTotalEl.textContent = grandTotal.toFixed(2);
    }

    // --- Render Logic ---
    function renderTable() {
        itemsTableBody.innerHTML = '';

        if (purchaseItems.length === 0) {
            itemsTableBody.innerHTML = `
                <tr id="empty_row">
                    <td colspan="6" class="text-center py-4 text-muted">
                        No products added to purchase order yet.
                    </td>
                </tr>`;
            calculateTotals();
            return;
        }

        purchaseItems.forEach((item, index) => {
            const line = calculateLine(item.quantity, item.unit_price, item.tax_rate);
            const tr = document.createElement('tr');

            tr.innerHTML = `
                <td>
                    ${item.name}
                    <input type="hidden" name="items[${index}][product_id]" value="${item.id}">
                </td>
                <td>
                    <input type="number" 
                           class="form-control form-control-sm item-qty" 
                           data-index="${index}" 
                           value="${item.quantity}" 
                           min="1" 
                           step="1" required>
                </td>
                <td>
                    <input type="number" 
                           class="form-control form-control-sm item-price" 
                           data-index="${index}" 
                           value="${item.unit_price}" 
                           min="0" 
                           step="0.01" required>
                </td>
                <td>
                    <input type="number" 
                           class="form-control form-control-sm item-tax" 
                           data-index="${index}" 
                           value="${item.tax_rate}" 
                           min="0" 
                           step="0.1">
                </td>
                <td class="text-end fw-bold align-middle">
                    $${line.total.toFixed(2)}
                </td>
                <td class="text-center align-middle">
                    <button type="button" 
                            class="btn btn-sm btn-outline-danger remove-item" 
                            data-index="${index}">
                        &times;
                    </button>
                </td>
            `;
            itemsTableBody.appendChild(tr);
        });

        calculateTotals();
    }

    // --- Event Listeners & Handlers ---

    // 1. Add Product
    if (productSelect) {
        productSelect.addEventListener('change', (e) => {
            const selectedOption = e.target.options[e.target.selectedIndex];
            if (!selectedOption.value) return;

            const productId = selectedOption.value;
            const productName = selectedOption.dataset.name || selectedOption.text;
            const defaultPrice = selectedOption.dataset.price || 0;
            const defaultTax = selectedOption.dataset.tax || 0;

            // Prevent duplicate selection
            const exists = purchaseItems.some(item => item.id === productId);
            if (exists) {
                alert('Product is already added to the list.');
                productSelect.value = '';
                return;
            }

            purchaseItems.push({
                id: productId,
                name: productName,
                quantity: 1,
                unit_price: parseFloat(defaultPrice),
                tax_rate: parseFloat(defaultTax)
            });

            renderTable();
            productSelect.value = ''; // Reset dropdown
        });
    }

    // 2. Line Item Updates & Removal (Delegated)
    itemsTableBody.addEventListener('input', (e) => {
        const index = e.target.dataset.index;
        if (index === undefined) return;

        if (e.target.classList.contains('item-qty')) {
            purchaseItems[index].quantity = Math.max(1, parseFloat(e.target.value) || 0);
        } else if (e.target.classList.contains('item-price')) {
            purchaseItems[index].unit_price = Math.max(0, parseFloat(e.target.value) || 0);
        } else if (e.target.classList.contains('item-tax')) {
            purchaseItems[index].tax_rate = Math.max(0, parseFloat(e.target.value) || 0);
        }

        // Re-calculate line total display without re-rendering entire table to preserve focus
        const line = calculateLine(
            purchaseItems[index].quantity, 
            purchaseItems[index].unit_price, 
            purchaseItems[index].tax_rate
        );
        
        const row = e.target.closest('tr');
        row.querySelector('td:nth-child(5)').textContent = `$${line.total.toFixed(2)}`;

        calculateTotals();
    });

    itemsTableBody.addEventListener('click', (e) => {
        if (e.target.classList.contains('remove-item')) {
            const index = e.target.dataset.index;
            purchaseItems.splice(index, 1);
            renderTable();
        }
    });

    // 3. Validation & Double Submit Guard
    if (purchaseForm) {
        purchaseForm.addEventListener('submit', (e) => {
            // Check supplier
            if (supplierSelect && !supplierSelect.value) {
                e.preventDefault();
                alert('Please select a supplier.');
                supplierSelect.focus();
                return;
            }

            // Check line items count
            if (purchaseItems.length === 0) {
                e.preventDefault();
                alert('At least one product must be added to the purchase order.');
                return;
            }

            // Prevent double submission
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = `
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    Processing...
                `;
            }
        });
    }
});