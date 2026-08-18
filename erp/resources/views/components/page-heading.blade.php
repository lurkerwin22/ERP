@props(['description' => null, 'action' => null, 'actionLabel' => null])

<div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
    <div class="min-w-0">
        <h1 class="text-2xl font-bold tracking-tight text-slate-950">{{ $slot }}</h1>
        @if($description)
            <p class="mt-1 text-sm text-slate-500">{{ $description }}</p>
        @endif
    </div>
    @if($action && $actionLabel)
        <a href="{{ $action }}" class="erp-btn-primary shrink-0">{{ $actionLabel }}</a>
    @endif
</div>
