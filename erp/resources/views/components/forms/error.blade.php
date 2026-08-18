@props(['error' => false])
@if ($error)
    <p class="erp-error" role="alert">{{ $error }}</p>
@endif
