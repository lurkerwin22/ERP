@php
   $classes='p-4 bg-white rounded-xl border border-gray-200 shadow-sm group';
@endphp

<div {{ $attributes([ 'class' => $classes ]) }}>
   {{$slot}}
</div>