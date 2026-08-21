<x-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
        
        <!-- Main Container: Sidebar + Product Content -->
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 items-start">
            
            <!-- Left Sidebar: Filters -->
            <aside class="lg:col-span-1 bg-white border border-blue-100 rounded-2xl p-5 shadow-sm sticky top-6">
                <div class="pb-3 mb-4 border-b border-gray-100">
                    <h2 class="text-sm font-extrabold text-gray-800 tracking-wider uppercase">FILTERS</h2>
                </div>

                <form id="product-filter-form" action="{{ route('products.index') }}" method="GET" class="space-y-5">
                    @if(request('search'))
                        <input type="hidden" name="search" value="{{ request('search') }}">
                    @endif

                    <!-- Category -->
                    <div class="space-y-1.5">
                        <label for="category_id" class="text-xs font-bold text-gray-700">Category</label>
                        <select name="category_id" id="category_id" class="w-full rounded-lg border-gray-200 text-xs text-gray-700 focus:border-blue-500 focus:ring-blue-500 py-2">
                            <option value="">All categories</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Stock Status -->
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-gray-700 block">Stock Status</label>
                        <div class="space-y-2 text-xs text-gray-600">
                            <label class="flex items-center space-x-2 cursor-pointer">
                                <input type="radio" name="stock_status" value="" {{ !request('stock_status') ? 'checked' : '' }} class="text-blue-600 focus:ring-blue-500 border-gray-300">
                                <span class="font-medium text-gray-700">All</span>
                            </label>
                            <label class="flex items-center space-x-2 cursor-pointer">
                                <input type="radio" name="stock_status" value="in_stock" {{ request('stock_status') === 'in_stock' ? 'checked' : '' }} class="text-blue-600 focus:ring-blue-500 border-gray-300">
                                <span>In stock</span>
                            </label>
                            <label class="flex items-center space-x-2 cursor-pointer">
                                <input type="radio" name="stock_status" value="low_stock" {{ request('stock_status') === 'low_stock' ? 'checked' : '' }} class="text-blue-600 focus:ring-blue-500 border-gray-300">
                                <span>Low stock</span>
                            </label>
                            <label class="flex items-center space-x-2 cursor-pointer">
                                <input type="radio" name="stock_status" value="out_of_stock" {{ request('stock_status') === 'out_of_stock' ? 'checked' : '' }} class="text-blue-600 focus:ring-blue-500 border-gray-300">
                                <span>Out of stock</span>
                            </label>
                        </div>
                    </div>

                    <!-- Price Range -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-gray-700 block">Price Range (TND)</label>
                        <div class="flex items-center gap-2">
                            <input type="number" 
                                   step="0.01" 
                                   min="0" 
                                   id="min_price_input"
                                   name="min_price" 
                                   value="{{ request('min_price') }}" 
                                   placeholder="Min" 
                                   class="w-full rounded-lg border-gray-200 text-xs focus:border-blue-500 focus:ring-blue-500 py-2 text-center">
                            <span class="text-gray-400 font-medium">-</span>
                            <input type="number" 
                                   step="0.01" 
                                   min="0" 
                                   id="max_price_input"
                                   name="max_price" 
                                   value="{{ request('max_price') }}" 
                                   placeholder="Max" 
                                   class="w-full rounded-lg border-gray-200 text-xs focus:border-blue-500 focus:ring-blue-500 py-2 text-center">
                        </div>
                        <p id="price_error" class="hidden text-[11px] text-red-600 font-semibold mt-1"></p>
                    </div>

                    <!-- Sort By -->
                    <div class="space-y-1.5">
                        <label for="sort" class="text-xs font-bold text-gray-700 block">Sort By</label>
                        <select name="sort" id="sort" class="w-full rounded-lg border-gray-200 text-xs text-gray-700 focus:border-blue-500 focus:ring-blue-500 py-2">
                            <option value="latest" {{ request('sort', 'latest') === 'latest' ? 'selected' : '' }}>Newest</option>
                            <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>Oldest</option>
                            <option value="name_asc" {{ request('sort') === 'name_asc' ? 'selected' : '' }}>Name A → Z</option>
                            <option value="name_desc" {{ request('sort') === 'name_desc' ? 'selected' : '' }}>Name Z → A</option>
                            <option value="price_asc" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>Price low → high</option>
                            <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Price high → low</option>
                            <option value="stock_asc" {{ request('sort') === 'stock_asc' ? 'selected' : '' }}>Stock low → high</option>
                            <option value="stock_desc" {{ request('sort') === 'stock_desc' ? 'selected' : '' }}>Stock high → low</option>
                        </select>
                    </div>

                    <!-- Action Buttons -->
                    <div class="pt-2 space-y-2">
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 px-4 rounded-xl text-xs transition-colors shadow-sm">
                            Apply Filters
                        </button>

                        <a href="{{ route('products.index') }}" class="block w-full text-center bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 font-semibold py-2 px-4 rounded-xl text-xs transition-colors">
                            Reset
                        </a>
                    </div>
                </form>
            </aside>

            <!-- Right Area: Header, Chips, Product Cards -->
            <main class="lg:col-span-3 space-y-4">
                
                <!-- Header Controls Bar -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <h1 class="text-xl font-bold text-gray-900">
                        Products <span class="text-gray-500 font-medium">({{ $products->total() }})</span>
                    </h1>

                    <div class="flex items-center gap-4 self-end sm:self-auto">
                        @php
                            $activeCount = collect([
                                request('search'),
                                request('category_id'),
                                request('stock_status'),
                                request('min_price'),
                                request('max_price')
                            ])->filter()->count();
                        @endphp

                        @if($activeCount > 0)
                            <span class="text-xs text-blue-600 font-semibold">{{ $activeCount }} {{ Str::plural('filter', $activeCount) }} active</span>
                            <a href="{{ route('products.index') }}" class="text-xs text-blue-600 hover:underline font-medium">Clear all</a>
                        @endif

                       @if(isset($category))
                            <a href="{{ route('products.create', ['category' => $category->id]) }}" 
                            class="inline-flex whitespace-nowrap px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg shadow-sm transition-colors text-sm">
                                New Product
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Active Filter Chips -->
                @if($activeCount > 0)
                    <div class="flex flex-wrap items-center gap-2 pt-1">
                        @if(request('search'))
                            <a href="{{ request()->fullUrlWithQuery(['search' => null]) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-blue-50 border border-blue-100 text-blue-700 text-xs font-medium hover:bg-blue-100 transition-colors">
                                Search: "{{ request('search') }}"
                                <span class="text-blue-500 font-bold">&times;</span>
                            </a>
                        @endif

                        @if(request('category_id'))
                            @php $selectedCat = $categories->firstWhere('id', request('category_id')); @endphp
                            <a href="{{ request()->fullUrlWithQuery(['category_id' => null]) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-blue-50 border border-blue-100 text-blue-700 text-xs font-medium hover:bg-blue-100 transition-colors">
                                Category: {{ $selectedCat->name ?? 'Selected' }}
                                <span class="text-blue-500 font-bold">&times;</span>
                            </a>
                        @endif

                        @if(request('stock_status'))
                            <a href="{{ request()->fullUrlWithQuery(['stock_status' => null]) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-blue-50 border border-blue-100 text-blue-700 text-xs font-medium hover:bg-blue-100 transition-colors">
                                {{ str_replace('_', ' ', ucfirst(request('stock_status'))) }}
                                <span class="text-blue-500 font-bold">&times;</span>
                            </a>
                        @endif

                        @if(request('min_price') || request('max_price'))
                            <a href="{{ request()->fullUrlWithQuery(['min_price' => null, 'max_price' => null]) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-blue-50 border border-blue-100 text-blue-700 text-xs font-medium hover:bg-blue-100 transition-colors">
                                Price: {{ request('min_price', '0') }} - {{ request('max_price', '100') }} TND
                                <span class="text-blue-500 font-bold">&times;</span>
                            </a>
                        @endif
                    </div>
                @endif

                <!-- Product List -->
                @if($products->count() > 0)
                    <div class="space-y-3">
                        @foreach ($products as $product)
                            <x-product-card :product="$product" />
                        @endforeach
                    </div>

                    <!-- Pagination Navigation -->
                    <div class="mt-6 flex justify-center">
                        {{ $products->links() }}
                    </div>
                @else
                    <div class="bg-white border border-gray-200 rounded-2xl p-12 text-center space-y-3">
                        <p class="text-sm text-gray-500">No products match your current filters.</p>
                        <a href="{{ route('products.index') }}" class="inline-block text-xs font-semibold text-blue-600 hover:underline">
                            Reset filters
                        </a>
                    </div>
                @endif

            </main>
        </div>
    </div>
</x-layout>