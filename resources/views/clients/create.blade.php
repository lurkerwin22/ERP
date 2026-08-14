<x-layout>
    <div class="mb-6 flex justify-between items-center">
        <x-page-heading>Create Customer</x-page-heading>
        <a href="{{ route('clients.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900">&larr; Back to Customers</a>
    </div>

    <x-panel class="p-6 max-w-3xl mx-auto">
        <x-forms.form action="{{ route('clients.store') }}" method="POST">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-forms.input label="Full Name *" name="nom" required />
                <x-forms.input label="Email *" name="email" type="email" required />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                <x-forms.input label="Phone Number *" name="telephone" required />
                <x-forms.input label="City" name="ville" />
            </div>

            <div class="mt-4">
                <x-forms.input label="Address" name="adresse" />
            </div>

            <div class="mt-4">
                <x-forms.field label="Notes" name="notes">
                    <textarea name="notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ old('notes') }}</textarea>
                </x-forms.field>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <a href="{{ route('clients.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-300">Cancel</a>
                <x-forms.button class="bg-indigo-600 hover:bg-indigo-700 text-white">Create Customer</x-forms.button>
            </div>
        </x-forms.form>
    </x-panel>
</x-layout>