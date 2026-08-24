<x-layout>
    <div class="max-w-3xl mx-auto py-6">
        <x-page-heading class="mb-6">Edit Supplier: {{ $supplier->name }}</x-page-heading>

        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6 md:p-8">
            <x-forms.form method="POST" action="{{ route('suppliers.update', $supplier) }}" class="space-y-6">
                @method('PATCH')

                <x-forms.input label="Supplier Name" name="name" :value="old('name', $supplier->name)" required />
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <x-forms.input label="Email" name="email" type="email" :value="old('email', $supplier->email)" />
                    <x-forms.input label="Phone" name="phone" :value="old('phone', $supplier->phone)" />
                </div>

                <x-forms.input label="Address" name="address" :value="old('address', $supplier->address)" />
                <x-forms.input label="Notes" name="notes" :value="old('notes', $supplier->notes)" />

                <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                    <button type="button" 
                        onclick="if(confirm('Are you sure?')) document.getElementById('delete-supplier-form').submit();"
                        class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                        Delete
                    </button>

                    <div class="flex items-center gap-x-3">
                        <a href="{{ route('suppliers.index') }}" class="px-4 py-2 text-sm font-semibold text-gray-700">Cancel</a>
                        <x-forms.button class="bg-blue-600 text-white px-5 py-2 rounded-lg text-sm">Update Supplier</x-forms.button>
                    </div>
                </div>
            </x-forms.form>

            <form id="delete-supplier-form" method="POST" action="{{ route('suppliers.destroy', $supplier) }}" class="hidden">
                @csrf
                @method('DELETE')
            </form>
        </div>
    </div>
</x-layout>