export function initProductMarginCalculator() {
    const purchaseInput = document.getElementById('purchase_price');
    const sellingInput = document.getElementById('price');
    const marginDisplay = document.getElementById('display_margin');
    const marginRateDisplay = document.getElementById('display_margin_rate');

    // Exit early if elements are not on the current page
    if (!purchaseInput || !sellingInput || !marginDisplay || !marginRateDisplay) {
        return;
    }

    function updateMargin() {
        const purchase = parseFloat(purchaseInput.value);
        const selling = parseFloat(sellingInput.value);

        if (isNaN(purchase) || isNaN(selling) || purchaseInput.value.trim() === '') {
            marginDisplay.textContent = '—';
            marginDisplay.className = 'text-lg font-semibold text-gray-900';
            marginRateDisplay.textContent = '—';
            marginRateDisplay.className = 'text-lg font-semibold text-gray-900';
            return;
        }

        const margin = selling - purchase;
        marginDisplay.textContent = `${margin.toFixed(2)} DT`;

        if (purchase <= 0) {
            marginRateDisplay.textContent = '—';
            marginRateDisplay.className = 'text-lg font-semibold text-gray-900';
        } else {
            const rate = (margin / purchase) * 100;
            marginRateDisplay.textContent = `${rate.toFixed(2)} %`;

            if (margin < 0) {
                marginDisplay.className = 'text-lg font-semibold text-red-600';
                marginRateDisplay.className = 'text-lg font-semibold text-red-600';
            } else {
                marginDisplay.className = 'text-lg font-semibold text-emerald-600';
                marginRateDisplay.className = 'text-lg font-semibold text-emerald-600';
            }
        }
    }

    purchaseInput.addEventListener('input', updateMargin);
    sellingInput.addEventListener('input', updateMargin);

    // Initial calculation on page load
    updateMargin();
}