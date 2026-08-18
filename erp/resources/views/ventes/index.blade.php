<x-layout>
    <!-- Header Block -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-gray-200">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Sales</h1>
                <p class="text-sm text-gray-500">Track client orders, sales totals, and order statuses.</p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <!-- Search Form -->
                <form action="{{ route('ventes.index') }}" method="GET" class="flex items-center gap-2">
                    <input 
                        type="text" 
                        name="search" 
                        placeholder="Search sale ID or client..." 
                        value="{{ request('search') }}" 
                        class="px-3.5 py-2 text-sm text-gray-900 bg-white border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 w-60"
                    />
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-gray-900 hover:bg-gray-800 rounded-lg shadow-sm transition-colors">
                        Search
                    </button>
                    @if(request('search'))
                        <a href="{{ route('ventes.index') }}" class="text-sm text-gray-500 hover:text-gray-700 underline px-1">
                            Clear
                        </a>
                    @endif
                </form>

                <!-- New Sale Button -->
                <a href="{{ route('ventes.create') }}" class="px-4 py-2 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg shadow-sm transition-colors whitespace-nowrap">
                    + New Sale
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

    @if($errors->has('cancel'))
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 font-medium rounded shadow-sm">
            {{ $errors->first('cancel') }}
        </div>
    @endif

    <!-- Table Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden w-full">
        <div class="overflow-x-auto w-full">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3.5 text-xs font-bold text-gray-600 uppercase tracking-wider">Sale #</th>
                        <th class="px-6 py-3.5 text-xs font-bold text-gray-600 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-3.5 text-xs font-bold text-gray-600 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3.5 text-xs font-bold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3.5 text-xs font-bold text-gray-600 uppercase tracking-wider">Total</th>
                        <th class="px-6 py-3.5 text-right text-xs font-bold text-gray-600 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse($ventes as $vente)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">
                                #{{ $vente->id }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-800">
                                {{ $vente->client_nom ?? optional($vente->client)->nom ?? 'Walk-in Customer' }}
                                @if(is_null($vente->client_id) && $vente->client_nom && $vente->client_nom !== 'Walk-in Customer')
                                    <span class="text-xs text-gray-400 block">(Deleted Customer)</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ \Carbon\Carbon::parse($vente->date_vente)->format('M d, Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @if($vente->statut === 'completee')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Completed
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        Cancelled
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">
                                {{ number_format($vente->total, 2) }} TND
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                <a href="{{ route('ventes.show', $vente) }}" class="text-indigo-600 hover:text-indigo-900">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-sm text-gray-500 font-medium">
                                No sales recorded yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-6 w-full">
        {{ $ventes->links() }}
    </div>
</x-layout>