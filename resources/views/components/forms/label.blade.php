@props(['name', 'label', 'required'=>false])

<label class="block text-sm font-medium text-gray-700 mb-1.5" for="{{ $name }}">
    {{ $label }}
    @if ($required)
        <span class="text-red-500">*</span>
    @endif
</label>
