<x-layout>
    <div class="mb-6 flex justify-between items-center">
        <x-page-heading>Customer Details</x-page-heading>
        <div class="space-x-3">
            <a href="{{ route('clients.edit', $client) }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-md">Edit Customer</a>
            <a href="{{ route('clients.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900">&larr; Back to Customers</a>
        </div>
    </div>

    <x-panel class="p-6 max-w-3xl mx-auto space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 border-b pb-4">
            <div>
                <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Full Name</span>
                <p class="text-lg font-bold text-gray-900 mt-1">{{ $client->nom }}</p>
            </div>
            <div>
                <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Email</span>
                <p class="text-lg text-gray-800 mt-1">{{ $client->email }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 border-b pb-4">
            <div>
                <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Phone Number</span>
                <p class="text-base text-gray-800 mt-1">{{ $client->telephone }}</p>
            </div>
            <div>
                <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">City</span>
                <p class="text-base text-gray-800 mt-1">{{ $client->ville ?? '-' }}</p>
            </div>
        </div>

        <div class="border-b pb-4">
            <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Address</span>
            <p class="text-base text-gray-800 mt-1">{{ $client->adresse ?? '-' }}</p>
        </div>

        <div>
            <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Notes</span>
            <p class="text-sm text-gray-700 mt-1 bg-gray-50 p-3 rounded border border-gray-100">{{ $client->notes ?? 'No notes provided.' }}</p>
        </div>
    </x-panel>
</x-layout>