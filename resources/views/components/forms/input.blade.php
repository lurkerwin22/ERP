@props([
    'label' => false, 
    'name', 
    'type' => 'text',
    'required' => false
])

@php
    $hasError = $errors->has($name);

    $baseClasses = 'rounded-xl bg-white border px-5 py-4 w-full text-slate-900 placeholder-slate-400 transition-all duration-150 focus:outline-none';
    
    // Dynamic border & ring states for Light Mode (Focus and Error)
    $stateClasses = $hasError 
        ? 'border-red-500 focus:border-red-500 focus:ring-4 focus:ring-red-500/10' 
        : 'border-slate-300 focus:border-indigo-600 focus:ring-4 focus:ring-indigo-600/10';

    $defaults = [
        'type' => $type,
        'id' => $name,
        'name' => $name,
        'class' => "{$baseClasses} {$stateClasses}",
        'value' => old($name),
        'required' => $required
    ];
@endphp

<x-forms.field :$label :$name :$required>
    <input {{ $attributes($defaults) }}>

</x-forms.field>