@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center px-1 pt-1 border-b-4 border-yellow-400 text-sm font-black uppercase tracking-wider text-black focus:outline-none transition duration-150 ease-in-out'
            : 'inline-flex items-center px-1 pt-1 border-b-4 border-transparent text-sm font-bold uppercase tracking-wider text-gray-500 hover:text-black hover:border-yellow-400 focus:outline-none transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
