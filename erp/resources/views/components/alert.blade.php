@props(['type' => 'info', 'message' => null])
@php
    $styles = [
        'success' => 'border-emerald-200 bg-emerald-50 text-emerald-800',
        'error' => 'border-rose-200 bg-rose-50 text-rose-800',
        'warning' => 'border-amber-200 bg-amber-50 text-amber-800',
        'info' => 'border-indigo-200 bg-indigo-50 text-indigo-800',
    ];
@endphp
<div class="mb-5 flex items-start gap-3 rounded-xl border px-4 py-3 text-sm {{ $styles[$type] ?? $styles['info'] }}" role="alert">
    <span class="mt-0.5 font-bold">{{ $type === 'success' ? '✓' : ($type === 'error' ? '!' : 'i') }}</span>
    <div>{{ $message ?? $slot }}</div>
</div>
