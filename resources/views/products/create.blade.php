<x-layout>
    <div class="max-w-3xl mx-auto">
        <div class="mb-6">
            <x-page-heading>Create Product</x-page-heading>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6 md:p-8">
            <!-- Added enctype="multipart/form-data" -->
            <x-forms.form method="POST" action="/products" enctype="multipart/form-data" class="space-y-6">

                <x-forms.input label="Product Name" name="name" placeholder="e.g. Wireless Mouse" />

                <x-forms.input label="Description" name="description" placeholder="Brief product summary..." />

                <!-- Image Inputs: Upload File OR Provide URL -->
                <div class="space-y-4">
                    <div>
                        <label for="image" class="block text-sm font-medium text-gray-700 mb-1">Upload Product Image (File)</label>
                        <input type="file" id="image" name="image" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition-colors border border-gray-300 rounded-lg p-1.5" />
                    </div>

                    <div class="relative flex py-1 items-center">
                        <div class="flex-grow border-t border-gray-200"></div>
                        <span class="flex-shrink mx-4 text-xs font-semibold text-gray-400 uppercase">OR</span>
                        <div class="flex-grow border-t border-gray-200"></div>
                    </div>

                    <x-forms.input label="Image URL" name="url" placeholder="https://example.com/image.png" />
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                    <x-forms.input label="Price ($)" name="prix" type="number" step="0.01" placeholder="0.00" />

                    <x-forms.input label="Stock" name="stock" type="number" placeholder="0" />

                    <x-forms.input label="Seuil d'alerte" name="seuil_alerte" type="number" placeholder="5" />
                    
                    @if($categorie)
                        <div class="mb-4">
                            <span class="text-sm text-gray-500">Category:</span>
                            <span class="text-sm font-semibold">
                                {{ $categorie->name }}
                            </span>
                        </div>

                        <input type="hidden" name="categorie_id" value="{{ $categorie->id }}">
                    @endif
                </div>

                <x-forms.divider />

                <!-- Action Buttons -->
                <div class="flex items-center justify-end gap-x-3 pt-2">
                    <a href="/products" class="px-4 py-2 text-sm font-semibold text-gray-700 hover:text-gray-900 transition-colors">
                        Cancel
                    </a>

                    <x-forms.button class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg text-sm font-medium shadow-sm transition-colors">
                        Apply
                    </x-forms.button>
                </div>
            </x-forms.form>
        </div>
    </div>
</x-layout>