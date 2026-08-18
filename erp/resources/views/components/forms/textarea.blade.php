@props(['label' => false, 'name', 'required' => false])
<x-forms.field :$label :$name :$required>
    <textarea id="{{ $name }}" name="{{ $name }}" {{ $attributes->merge(['class' => 'erp-input min-h-28 resize-y']) }}>{{ old($name) }}</textarea>
</x-forms.field>
