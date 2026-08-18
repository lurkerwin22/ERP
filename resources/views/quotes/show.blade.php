<x-layout>
    @if (session('success'))
        <div class="mb-4 p-4 text-sm text-green-800 bg-green-50 border border-green-200 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 p-4 text-sm text-red-800 bg-red-50 border border-red-200 rounded-lg">
            {{ session('error') }}
        </div>
    @endif
    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Top Navigation & Action Bar -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <a href="{{ route('quotes.index') }}" class="text-xs font-medium text-gray-500 hover:text-gray-800 transition">
                    ← Back to All Quotes
                </a>
                <h1 class="text-2xl font-bold text-gray-900 mt-1">
                    Quote #{{ $quote->reference ?? $quote->id }}
                </h1>
            </div>

            <!-- Status Badge & Action Controls -->
            <div class="flex items-center space-x-3">
                @php
                    $statusColors = [
                        'draft' => 'bg-gray-100 text-gray-700 border-gray-300',
                        'sent' => 'bg-blue-50 text-blue-700 border-blue-200',
                        'accepted' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                        'converted' => 'bg-purple-50 text-purple-700 border-purple-200',
                        'rejected' => 'bg-red-50 text-red-700 border-red-200',
                    ];
                    $badgeClass = $statusColors[$quote->status] ?? 'bg-gray-100 text-gray-700 border-gray-300';
                @endphp

                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold border {{ $badgeClass }} uppercase tracking-wider">
                    {{ ucfirst($quote->status) }}
                </span>

                @if($quote->status !== 'converted')
                    <form action="{{ route('quotes.convert', $quote->id) }}" method="POST" onsubmit="return confirm('Convert this quote into an official sale? This will deduct stock for the items.');">
                        @csrf
                        <button type="submit" class="inline-flex items-center px-4 py-2 text-xs font-bold text-white bg-indigo-600 rounded-lg shadow hover:bg-indigo-700 transition">
                            ⚡ Convert to Sale
                        </button>
                    </form>
                @endif

                <button onclick="window.print()" class="p-2 text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 shadow-sm transition" title="Print Quote">
                    🖨️
                </button>
            </div>
        </div>

        <!-- Main Invoice/Quote Document Card -->
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-8 space-y-8 print:border-none print:shadow-none">
            
            <!-- Document Header -->
            <div class="flex justify-between items-start border-b border-gray-100 pb-6">
                <div>
                    <h2 class="text-xl font-bold text-gray-900">COMMERCIAL QUOTATION</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Date: {{ \Carbon\Carbon::parse($quote->date)->format('M d, Y') }}</p>
                    @if($quote->valid_until)
                        <p class="text-xs text-gray-500">Valid Until: {{ \Carbon\Carbon::parse($quote->valid_until)->format('M d, Y') }}</p>
                    @endif
                </div>

                <div class="text-right">
                    <span class="text-xs text-gray-400 uppercase font-semibold block">Total Amount</span>
                    <span class="text-3xl font-extrabold text-indigo-600">
                        {{ number_format($quote->total_amount ?? $quote->items->sum(fn($i) => $i->quantity * $i->unit_price), 2) }} TND
                    </span>
                </div>
            </div>

            <!-- Customer & Details Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-gray-50/50 p-4 rounded-lg border border-gray-100">
                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400 block mb-1">Customer Information</span>
                    <p class="text-sm font-semibold text-gray-900">{{ $quote->customer->name ?? 'Walk-in Customer' }}</p>
                    @if(optional($quote->customer)->phone)
                        <p class="text-xs text-gray-600 mt-0.5">Phone: {{ $quote->customer->phone }}</p>
                    @endif
                    @if(optional($quote->customer)->email)
                        <p class="text-xs text-gray-600">Email: {{ $quote->customer->email }}</p>
                    @endif
                </div>

                <div class="md:text-right">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400 block mb-1">Quote Reference</span>
                    <p class="text-sm font-mono font-medium text-gray-800">#{{ $quote->reference ?? $quote->id }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">Created by: {{ $quote->user->name ?? 'System' }}</p>
                </div>
            </div>

            <!-- Items Table -->
            <div class="overflow-x-auto border border-gray-200 rounded-lg">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs text-gray-600 uppercase bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="py-3 px-4">#</th>
                            <th class="py-3 px-4">Item & Description</th>
                            <th class="py-3 px-4 text-center">Qty</th>
                            <th class="py-3 px-4 text-right">Unit Price</th>
                            <th class="py-3 px-4 text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($quote->items as $index => $item)
                            <tr class="hover:bg-gray-50/50">
                                <td class="py-3 px-4 text-gray-400 text-xs">{{ $index + 1 }}</td>
                                <td class="py-3 px-4 font-medium text-gray-900">
                                    {{ $item->product->name ?? $item->product_name ?? 'Product' }}
                                </td>
                                <td class="py-3 px-4 text-center text-gray-700">{{ $item->quantity }}</td>
                                <td class="py-3 px-4 text-right text-gray-700">{{ number_format($item->unit_price, 2) }} TND</td>
                                <td class="py-3 px-4 text-right font-semibold text-gray-900">
                                    {{ number_format($item->quantity * $item->unit_price, 2) }} TND
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-6 text-center text-gray-400 text-xs">No items found in this quote.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Total Calculation Summary -->
            <div class="flex justify-end pt-2">
                <div class="w-full sm:w-64 space-y-2 text-sm">
                    <div class="flex justify-between text-gray-600">
                        <span>Subtotal:</span>
                        <span class="font-medium">{{ number_format($quote->items->sum(fn($i) => $i->quantity * $i->unit_price), 2) }} TND</span>
                    </div>
                    <div class="flex justify-between text-gray-600">
                        <span>Tax / VAT:</span>
                        <span class="font-medium">0.00 TND</span>
                    </div>
                    <div class="border-t border-gray-200 pt-2 flex justify-between font-bold text-gray-900 text-base">
                        <span>Total:</span>
                        <span class="text-indigo-600">{{ number_format($quote->total_amount ?? $quote->items->sum(fn($i) => $i->quantity * $i->unit_price), 2) }} TND</span>
                    </div>
                </div>
            </div>

            <!-- Notes Footer -->
            @if($quote->notes)
                <div class="border-t border-gray-100 pt-4 text-xs text-gray-500">
                    <span class="font-semibold text-gray-700">Notes & Terms:</span>
                    <p class="mt-0.5 leading-relaxed">{{ $quote->notes }}</p>
                </div>
            @endif
        </div>
    </div>
</x-layout>