<x-layout>
    <div class="mb-6 flex justify-between items-center">
        <x-page-heading>Manage Stock: {{ $product->nom ?? $product->name }}</x-page-heading>
        <a href="{{ route('stock.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900">&larr; Back to Stock Overview</a>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 font-medium rounded shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    <!-- Current Stock Stat Card -->
    <x-panel class="mb-8 p-6 text-center">
        <span class="text-sm font-medium text-gray-500 uppercase tracking-wider">Current Available Quantity</span>
        <p class="text-5xl font-black text-gray-900 mt-2">{{ $product->stock }}</p>
    </x-panel>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Add Stock Form -->
        <x-panel class="p-6">
            <h2 class="text-lg font-bold text-green-700 mb-4 flex items-center gap-2">
                <span>+</span> Add Stock (IN)
            </h2>
            <x-forms.form action="{{ route('stock.add', $product) }}" method="POST">
                <x-forms.input label="Quantity to Add" name="quantity" type="number" min="1" required />
                <x-forms.input label="Reason / Notes" name="reason" placeholder="e.g. Supplier Purchase, Restock" />
                
                <div class="mt-4">
                    <x-forms.button class="bg-green-600 hover:bg-green-700 text-white w-full">Add Stock</x-forms.button>
                </div>
            </x-forms.form>
        </x-panel>

        <!-- Remove Stock Form -->
        <x-panel class="p-6">
            <h2 class="text-lg font-bold text-red-700 mb-4 flex items-center gap-2">
                <span>-</span> Remove Stock (OUT)
            </h2>
            <x-forms.form action="{{ route('stock.remove', $product) }}" method="POST">
                <x-forms.input label="Quantity to Remove" name="quantity" type="number" min="1" max="{{ $product->stock }}" required />
                <x-forms.input label="Reason / Notes" name="reason" placeholder="e.g. Sale, Damaged Item" />
                
                <div class="mt-4">
                    <x-forms.button class="bg-red-600 hover:bg-red-700 text-white w-full">Remove Stock</x-forms.button>
                </div>
            </x-forms.form>
        </x-panel>
    </div>
</x-layout>