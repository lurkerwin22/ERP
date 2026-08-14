<x-layout>
    <div class="max-w-2xl mx-auto bg-white p-6 rounded-lg shadow-md">

        <h1 class="text-2xl font-bold text-blue-600 mb-6">
            Edit Category
        </h1>

        {{-- Update form --}}
        <form method="POST" action="/categories/{{ $categorie->id }}" class="space-y-6">
            @csrf
            @method('PATCH')

            <!-- Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                    Category Name
                </label>

                <input
                    type="text"
                    id="name"
                    name="name"
                    value="{{ old('name', $categorie->name) }}"
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
                >{{ old('description', $categorie->description) }}</textarea>

                @error('description')
                    <p class="mt-1 text-sm text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Update / Cancel -->
            <div class="flex gap-4">
                <a
                    href="/categories"
                    class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100 transition-colors"
                >
                    Cancel
                </a>

                <button
                    type="submit"
                    class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                >
                    Update Category
                </button>
            </div>
        </form>

        {{-- Delete form --}}
        <div class="mt-6 pt-6 border-t border-gray-200">
            <form
                method="POST"
                action="/categories/{{ $categorie->id }}"
                onsubmit="return confirm('Are you sure you want to delete this category?');"
            >
                @csrf
                @method('DELETE')

                <button
                    type="submit"
                    class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors"
                >
                    Delete
                </button>
            </form>
        </div>

    </div>
</x-layout>