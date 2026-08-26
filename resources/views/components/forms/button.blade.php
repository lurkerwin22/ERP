@props(['variant' => 'primary'])

@php
    $variants = [
        'primary' => 'bg-indigo-600 text-white hover:bg-indigo-700 focus:ring-indigo-600/20',
        'secondary' => 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50 hover:border-gray-400 focus:ring-gray-300/40',
        'danger' => 'bg-red-600 text-white hover:bg-red-700 focus:ring-red-600/20',
    ];
    $variantClasses = $variants[$variant] ?? $variants['primary'];
@endphp

<button {{ $attributes([
    'type' => 'submit',
    'class' => "inline-flex items-center justify-center gap-2 text-sm font-semibold rounded-lg py-2 px-5 shadow-sm transition-colors duration-150 focus:outline-none focus:ring-4 disabled:opacity-60 disabled:cursor-not-allowed {$variantClasses}",
]) }}>{{ $slot }}</button>
