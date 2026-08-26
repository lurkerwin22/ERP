document.addEventListener('DOMContentLoaded', function () {

    const spinnerSvg =
        '<svg class="inline-block w-4 h-4 animate-spin" viewBox="0 0 24 24" fill="none">' +
        '<circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>' +
        '<path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>' +
        '</svg>';

    document.addEventListener('submit', (e) => {

        const form = e.target;

        if (!(form instanceof HTMLFormElement)) {
            return;
        }

        form.querySelectorAll(
            'button[type="submit"], input[type="submit"]'
        ).forEach((btn) => {

            setTimeout(() => {

                btn.disabled = true;

                btn.classList.add(
                    'opacity-70',
                    'cursor-not-allowed'
                );

                if (btn.tagName === 'BUTTON') {

                    btn.dataset.originalHtml = btn.innerHTML;

                    btn.innerHTML =
                        spinnerSvg +
                        '<span>' +
                        btn.innerHTML +
                        '</span>';
                }

            }, 0);

        });

    });

});