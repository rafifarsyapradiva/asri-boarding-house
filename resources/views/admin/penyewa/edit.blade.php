<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-xl text-black leading-tight uppercase tracking-wider">
            {{ __('Edit Data Penyewa') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="admin-card">
                <div class="text-gray-900 dark:text-gray-100">
                    
                    <div class="mb-8 flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-extrabold text-slate-800 dark:text-slate-100">Form Edit Data Penyewa</h3>
                            <p class="admin-subtitle">Perbarui data diri, wali, atau alokasi kamar sewa untuk penyewa terpilih.</p>
                        </div>
                        <a href="{{ route('admin.penyewa.show', $penyewa->id) }}" class="text-sm font-bold text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 transition duration-150">&larr; Kembali ke Detail</a>
                    </div>

                    <!-- Flash Messages & Validation Errors -->
                    <x-flash-message />

                    <form action="{{ route('admin.penyewa.update', $penyewa->id) }}" method="POST" class="space-y-8" onsubmit="if(this.checkValidity()){ this.querySelector('button[type=submit]').disabled=true; this.querySelector('button[type=submit]').innerText='Memproses...'; }">
                        @csrf
                        @method('PUT')

                        <!-- Section 1: Data Akun & Diri Penyewa -->
                        <div class="border-b border-slate-100 dark:border-slate-700/50 pb-6">
                            <h4 class="admin-section-title">1. Data Diri Penyewa</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Nama -->
                                <div>
                                    <label for="nama" class="admin-label">Nama Lengkap</label>
                                    <input id="nama" name="nama" type="text" class="admin-input" value="{{ old('nama', $penyewa->user->nama ?? '') }}" required />
                                </div>
                                <!-- NIK -->
                                <div>
                                    <label for="nik" class="admin-label">NIK (KTP - 16 Digit)</label>
                                    <input id="nik" name="nik" type="text" class="admin-input" value="{{ old('nik', $penyewa->nik) }}" required maxlength="16" minlength="16" />
                                </div>
                                <!-- Email -->
                                <div>
                                    <label for="email" class="admin-label">Alamat Email</label>
                                    <input id="email" name="email" type="email" class="admin-input" value="{{ old('email', $penyewa->user->email ?? '') }}" required />
                                </div>
                                <!-- No HP -->
                                <div>
                                    <label for="no_hp" class="admin-label">Nomor HP / WhatsApp</label>
                                    <input id="no_hp" name="no_hp" type="text" class="admin-input" value="{{ old('no_hp', $penyewa->user->no_hp ?? '') }}" required />
                                </div>
                            </div>
                        </div>

                        <!-- Section 2: Informasi Wali / Orang Tua -->
                        <div class="border-b border-slate-100 dark:border-slate-700/50 pb-6">
                            <h4 class="admin-section-title">2. Data Wali / Orang Tua</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Nama Wali -->
                                <div>
                                    <label for="nama_wali" class="admin-label">Nama Wali / Orang Tua</label>
                                    <input id="nama_wali" name="nama_wali" type="text" class="admin-input" value="{{ old('nama_wali', $penyewa->nama_wali) }}" required />
                                </div>
                                <!-- No Wali -->
                                <div>
                                    <label for="no_wali" class="admin-label">Nomor HP Wali</label>
                                    <input id="no_wali" name="no_wali" type="text" class="admin-input" value="{{ old('no_wali', $penyewa->no_wali) }}" required />
                                </div>
                            </div>
                        </div>

                        <!-- Section 3: Informasi Kamar & Sewa -->
                        <div class="pb-6">
                            <h4 class="admin-section-title">3. Detail Kamar & Sewa</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Pilih Kamar -->
                                <div>
                                    <label for="kamar_id" class="admin-label">Pilih Kamar</label>
                                    <select id="kamar_id" name="kamar_id" class="admin-select" required>
                                        @foreach($kamar as $k)
                                            <option value="{{ $k->id }}" {{ old('kamar_id', $penyewa->kamar_id) == $k->id ? 'selected' : '' }}>
                                                Lantai {{ $k->lantai }} - Kamar {{ $k->nomor_kamar }} ({{ ucfirst($k->tipe) }} - Rp {{ number_format($k->harga_bulan, 0, ',', '.') }}/bln)
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <!-- Tanggal Masuk -->
                                <div>
                                    <label for="tanggal_masuk" class="admin-label">Tanggal Masuk</label>
                                    <input id="tanggal_masuk" name="tanggal_masuk" type="date" class="admin-input" value="{{ old('tanggal_masuk', $penyewa->tanggal_masuk) }}" required />
                                </div>
                                <!-- Tipe Sewa -->
                                <div>
                                    <label for="tipe_sewa" class="admin-label">Tipe Sewa</label>
                                    <select id="tipe_sewa" name="tipe_sewa" class="admin-select" required>
                                        <option value="bulanan" {{ old('tipe_sewa', $penyewa->tipe_sewa) == 'bulanan' ? 'selected' : '' }}>Bulanan</option>
                                        <option value="mingguan" {{ old('tipe_sewa', $penyewa->tipe_sewa) == 'mingguan' ? 'selected' : '' }}>Mingguan</option>
                                        <option value="harian" {{ old('tipe_sewa', $penyewa->tipe_sewa) == 'harian' ? 'selected' : '' }}>Harian</option>
                                    </select>
                                </div>
                                <!-- Durasi -->
                                <div>
                                    <label for="durasi" class="admin-label">Durasi Sewa</label>
                                    <input id="durasi" name="durasi" type="number" class="admin-input" value="{{ old('durasi', $penyewa->durasi) }}" min="1" required />
                                    <p class="text-[10px] text-slate-400 mt-1" id="durasi-help">Durasi sewa aktif.</p>
                                </div>
                                <!-- Harga Sewa per Bulan -->
                                <div>
                                    <label for="harga_sewa_display" class="admin-label">Harga Sewa per Bulan</label>
                                    <input type="hidden" name="harga_sewa" id="harga_sewa" value="{{ old('harga_sewa', intval($penyewa->harga_sewa)) }}">
                                    <input id="harga_sewa_display" type="text" class="admin-input rupiah-input" data-target="harga_sewa" value="{{ old('harga_sewa', intval($penyewa->harga_sewa)) }}" required />
                                    <p class="text-[10px] text-slate-400 mt-1">Harga sewa aktif yang digunakan untuk tagihan bulanan.</p>
                                </div>
                                <!-- Uang Jaminan (Deposit) -->
                                <div>
                                    <label for="deposit_display" class="admin-label">Uang Jaminan (Deposit)</label>
                                    <input type="hidden" name="deposit" id="deposit" value="{{ old('deposit', intval($penyewa->deposit)) }}">
                                    <input id="deposit_display" type="text" class="admin-input rupiah-input" data-target="deposit" value="{{ old('deposit', intval($penyewa->deposit)) }}" required />
                                    <p class="text-[10px] text-slate-400 mt-1">Uang jaminan sewa penyewa.</p>
                                </div>
                                <!-- Status Keaktifan -->
                                <div>
                                    <label for="status" class="admin-label">Status Penyewa</label>
                                    <select id="status" name="status" class="admin-select" required>
                                        <option value="aktif" {{ old('status', $penyewa->status) == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                        <option value="nonaktif" {{ old('status', $penyewa->status) == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                                    </select>
                                </div>
                                <!-- Catatan Tambahan -->
                                <div class="md:col-span-2">
                                    <label for="catatan" class="admin-label">Catatan Tambahan (Opsional)</label>
                                    <textarea id="catatan" name="catatan" class="admin-textarea">{{ old('catatan', $penyewa->catatan) }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex justify-end gap-4 mt-8">
                            <a href="{{ route('admin.penyewa.show', $penyewa->id) }}" class="admin-btn-secondary">
                                Batal
                            </a>
                            <button type="submit" class="admin-btn-primary">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <!-- Script to dynamically update duration input label help based on selected tipe sewa -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const tipeSewa = document.getElementById('tipe_sewa');
            const durasiHelp = document.getElementById('durasi-help');

            function updateHelpText() {
                const selected = tipeSewa.value;
                if (selected === 'bulanan') {
                    durasiHelp.textContent = 'Durasi dalam satuan bulan (Maksimal 12 bulan).';
                } else if (selected === 'mingguan') {
                    durasiHelp.textContent = 'Durasi dalam satuan minggu (Maksimal 8 minggu).';
                } else if (selected === 'harian') {
                    durasiHelp.textContent = 'Durasi dalam satuan hari (Maksimal 30 hari).';
                }
            }

            tipeSewa.addEventListener('change', updateHelpText);
            updateHelpText(); // run on load

        });
    </script>
</x-app-layout>
