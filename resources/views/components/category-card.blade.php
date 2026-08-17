@props(['categorie'])

<div
    onclick="window.location='{{ route('products.categorie', $categorie) }}'"
    class="relative flex flex-col justify-between p-6 bg-white border-2 border-blue-500 rounded-lg shadow-sm hover:shadow-md transition-shadow cursor-pointer"
    x-data="{ showModal: false }"
>
    <!-- Category Title -->
    <div class="text-center mb-4">
        <h3 class="text-xl font-bold text-blue-600 uppercase tracking-wide">
            {{ $categorie->name ?? 'CATEGORIE NAME' }}
        </h3>
    </div>

    <!-- Description & Stats -->
    <div class="space-y-3 mb-6 text-sm text-blue-500 font-medium">
        
        <!-- Description Sentence with See More -->
        <div class="text-xs text-gray-600 leading-relaxed">
            @if(!empty($categorie->description))
                <span>{{ Str::limit($categorie->description, 60) }}</span>
                
                @if(strlen($categorie->description) > 60)
                    <button 
                        type="button"
                        onclick="event.stopPropagation(); openCategoryModal('modal-{{ $categorie->id }}')"
                        class="text-blue-600 font-semibold underline hover:text-blue-800 ml-1 inline-block"
                    >
                        See more
                    </button>
                @endif
            @else
                <span class="italic text-gray-400">No description provided.</span>
            @endif
        </div>

        <div class="flex justify-between items-center border-t border-blue-100 pt-2">
            <span>nb of products:</span>
            <span class="font-semibold text-blue-600">
                {{ $categorie->products_count ?? 0 }}
            </span>
        </div>
    </div>

    <!-- Edit Button -->
    <a
        href="{{ route('categories.edit', $categorie) }}"
        onclick="event.stopPropagation()"
        class="w-full py-2 border-2 border-blue-500 text-blue-600 font-semibold text-center rounded hover:bg-blue-50 transition-colors block"
    >
        edit
    </a>

    <!-- Category Description Modal Popup -->
    <div 
        id="modal-{{ $categorie->id }}"
        onclick="event.stopPropagation(); closeCategoryModal('modal-{{ $categorie->id }}')"
        class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
    >
        <div 
            onclick="event.stopPropagation()" 
            class="bg-white border-2 border-blue-500 rounded-xl p-6 max-w-md w-full shadow-2xl relative"
        >
            <div class="flex justify-between items-center mb-4 border-b pb-2">
                <h4 class="text-lg font-bold text-blue-600">
                    {{ $categorie->name }} - Description
                </h4>
                <button 
                    type="button"
                    onclick="closeCategoryModal('modal-{{ $categorie->id }}')"
                    class="text-gray-400 hover:text-gray-600 text-xl font-bold leading-none"
                >
                    &times;
                </button>
            </div>

            <p class="text-sm text-gray-700 leading-relaxed max-h-60 overflow-y-auto">
                {{ $categorie->description }}
            </p>

            <div class="mt-6 flex justify-end">
                <button 
                    type="button"
                    onclick="closeCategoryModal('modal-{{ $categorie->id }}')"
                    class="px-4 py-1.5 border border-blue-500 text-blue-600 font-semibold rounded hover:bg-blue-50 text-sm transition-colors"
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