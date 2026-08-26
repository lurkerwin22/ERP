@props(['label', 'name'])

@php
    $defaults = [
        'type' => 'checkbox',
        'id' => $name,
        'name' => $name,
        'value' => old($name),
        'class' => 'h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600/30',
    ];
@endphp

<div class="flex items-center gap-2">
    <input {{ $attributes($defaults) }}>
    <label for="{{ $name }}" class="text-sm text-gray-700">{{ $label }}</label>
</div>

