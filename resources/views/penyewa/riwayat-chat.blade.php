<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-xl text-black leading-tight uppercase tracking-wider">
            {{ __('Riwayat Chat') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-white dark:bg-slate-900 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Statistics cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Card 1: Total Obrolan -->
                <div class="bg-purple-300 dark:bg-purple-950 p-5 border-4 border-black dark:border-white shadow-[4px_4px_0px_0px_#000000] dark:shadow-[4px_4px_0px_0px_#ffffff] transition-all duration-200 hover:translate-x-[-2px] hover:translate-y-[-2px] hover:shadow-[6px_6px_0px_0px_#000000] dark:hover:shadow-[6px_6px_0px_0px_#ffffff]">
                    <div class="flex items-center justify-between">
                        <div class="text-xs font-black uppercase tracking-wider text-black dark:text-slate-200">Total Obrolan</div>
                        <div class="p-1.5 border-2 border-black dark:border-white bg-white dark:bg-slate-900 text-black dark:text-white">
                            💬
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="text-3xl font-black text-black dark:text-white tracking-tight">{{ $totalChatRooms }} Percakapan</div>
                        <div class="text-[10px] text-slate-800 dark:text-slate-300 font-extrabold mt-1">Seluruh log percakapan reservasi Anda</div>
                    </div>
                </div>

                <!-- Card 2: Obrolan Aktif -->
                <div class="bg-yellow-300 dark:bg-yellow-950 p-5 border-4 border-black dark:border-white shadow-[4px_4px_0px_0px_#000000] dark:shadow-[4px_4px_0px_0px_#ffffff] transition-all duration-200 hover:translate-x-[-2px] hover:translate-y-[-2px] hover:shadow-[6px_6px_0px_0px_#000000] dark:hover:shadow-[6px_6px_0px_0px_#ffffff]">
                    <div class="flex items-center justify-between">
                        <div class="text-xs font-black uppercase tracking-wider text-black dark:text-slate-200">Obrolan Aktif</div>
                        <div class="p-1.5 border-2 border-black dark:border-white bg-white dark:bg-slate-900 text-black dark:text-white">
                            ⚡
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="text-3xl font-black text-black dark:text-white tracking-tight">{{ $activeChatRooms }} Aktif</div>
                        <div class="text-[10px] text-slate-800 dark:text-slate-300 font-extrabold mt-1">Percakapan dengan status reservasi aktif</div>
                    </div>
                </div>
            </div>

            <!-- Chat Room List Card -->
            <div class="admin-card">
                <div class="mb-6 border-b-4 border-black dark:border-white pb-4">
                    <h3 class="text-base font-black text-slate-900 dark:text-slate-100 uppercase tracking-wide">Daftar Percakapan Reservasi</h3>
                    <p class="admin-subtitle">Hubungi pengelola kost secara langsung terkait detail reservasi Anda.</p>
                </div>

                <div class="admin-table-container">
                    <table class="admin-table">
                        <thead class="admin-table-thead">
                            <tr>
                                <th class="admin-table-th">Order ID</th>
                                <th class="admin-table-th">Kamar</th>
                                <th class="admin-table-th">Status Reservasi</th>
                                <th class="admin-table-th">Pesan Terakhir</th>
                                <th class="admin-table-th">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="admin-table-tbody">
                            @forelse($reservasi as $item)
                                <tr class="admin-table-tr">
                                    <td class="admin-table-td text-slate-500 dark:text-slate-400 font-bold">
                                        {{ $item->order_id ?? 'RSV-' . $item->id }}
                                    </td>
                                    <td class="admin-table-td text-slate-700 dark:text-slate-300 font-bold">
                                        Kamar {{ $item->kamar?->nomor_kamar ?? '-' }}
                                    </td>
                                    <td class="admin-table-td">
                                        <span class="admin-badge {{ $item->status_badge_class }}">
                                            {{ strtoupper($item->status) }}
                                        </span>
                                    </td>
                                    <td class="admin-table-td text-slate-600 dark:text-slate-400 font-medium">
                                        @if($lastMsg = $item->latestChatMessage)
                                            <div class="truncate max-w-[200px] sm:max-w-md">
                                                <span class="font-black text-black dark:text-white text-[10px] uppercase mr-1 bg-slate-200 dark:bg-slate-700 px-1 border border-black dark:border-white">
                                                    {{ $lastMsg->sender?->role === 'admin' ? 'Admin' : 'Anda' }}:
                                                </span>
                                                <span class="text-slate-700 dark:text-slate-300 text-xs">{{ $lastMsg->message }}</span>
                                            </div>
                                            <div class="text-[9px] text-slate-400 dark:text-slate-500 font-bold mt-1">
                                                {{ $lastMsg->created_at?->diffForHumans() ?? '-' }}
                                            </div>
                                        @else
                                            <span class="italic text-slate-400 dark:text-slate-500 text-xs">Belum ada obrolan</span>
                                        @endif
                                    </td>
                                    <td class="admin-table-td">
                                        @if(in_array($item->status, ['pending', 'dp', 'lunas']))
                                            <a href="{{ route('penyewa.reservasi.chat', $item->id) }}" class="admin-btn-primary py-1.5 px-4 text-xs">
                                                Buka Obrolan 💬
                                            </a>
                                        @else
                                            <a href="{{ route('penyewa.reservasi.chat', $item->id) }}" class="inline-flex items-center justify-center bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:text-slate-200 dark:hover:bg-slate-600 text-slate-900 font-extrabold py-1.5 px-4 text-xs border-2 border-black dark:border-white rounded-none shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,1)] hover:translate-x-[-1px] hover:translate-y-[-1px] hover:shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:hover:shadow-[3px_3px_0px_0px_rgba(255,255,255,1)] active:translate-x-[0px] active:translate-y-[0px] active:shadow-[1px_1px_0px_0px_rgba(0,0,0,1)] transition-all duration-150 gap-2 cursor-pointer">
                                                Lihat Obrolan 📖
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-slate-400 dark:text-slate-500 italic">
                                        Anda belum memiliki obrolan reservasi kamar.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
