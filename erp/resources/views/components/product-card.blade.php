@props(['product'])

<article class="erp-card erp-card-hover overflow-hidden">
    <div class="flex flex-col gap-4 p-4 sm:flex-row sm:items-center">
        <div class="h-28 w-full shrink-0 overflow-hidden rounded-lg border border-slate-200 bg-slate-50 sm:h-24 sm:w-24">
            @if(!empty($product->image))
                <img src="{{ str_starts_with($product->image, 'http') ? $product->image : asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="h-full w-full object-cover">
            @else
                <div class="flex h-full w-full items-center justify-center text-slate-300">
                    <svg class="h-10 w-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m3 16 5-6 4 4 5-7 4 9H3Zm5-8a2 2 0 1 0 0-4 2 2 0 0 0 0 4Z"/></svg>
                </div>
            @endif
        </div>

        <div class="min-w-0 flex-1">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                <div class="min-w-0">
                    <h3 class="truncate text-base font-bold text-slate-950">{{ $product->name ?? 'Product' }}</h3>
                    <p class="mt-1 text-sm text-slate-500">{{ $product->category->name ?? 'General' }}</p>
                </div>
                <span class="erp-badge-info shrink-0">{{ number_format($product->price ?? 0, 2) }} TND</span>
            </div>

            <div class="mt-4 flex flex-col gap-3 border-t border-slate-100 pt-3 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex flex-wrap gap-3 text-xs text-slate-500">
                    <span><strong class="text-slate-700">Stock:</strong> {{ $product->stock ?? 0 }}</span>
                    <span><strong class="text-slate-700">Alert:</strong> {{ $product->alert_threshold ?? 0 }}</span>
                </div>
                <a href="{{ route('products.edit', $product) }}" class="erp-btn-secondary min-h-9 px-3 py-1.5 text-xs">Edit product</a>
            </div>
        </div>
    </div>
</article>
