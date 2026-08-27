/*
|--------------------------------------------------------------------------
| Global Live Input Validation
|--------------------------------------------------------------------------
*/

/**
 * Get the error message from the browser's native validation API.
 */
function getValidationError(input) {

    if (input.validity.valueMissing) {
        return 'This field is required.';
    }

    if (input.validity.typeMismatch) {

        if (input.type === 'email') {
            return 'Please enter a valid email address.';
        }

        return 'Please enter a valid value.';
    }

    if (input.validity.tooShort) {
        return `Minimum ${input.minLength} characters required.`;
    }

    if (input.validity.tooLong) {
        return `Maximum ${input.maxLength} characters allowed.`;
    }

    if (input.validity.rangeUnderflow) {
        return `Value must be at least ${input.min}.`;
    }

    if (input.validity.rangeOverflow) {
        return `Value must be at most ${input.max}.`;
    }

    if (input.validity.stepMismatch) {
        return 'Please enter a valid value.';
    }

    if (input.validity.badInput) {
        return 'Please enter a valid value.';
    }

    if (input.validity.patternMismatch) {
        return 'Please enter a valid format.';
    }

    return '';
}


/**
 * Find the error element belonging to an input.
 */
function getErrorElement(input) {

    if (!input.name) {
        return null;
    }

    return document.querySelector(
        `[data-validation-error="${CSS.escape(input.name)}"]`
    );
}


/**
 * Show a frontend validation error.
 */
function showError(input, message) {

    const errorElement = getErrorElement(input);

    input.classList.remove('border-green-500');

    input.classList.add(
        'border-red-500',
        'focus:border-red-500',
        'focus:ring-red-500/10'
    );

    if (errorElement) {
        errorElement.textContent = message;
        errorElement.classList.remove('hidden');
    }
}


/**
 * Clear a frontend validation error.
 */
function clearError(input) {

    const errorElement = getErrorElement(input);

    input.classList.remove(
        'border-red-500',
        'focus:border-red-500',
        'focus:ring-red-500/10'
    );

    /*
     * Only show green when the user actually entered something.
     */
    if (input.value.trim() !== '') {
        input.classList.add('border-green-500');
    }

    if (errorElement) {
        errorElement.textContent = '';
        errorElement.classList.add('hidden');
    }
}


/**
 * Validate one field.
 */
function validateField(input) {

    /*
     * Ignore non-input controls.
     */
    if (
        input.type === 'submit' ||
        input.type === 'button' ||
        input.type === 'reset' ||
        input.type === 'hidden'
    ) {
        return true;
    }

    /*
     * Browser HTML validation.
     */
    if (input.checkValidity()) {
        clearError(input);
        return true;
    }

    showError(
        input,
        getValidationError(input)
    );

    return false;
}


/*
|--------------------------------------------------------------------------
| Password Confirmation
|--------------------------------------------------------------------------
*/

function validatePasswordConfirmation(input) {

    if (input.name !== 'password_confirmation') {
        return validateField(input);
    }

    const password = document.querySelector(
        '[name="password"]'
    );

    if (!password) {
        return validateField(input);
    }

    /*
     * Don't show "passwords don't match"
     * when the confirmation field is empty.
     *
     * The required rule will handle that.
     */
    if (
        input.value === '' ||
        password.value === ''
    ) {
        return validateField(input);
    }

    if (input.value !== password.value) {

        showError(
            input,
            'Passwords do not match.'
        );

        return false;
    }

    return validateField(input);
}


/*
|--------------------------------------------------------------------------
| Live Validation
|--------------------------------------------------------------------------
*/

document.addEventListener('DOMContentLoaded', () => {

    /*
    |--------------------------------------------------------------------------
    | Validate while typing
    |--------------------------------------------------------------------------
    */

    document.addEventListener('input', (event) => {

        const input = event.target;

        if (
            !input.matches(
                'input:not([type="submit"]):not([type="button"]):not([type="hidden"]), textarea'
            )
        ) {
            return;
        }

        if (input.name === 'password_confirmation') {
            validatePasswordConfirmation(input);
        } else {
            validateField(input);
        }

    });


    /*
    |--------------------------------------------------------------------------
    | Validate select fields
    |--------------------------------------------------------------------------
    */

    document.addEventListener('change', (event) => {

        const input = event.target;

        if (input.matches('select')) {
            validateField(input);
        }

    });


    /*
    |--------------------------------------------------------------------------
    | Validate when leaving a field
    |--------------------------------------------------------------------------
    */

    document.addEventListener('blur', (event) => {

        const input = event.target;

        if (
            input.matches(
                'input, textarea, select'
            )
        ) {
            validateField(input);
        }

    }, true);


    /*
    |--------------------------------------------------------------------------
    | Validate entire form before submit
    |--------------------------------------------------------------------------
    */

    document.addEventListener('submit', (event) => {

        const form = event.target;

        if (!(form instanceof HTMLFormElement)) {
            return;
        }

        let valid = true;

        form.querySelectorAll(
            'input, textarea, select'
        ).forEach((input) => {

            if (
                input.type === 'submit' ||
                input.type === 'button' ||
                input.type === 'reset' ||
                input.type === 'hidden'
            ) {
                return;
            }

            if (
                input.name === 'password_confirmation'
            ) {
                if (!validatePasswordConfirmation(input)) {
                    valid = false;
                }

                return;
            }

            if (!validateField(input)) {
                valid = false;
            }

        });


        /*
        |--------------------------------------------------------------------------
        | Stop submission when invalid
        |--------------------------------------------------------------------------
        */

        if (!valid) {

            event.preventDefault();

            const firstInvalid = form.querySelector(
                ':invalid'
            );

            if (firstInvalid) {
                firstInvalid.focus();
            }

        }

    });

});
