@props(['title' => 'Nothing here yet', 'description' => null])
<div class="erp-empty">
    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-400">
        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 8h10M7 12h6m7 0a8 8 0 1 1-16 0 8 8 0 0 1 16 0Z"/></svg>
    </div>
    <h3 class="mt-4 text-base font-semibold text-slate-900">{{ $title }}</h3>
    @if($description)<p class="mt-1 max-w-md text-sm text-slate-500">{{ $description }}</p>@endif
    @if($slot->isNotEmpty())<div class="mt-5">{{ $slot }}</div>@endif
</div>
