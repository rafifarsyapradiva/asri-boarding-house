<x-app-layout>
    @hasSection('header')
        <x-slot name="header">
            @yield('header')
        </x-slot>
    @endif

    @yield('content')
</x-app-layout>
