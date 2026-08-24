<x-layout>
    <div class="max-w-3xl mx-auto py-6">
        <div class="mb-6">
            <x-page-heading>Edit Product: {{ $product->name }}</x-page-heading>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6 md:p-8">
            <x-forms.form method="POST" action="/products/{{ $product->id }}" class="space-y-6">
                @method('PATCH')

                <x-forms.input label="Product Name" name="name" :value="old('name', $product->name)" />

                <x-forms.input label="Description" name="description" :value="old('description', $product->description)" />

                <div>
                    <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                    <select id="category_id" name="category_id" class="block w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white text-gray-900 text-sm">
                        <option value="">No category</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Price Fields -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 bg-gray-50 p-4 rounded-xl border border-gray-200">
                    <x-forms.input label="Purchase Price (DT)" name="purchase_price" type="number" step="0.01" :value="old('purchase_price', $product->purchase_price)" placeholder="80.00" />
                    <x-forms.input label="Selling Price (DT)" name="price" type="number" step="0.01" :value="old('price', $product->price)" placeholder="100.00" />
                </div>

                <!-- Stock Controls -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <x-forms.input label="Stock" name="stock" type="number" :value="old('stock', $product->stock)" />
                    <x-forms.input label="Alert Threshold" name="alert_threshold" type="number" :value="old('alert_threshold', $product->alert_threshold)" />
                </div>

                <x-forms.divider />

                <div class="flex items-center justify-between pt-2">
                    <button 
                        type="button" 
                        onclick="if(confirm('Are you sure you want to delete this product?')) document.getElementById('delete-form').submit();"
                        class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        Delete
                    </button>

                    <div class="flex items-center gap-x-3">
                        <a href="/products" class="px-4 py-2 text-sm font-semibold text-gray-700 hover:text-gray-900 transition-colors">
                            Cancel
                        </a>
                        <x-forms.button class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg text-sm font-medium shadow-sm transition-colors">
                            Update Product
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