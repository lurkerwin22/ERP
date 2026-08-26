<x-layout>
    <div class="mb-6 flex justify-between items-center">
        <x-page-heading>Movement History: {{ $product->name ?? $product->name }}</x-page-heading>
        <div class="space-x-4 w-1/4">
            <a href="{{ route('stock.adjust', $product) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">Manage Stock</a>
            <a href="{{ route('stock.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900 ">&larr; Back to Stock</a>
        </div>
    </div>

    <x-panel class="overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reason</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($movements as $movement)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $movement->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold">
                            @if($movement->type === 'in')
                                <span class="px-2 py-0.5 rounded text-xs bg-green-100 text-green-800">IN</span>
                            @else
                                <span class="px-2 py-0.5 rounded text-xs bg-red-100 text-red-800">OUT</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">
                            {{ $movement->type === 'in' ? '+' : '-' }}{{ $movement->quantity }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ $movement->reason ?? '-' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                            No stock movements recorded for this product yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </x-panel>
</x-layout>