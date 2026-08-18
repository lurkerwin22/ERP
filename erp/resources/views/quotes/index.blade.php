<x-layout>
    <div class="erp-page">
        <!-- Header & Action Button -->
        <div class="flex items-center justify-between">
            <div>
                <x-page-heading>Quotes & Estimates</x-page-heading>
                <p class="text-xs text-gray-500 mt-1">Manage, consult, and convert quotes into sales</p>
            </div>
            
            <a href="{{ route('quotes.create') }}" 
               class="erp-btn-primary">
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
        <x-panel class="overflow-hidden p-0">
            <div class="overflow-x-auto">
            <table class="erp-table">
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
                                    <x-badge type="info">
                                        Converted (Sale #{{ $quote->sale_id }})
                                    </x-badge>
                                @elseif($quote->status === 'accepted')
                                    <x-badge type="accepted">
                                        Accepted
                                    </x-badge>
                                @elseif($quote->status === 'sent')
                                    <x-badge type="sent">
                                        Sent
                                    </x-badge>
                                @elseif($quote->status === 'rejected')
                                    <x-badge type="rejected">
                                        Rejected
                                    </x-badge>
                                @else
                                    <x-badge type="draft">
                                        Draft
                                    </x-badge>
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
            </div>
        </x-panel>
    </div>
</x-layout>