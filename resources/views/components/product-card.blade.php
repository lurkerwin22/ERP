@props(['product'])

<div class="flex flex-col sm:flex-row items-center gap-4 p-4 bg-white border border-gray-200 rounded-xl shadow-sm hover:shadow-md transition-shadow">
    <!-- Image / Placeholder -->
    <div class="w-full sm:w-32 h-32 flex-shrink-0  bg-indigo-50 border border-indigo-100 rounded-lg flex items-center justify-center overflow-hidden">
        @if ($product->image)
            <img 
                src="{{ Str::startsWith($product->image, ['http://', 'https://']) ? $product->image : asset('storage/' . $product->image) }}" 
                alt="{{ $product->name }}" 
                class="object-cover rounded-lg border border-gray-200"
            >
        @else
            <div class="w-24 h-24 bg-gray-100 rounded-lg flex items-center justify-center text-gray-400 text-xs font-medium">
                No img
            </div>
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