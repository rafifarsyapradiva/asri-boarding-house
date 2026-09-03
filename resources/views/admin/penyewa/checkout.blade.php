<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-xl text-black leading-tight uppercase tracking-wider">
            {{ __('Prosedur Checkout Penyewa') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="admin-card">
                <div class="text-gray-900 dark:text-gray-100">
                    
                    <div class="mb-8 flex justify-between items-center border-b border-slate-100 dark:border-slate-700/50 pb-4">
                        <div>
                            <h3 class="text-lg font-extrabold text-slate-800 dark:text-slate-100">Checkout Penyewa & Penyelesaian Finansial</h3>
                            <p class="admin-subtitle">Prosedur akhir masa sewa, penyesuaian uang jaminan (deposit), dan pelaporan kerusakan kamar.</p>
                        </div>
                        <a href="{{ route('admin.penyewa.show', $penyewa->id) }}" class="text-sm font-bold text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 transition duration-150">&larr; Batal & Kembali</a>
                    </div>

                    <!-- Display Server Success or Error Alerts -->
                    <x-flash-message />

                    <!-- Detail Penyewa Ringkas -->
                    <div class="mb-8 p-5 bg-slate-50 dark:bg-slate-900 border-2 border-black dark:border-white rounded-none shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,1)]">
                        <h4 class="font-bold text-xs text-blue-600 dark:text-blue-400 uppercase tracking-wider mb-3">Data Hunian Saat Ini</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm font-bold text-slate-800 dark:text-slate-100">
                            <div>
                                <span class="text-xs text-slate-400 font-medium block">Nama Penyewa</span>
                                {{ $penyewa->user->nama ?? '-' }}
                            </div>
                            <div>
                                <span class="text-xs text-slate-400 font-medium block">Nomor Kamar</span>
                                Kamar {{ $penyewa->kamar->nomor_kamar ?? '-' }} ({{ ucfirst($penyewa->kamar->tipe ?? '-') }})
                            </div>
                            <div>
                                <span class="text-xs text-slate-400 font-medium block">Jaminan Deposit Awal</span>
                                <span class="text-emerald-600 dark:text-emerald-400">Rp {{ number_format($penyewa->deposit, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Form Checkout -->
                    <form action="{{ route('admin.penyewa.checkout.process', $penyewa->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6" onsubmit="if(this.checkValidity()){ this.querySelector('button[type=submit]').disabled=true; this.querySelector('button[type=submit]').innerText='Memproses...'; }">
                        @csrf

                        <!-- Skenario Pilihan Kerusakan -->
                        <div>
                            <label class="admin-label block mb-2">Apakah terdapat kerusakan fasilitas kamar / denda keluar?</label>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <label class="flex items-center gap-3 p-4 bg-white dark:bg-slate-900 border-2 border-black dark:border-white cursor-pointer shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,1)]">
                                    <input type="radio" name="apakah_ada_kerusakan" value="0" {{ old('apakah_ada_kerusakan', '0') == '0' ? 'checked' : '' }} class="border-2 border-black focus:ring-0 text-blue-600 w-5 h-5" id="radio-no-damage">
                                    <div>
                                        <span class="block text-sm font-black text-slate-800 dark:text-white">Tidak Ada (Skenario A)</span>
                                        <span class="text-[10px] text-slate-400">Kamar steril. Uang jaminan deposit akan dikembalikan penuh (Rp {{ number_format($penyewa->deposit, 0, ',', '.') }}).</span>
                                    </div>
                                </label>

                                <label class="flex items-center gap-3 p-4 bg-white dark:bg-slate-900 border-2 border-black dark:border-white cursor-pointer shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,1)]">
                                    <input type="radio" name="apakah_ada_kerusakan" value="1" {{ old('apakah_ada_kerusakan') == '1' ? 'checked' : '' }} class="border-2 border-black focus:ring-0 text-blue-600 w-5 h-5" id="radio-has-damage">
                                    <div>
                                        <span class="block text-sm font-black text-slate-800 dark:text-white">Ada Kerusakan (Skenario B)</span>
                                        <span class="text-[10px] text-slate-400">Terdapat kerusakan fisik. Deposit akan dipotong untuk membiayai perbaikan.</span>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Dynamic Section for Repair Cost (Skenario B) -->
                        <div id="damage-details-section" class="{{ old('apakah_ada_kerusakan') == '1' ? 'block' : 'hidden' }} space-y-6 border-t-2 border-dashed border-slate-200 dark:border-slate-700 pt-6">
                            <h4 class="font-bold text-xs text-rose-600 dark:text-rose-400 uppercase tracking-wider">Detail Kerusakan & Potongan</h4>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Nominal Potongan -->
                                <div>
                                    <label for="nominal_potongan_display" class="admin-label">Nominal Potongan Biaya Perbaikan (Rp)</label>
                                    <input type="hidden" name="nominal_potongan" id="nominal_potongan" value="{{ old('nominal_potongan', 0) }}">
                                    <input id="nominal_potongan_display" type="text" class="admin-input rupiah-input" data-target="nominal_potongan" value="{{ old('nominal_potongan', 0) }}" placeholder="Contoh: 150.000" />
                                    <p class="text-[10px] text-slate-400 mt-1">Biaya perbaikan akan memotong deposit dan dicatat otomatis ke pengeluaran (kategori: perbaikan/fasilitas).</p>
                                </div>

                                <!-- Bukti Nota -->
                                <div>
                                    <label for="bukti_nota" class="admin-label">Unggah Foto Bukti Nota Perbaikan / Kerusakan</label>
                                    <input id="bukti_nota" name="bukti_nota" type="file" class="admin-input !p-1.5" accept="image/*" />
                                    <p class="text-[10px] text-slate-400 mt-1">Format gambar JPG, JPEG, PNG. Ukuran berkas maksimal 2MB.</p>
                                </div>
                            </div>

                            <!-- Keterangan Kerusakan -->
                            <div>
                                <label for="keterangan" class="admin-label">Keterangan / Rincian Kerusakan</label>
                                <textarea id="keterangan" name="keterangan" class="admin-textarea" placeholder="Contoh: Penggantian kunci pintu kamar yang patah dan cat ulang tembok tercoret.">{{ old('keterangan') }}</textarea>
                                <p class="text-[10px] text-slate-400 mt-1">Sebutkan secara mendalam item yang rusak agar laporan keuangan transparan.</p>
                            </div>
                        </div>

                        <!-- Important Warning Rule -->
                        <div class="p-4 bg-amber-50 dark:bg-amber-950/40 border-2 border-black dark:border-white text-xs font-bold text-amber-800 dark:text-amber-200 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,1)]">
                            <span class="block font-black text-sm uppercase tracking-wider mb-1">⚠️ PENTING (ATURAN KEAMANAN KAMAR KOST PUTRI):</span>
                            Setelah tombol checkout ditekan, status penyewa akan diubah menjadi <strong class="underline">Nonaktif</strong>. Status kamar terkait akan tetap dipertahankan sebagai <strong class="underline">Terisi</strong> di database. Admin wajib mengubah status kamar menjadi <strong class="underline">Tersedia</strong> secara MANUAL melalui halaman Manajemen Kamar setelah kamar dibersihkan dan siap dipublikasikan kembali.
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex justify-end gap-4 mt-8">
                            <a href="{{ route('admin.penyewa.show', $penyewa->id) }}" class="admin-btn-secondary">
                                Batal
                            </a>
                            <button type="submit" class="admin-btn-danger">
                                Konfirmasi Checkout
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <!-- Script for Dynamic Skenario B Form Toggle & Currency Format -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const radioNoDamage = document.getElementById('radio-no-damage');
            const radioHasDamage = document.getElementById('radio-has-damage');
            const damageSection = document.getElementById('damage-details-section');

            function toggleDamageSection() {
                if (radioHasDamage.checked) {
                    damageSection.classList.remove('hidden');
                } else {
                    damageSection.classList.add('hidden');
                }
            }

            radioNoDamage.addEventListener('change', toggleDamageSection);
            radioHasDamage.addEventListener('change', toggleDamageSection);

        });
    </script>
</x-app-layout>
