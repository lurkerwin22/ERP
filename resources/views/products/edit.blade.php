<x-layout>
    <div class="max-w-3xl mx-auto">
        <div class="mb-6">
            <x-page-heading>Edit Product: {{ $product->name }}</x-page-heading>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6 md:p-8">
            <x-forms.form method="POST" action="/products/{{ $product->id }}" class="space-y-6">
                @method('PATCH')

                <x-forms.input label="Product Name" name="name" :value="old('name', $product->name)" />

                <x-forms.input label="Description" name="description" :value="old('description', $product->description)" />

                <x-forms.input label="Image URL" name="url" :value="old('url', $product->url)" />

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                    <x-forms.input label="Price ($)" name="prix" type="number" step="0.01" :value="old('prix', $product->prix)" />

                    <x-forms.input label="Stock" name="stock" type="number" :value="old('stock', $product->stock)" />

                    <x-forms.input label="Seuil d'alerte" name="seuil_alerte" type="number" :value="old('seuil_alerte', $product->seuil_alerte)" />
                </div>

                <x-forms.divider />

                <!-- Action Buttons: Delete / Cancel / Apply -->
                <div class="flex items-center justify-between pt-2">
                    <!-- Delete Button -->
                    <x-forms.button 
                        type="button" 
                        form="delete-form"
                        onclick="if(!confirm('Are you sure you want to delete this product?')) event.preventDefault(); else document.getElementById('delete-form').submit();"
                        class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        Delete
                    </x-forms.button>

                    <div class="flex items-center gap-x-3">
                        <!-- Cancel Link -->
                        <a href="/products" class="px-4 py-2 text-sm font-semibold text-gray-700 hover:text-gray-900 transition-colors">
                            Cancel
                        </a>

                        <!-- Apply (Submit) Button -->
                        <x-forms.button class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg text-sm font-medium shadow-sm transition-colors">
                            Apply
                        </x-forms.button>
                    </div>
                </div>
            </x-forms.form>

            <!-- Hidden Delete Form -->
            <form id="delete-form" method="POST" action="/products/{{ $product->id }}" class="hidden">
                @csrf
                @method('DELETE')
            </form>
        </div>
    </div>
</x-layout>