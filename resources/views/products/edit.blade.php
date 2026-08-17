<x-layout>
    <div class="max-w-3xl mx-auto">
        <div class="mb-6">
            <x-page-heading>Edit Product: {{ $product->name }}</x-page-heading>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6 md:p-8">
            <!-- Added enctype="multipart/form-data" -->
            <x-forms.form method="POST" action="/products/{{ $product->id }}" enctype="multipart/form-data" class="space-y-6">
                @method('PATCH')

                <x-forms.input label="Product Name" name="name" :value="old('name', $product->name)" />

                <x-forms.input label="Description" name="description" :value="old('description', $product->description)" />

                <!-- Category Select Dropdown (ADDED HERE) -->
                <div>
                    <label for="categorie_id" class="block text-sm font-medium text-gray-700 mb-1">
                        Category
                    </label>
                    <select 
                        id="categorie_id" 
                        name="categorie_id" 
                        class="block w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white text-gray-900 text-sm"
                    >
                        <option value="" {{ is_null(old('categorie_id', $product->categorie_id)) ? 'selected' : '' }}>
                            No category
                        </option>
                        <option disabled>──────────</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" 
                                {{ old('categorie_id', $product->categorie_id) == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Existing Image Preview & Upload Fields -->
                <div class="space-y-4">
                    @if($product->url)
                        <div>
                            <span class="block text-sm font-medium text-gray-700 mb-2">Current Image</span>
                            <img src="{{ str_starts_with($product->url, 'http') ? $product->url : asset('storage/' . $product->url) }}" 
                                 alt="{{ $product->name }}" 
                                 class="h-24 w-24 object-cover rounded-lg border" />
                        </div>
                    @endif

                    <div>
                        <label for="image" class="block text-sm font-medium text-gray-700 mb-1">Upload New Image File</label>
                        <input type="file" id="image" name="image" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition-colors border border-gray-300 rounded-lg p-1.5" />
                    </div>

                    <div class="relative flex py-1 items-center">
                        <div class="flex-grow border-t border-gray-200"></div>
                        <span class="flex-shrink mx-4 text-xs font-semibold text-gray-400 uppercase">OR</span>
                        <div class="flex-grow border-t border-gray-200"></div>
                    </div>

                    <x-forms.input label="Image URL" name="url" :value="old('url', $product->url)" />
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                    <x-forms.input label="Price ($)" name="prix" type="number" step="0.01" :value="old('prix', $product->prix)" />

                    <x-forms.input label="Stock" name="stock" type="number" :value="old('stock', $product->stock)" />

                    <x-forms.input label="Seuil d'alerte" name="seuil_alerte" type="number" :value="old('seuil_alerte', $product->seuil_alerte)" />
                </div>

                <x-forms.divider />

                <!-- Action Buttons -->
                <div class="flex items-center justify-between pt-2">
                    <x-forms.button 
                        type="button" 
                        form="delete-form"
                        onclick="if(!confirm('Are you sure you want to delete this product?')) event.preventDefault(); else document.getElementById('delete-form').submit();"
                        class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        Delete
                    </x-forms.button>

                    <div class="flex items-center gap-x-3">
                        <a href="/products" class="px-4 py-2 text-sm font-semibold text-gray-700 hover:text-gray-900 transition-colors">
                            Cancel
                        </a>

                        <x-forms.button class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg text-sm font-medium shadow-sm transition-colors">
                            Apply
                        </x-forms.button>
                    </div>
                </div>
            </x-forms.form>

            <form id="delete-form" method="POST" action="/products/{{ $product->id }}" class="hidden">
                @csrf
                @method('DELETE')
            </form>
        </div>
    </div>
</x-layout>