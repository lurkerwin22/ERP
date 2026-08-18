<x-layout>
    <div class="erp-page">
    <!-- Header Block -->
    <div>
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-950">Customers</h1>
                <p class="text-sm text-gray-500">Manage your customer list, contacts, and account details.</p>
            </div>

            <!-- Actions Block -->
            <div class="flex flex-wrap items-center gap-3">
                <!-- Search Form -->
                <form action="{{ route('customers.index') }}" method="GET" class="flex items-center gap-2">
                    <input 
                        type="text" 
                        name="search" 
                        placeholder="Search name, email, phone..." 
                        value="{{ request('search') }}" 
                        class="erp-input w-60"
                    />
                    <button type="submit" class="erp-btn-secondary">
                        Search
                    </button>
                    @if(request('search'))
                        <a href="{{ route('customers.index') }}" class="text-sm text-gray-500 hover:text-gray-700 underline px-1">
                            Clear
                        </a>
                    @endif
                </form>

                <!-- Add Customer Button -->
                <a href="{{ route('customers.create') }}" class="erp-btn-primary whitespace-nowrap">
                    + Add Customer
                </a>
            </div>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 font-medium rounded shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->has('delete'))
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 font-medium rounded shadow-sm">
            {{ $errors->first('delete') }}
        </div>
    @endif

    <!-- Table Section -->
    <div class="erp-table-wrap">
        <div class="overflow-x-auto w-full">
            <table class="erp-table">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3.5 text-xs font-bold text-gray-600 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3.5 text-xs font-bold text-gray-600 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3.5 text-xs font-bold text-gray-600 uppercase tracking-wider">Phone</th>
                        <th class="px-6 py-3.5 text-xs font-bold text-gray-600 uppercase tracking-wider">City</th>
                        <th class="px-6 py-3.5 text-right text-xs font-bold text-gray-600 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse($customers as $customer)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                <a href="{{ route('customers.show', $customer) }}" class="hover:text-indigo-600">
                                    {{ $customer->name }}
                                </a>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ $customer->email }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ $customer->phone }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $customer->city ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                <a href="{{ route('customers.show', $customer) }}" class="text-gray-600 hover:text-gray-900">View</a>
                                <span class="text-gray-300">|</span>
                                <a href="{{ route('customers.edit', $customer) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-sm text-gray-500 font-medium">
                                No customers found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-6 w-full">
        {{ $customers->links() }}
    </div>
    </div>
</x-layout>