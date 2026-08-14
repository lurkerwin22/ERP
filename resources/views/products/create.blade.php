<x-layout>
    <div class="max-w-3xl mx-auto">
        <div class="mb-6">
            <x-page-heading>Create Product</x-page-heading>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6 md:p-8">
            <x-forms.form method="POST" action="/products" class="space-y-6">

                <x-forms.input label="Product Name" name="name" placeholder="e.g. Wireless Mouse" />

                <x-forms.input label="Description" name="description" placeholder="Brief product summary..." />

                <x-forms.input label="Image URL" name="url" placeholder="https://example.com/image.png" />

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

                <!-- Action Buttons: Cancel / Apply -->
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