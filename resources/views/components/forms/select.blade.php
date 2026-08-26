@props(['label', 'name', 'required' => false])

@php
    $defaults = [
        'id' => $name,
        'name' => $name,
        'class' => 'rounded-xl bg-white border border-slate-300 px-5 py-4 w-full text-sm text-slate-900 transition-all duration-150 focus:outline-none focus:border-indigo-600 focus:ring-4 focus:ring-indigo-600/10',
        'required' => $required,
    ];
@endphp

<x-forms.field :$label :$name :$required>
    <select {{ $attributes($defaults) }}>
        {{ $slot }}
    </select>
</x-forms.field>

