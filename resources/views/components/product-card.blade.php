@props(['product'])

<div class="flex flex-col sm:flex-row items-center gap-4 p-4 bg-white border border-blue-500 rounded-lg shadow-sm">
    <!-- Image / Placeholder -->
    <div class="w-full sm:w-32 h-32 flex-shrink-0 bg-blue-50 border border-blue-200 rounded-md flex items-center justify-center overflow-hidden">
        @if(!empty($product->url))
            <img 
                src="{{ str_starts_with($product->url, 'http') ? $product->url : asset('storage/' . $product->url) }}" 
                alt="{{ $product->name }}" 
                class="w-full h-full object-cover"
            />
        @else
            <!-- Placeholder Icon matching image mockup -->
            <svg class="w-12 h-12 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 3v2m0 14v2m9-9h-2M5 9H3m15.364-6.364l-1.414 1.414M6.05 17.95l-1.414 1.414m12.728 0l-1.414-1.414M6.05 6.05L4.636 4.636M12 8a4 4 0 100 8 4 4 0 000-8z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 20l5-6 4 4 5-7 4 9H3z" />
            </svg>
        @endif
    </div>

    <!-- Main Info & Actions -->
    <div class="flex-1 flex flex-col justify-between w-full h-full min-h-[8rem]">
        <!-- Top Row: Name, Category & Price badge -->
        <div class="flex justify-between items-start gap-2">
            <div>
                <h3 class="font-semibold text-lg text-blue-600 leading-tight">
                    {{ $product->name ?? 'Product Name' }}
                </h3>
                <span class="text-sm text-blue-400">
                    {{ $product->category->name ?? 'general' }}
                </span>
            </div>
            
            <div class="px-3 py-1 border border-blue-500 text-blue-600 text-sm font-medium rounded">
                ${{ number_format($product->prix ?? 0, 2) }}
            </div>
        </div>

        <!-- Bottom Row: Stock, Alert Threshold & Edit Button -->
        <div class="flex justify-between items-end mt-4">
            <div class="text-xs text-blue-500 space-y-0.5">
                <div>
                    <span class="font-medium">stock:</span> {{ $product->stock ?? 0 }}
                </div>
                <div>
                    <span class="font-medium">seuil d'alerte:</span> {{ $product->seuil_alerte ?? 0 }}
                </div>
            </div>

            <a href="{{ route('products.edit', $product) }}"
               class="px-4 py-1.5 border border-blue-500 text-blue-600 text-sm font-medium rounded hover:bg-blue-50 transition-colors">
                edit
            </a>
        </div>
    </div>
</div>