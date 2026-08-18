<form {{ $attributes->merge(['class' => 'space-y-5', 'method' => 'GET']) }}>
    @if (strtoupper($attributes->get('method', 'GET')) !== 'GET')
        @csrf
        @method($attributes->get('method'))
    @endif
    {{ $slot }}
</form>
