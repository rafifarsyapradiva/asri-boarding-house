<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 w-full">
            <h2 class="font-black text-xl text-black leading-tight uppercase tracking-wider">
                {{ __('Peraturan & Tata Tertib') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12 bg-slate-50 dark:bg-slate-900 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <!-- Banner / Header Card -->
            <div class="bg-blue-300 dark:bg-blue-800 border-4 border-black dark:border-white p-8 md:p-10 text-black dark:text-white shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] dark:shadow-[6px_6px_0px_0px_rgba(255,255,255,1)] relative overflow-hidden">
                <div class="relative z-10 max-w-3xl">
                    <span class="px-3 py-1 bg-yellow-300 border-2 border-black dark:border-white text-xs font-black uppercase tracking-widest text-black">Panduan Penghuni</span>
                    <h1 class="text-2xl md:text-3xl font-black uppercase tracking-tight mt-4 mb-2">
                        Peraturan & Tata Tertib Kost Putri Asri Boarding House
                    </h1>
                    <p class="text-xs md:text-sm text-slate-800 dark:text-slate-200 leading-relaxed font-bold">
                        Demi kenyamanan, keamanan, dan keharmonisan bersama seluruh penghuni, mohon untuk memahami dan mematuhi tata tertib kost di bawah ini secara disiplin.
                    </p>
                </div>
            </div>

            <!-- Rules Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($peraturan as $p)
                    <div class="bg-white dark:bg-slate-800 border-4 border-black dark:border-white p-6 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,1)] hover:translate-x-[-2px] hover:translate-y-[-2px] hover:shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] dark:hover:shadow-[6px_6px_0px_0px_rgba(255,255,255,1)] transition-all duration-200 flex flex-col justify-between">
                        <div>
                            <div class="w-12 h-12 flex items-center justify-center mb-4 {{ $p->badge_color_class }}">
                                <x-icon :name="$p->ikon" class="w-6 h-6" />
                            </div>
                            <h3 class="text-base font-black text-slate-900 dark:text-slate-100 mb-2 uppercase tracking-wide">{{ $p->urutan }}. {{ $p->judul }}</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed font-semibold">
                                {{ $p->deskripsi }}
                            </p>
                        </div>
                    </div>
                @empty
                    <div class="col-span-1 md:col-span-2 lg:col-span-3 text-center py-12 text-slate-400 dark:text-slate-500 italic font-bold">
                        Belum ada peraturan kost yang ditambahkan oleh pengelola.
                    </div>
                @endforelse
            </div>

            <!-- Footer Note -->
            <div class="text-center pt-8 border-t-4 border-black dark:border-white">
                <p class="text-sm italic text-slate-700 dark:text-slate-300 font-extrabold">
                    Salam hangat, Manajemen Asri Boarding House.
                </p>
            </div>

        </div>
    </div>
</x-app-layout>
