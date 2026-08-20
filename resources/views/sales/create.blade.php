<x-layout>
    <div class="mb-6 flex justify-between items-center pb-4 border-b">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">NEW SALE</h1>
            <p class="text-sm text-gray-500">Search products, set quantities, and record payments.</p>
        </div>
        <a href="{{ route('sales.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900">&larr; Back to Sales</a>
    </div>

    @if($errors->any())
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 font-medium rounded shadow-sm">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('sales.store') }}" method="POST" id="sale-form">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Left Column: Product Search & Item List -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Product Search Box -->
                <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm relative">
                    <h2 class="text-lg font-bold text-gray-900 mb-2">Search & Add Products</h2>
                    <p class="text-xs text-gray-500 mb-4">Type a product name or SKU reference to filter live results.</p>

                    <div class="relative">
                        <input type="text" id="product-search-input" placeholder="🔍 Search product..." autocomplete="off"
                            class="w-full text-sm border-gray-300 rounded-lg p-3 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">

                        <!-- Search Dropdown Results -->
                        <div id="search-results-dropdown" class="hidden absolute z-20 left-0 right-0 mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-60 overflow-y-auto divide-y divide-gray-100">
                        </div>
                    </div>

                    <!-- Stock Warning Box -->
                    <div id="stock-warning" class="hidden mt-3 p-3 bg-red-50 border border-red-200 text-red-600 text-xs rounded-lg font-semibold">
                    </div>
                </div>

                <!-- Products Cart Table -->
                <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">Sale Items</h2>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse" id="items-table">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-4 py-3 text-xs font-bold text-gray-600 uppercase">Product</th>
                                    <th class="px-4 py-3 text-xs font-bold text-gray-600 uppercase text-center w-28">QTY</th>
                                    <th class="px-4 py-3 text-xs font-bold text-gray-600 uppercase text-right">Price</th>
                                    <th class="px-4 py-3 text-xs font-bold text-gray-600 uppercase text-right">Total</th>
                                    <th class="px-4 py-3 text-xs font-bold text-gray-600 uppercase text-center w-12"></th>
                                </tr>
                            </thead>
                            <tbody id="cart-rows" class="divide-y divide-gray-200 text-sm">
                                <tr id="empty-cart-msg">
                                    <td colspan="5" class="px-4 py-6 text-center text-gray-400">
                                        No products added yet. Use the search bar above to add items.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Right Column: Customer & Payment Totals -->
            <div class="space-y-6">
                <!-- Customer Selection -->
                <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
                    <h2 class="text-lg font-bold text-gray-900 mb-3">Customer</h2>
                    <select name="customer_id" id="customer_id" class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Walk-in Customer</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                {{ $customer->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Summary & Payment Methods -->
                <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm space-y-4">
                    <h2 class="text-lg font-bold text-gray-900 border-b pb-2">Payment Details</h2>

                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-600 font-semibold">Subtotal</span>
                        <span class="text-base font-extrabold text-gray-900"><span id="summary-subtotal">0.00</span> TND</span>
                    </div>

                    <div class="flex justify-between items-center text-sm">
                        <label for="amount_paid" class="text-gray-600 font-semibold">Amount Paid</label>
                        <div class="w-36">
                            <input type="number" step="0.01" min="0" name="amount_paid" id="amount_paid" value="0.00"
                                class="w-full text-right text-sm border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 p-1.5 font-bold">
                        </div>
                    </div>

                    <div class="flex justify-between items-center text-sm pt-2 border-t">
                        <span class="text-gray-600 font-semibold">Remaining</span>
                        <span class="text-base font-extrabold text-red-600"><span id="summary-remaining">0.00</span> TND</span>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Payment Method</label>
                        <select name="payment_method" class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="cash">Cash</option>
                            <option value="card">Card</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="check">Check</option>
                        </select>
                    </div>

                    <button type="submit" class="w-full mt-4 bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-4 rounded-xl shadow transition">
                        Create Sale
                    </button>
                </div>
            </div>
        </div>
    </form>

  
</x-layout>