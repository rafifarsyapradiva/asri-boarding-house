@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'p-3 bg-emerald-400 text-black border-4 border-black font-black text-xs uppercase tracking-wider shadow-[4px_4px_0px_0px_#000000] mb-4']) }}>
        ✅ {{ $status }}
    </div>
@endif

