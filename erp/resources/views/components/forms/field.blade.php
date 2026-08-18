@props(['label' => false, 'name', 'required' => false])
<div>
    @if ($label)
        <x-forms.label :$name :$label :$required />
    @endif
    {{ $slot }}
    <x-forms.error :error="$errors->first($name)" />
</div>
