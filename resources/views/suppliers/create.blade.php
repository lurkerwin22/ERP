<x-layout>
    <div class="max-w-3xl mx-auto py-6">
        <x-page-heading class="mb-6">Create Supplier</x-page-heading>

        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6 md:p-8">
            <x-forms.form method="POST" action="{{ route('suppliers.store') }}" class="space-y-6">
                <x-forms.input label="Supplier Name" name="name" placeholder="e.g. Tech Supplier SARL" required />
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <x-forms.input label="Email" name="email" type="email" placeholder="contact@supplier.com" />
                    <x-forms.input label="Phone" name="phone" placeholder="+216 22 000 000" />
                </div>

                <x-forms.input label="Address" name="address" placeholder="123 Business Street, Tunis" />
                <x-forms.input label="Notes" name="notes" placeholder="Payment terms, direct contact info..." />

                <div class="flex items-center justify-end gap-x-3 pt-4 border-t border-gray-100">
                    <a href="{{ route('suppliers.index') }}" class="px-4 py-2 text-sm font-semibold text-gray-700">Cancel</a>
                    <x-forms.button class="bg-blue-600 text-white px-5 py-2 rounded-lg text-sm">Save Supplier</x-forms.button>
                </div>
            </x-forms.form>
        </div>
    </div>
</x-layout>