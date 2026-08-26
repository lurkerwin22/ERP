export function initProductMarginCalculator() {
    // 1. Selector fallback: Try ID first, then name attributes
    const purchaseInput = document.getElementById('purchase_price') || document.querySelector('input[name="purchase_price"]');
    const sellingInput = document.getElementById('price') || document.querySelector('input[name="price"]');
    const marginDisplay = document.getElementById('display_margin');
    const marginRateDisplay = document.getElementById('display_margin_rate');

    // Debugging: Check if elements are found in DOM
    if (!purchaseInput || !sellingInput || !marginDisplay || !marginRateDisplay) {
        console.warn('[Margin Calc] Required DOM elements not found on this page.');
        return;
    }

    console.log('[Margin Calc] Initialized successfully on inputs:', purchaseInput, sellingInput);

    function resetDisplays() {
        marginDisplay.textContent = '—';
        marginDisplay.className = 'text-lg font-semibold text-gray-900';
        marginRateDisplay.textContent = '—';
        marginRateDisplay.className = 'text-lg font-semibold text-gray-900';
    }

    function updateMargin() {
        // Clean values (replace comma with dot if user inputs decimal like '12,50')
        const purchaseVal = purchaseInput.value.replace(',', '.').trim();
        const sellingVal = sellingInput.value.replace(',', '.').trim();

        if (purchaseVal === '' || sellingVal === '') {
            resetDisplays();
            return;
        }

        const purchase = parseFloat(purchaseVal);
        const selling = parseFloat(sellingVal);

        if (isNaN(purchase) || isNaN(selling)) {
            resetDisplays();
            return;
        }

        const margin = selling - purchase;
        marginDisplay.textContent = `${margin.toFixed(2)} DT`;

        // Style margin color
        if (margin < 0) {
            marginDisplay.className = 'text-lg font-semibold text-red-600';
        } else {
            marginDisplay.className = 'text-lg font-semibold text-emerald-600';
        }

        // Calculate Rate
        if (purchase <= 0) {
            marginRateDisplay.textContent = '—';
            marginRateDisplay.className = 'text-lg font-semibold text-gray-900';
        } else {
            const rate = (margin / purchase) * 100;
            marginRateDisplay.textContent = `${rate.toFixed(2)} %`;

            if (margin < 0) {
                marginRateDisplay.className = 'text-lg font-semibold text-red-600';
            } else {
                marginRateDisplay.className = 'text-lg font-semibold text-emerald-600';
            }
        }
    }

    // Attach listeners across all input event types
    ['input', 'change', 'keyup'].forEach(eventType => {
        purchaseInput.addEventListener(eventType, updateMargin);
        sellingInput.addEventListener(eventType, updateMargin);
    });

    // Run initial calculation for edit forms pre-filled with data
    updateMargin();
}