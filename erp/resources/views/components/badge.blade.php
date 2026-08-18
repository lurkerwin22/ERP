@props(['type' => 'neutral'])
@php
    $class = match ($type) {
        'success', 'paid', 'completed', 'accepted', 'available' => 'erp-badge-success',
        'warning', 'partial', 'draft', 'low_stock' => 'erp-badge-warning',
        'danger', 'unpaid', 'cancelled', 'rejected', 'out_of_stock' => 'erp-badge-danger',
        'info', 'sent' => 'erp-badge-info',
        default => 'erp-badge-neutral',
    };
@endphp
<span {{ $attributes->merge(['class' => $class]) }}>{{ $slot }}</span>
