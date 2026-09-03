<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 w-full">
            <h2 class="font-black text-xl text-black leading-tight uppercase tracking-wider">
                {{ __('Obrolan Reservasi') }}
            </h2>
            <a href="{{ route('penyewa.reservasi.pembayaran', $reservasi->id) }}" class="inline-block px-4 py-2 bg-white dark:bg-slate-800 hover:bg-yellow-400 dark:hover:bg-yellow-500 text-black dark:text-white dark:hover:text-black border-4 border-black dark:border-white font-black text-xs uppercase tracking-wider shadow-[4px_4px_0px_0px_#000000] dark:shadow-[4px_4px_0px_0px_#ffffff] hover:translate-x-[2px] hover:translate-y-[2px] hover:shadow-[2px_2px_0px_0px_#000000] dark:hover:shadow-[2px_2px_0px_0px_#ffffff] active:translate-x-[3px] active:translate-y-[3px] active:shadow-[0px_0px_0px_0px_#000000] transition duration-150 cursor-pointer text-center">
                ← Kembali ke Detail Reservasi
            </a>
        </div>
    </x-slot>

    <div class="py-12 bg-white dark:bg-slate-900 min-h-screen text-black dark:text-white">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            <div class="bg-white dark:bg-slate-800 border-4 border-black dark:border-white p-6 shadow-[6px_6px_0px_0px_#000000] dark:shadow-[6px_6px_0px_0px_#ffffff] space-y-6">
                <!-- Rincian Ringkas Reservasi -->
                <div class="bg-yellow-50 dark:bg-yellow-950/30 p-4 border-4 border-black dark:border-white shadow-[4px_4px_0px_0px_#000000] dark:shadow-[4px_4px_0px_0px_#ffffff] flex justify-between items-center flex-wrap gap-4 text-xs font-black uppercase tracking-wider text-black dark:text-white">
                    <div>
                        <span class="opacity-70">Kamar:</span> Kamar {{ $reservasi->kamar?->nomor_kamar ?? '-' }}
                    </div>
                    <div>
                        <span class="opacity-70">Order ID:</span> {{ $reservasi->order_id }}
                    </div>
                    <div>
                        <span class="opacity-70">Status:</span> {{ $reservasi->status }}
                    </div>
                </div>

                <!-- Obrolan Chat Box -->
                @include('reservasi.chat-box')
            </div>

        </div>
    </div>
</x-app-layout>
