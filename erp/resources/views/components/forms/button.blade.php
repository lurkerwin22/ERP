@props(['variant' => 'primary'])
@php
    $variantClass = match ($variant) {
        'secondary' => 'erp-btn-secondary',
        'danger' => 'erp-btn-danger',
        'ghost' => 'erp-btn-ghost',
        default => 'erp-btn-primary',
    };
@endphp
<button {{ $attributes->merge(['class' => $variantClass]) }}>{{ $slot }}</button>
