document.addEventListener('DOMContentLoaded', function () {

    const filterForm = document.getElementById('product-filter-form');

    if (!filterForm) {
        return;
    }

    filterForm.addEventListener('submit', (e) => {

        const minInput = document.getElementById('min_price_input');
        const maxInput = document.getElementById('max_price_input');
        const errorEl = document.getElementById('price_error');

        if (!minInput || !maxInput || !errorEl) {
            return;
        }

        const minVal = parseFloat(minInput.value);
        const maxVal = parseFloat(maxInput.value);

        errorEl.classList.add('hidden');
        errorEl.textContent = '';

        minInput.classList.remove('border-red-500');
        maxInput.classList.remove('border-red-500');

        if (
            !isNaN(minVal) &&
            !isNaN(maxVal) &&
            minVal > maxVal
        ) {
            e.preventDefault();

            errorEl.textContent =
                'Min price cannot be greater than Max price.';

            errorEl.classList.remove('hidden');

            minInput.classList.add('border-red-500');
            maxInput.classList.add('border-red-500');

            return false;
        }

        if (
            (!isNaN(minVal) && minVal < 0) ||
            (!isNaN(maxVal) && maxVal < 0)
        ) {
            e.preventDefault();

            errorEl.textContent =
                'Price cannot be negative.';

            errorEl.classList.remove('hidden');

            return false;
        }

    });

});