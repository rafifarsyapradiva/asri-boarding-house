<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-xl text-black leading-tight uppercase tracking-wider">
            {{ __('Manajemen FAQ') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="admin-card">
                <div class="text-gray-900 dark:text-gray-100">

                    <!-- Header Section -->
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
                        <div>
                            <h3 class="text-lg font-extrabold text-slate-800 dark:text-slate-100">Daftar FAQ (Frequently Asked Questions)</h3>
                            <p class="admin-subtitle">Kelola daftar pertanyaan dan jawaban yang sering diajukan oleh calon penyewa kost.</p>
                        </div>
                        <a href="{{ route('admin.faq.create') }}" class="admin-btn-primary" data-testid="faq-btn-create">
                            <span class="text-sm" aria-hidden="true">+</span> Tambah FAQ Baru
                        </a>
                    </div>

                    <!-- Search Bar -->
                    <div class="bg-slate-50 dark:bg-slate-900 border-4 border-black dark:border-white p-5 rounded-none mb-6 flex flex-wrap gap-4 items-center justify-between shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,1)]">
                        <form method="GET" action="{{ route('admin.faq.index') }}" class="flex flex-wrap gap-3 items-center w-full" data-testid="faq-search-form">
                            <div class="relative flex-grow max-w-md">
                                <label for="search-input" class="sr-only">Cari FAQ</label>
                                <input type="text" 
                                       name="search" 
                                       id="search-input" 
                                       class="admin-input !py-2 !pl-10 w-full" 
                                       placeholder="Cari pertanyaan, jawaban..."
                                       value="{{ request('search') }}"
                                       data-testid="faq-search-input">
                                <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none text-slate-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="flex gap-2">
                                <button type="submit" class="admin-btn-primary !py-2.5 !px-5" data-testid="faq-btn-search">
                                    Cari
                                </button>
                                @if(request('search'))
                                    <a href="{{ route('admin.faq.index') }}" class="admin-btn-secondary !py-2.5 !px-5" data-testid="faq-btn-reset">
                                        Reset
                                    </a>
                                @endif
                            </div>
                        </form>
                    </div>

                    <!-- Alert Notifications -->
                    @if(session('success'))
                        <div class="mb-6 p-4 bg-emerald-300 dark:bg-emerald-950 text-black dark:text-white border-4 border-black dark:border-white rounded-none shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,1)]" role="alert" data-testid="faq-alert-success">
                            <div class="flex items-center">
                                <span class="mr-2 font-black" aria-hidden="true">✅</span>
                                <span class="font-black text-sm">{{ session('success') }}</span>
                            </div>
                        </div>
                    @endif

                    <!-- FAQ Table Component -->
                    <div class="admin-table-container">
                        <table class="admin-table" data-testid="faq-table">
                            <thead class="admin-table-thead">
                                <tr>
                                    <th scope="col" class="admin-table-th">Pertanyaan</th>
                                    <th scope="col" class="admin-table-th">Jawaban</th>
                                    <th scope="col" class="admin-table-th text-center">Urutan</th>
                                    <th scope="col" class="admin-table-th text-center">Status Aktif</th>
                                    <th scope="col" class="admin-table-th">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="admin-table-tbody">
                                @forelse($faqs as $faq)
                                    <tr class="admin-table-tr" data-testid="faq-row-{{ $faq->id }}">
                                        <!-- Pertanyaan -->
                                        <td class="admin-table-td !whitespace-normal font-bold text-slate-800 dark:text-slate-100 max-w-[250px] break-words" title="{{ $faq->pertanyaan }}">
                                            {{ \Illuminate\Support\Str::limit($faq->pertanyaan ?? '-', 80) }}
                                        </td>
                                        <!-- Jawaban -->
                                        <td class="admin-table-td !whitespace-normal text-slate-500 dark:text-slate-400 font-medium max-w-[350px] break-words" title="{{ $faq->jawaban }}">
                                            {{ \Illuminate\Support\Str::limit($faq->jawaban ?? '-', 100) }}
                                        </td>
                                        <!-- Urutan -->
                                        <td class="admin-table-td text-center font-bold text-slate-700 dark:text-slate-300">
                                            {{ $faq->urutan ?? 0 }}
                                        </td>
                                        <!-- Status Aktif (Badge) -->
                                        <td class="admin-table-td text-center">
                                            @if($faq->is_active)
                                                <span class="admin-badge admin-badge-success" data-testid="faq-status-active">Aktif</span>
                                            @else
                                                <span class="admin-badge admin-badge-danger" data-testid="faq-status-inactive">Non-Aktif</span>
                                            @endif
                                        </td>
                                        <!-- Aksi -->
                                        <td class="admin-table-td">
                                            <div class="flex items-center gap-2">
                                                <a href="{{ route('admin.faq.edit', $faq->id) }}" 
                                                   class="inline-flex items-center px-2 py-1 bg-cyan-300 hover:bg-cyan-400 text-black border-2 border-black dark:border-white font-black text-xs uppercase shadow-[1.5px_1.5px_0px_0px_rgba(0,0,0,1)] dark:shadow-[1.5px_1.5px_0px_0px_rgba(255,255,255,1)] hover:translate-y-[-1px] transition-all cursor-pointer"
                                                   data-testid="faq-btn-edit-{{ $faq->id }}">
                                                    Edit
                                                </a>
                                                <form action="{{ route('admin.faq.destroy', $faq->id) }}" 
                                                      method="POST" 
                                                      class="inline" 
                                                      data-confirm="Apakah Anda yakin ingin menghapus FAQ ini?" 
                                                      data-title="Hapus FAQ" 
                                                      data-confirm-danger="true">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="inline-flex items-center px-2 py-1 bg-red-400 hover:bg-red-500 text-white border-2 border-black dark:border-white font-black text-xs uppercase shadow-[1.5px_1.5px_0px_0px_rgba(0,0,0,1)] dark:shadow-[1.5px_1.5px_0px_0px_rgba(255,255,255,1)] hover:translate-y-[-1px] transition-all cursor-pointer"
                                                            data-testid="faq-btn-delete-{{ $faq->id }}">
                                                        Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-8 text-center text-slate-400 dark:text-slate-500 italic" data-testid="faq-empty-state">
                                            Belum ada data FAQ yang terdaftar.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($faqs->hasPages())
                        <div class="mt-6" data-testid="faq-pagination">
                            {{ $faqs->links('vendor.pagination.neo-brutalist') }}
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
