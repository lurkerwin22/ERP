@props(['name', 'label', 'required' => false])
<label class="erp-label" for="{{ $name }}">
    {{ $label }}
    @if($required)<span class="text-rose-500" aria-hidden="true">*</span>@endif
</label>
