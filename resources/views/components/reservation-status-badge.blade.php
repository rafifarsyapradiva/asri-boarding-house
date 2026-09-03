@props(['status' => null])

@php
    $normalizedStatus = strtolower(trim((string) $status));
    $badgeClass = match($normalizedStatus) {
        'pending'      => 'admin-badge-warning',
        'dp'           => 'admin-badge-info',
        'lunas'        => 'admin-badge-success',
        'dikonfirmasi' => 'admin-badge-neutral',
        'batal'        => 'admin-badge-danger',
        default        => 'admin-badge-neutral',
    };
    $displayLabel = $normalizedStatus !== '' ? strtoupper($normalizedStatus) : 'N/A';
@endphp

<span {{ $attributes->merge(['class' => "admin-badge {$badgeClass} inline-block font-black text-[10px] uppercase tracking-wider px-2 py-0.5 border-2 border-black shadow-[2px_2px_0px_0px_#000000]"]) }}>
    {{ $displayLabel }}
</span>

