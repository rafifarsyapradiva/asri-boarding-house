@props([
    'id' => 'password',
    'name' => 'password',
    'label' => 'Kata Sandi',
    'placeholder' => '••••••••',
    'required' => true,
    'autocomplete' => 'current-password',
    'theme' => 'neobrutalism' // 'neobrutalism' | 'modern'
])

@php
    $inputClasses = $theme === 'neobrutalism'
        ? 'w-full border-4 border-black dark:border-white bg-white dark:bg-slate-900 focus:bg-yellow-50 dark:focus:bg-slate-800 pl-10 pr-10 py-3.5 text-xs font-bold text-black dark:text-white outline-none transition duration-150 focus:ring-4 focus:ring-yellow-400 placeholder-gray-500 shadow-[4px_4px_0px_0px_#000000] dark:shadow-[4px_4px_0px_0px_#ffffff]'
        : 'w-full rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white pl-10 pr-10 py-3 text-sm font-medium transition duration-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-50 outline-none dark:bg-slate-900/50 dark:border-slate-700 dark:text-white dark:focus:border-indigo-500';
        
    $labelClasses = $theme === 'neobrutalism'
        ? 'block text-[11px] font-black uppercase tracking-wider text-black dark:text-white mb-2'
        : 'block text-[10px] font-extrabold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-1.5';
@endphp

<div x-data="{ show: false }" class="w-full">
    @if($label)
        <label for="{{ $id }}" class="{{ $labelClasses }}">{{ $label }}</label>
    @endif
    <div class="relative">
        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-400">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
        </span>
        
        <input 
            type="password"
            id="{{ $id }}" 
            name="{{ $name }}" 
            :type="show ? 'text' : 'password'" 
            {{ $required ? 'required' : '' }} 
            autocomplete="{{ $autocomplete }}" 
            placeholder="{{ $placeholder }}"
            {{ $attributes->merge(['class' => $inputClasses, 'data-testid' => "input-{$name}"]) }}
        >
        
        <button 
            type="button" 
            @click="show = !show" 
            class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-indigo-500 focus:outline-none focus:text-indigo-600 transition duration-150 cursor-pointer"
            :aria-label="show ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi'"
            :aria-pressed="show.toString()"
        >
            <!-- Eye Icon (Visible when password is hidden) -->
            <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>
            <!-- Eye Off Icon (Visible when password is shown) -->
            <svg x-show="show" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.822 7.822L21 21m-2.228-2.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
            </svg>
        </button>
    </div>
    <x-input-error :messages="isset($errors) ? $errors->get($name) : []" class="mt-1.5" />
</div>
