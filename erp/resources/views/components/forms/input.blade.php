@props(['label' => false, 'name', 'help' => null, 'required' => false])
@php
    $defaults = [
        'type' => 'text',
        'id' => $name,
        'name' => $name,
        'class' => 'erp-input',
        'value' => old($name),
    ];
@endphp
<x-forms.field :$label :$name :$required>
    <input {{ $attributes->merge($defaults) }}>
    @if($help)<p class="erp-help">{{ $help }}</p>@endif
</x-forms.field>
