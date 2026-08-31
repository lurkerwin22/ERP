<x-layout>
    <div class="max-w-3xl mx-auto py-6">
        <div class="mb-6">
            <x-page-heading>Create Product</x-page-heading>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6 md:p-8">
            <x-forms.form method="POST" action="/products" enctype="multipart/form-data" class="space-y-6">

                <x-forms.input label="Product Name" name="name" placeholder="e.g. Wireless Mouse" />

                <x-forms.input label="Description" name="description" placeholder="Brief product summary..." />

                <div class="bg-gray-50 p-5 rounded-xl border border-gray-200 space-y-4">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-gray-500">Product Image</h4>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <x-forms.input label="Upload File from PC" name="image_file" id="product-image-file" type="file" accept="image/jpeg,image/png,image/webp,image/gif" />
                            <img id="product-image-preview" class="hidden mt-3 w-28 h-28 rounded-xl object-cover border border-slate-200" alt="Image preview">
                        </div>
                        <div>
                            <x-forms.input label="Or External Image URL" name="image_url" placeholder="https://example.com/image.jpg" :value="old('image_url')" />
                        </div>
                    </div>
                </div>                
                <!--  Category & Supplier Section -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                        <select id="category_id" name="category_id" class="block w-full px-4 py-2 border border-gray-300 rounded-lg bg-white text-sm">
                            <option value="" {{ empty(old('category_id', $selectedCategoryId ?? '')) ? 'selected' : '' }}>No category</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id', $selectedCategoryId ?? '') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="supplier_id" class="block text-sm font-medium text-gray-700 mb-1">Supplier</label>
                        <select id="supplier_id" name="supplier_id" class="block w-full px-4 py-2 border border-gray-300 rounded-lg bg-white text-sm">
                            <option value="">Select supplier</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                    {{ $supplier->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- PRICING SECTION -->
                <div class="bg-gray-50 p-5 rounded-xl border border-gray-200 space-y-4">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-gray-500">Pricing</h4>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <x-forms.input label="Purchase price (DT)" id="purchase_price" name="purchase_price" type="number" step="0.01" placeholder="80.00" :value="old('purchase_price')" />
                        <x-forms.input label="Selling price (DT)" id="price" name="price" type="number" step="0.01" placeholder="100.00" :value="old('price')" />
                    </div>

                    <!-- Dynamic Calculation Display -->
                    <div class="grid grid-cols-2 gap-4 pt-3 border-t border-gray-200">
                        <div>
                            <span class="block text-xs text-gray-500 font-medium">Margin</span>
                            <span id="display_margin" class="text-lg font-semibold text-gray-900">—</span>
                        </div>
                        <div>
                            <span class="block text-xs text-gray-500 font-medium">Margin rate</span>
                            <span id="display_margin_rate" class="text-lg font-semibold text-gray-900">—</span>
                        </div>
                    </div>
                </div>

                <!-- STOCK SECTION -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <x-forms.input
                        label="Stock"
                        name="stock"
                        type="number"
                        step="0.01"
                        placeholder="0"
                        :value="old('stock', 0)"
                    />

                    <div>
                        <label for="unit" class="block text-sm font-medium text-gray-700 mb-1">
                            Unit
                        </label>

                        <select
                            id="unit"
                            name="unit"
                            class="block w-full px-4 py-2 border border-gray-300 rounded-lg bg-white text-sm"
                        >
                            <option value="piece" {{ old('unit', 'piece') === 'piece' ? 'selected' : '' }}>
                                Piece
                            </option>
                            <option value="kg" {{ old('unit') === 'kg' ? 'selected' : '' }}>
                                Kilogram (kg)
                            </option>
                            <option value="g" {{ old('unit') === 'g' ? 'selected' : '' }}>
                                Gram (g)
                            </option>
                            <option value="l" {{ old('unit') === 'l' ? 'selected' : '' }}>
                                Liter (l)
                            </option>
                            <option value="ml" {{ old('unit') === 'ml' ? 'selected' : '' }}>
                                Milliliter (ml)
                            </option>
                            <option value="m" {{ old('unit') === 'm' ? 'selected' : '' }}>
                                Meter (m)
                            </option>
                            <option value="cm" {{ old('unit') === 'cm' ? 'selected' : '' }}>
                                Centimeter (cm)
                            </option>
                            <option value="box" {{ old('unit') === 'box' ? 'selected' : '' }}>
                                Box
                            </option>
                            <option value="pack" {{ old('unit') === 'pack' ? 'selected' : '' }}>
                                Pack
                            </option>
                        </select>

                        @error('unit')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <x-forms.input
                        label="Alert Threshold"
                        name="alert_threshold"
                        type="number"
                        step="0.01"
                        placeholder="5"
                        :value="old('alert_threshold', 0)"
                    />
                </div>

                <x-forms.divider />

                <div class="flex items-center justify-end gap-x-3 pt-2">
                    <x-forms.link-button href="/products">
                        Cancel
                    </x-forms.link-button>
                    <x-forms.button>
                        Save Product
                    </x-forms.button>
                </div>
            </x-forms.form>
        </div>
    </div>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const input = document.getElementById('product-image-file');
    const preview = document.getElementById('product-image-preview');
    if (!input || !preview) return;
    input.addEventListener('change', () => {
        const file = input.files?.[0];
        if (!file || !file.type.startsWith('image/')) return;
        preview.src = URL.createObjectURL(file);
        preview.classList.remove('hidden');
    });
});
</script>
</x-layout>