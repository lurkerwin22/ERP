<x-layout>
    <div class="max-w-5xl mx-auto space-y-6 py-6">

        <!-- Header -->
        <div class="flex items-center justify-between border-b border-gray-200 pb-4">
            <div>
                <h1 class="text-xl font-bold text-gray-900">
                    Create New Quote
                </h1>

                <p class="text-xs text-gray-500 mt-0.5">
                    Prepare an official quotation for a customer
                </p>
            </div>

            <a href="{{ route('quotes.index') }}"
               class="text-xs font-semibold text-gray-500 hover:text-indigo-600 transition">
                ← Back to Quotes
            </a>
        </div>


        <!-- Validation Errors -->
        @if ($errors->any())
            <div class="p-4 bg-red-50 border border-red-200 text-red-700 text-xs rounded-lg">
                <p class="font-semibold mb-1 text-sm">
                    Please correct the following errors:
                </p>

                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif


        <form action="{{ route('quotes.store') }}" method="POST">
            @csrf

            <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6 space-y-6">

                <!-- ================================================= -->
                <!-- 1. GENERAL INFORMATION -->
                <!-- ================================================= -->

                <div>
                    <h2 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">
                        1. General Information
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                        <!-- Customer -->
                        <div>
                            <label for="customer_id"
                                   class="block text-sm font-medium text-gray-700 mb-1.5">
                                Customer
                            </label>

                            <select
                                name="customer_id"
                                id="customer_id"
                                required
                                class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                                <option value="">
                                    Select a customer
                                </option>

                                @foreach($customers as $customer)
                                    @php
                                        $isWalkIn = Str::contains(
                                            strtolower($customer->name),
                                            ['walk-in', 'guest', 'passing', 'comptoir', 'passager']
                                        );

                                        $isSelected = old('customer_id')
                                            ? old('customer_id') == $customer->id
                                            : ($isWalkIn || $loop->first);
                                    @endphp

                                    <option
                                        value="{{ $customer->id }}"
                                        {{ $isSelected ? 'selected' : '' }}
                                    >
                                        {{ $customer->name }}
                                        {{ $customer->phone ? '(' . $customer->phone . ')' : '' }}
                                    </option>
                                @endforeach
                            </select>

                            @error('customer_id')
                                <p class="mt-1 text-xs text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>


                        <!-- Date -->
                        <div>
                            <label for="date"
                                   class="block text-sm font-medium text-gray-700 mb-1.5">
                                Quote Date
                            </label>

                            <input
                                type="date"
                                name="date"
                                id="date"
                                value="{{ old('date', now()->format('Y-m-d')) }}"
                                required
                                class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >

                            @error('date')
                                <p class="mt-1 text-xs text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                    </div>
                </div>


                <hr class="border-gray-100">


                <!-- ================================================= -->
                <!-- 2. QUOTE LINE ITEMS -->
                <!-- ================================================= -->

                <div>

                    <div class="flex items-center justify-between mb-4">

                        <div>
                            <h2 class="text-xs font-bold text-gray-400 uppercase tracking-wider">
                                2. Quote Line Items
                            </h2>

                            <p class="text-xs text-gray-500 mt-0.5">
                                Add the products included in this quotation.
                            </p>
                        </div>

                        <button
                            type="button"
                            id="add-product-btn"
                            class="inline-flex items-center justify-center px-3 py-1.5 text-xs font-semibold text-indigo-600 bg-indigo-50 border border-indigo-100 rounded-lg hover:bg-indigo-100 transition"
                        >
                            + Add Product
                        </button>

                    </div>


                    <!-- Table -->
                    <div class="border border-gray-200 rounded-lg overflow-hidden">

                        <div class="overflow-x-auto">

                            <table class="w-full text-sm text-left border-collapse">

                                <thead>
                                    <tr class="bg-gray-50 text-[11px] font-bold text-gray-500 uppercase tracking-wider border-b border-gray-200">

                                        <th class="py-3 px-4">
                                            Product
                                        </th>

                                        <th class="py-3 px-3 w-32 text-center">
                                            Quantity
                                        </th>

                                        <th class="py-3 px-3 w-36 text-right">
                                            Unit Price (TND)
                                        </th>

                                        <th class="py-3 px-4 w-36 text-right">
                                            Subtotal
                                        </th>

                                        <th class="py-3 px-2 w-10">
                                        </th>

                                    </tr>
                                </thead>


                                <tbody id="items-table-body"
                                       class="divide-y divide-gray-200 bg-white">

                                    @if(old('items'))

                                        @foreach(old('items') as $index => $item)

                                            <tr>

                                                <!-- Product -->
                                                <td class="px-4 py-3">

                                                    <select
                                                        name="items[{{ $index }}][product_id]"
                                                        class="product-select w-full text-sm border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-indigo-500"
                                                        required
                                                    >
                                                        <option value="">
                                                            Select product
                                                        </option>

                                                        @foreach($products as $product)
                                                            <option
                                                                value="{{ $product->id }}"
                                                                data-price="{{ $product->selling_price }}"
                                                            >
                                                                {{ $product->name }}
                                                            </option>
                                                        @endforeach

                                                    </select>

                                                </td>


                                                <!-- Quantity -->
                                                <td class="px-3 py-3">

                                                    <input
                                                        type="number"
                                                        name="items[{{ $index }}][quantity]"
                                                        value="{{ $item['quantity'] ?? 1 }}"
                                                        min="1"
                                                        step="1"
                                                        class="quantity-input w-full text-sm text-center border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-indigo-500"
                                                        required
                                                    >

                                                </td>


                                                <!-- Unit Price -->
                                                <td class="px-3 py-3">

                                                    <input
                                                        type="number"
                                                        name="items[{{ $index }}][unit_price]"
                                                        value="{{ $item['unit_price'] ?? 0 }}"
                                                        min="0"
                                                        step="0.01"
                                                        class="price-input w-full text-sm text-right border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-indigo-500"
                                                        required
                                                    >

                                                </td>


                                                <!-- Subtotal -->
                                                <td class="px-4 py-3 text-right">

                                                    <span class="subtotal font-semibold text-gray-900">
                                                        —
                                                    </span>

                                                </td>


                                                <!-- Remove -->
                                                <td class="px-2 py-3 text-center">

                                                    <button
                                                        type="button"
                                                        class="remove-product text-gray-400 hover:text-red-600 text-lg font-bold"
                                                        title="Remove"
                                                    >
                                                        ×
                                                    </button>

                                                </td>

                                            </tr>

                                        @endforeach

                                    @endif

                                </tbody>

                            </table>

                        </div>

                    </div>

                    @error('items')
                        <p class="mt-2 text-xs text-red-600">
                            {{ $message }}
                        </p>
                    @enderror

                </div>


                <hr class="border-gray-100">


                <!-- ================================================= -->
                <!-- 3. SUMMARY -->
                <!-- ================================================= -->

                <div class="flex flex-col md:flex-row justify-between items-center gap-4">

                    <div class="flex items-center gap-2 text-xs text-gray-500 bg-gray-50 px-3 py-2 rounded-lg border border-gray-200">
                        <span>💡</span>

                        <span>
                            Stock is <strong>not</strong> deducted when saving a quote draft.
                        </span>
                    </div>


                    <button
                        type="submit"
                        class="inline-flex items-center justify-center min-w-[140px] px-4 py-2.5 bg-indigo-600 text-white font-semibold text-xs rounded-lg shadow-sm hover:bg-indigo-700 active:bg-indigo-800 transition"
                    >
                        Save Quote Draft
                    </button>

                </div>

            </div>

        </form>

    </div>


    <!-- ========================================================= -->
    <!-- HIDDEN ROW TEMPLATE -->
    <!-- ========================================================= -->

    <template id="product-row-template">
    <tr>

        <!-- Product -->
        <td class="px-4 py-3">
            <select
                data-name="product_id"
                class="product-select w-full text-sm border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-indigo-500"
                required
            >
                <option value="">Select product</option>

                @foreach($products as $product)
                    <option
                        value="{{ $product->id }}"
                        data-price="{{ $product->selling_price ?? $product->price ?? 0 }}"
                    >
                        {{ $product->name }}
                    </option>
                @endforeach
            </select>
        </td>

        <!-- Quantity -->
        <td class="px-3 py-3">
            <input
                type="number"
                data-name="quantity"
                value="1"
                min="1"
                step="1"
                class="quantity-input w-full text-sm text-center border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-indigo-500"
                required
            >
        </td>

        <!-- Unit Price -->
        <td class="px-3 py-3">
            <input
                type="number"
                data-name="unit_price"
                value="0"
                min="0"
                step="0.01"
                class="price-input w-full text-sm text-right border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-indigo-500"
                required
            >
        </td>

        <!-- Subtotal -->
        <td class="px-4 py-3 text-right">
            <span class="subtotal font-semibold text-gray-900">
                —
            </span>
        </td>

        <!-- Remove -->
        <td class="px-2 py-3 text-center">
            <button
                type="button"
                class="remove-product text-gray-400 hover:text-red-600 text-lg font-bold"
                title="Remove"
            >
                ×
            </button>
        </td>

    </tr>
