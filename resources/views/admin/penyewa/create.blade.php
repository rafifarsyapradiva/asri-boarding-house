<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-xl text-black leading-tight uppercase tracking-wider">
            {{ __('Registrasi Penyewa Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="admin-card">
                <div class="text-gray-900 dark:text-gray-100">
                    
                    <div class="mb-8 flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-extrabold text-slate-800 dark:text-slate-100">Form Registrasi Penyewa</h3>
                            <p class="admin-subtitle">Masukkan data diri penyewa, wali/orang tua, serta detail kamar yang disewa.</p>
                        </div>
                        <a href="{{ route('admin.penyewa.index') }}" class="text-sm font-bold text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 transition duration-150">&larr; Kembali</a>
                    </div>

                    <!-- Flash Messages & Validation Errors -->
                    <x-flash-message />

                    <form action="{{ route('admin.penyewa.store') }}" method="POST" class="space-y-8" onsubmit="if(this.checkValidity()){ this.querySelector('button[type=submit]').disabled=true; this.querySelector('button[type=submit]').innerText='Memproses...'; }">
                        @csrf

                        <!-- Section 1: Data Akun & Diri Penyewa -->
                        <div class="border-b border-slate-100 dark:border-slate-700/50 pb-6">
                            <h4 class="admin-section-title">1. Data Diri Penyewa</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Nama -->
                                <div>
                                    <label for="nama" class="admin-label">Nama Lengkap</label>
                                    <input id="nama" name="nama" type="text" class="admin-input" value="{{ old('nama') }}" required autofocus />
                                </div>
                                <!-- NIK -->
                                <div>
                                    <label for="nik" class="admin-label">NIK (KTP - 16 Digit)</label>
                                    <input id="nik" name="nik" type="text" class="admin-input" value="{{ old('nik') }}" required maxlength="16" minlength="16" />
                                </div>
                                <!-- Email -->
                                <div>
                                    <label for="email" class="admin-label">Alamat Email</label>
                                    <input id="email" name="email" type="email" class="admin-input" value="{{ old('email') }}" required />
                                </div>
                                <!-- No HP -->
                                <div>
                                    <label for="no_hp" class="admin-label">Nomor HP / WhatsApp</label>
                                    <input id="no_hp" name="no_hp" type="text" class="admin-input" value="{{ old('no_hp') }}" placeholder="Contoh: 081234567890" required />
                                    <p class="text-[10px] text-slate-400 mt-1">Digunakan untuk mengirimkan kredensial login default via WhatsApp.</p>
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
                                    <input id="nama_wali" name="nama_wali" type="text" class="admin-input" value="{{ old('nama_wali') }}" required />
                                </div>
                                <!-- No Wali -->
                                <div>
                                    <label for="no_wali" class="admin-label">Nomor HP Wali</label>
                                    <input id="no_wali" name="no_wali" type="text" class="admin-input" value="{{ old('no_wali') }}" placeholder="Contoh: 081234567890" required />
                                    <p class="text-[10px] text-slate-400 mt-1">Wajib digunakan untuk notifikasi eskalasi keterlambatan pembayaran.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Section 3: Informasi Kamar & Sewa -->
                        <div class="pb-6">
                            <h4 class="admin-section-title">3. Detail Kamar & Sewa</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Pilih Kamar -->
                                <div>
                                    <label for="kamar_id" class="admin-label">Pilih Kamar (Yang Tersedia)</label>
                                    <select id="kamar_id" name="kamar_id" class="admin-select" required>
                                        <option value="">-- Pilih Kamar --</option>
                                        @foreach($kamar as $k)
                                            <option value="{{ $k->id }}" {{ old('kamar_id') == $k->id ? 'selected' : '' }}>
                                                Lantai {{ $k->lantai }} - Kamar {{ $k->nomor_kamar }} ({{ ucfirst($k->tipe) }} - Rp {{ number_format($k->harga_bulan, 0, ',', '.') }}/bln)
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <!-- Tanggal Masuk -->
                                <div>
                                    <label for="tanggal_masuk" class="admin-label">Tanggal Masuk</label>
                                    <input id="tanggal_masuk" name="tanggal_masuk" type="date" class="admin-input" value="{{ old('tanggal_masuk') }}" required />
                                </div>
                                <!-- Tipe Sewa -->
                                <div>
                                    <label for="tipe_sewa" class="admin-label">Tipe Sewa</label>
                                    <select id="tipe_sewa" name="tipe_sewa" class="admin-select" required>
                                        <option value="bulanan" {{ old('tipe_sewa') == 'bulanan' ? 'selected' : '' }}>Bulanan</option>
                                        <option value="mingguan" {{ old('tipe_sewa') == 'mingguan' ? 'selected' : '' }}>Mingguan</option>
                                        <option value="harian" {{ old('tipe_sewa') == 'harian' ? 'selected' : '' }}>Harian</option>
                                    </select>
                                </div>
                                <!-- Durasi -->
                                <div>
                                    <label for="durasi" class="admin-label">Durasi Sewa</label>
                                    <input id="durasi" name="durasi" type="number" class="admin-input" value="{{ old('durasi', 1) }}" min="1" required />
                                    <p class="text-[10px] text-slate-400 mt-1" id="durasi-help">Menentukan jumlah satuan tipe sewa (misal: 3 untuk 3 bulan).</p>
                                </div>
                                <!-- Harga Sewa per Bulan -->
                                <div>
                                    <label for="harga_sewa_display" class="admin-label">Harga Sewa per Bulan (Opsional)</label>
                                    <input type="hidden" name="harga_sewa" id="harga_sewa" value="{{ old('harga_sewa') }}">
                                    <input id="harga_sewa_display" type="text" class="admin-input rupiah-input" data-target="harga_sewa" value="{{ old('harga_sewa') }}" placeholder="Kosongkan untuk otomatis setara harga kamar" />
                                    <p class="text-[10px] text-slate-400 mt-1">Jika dikosongkan, default harga sewa disetarakan dengan kamar yang dipilih.</p>
                                </div>
                                <!-- Uang Jaminan (Deposit) -->
                                <div>
                                    <label for="deposit_display" class="admin-label">Uang Jaminan (Deposit) - Opsional</label>
                                    <input type="hidden" name="deposit" id="deposit" value="{{ old('deposit') }}">
                                    <input id="deposit_display" type="text" class="admin-input rupiah-input" data-target="deposit" value="{{ old('deposit') }}" placeholder="Kosongkan untuk otomatis setara 1 bulan sewa" />
                                    <p class="text-[10px] text-slate-400 mt-1">Jika dikosongkan, default deposit disetara harga sewa 1 bulan kamar yang dipilih.</p>
                                </div>
                                <!-- Catatan Tambahan -->
                                <div class="md:col-span-2">
                                    <label for="catatan" class="admin-label">Catatan Tambahan (Opsional)</label>
                                    <textarea id="catatan" name="catatan" class="admin-textarea">{{ old('catatan') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex justify-end gap-4 mt-8">
                            <a href="{{ route('admin.penyewa.index') }}" class="admin-btn-secondary">
                                Batal
                            </a>
                            <button type="submit" class="admin-btn-primary">
                                Daftarkan Penyewa
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
