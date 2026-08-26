@props(['label', 'name', 'required' => false])

@php
    $defaults = [
        'id' => $name,
        'name' => $name,
        'class' => 'rounded-lg bg-white border border-gray-300 px-4 py-2 w-full text-sm text-gray-900 transition-colors duration-150 focus:outline-none focus:border-indigo-600 focus:ring-4 focus:ring-indigo-600/10',
        'required' => $required,
    ];
@endphp

<x-forms.field :$label :$name :$required>
    <select {{ $attributes($defaults) }}>
        {{ $slot }}
    </select>
</x-forms.field>

