<form {{ $attributes->merge(['method' => 'POST']) }}>
    @csrf
    {{ $slot }}
</form>
