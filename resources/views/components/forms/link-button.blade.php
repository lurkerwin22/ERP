@props(['variant' => 'secondary'])

@php
    $variants = [
        'primary' => 'bg-indigo-600 text-white hover:bg-indigo-700',
        'secondary' => 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50 hover:border-gray-400',
        'danger' => 'bg-red-600 text-white hover:bg-red-700',
        'ghost' => 'text-gray-600 hover:text-gray-900',
    ];
    $variantClasses = $variants[$variant] ?? $variants['secondary'];
@endphp

<a {{ $attributes([
    'class' => "inline-flex items-center justify-center gap-2 text-sm font-semibold rounded-lg py-2 px-5 transition-colors duration-150 {$variantClasses}",
]) }}>{{ $slot }}</a>
