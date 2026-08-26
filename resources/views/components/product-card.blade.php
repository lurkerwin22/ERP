@props(['product'])

<div class="flex flex-col sm:flex-row items-center gap-4 p-4 bg-white border border-gray-200 rounded-xl shadow-sm hover:shadow-md transition-shadow">
    <!-- Image / Placeholder -->
    <div class="w-full sm:w-32 h-32 flex-shrink-0 bg-indigo-50 border border-indigo-100 rounded-lg flex items-center justify-center overflow-hidden">
        @if(!empty($product->image))
            <img 
                src="{{ str_starts_with($product->image, 'http') ? $product->image : asset('storage/' . $product->image) }}" 
                alt="{{ $product->name }}" 
                class="w-full h-full object-cover"
            />
        @else
            <!-- Placeholder Icon matching image mockup -->
            <svg class="w-12 h-12 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                <h3 class="font-semibold text-lg text-gray-900 leading-tight">
                    {{ $product->name ?? 'Product Name' }}
                </h3>
                <span class="text-sm text-gray-500">
                    {{ $product->category->name ?? 'general' }}
                </span>
            </div>
            
            <div class="px-3 py-1 bg-indigo-50 text-indigo-700 text-sm font-semibold rounded-full">
                {{ number_format($product->price ?? 0, 2) }} TND
            </div>
        </div>

        <!-- Bottom Row: Stock, Alert Threshold & Edit Button -->
        <div class="flex justify-between items-end mt-4">
            <div class="text-xs text-gray-500 space-y-0.5">
                <div>
                    <span class="font-medium text-gray-600">stock:</span> {{ $product->stock ?? 0 }}
                </div>
                <div>
                    <span class="font-medium text-gray-600">seuil d'alerte:</span> {{ $product->alert_threshold ?? 0 }}
                </div>
            </div>

            <x-forms.link-button href="{{ route('products.edit', $product) }}" class="px-4 py-1.5 text-sm">
                Edit
            </x-forms.link-button>
        </div>
    </div>
</div>