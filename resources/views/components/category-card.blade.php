@props(['categorie'])

<div
    onclick="window.location='{{ route('products.categorie', $categorie) }}'"
    class="relative flex flex-col justify-between p-6 bg-white border-2 border-blue-500 rounded-lg shadow-sm hover:shadow-md transition-shadow cursor-pointer"
>
    <!-- Category Title -->
    <div class="text-center mb-6">
        <h3 class="text-xl font-bold text-blue-600 uppercase tracking-wide">
            {{ $categorie->name ?? 'CATEGORIE NAME' }}
        </h3>
    </div>

    <!-- Category Stats -->
    <div class="space-y-2 mb-6 text-sm text-blue-500 font-medium">
        <div class="flex justify-between items-center">
            <span>tot_revenue:</span>
            <span class="font-semibold">$0</span>
        </div>

        <div class="flex justify-between items-center">
            <span>nb of products:</span>
            <span class="font-semibold">
                {{ $categorie->products_count ?? $categorie->nb_of_products ?? 0 }}
            </span>
        </div>
    </div>

    <!-- Edit Button -->
    <a
        href="{{ route('categories.edit', $categorie) }}"
        onclick="event.stopPropagation()"
        class="w-full py-2 border-2 border-blue-500 text-blue-600 font-semibold text-center rounded hover:bg-blue-50 transition-colors block"
    >
        edit
    </a>
</div>