<x-layout>
    <div class="mb-6 flex justify-between items-center">
        <x-page-heading>Edit Customer: {{ $client->nom }}</x-page-heading>
        <a href="{{ route('clients.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900">&larr; Back to Customers</a>
    </div>

    <x-panel class="p-6 max-w-3xl mx-auto">
        <x-forms.form action="{{ route('clients.update', $client) }}" method="POST">
            @method('PATCH')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-forms.input label="Full Name *" name="nom" :value="old('nom', $client->nom)" required />
                <x-forms.input label="Email *" name="email" type="email" :value="old('email', $client->email)" required />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                <x-forms.input label="Phone Number *" name="telephone" :value="old('telephone', $client->telephone)" required />
                <x-forms.input label="City" name="ville" :value="old('ville', $client->ville)" />
            </div>

            <div class="mt-4">
                <x-forms.input label="Address" name="adresse" :value="old('adresse', $client->adresse)" />
            </div>

            <div class="mt-4">
                <x-forms.field label="Notes" name="notes">
                    <textarea name="notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ old('notes', $client->notes) }}</textarea>
                </x-forms.field>
            </div>

            <div class="mt-6 flex justify-between items-center">
                <!-- Step 12: Delete button on the far left -->
                <button type="button" 
                        onclick="if(confirm('Are you sure you want to delete this customer?')) { document.getElementById('delete-client-form').submit(); }" 
                        class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-md">
                    Delete
                </button>

                <div class="flex gap-3">
                    <a href="{{ route('clients.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-300">Cancel</a>
                    <x-forms.button class="bg-indigo-600 hover:bg-indigo-700 text-white">Update Customer</x-forms.button>
                </div>
            </div>
        </x-forms.form>

        <!-- Hidden Delete Form -->
        <form id="delete-client-form" action="{{ route('clients.destroy', $client) }}" method="POST" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    </x-panel>
</x-layout>