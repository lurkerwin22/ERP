@props(['label', 'name', 'required' => false])
@php
    $defaults = [
        'id' => $name,
        'name' => $name,
        'class' => 'erp-input',
    ];
@endphp
<x-forms.field :$label :$name :$required>
    <select {{ $attributes->merge($defaults) }}>{{ $slot }}</select>
</x-forms.field>
