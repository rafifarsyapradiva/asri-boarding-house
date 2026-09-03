@props([
    'waNumber' => config('app.whatsapp_number', '62895330031313'),
    'logoText' => config('app.name', 'Asri Boarding House'),
    'message' => null
])

@php
    $resolvedWa = preg_replace('/[^0-9]/', '', $waNumber ?? '62895330031313');
    $defaultMsg = "Halo Admin {$logoText}, saya tertarik untuk menanyakan ketersediaan kamar dan informasi lebih lanjut mengenai hunian kost. Terima kasih.";
    $msgText = $message ?? $defaultMsg;
    $url = "https://wa.me/{$resolvedWa}?text=" . rawurlencode($msgText);
@endphp

<a id="wa-floating-button"
   href="{{ $url }}" 
   target="_blank" 
   rel="noopener noreferrer" 
   aria-label="Hubungi Admin via WhatsApp"
   class="fixed bottom-6 right-6 z-50 flex items-center justify-center w-14 h-14 bg-emerald-500 hover:bg-emerald-400 text-black border-4 border-black shadow-[4px_4px_0px_0px_#000000] hover:translate-x-[2px] hover:translate-y-[2px] hover:shadow-[2px_2px_0px_0px_#000000] active:translate-x-[4px] active:translate-y-[4px] active:shadow-none transition-all duration-150 cursor-pointer">
    <svg class="w-7 h-7 fill-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
        <path d="M12.012 2c-5.506 0-9.989 4.478-9.99 9.984a9.96 9.96 0 0 0 1.333 4.982L2 22l5.233-1.372a9.95 9.95 0 0 0 4.777 1.22c5.507 0 9.99-4.479 9.99-9.986 0-2.668-1.037-5.176-2.924-7.062C17.189 3.038 14.68 2 12.012 2zm5.795 13.918c-.254.717-1.468 1.385-2.018 1.47-.487.076-1.127.127-3.23-.746-2.69-1.12-4.409-3.856-4.544-4.037-.134-.18-1.09-1.45-1.09-2.766 0-1.316.69-1.96.938-2.222.25-.262.548-.328.73-.328.18 0 .363.003.52.01.164.007.387-.063.606.463.224.537.766 1.866.83 2 .066.134.11.292.02.472-.09.18-.135.292-.27.45l-.41.48c-.134.148-.28.307-.12.582.16.27.705 1.155 1.51 1.87.803.71 1.48.93 1.758 1.07.277.135.438.113.601-.073.16-.18.69-.803.876-1.08.188-.276.376-.232.633-.135.26.096 1.636.772 1.918.914.28.14.47.21.538.328.068.12.068.69-.186 1.407z"/>
    </svg>
</a>

