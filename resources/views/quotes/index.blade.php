<x-layout>
    <div class="space-y-6">
        <!-- Header & Action Button -->
        <div class="flex items-center justify-between">
            <div>
                <x-page-heading>Quotes & Estimates</x-page-heading>
                <p class="text-xs text-gray-500 mt-1">Manage, consult, and convert quotes into sales</p>
            </div>
            
            <a href="{{ route('quotes.create') }}" 
               class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-md shadow-sm transition">
                + Create Quote
            </a>
        </div>

        <!-- Success / Error Alert Messages -->
        @if(session('success'))
            <div class="p-4 bg-green-50 border-l-4 border-green-500 text-green-700 text-sm rounded">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 bg-red-50 border-l-4 border-red-500 text-red-700 text-sm rounded">
                {{ session('error') }}
            </div>
        @endif

        <!-- Quotes Table wrapped in Panel -->
        <x-panel class="p-0 overflow-hidden bg-white border border-gray-200">
            <table class="w-full text-sm text-left text-gray-700">
                <thead class="text-xs text-gray-500 uppercase bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3">Quote #</th>
                        <th class="px-6 py-3">Customer</th>
                        <th class="px-6 py-3">Date</th>
                        <th class="px-6 py-3">Total</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($quotes as $quote)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 font-semibold text-gray-900">
                                <a href="{{ route('quotes.show', $quote) }}" class="hover:underline text-indigo-600">
                                    {{ $quote->quote_number }}
                                </a>
                            </td>
                            <td class="px-6 py-4">
                                {{ $quote->customer->name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4">
                                {{ $quote->date->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 font-medium text-gray-900">
                                {{ number_format($quote->total, 2) }} TND
                            </td>
                            <td class="px-6 py-4">
                                @if($quote->isConverted())
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                        Converted (Sale #{{ $quote->sale_id }})
                                    </span>
                                @elseif($quote->status === 'accepted')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Accepted
                                    </span>
                                @elseif($quote->status === 'sent')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        Sent
                                    </span>
                                @elseif($quote->status === 'rejected')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        Rejected
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        Draft
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <a href="{{ route('quotes.show', $quote) }}" 
                                   class="text-xs font-medium text-indigo-600 hover:text-indigo-900">
                                    Consult
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                No quotes found. Click <a href="{{ route('quotes.create') }}" class="text-indigo-600 underline">here</a> to create one.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Pagination Links -->
            @if($quotes->hasPages())
                <div class="p-4 border-t border-gray-200">
                    {{ $quotes->links() }}
                </div>
            @endif
        </x-panel>
    </div>
</x-layout>