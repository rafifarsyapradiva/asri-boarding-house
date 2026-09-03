@props(['value'])

<label {{ $attributes->merge(['class' => 'block text-xs font-extrabold uppercase tracking-wider text-slate-900 dark:text-slate-200 mb-1.5 select-none']) }}>
    {{ $value ?? $slot }}
</label>
