<x-layout>
    <div class="max-w-5xl mx-auto space-y-6 py-6">
        <!-- Header -->
        <div class="flex items-center justify-between border-b border-gray-200 pb-4">
            <div>
                <h1 class="text-xl font-bold text-gray-900">Create New Quote</h1>
                <p class="text-xs text-gray-500 mt-0.5">Prepare an official quotation for a customer</p>
            </div>
            <a href="{{ route('quotes.index') }}" class="text-xs font-semibold text-gray-500 hover:text-indigo-600 transition">
                ← Back to Quotes
            </a>
        </div>

        <!-- Validation Errors -->
        @if ($errors->any())
            <div class="p-4 bg-red-50 border border-red-200 text-red-700 text-xs rounded-lg">
                <p class="font-semibold mb-1 text-sm">Please correct the following errors:</p>
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('quotes.store') }}" method="POST" id="quote-form">
            @csrf

            <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6 space-y-6">
                <!-- Section 1: Customer & Date Selection -->
                <div>
                    <h2 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">1. General Information</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <x-forms.select label="Customer" name="customer_id" id="customer_id" class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                @foreach($customers as $customer)
                                    @php
                                        $isWalkIn = Str::contains(strtolower($customer->name), ['walk-in', 'guest', 'passing', 'comptoir', 'passager']);
                                        $isSelected = old('customer_id') ? (old('customer_id') == $customer->id) : ($isWalkIn || $loop->first);
                                    @endphp
                                    <option value="{{ $customer->id }}" {{ $isSelected ? 'selected' : '' }}>
                                        {{ $customer->name }} {{ $customer->phone ? '('.$customer->phone.')' : '' }}
                                    </option>
                                @endforeach
                            </x-forms.select>
                        </div>

                        <div>
                            <x-forms.input label="Quote Date" type="date" name="date" id="date" value="{{ old('date', now()->format('Y-m-d')) }}" class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required />
                        </div>
                    </div>
                </div>

                <hr class="border-gray-100" />

                <!-- Section 2: Quote Items Table -->
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h2 class="text-xs font-bold text-gray-400 uppercase tracking-wider">2. Quote Line Items</h2>
                            <p class="text-xs text-gray-500 mt-0.5">Search and add items to auto-populate default unit pricing.</p>
                        </div>
                        <button type="button" id="add-product-btn" 
                                class="inline-flex items-center px-3 py-1.5 text-xs font-semibold text-indigo-600 bg-indigo-50 border border-indigo-100 rounded-lg hover:bg-indigo-100 transition">
                            + Add Product
                        </button>
                    </div>

                    <div class="overflow-visible border border-gray-200 rounded-lg">
                        <table class="w-full text-sm text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50 text-[11px] font-bold text-gray-500 uppercase tracking-wider border-b border-gray-200">
                                    <th class="py-3 px-4">Product Name</th>
                                    <th class="py-3 px-3 w-32 text-center">Quantity</th>
                                    <th class="py-3 px-3 w-36 text-right">Unit Price (TND)</th>
                                    <th class="py-3 px-4 w-36 text-right">Subtotal</th>
                                    <th class="py-3 px-2 w-10 text-center"></th>
                                </tr>
                            </thead>
                            <tbody id="items-table-body" class="divide-y divide-gray-200 bg-white">
                                <!-- Dynamic Rows Injected Here -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <hr class="border-gray-100" />

                <!-- Section 3: Summary & Submission -->
                <div class="flex flex-col md:flex-row justify-between items-center gap-4 pt-1">
                    <div class="flex items-center space-x-2 text-xs text-gray-500 bg-gray-50 px-3 py-2 rounded-lg border border-gray-200">
                        <span>💡</span>
                        <span>Stock is <strong>not</strong> deducted when saving a quote draft.</span>
                    </div>

                    <div class="flex items-center space-x-6 w-full md:w-auto justify-between md:justify-end">
                        <div class="text-right">
                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Grand Total</span>
                            <span id="grand-total" class="text-xl font-bold text-indigo-600">0.00 TND</span>
                        </div>

                        <button type="submit" id="submit-btn" class="px-4 py-2 bg-indigo-600 text-white font-semibold text-xs rounded-lg shadow-sm hover:bg-indigo-700 active:bg-indigo-800 transition">
                            Save Quote Draft
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</x-layout>