<x-layout>
    <!-- Header Bar -->
    <div class="mb-6 flex justify-between items-center pb-4 border-b">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $customer->name }}</h1>
            <p class="text-sm text-gray-500">Customer account profile and sales overview</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('customers.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                &larr; Back to Customers
            </a>
            <a href="{{ route('customers.edit', $customer) }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow-sm transition">
                Edit Profile
            </a>
        </div>
    </div>

    <!-- 1. Customer Financial Summary Cards (Top Level) -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
            <span class="text-xs text-gray-500 uppercase font-semibold">Total Purchases</span>
            <p class="text-3xl font-extrabold text-gray-900 mt-1">{{ number_format($customer->total_purchases, 2) }} TND</p>
        </div>

        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
            <span class="text-xs text-gray-500 uppercase font-semibold">Total Paid</span>
            <p class="text-3xl font-extrabold text-green-600 mt-1">{{ number_format($customer->total_paid, 2) }} TND</p>
        </div>

        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
            <span class="text-xs text-gray-500 uppercase font-semibold">Outstanding Balance</span>
            <p class="text-3xl font-extrabold {{ $customer->total_outstanding_debt > 0 ? 'text-red-600' : 'text-gray-900' }} mt-1">
                {{ number_format($customer->total_outstanding_debt, 2) }} TND
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- 2. Customer Info Panel -->
        <div class="lg:col-span-1">
            <x-panel class="p-6 bg-white rounded-xl border border-gray-200 shadow-sm space-y-4">
                <h3 class="text-lg font-bold text-gray-900 border-b pb-3">Contact Information</h3>
                
                <div>
                    <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Email</span>
                    <p class="text-sm font-medium text-gray-800 mt-0.5">{{ $customer->email ?? 'N/A' }}</p>
                </div>

                <div>
                    <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Phone</span>
                    <p class="text-sm font-medium text-gray-800 mt-0.5">{{ $customer->phone ?? 'N/A' }}</p>
                </div>

                <div>
                    <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">City</span>
                    <p class="text-sm font-medium text-gray-800 mt-0.5">{{ $customer->city ?? 'N/A' }}</p>
                </div>

                <div>
                    <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Address</span>
                    <p class="text-sm font-medium text-gray-800 mt-0.5">{{ $customer->address ?? 'N/A' }}</p>
                </div>

                <div class="pt-2 border-t">
                    <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Notes</span>
                    <p class="text-xs text-gray-600 mt-1 bg-gray-50 p-3 rounded-lg border border-gray-100">
                        {{ $customer->notes ?? 'No notes provided.' }}
                    </p>
                </div>
            </x-panel>
        </div>

        <!-- 3. Sales History Table for this Customer -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-gray-900">Purchase History</h3>
                    <span class="text-xs text-gray-500 font-medium">{{ $customer->sales->count() }} total sales</span>
                </div>

                @if($customer->sales->isEmpty())
                    <div class="p-8 text-center text-sm text-gray-500">
                        No sales recorded for this customer yet.
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-5 py-3 text-xs font-bold text-gray-600 uppercase">Sale Ref</th>
                                    <th class="px-5 py-3 text-xs font-bold text-gray-600 uppercase">Date</th>
                                    <th class="px-5 py-3 text-xs font-bold text-gray-600 uppercase text-right">Total</th>
                                    <th class="px-5 py-3 text-xs font-bold text-gray-600 uppercase text-right">Paid</th>
                                    <th class="px-5 py-3 text-xs font-bold text-gray-600 uppercase text-right">Remaining</th>
                                    <th class="px-5 py-3 text-xs font-bold text-gray-600 uppercase text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-sm">
                                @foreach($customer->sales as $sale)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-5 py-3.5 font-bold text-indigo-600">
                                            <a href="{{ route('sales.show', $sale) }}">#{{ $sale->id }}</a>
                                        </td>
                                        <td class="px-5 py-3.5 text-gray-600">
                                            {{ $sale->created_at->format('d/m/Y') }}
                                        </td>
                                        <td class="px-5 py-3.5 text-right font-medium text-gray-900">
                                            {{ number_format($sale->total, 2) }} TND
                                        </td>
                                        <td class="px-5 py-3.5 text-right font-semibold text-green-600">
                                            {{ number_format($sale->amount_paid, 2) }} TND
                                        </td>
                                        <td class="px-5 py-3.5 text-right font-bold {{ $sale->remaining_amount > 0 ? 'text-red-600' : 'text-gray-800' }}">
                                            {{ number_format($sale->remaining_amount, 2) }} TND
                                        </td>
                                        <td class="px-5 py-3.5 text-center">
                                            <span class="px-2 py-0.5 text-xs font-semibold rounded-full 
                                                {{ $sale->payment_status === 'paid' ? 'bg-green-100 text-green-800' : ($sale->payment_status === 'partial' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                                {{ strtoupper($sale->payment_status) }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layout>