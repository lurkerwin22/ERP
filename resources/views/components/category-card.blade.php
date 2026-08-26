@props(['category'])

<div
    class="relative flex flex-col justify-between p-6 bg-white border border-gray-400 rounded-xl shadow-sm hover:shadow-md hover:border-indigo-200 transition-shadow"
    x-data="{ showModal: false }"
>
        <div
        onclick="window.location='{{ route('products.category', $category) }}'"
        class="cursor-pointer"
    >
        <!-- Category Title -->
        <div class="text-center mb-4">
            <h3 class="text-lg font-semibold text-gray-900 tracking-wide">
                {{ $category->name ?? 'CATEGORIE NAME' }}
            </h3>
        </div>

    <!-- Description & Stats -->
    <div class="space-y-3 mb-6 text-sm text-gray-500 font-medium">
        
        <!-- Description Sentence with See More -->
        <div class="text-xs text-gray-600 leading-relaxed">
            @if(!empty($category->description))
                <span>{{ Str::limit($category->description, 60) }}</span>
                
                @if(strlen($category->description) > 60)
                    <button 
                        type="button"
                        onclick="event.stopPropagation(); openCategoryModal('modal-{{ $category->id }}')"
                        class="text-indigo-600 font-semibold underline hover:text-indigo-800 ml-1 inline-block"
                    >
                        See more
                    </button>
                @endif
            @else
                <span class="italic text-gray-400">No description provided.</span>
            @endif
        </div>

        <div class="flex justify-between items-center border-t border-gray-100 pt-2">
            <span>nb of products:</span>
            <span class="font-semibold text-indigo-600">
                {{ $category->products_count ?? 0 }}
            </span>
        </div>
    </div>
</div>
   <!-- Action Buttons -->
    <div class="mt-6 pt-6 border-t border-gray-200">
        <div class="flex gap-3">
            
            <!-- Edit Button -->
            <x-forms.link-button
                href="{{ route('categories.edit', $category) }}"
                onclick="event.stopPropagation()"
                class="flex-1 block bg-indigo-600 hover:bg-indigo-700 text-white"
            >
                Edit
            </x-forms.link-button>

            <!-- Delete Button -->
            <form
                method="POST"
                action="{{ route('categories.destroy', $category) }}"
                onsubmit="event.stopPropagation(); return confirm('Are you sure you want to delete this category?');"
                class="flex-1"
            >
                @csrf
                @method('DELETE')

                <x-forms.button
                    variant="danger"
                    class="w-full"
                >
                    Delete
                </x-forms.button>
            </form>

        </div>
    </div>

    <!-- Category Description Modal Popup -->
    <div 
        id="modal-{{ $category->id }}"
        onclick="event.stopPropagation(); closeCategoryModal('modal-{{ $category->id }}')"
        class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
    >
        <div 
            onclick="event.stopPropagation()" 
            class="bg-white border border-gray-200 rounded-xl p-6 max-w-md w-full shadow-2xl relative"
        >
            <div class="flex justify-between items-center mb-4 border-b border-gray-100 pb-2">
                <h4 class="text-lg font-semibold text-gray-900">
                    {{ $category->name }} - Description
                </h4>
                <button 
                    type="button"
                    onclick="closeCategoryModal('modal-{{ $category->id }}')"
                    class="text-gray-400 hover:text-gray-600 text-xl font-bold leading-none"
                >
                    &times;
                </button>
            </div>

            <p class="text-sm text-gray-700 leading-relaxed max-h-60 overflow-y-auto">
                {{ $category->description }}
            </p>

            <div class="mt-6 flex justify-end">
                <button 
                    type="button"
                    onclick="closeCategoryModal('modal-{{ $category->id }}')"
                    class="px-4 py-1.5 border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 text-sm transition-colors"
                >
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Toggle Helper Scripts -->
<script>
    if (typeof openCategoryModal === 'undefined') {
        function openCategoryModal(id) {
            document.getElementById(id).classList.remove('hidden');
        }
        function closeCategoryModal(id) {
            document.getElementById(id).classList.add('hidden');
        }
    }
</script>