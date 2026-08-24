<x-layout>
    <div class="max-w-3xl mx-auto py-6">
        <div class="mb-6">
            <x-page-heading>Create Product</x-page-heading>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6 md:p-8">
            <x-forms.form method="POST" action="/products" class="space-y-6">

                <x-forms.input label="Product Name" name="name" placeholder="e.g. Wireless Mouse" />

                <x-forms.input label="Description" name="description" placeholder="Brief product summary..." />

                <div>
                    <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                    <select id="category_id" name="category_id" class="block w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white text-gray-900 text-sm">
                        
                        {{-- Default "No Category" --}}
                        <option value="" {{ empty(old('category_id', $selectedCategoryId)) ? 'selected' : '' }}>
                            No category
                        </option>

                        {{-- Dynamic Categories --}}
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" 
                                {{ old('category_id', $selectedCategoryId) == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach

                    </select>
                </div>

                <!-- Price Fields -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 bg-gray-50 p-4 rounded-xl border border-gray-200">
                    <x-forms.input label="Purchase Price (DT)" name="purchase_price" type="number" step="0.01" placeholder="80.00" />
                    <x-forms.input label="Selling Price (DT)" name="price" type="number" step="0.01" placeholder="100.00" />
                </div>

                <!-- Stock Controls -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <x-forms.input label="Stock" name="stock" type="number" placeholder="0" />
                    <x-forms.input label="Alert Threshold" name="alert_threshold" type="number" placeholder="5" />
                </div>

                <x-forms.divider />

                <div class="flex items-center justify-end gap-x-3 pt-2">
                    <a href="/products" class="px-4 py-2 text-sm font-semibold text-gray-700 hover:text-gray-900 transition-colors">
                        Cancel
                    </a>
                    <x-forms.button class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg text-sm font-medium shadow-sm transition-colors">
                        Save Product
                    </x-forms.button>
                </div>
            </x-forms.form>
        </div>
    </div>
</x-layout>