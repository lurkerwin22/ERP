@props([
    'label' => false, 
    'name', 
    'type' => 'text',
    'required' => false,
    'value' => null
])

@php
    $hasError = $errors->has($name);

    $baseClasses = 'rounded-xl bg-white border px-5 py-4 w-full text-slate-900 placeholder-slate-400 transition-all duration-150 focus:outline-none';
    
    $stateClasses = $hasError 
        ? 'border-red-500 focus:border-red-500 focus:ring-4 focus:ring-red-500/10' 
        : 'border-slate-300 focus:border-indigo-600 focus:ring-4 focus:ring-indigo-600/10';

    $defaults = [
        'type' => $type,
        'id' => $name,
        'name' => $name,
        'class' => "{$baseClasses} {$stateClasses}",
        'required' => $required
    ];

    if ($type !== 'password') {
        $defaults['value'] = old($name, $value);
    }
@endphp

<x-forms.field :$label :$name :$required>
    @if($type === 'password')
        <div class="relative">
            <input {{ $attributes->merge($defaults) }}>

            <button
                type="button"
                onclick="togglePassword('{{ $attributes->get('id', $name) }}', this)"
                class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition"
                tabindex="-1"
            >
                {{-- Eye icon --}}
                <svg class="eye-open w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542-7z" />
                </svg>

                {{-- Eye slash icon --}}
                <svg class="eye-closed hidden w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 3l18 18" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10.584 10.587a2 2 0 002.829 2.829" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9.88 4.24A9.77 9.77 0 0112 4c4.477 0 8.268 2.943 9.542 8a10.02 10.02 0 01-4.07 5.2M6.61 6.61C4.72 7.82 3.3 9.68 2.458 12c.738 2.35 2.17 4.22 4.09 5.43" />
                </svg>
            </button>
        </div>

        <p data-validation-error="{{ $name }}" class="hidden mt-1 text-sm text-red-500"></p>
    @else
        <input {{ $attributes->merge($defaults) }}>

        <p data-validation-error="{{ $name }}" class="hidden mt-1 text-sm text-red-500"></p>
    @endif
</x-forms.field>

@once
<script>
    function togglePassword(inputId, button) {
        const input = document.getElementById(inputId);
        const eyeOpen = button.querySelector('.eye-open');
        const eyeClosed = button.querySelector('.eye-closed');

        if (input.type === 'password') {
            input.type = 'text';
            eyeOpen.classList.add('hidden');
            eyeClosed.classList.remove('hidden');
        } else {
            input.type = 'password';
            eyeOpen.classList.remove('hidden');
            eyeClosed.classList.add('hidden');
        }
    }
</script>
@endonce