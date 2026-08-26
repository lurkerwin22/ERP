<x-layout>
    <div class="max-w-2xl mx-auto bg-white p-6 rounded-lg shadow-md">

        <x-page-heading>Create Category</x-page-heading>

        <form method="POST" action="/categories" class="space-y-6">
            @csrf

            <!-- Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                    Category Name
                </label>

                <input
                    type="text"
                    id="name"
                    name="name"
                    value="{{ old('name') }}"
                    required
                    maxlength="255"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Enter category name"
                >

                @error('name')
                    <p class="mt-1 text-sm text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                    Description
                </label>

                <textarea
                    id="description"
                    name="description"
                    rows="4"
                    maxlength="254"
                    
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Enter category description"
                >{{ old('description') }}</textarea>

                @error('description')
                    <p class="mt-1 text-sm text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Buttons -->
            <div class="flex gap-3">
                <x-forms.link-button href="/categories">
                    Cancel
                </x-forms.link-button>

                <x-forms.button>
                    Create Category
                </x-forms.button>
            </div>

        </form>
    </div>
</x-layout>