<x-layout>
    <div class="mb-6 flex justify-between items-center">
        <x-page-heading>Edit Customer: {{ $customer->name }}</x-page-heading>
        <a href="{{ route('customers.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900">&larr; Back to Customers</a>
    </div>

    <x-panel class="p-6 max-w-3xl mx-auto">
        <x-forms.form action="{{ route('customers.update', $customer) }}" method="POST">
            @method('PATCH')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-forms.input label="Full Name *" name="name" :value="old('name', $customer->name)" required />
                <x-forms.input label="Email *" name="email" type="email" :value="old('email', $customer->email)" required />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                <x-forms.input label="Phone Number *" name="phone" :value="old('phone', $customer->phone)" required />
                <x-forms.input label="City" name="city" :value="old('city', $customer->city)" />
            </div>

            <div class="mt-4">
                <x-forms.input label="Address" name="address" :value="old('address', $customer->address)" />
            </div>

            <div class="mt-4">
                <x-forms.field label="Notes" name="notes">
                    <textarea name="notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ old('notes', $customer->notes) }}</textarea>
                </x-forms.field>
            </div>

            <div class="mt-6 flex justify-between items-center">
                <!-- Step 12: Delete button on the far left -->
                <x-forms.button 
                    type="button" 
                    variant="danger"
                    onclick="if(confirm('Are you sure you want to delete this customer?')) { document.getElementById('delete-customer-form').submit(); }">
                    Delete
                </x-forms.button>

                <div class="flex gap-3">
                    <x-forms.link-button href="{{ route('customers.index') }}">Cancel</x-forms.link-button>
                    <x-forms.button>Update Customer</x-forms.button>
                </div>
            </div>
        </x-forms.form>

        <!-- Hidden Delete Form -->
        <form id="delete-customer-form" action="{{ route('customers.destroy', $customer) }}" method="POST" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    </x-panel>
</x-layout>