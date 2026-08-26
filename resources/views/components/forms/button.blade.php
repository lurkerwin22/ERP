<button {{ $attributes([
    'type' => 'submit',
    'class' => 'inline-flex items-center justify-center gap-2 bg-indigo-600 text-white text-sm font-semibold rounded-lg py-2 px-5 shadow-sm transition-colors duration-150 hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-600/20 disabled:opacity-60 disabled:cursor-not-allowed',
]) }}>{{ $slot }}</button>
