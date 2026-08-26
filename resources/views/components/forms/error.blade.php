@props([
    'error' => false,
    'name' => null,
])

@if ($error)
    <p
        data-validation-error="{{ $name }}"
        class="text-sm text-red-500 mt-1"
    >
        {{ $error }}
    </p>
@else
    <p
        data-validation-error="{{ $name }}"
        class="hidden text-sm text-red-500 mt-1"
    ></p>
@endif