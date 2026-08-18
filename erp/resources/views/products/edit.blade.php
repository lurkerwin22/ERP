<x-layout>
    <div class="max-w-3xl mx-auto">
        <div class="mb-6">
            <x-page-heading>Edit Product: {{ $product->name }}</x-page-heading>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6 md:p-8">
            <x-forms.form method="POST" action="/products/{{ $product->id }}" enctype="multipart/form-data" class="space-y-6">
                @method('PATCH')

                <x-forms.input label="Product Name" name="name" :value="old('name', $product->name)" />

                <x-forms.input label="Description" name="description" :value="old('description', $product->description)" />

                <!-- Category Select Dropdown -->
                <div>
                    <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">
                        Category
                    </label>
                    <select 
                        id="category_id" 
                        name="category_id" 
                        class="block w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white text-gray-900 text-sm"
                    >
                        <option value="" {{ is_null(old('category_id', $product->category_id)) ? 'selected' : '' }}>
                            No category
                        </option>
                        <option disabled>──────────</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" 
                                {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Image Section -->
                <div class="space-y-4">
                    <!-- Image Preview -->
                    @if($product->image)
                        <div>
                            <span class="block text-sm font-medium text-gray-700 mb-2">Current Image</span>
                            <img src="{{ str_starts_with($product->image, 'http') ? $product->image : asset('storage/' . $product->image) }}" 
                                 alt="{{ $product->name }}" 
                                 class="h-24 w-24 object-cover rounded-lg border border-gray-200" />
                        </div>
                    @endif

                    <!-- Unified Single Image Input with Browse Button -->
                    <div>
                        <label for="image_input" class="block text-sm font-medium text-gray-700 mb-1">
                            Product Image
                        </label>
                        <div class="relative flex items-center">
                            <input 
                                type="text" 
                                id="image_input"
                                name="image" 
                                value="{{ old('image', $product->image) }}"
                                placeholder="Paste image URL (https://...) or click Browse to upload" 
                                class="block w-full pr-24 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm bg-white text-gray-900"
                            />
                            
                            <!-- Hidden Real File Input -->
                            <input 
                                type="file" 
                                id="file_upload" 
                                name="image" 
                                accept="image/*" 
                                class="hidden" 
                                onchange="
                                    if (this.files.length > 0) { 
                                        document.getElementById('image_input').value = this.files[0].name; 
                                    }
                                "
                            />

                            <!-- Browse Button -->
                            <button 
                                type="button" 
                                onclick="document.getElementById('file_upload').click()" 
                                class="absolute right-1.5 top-1.5 bottom-1.5 px-3 bg-blue-50 hover:bg-blue-100 text-blue-600 text-xs font-semibold rounded-md border border-blue-200 transition-colors flex items-center justify-center"
                            >
                                Browse...
                            </button>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                    <x-forms.input label="Price ($)" name="price" type="number" step="0.01" :value="old('price', $product->price)" />

                    <x-forms.input label="Stock" name="stock" type="number" :value="old('stock', $product->stock)" />

                    <x-forms.input label="Alert Threshold" name="alert_threshold" type="number" :value="old('alert_threshold', $product->alert_threshold)" />
                </div>

                <x-forms.divider />

                <!-- Action Buttons -->
                <div class="flex items-center justify-between pt-2">
                    <x-forms.button 
                        type="button" 
                        form="delete-form"
                        onclick="if(!confirm('Are you sure you want to delete this product?')) event.preventDefault(); else document.getElementById('delete-form').submit();"
                        class="erp-btn-danger">
                        Delete
                    </x-forms.button>

                    <div class="flex items-center gap-x-3">
                        <a href="/products" class="px-4 py-2 text-sm font-semibold text-gray-700 hover:text-gray-900 transition-colors">
                            Cancel
                        </a>

                        <x-forms.button class="erp-btn-primary">
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