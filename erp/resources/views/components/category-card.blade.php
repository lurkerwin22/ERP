@props(['category'])

<article class="erp-card erp-card-hover group flex h-full flex-col p-5">
    <div class="flex items-start justify-between gap-3">
        <div>
            <div class="mb-2 flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a2 2 0 0 1 2-2h4l2 2h6a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V5Z"/></svg>
            </div>
            <h3 class="text-lg font-bold text-slate-950">{{ $category->name ?? 'Category' }}</h3>
        </div>
        <span class="erp-badge-info">{{ $category->products_count ?? 0 }} products</span>
    </div>

    <p class="mt-4 flex-1 text-sm leading-6 text-slate-500">{{ Str::limit($category->description ?? 'No description provided.', 90) }}</p>

    <div class="mt-5 flex items-center justify-between border-t border-slate-100 pt-4">
        <a href="{{ route('products.category', $category) }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-700">View products</a>
        <a href="{{ route('categories.edit', $category) }}" class="erp-btn-secondary min-h-9 px-3 py-1.5 text-xs">Edit</a>
    </div>
</article>