</template>

    <!-- ========================================================= -->
    <!-- MINIMAL JAVASCRIPT -->
    <!-- ========================================================= -->

   
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const addButton = document.getElementById('add-product-btn');
            const tableBody = document.getElementById('items-table-body');
            const template = document.getElementById('product-row-template');

            let rowIndex = {{ old('items') ? count(old('items')) : 0 }};


            // Add product row
            addButton.addEventListener('click', function () {

                const row = template.content.cloneNode(true);

                row.querySelectorAll('[data-name]').forEach(function (element) {

                    const field = element.dataset.name;

                    element.name = `items[${rowIndex}][${field}]`;

                    element.removeAttribute('data-name');
                });

                tableBody.appendChild(row);

                rowIndex++;
            });


            // Product selected
            tableBody.addEventListener('change', function (event) {

                if (!event.target.classList.contains('product-select')) {
                    return;
                }

                const select = event.target;
                const row = select.closest('tr');

                const option = select.options[select.selectedIndex];

                const price = option.dataset.price;

                if (price !== undefined) {
                    row.querySelector('.price-input').value = price;
                }
            });


            // Remove row
            tableBody.addEventListener('click', function (event) {

                const button = event.target.closest('.remove-product');

                if (!button) {
                    return;
                }

                button.closest('tr').remove();
            });

        });
    </script>
  

</x-layout>