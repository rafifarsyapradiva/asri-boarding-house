<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-xl text-black leading-tight uppercase tracking-wider">
            {{ __('Pengaturan Konten Landing Page') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Alert Notifications -->
            @if(session('success'))
                <div class="p-4 bg-emerald-400 dark:bg-emerald-800 border-4 border-black dark:border-white text-black dark:text-white font-extrabold shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,1)]">
                    <div class="flex items-center">
                        <span class="mr-2">✅</span>
                        <span class="font-semibold text-sm">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 bg-red-400 dark:bg-red-800 border-4 border-black dark:border-white text-black dark:text-white font-extrabold shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,1)]">
                    <div class="flex items-center">
                        <span class="mr-2">❌</span>
                        <span class="font-semibold text-sm">{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-6" enctype="multipart/form-data">
                @csrf

                <!-- Section 1: Branding -->
                <div class="admin-card">
                    <div class="mb-6 border-b-4 border-black dark:border-white pb-4">
                        <h3 class="text-lg font-extrabold text-slate-800 dark:text-slate-100 flex items-center gap-2">
                            <span>🏡</span> Identitas & Branding Kost
                        </h3>
                        <p class="admin-subtitle">Atur nama brand dan ikon logo kost Anda yang tampil di navbar & footer.</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="md:col-span-2">
                            <label for="logo_text" class="admin-label">Nama Brand / Logo Kost <span class="text-red-500">*</span></label>
                            <input type="text" id="logo_text" name="logo_text" value="{{ old('logo_text', $settings['logo_text'] ?? '') }}" required class="admin-input @error('logo_text') !border-red-500 @enderror">
                            @error('logo_text')
                                <span class="text-red-500 text-xs mt-1.5 block font-bold">⚠️ {{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label for="logo_icon" class="admin-label">Ikon / Emoji Logo <span class="text-red-500">*</span></label>
                            <input type="text" id="logo_icon" name="logo_icon" value="{{ old('logo_icon', $settings['logo_icon'] ?? '') }}" required class="admin-input @error('logo_icon') !border-red-500 @enderror" placeholder="Contoh: 🏡 atau 🏢">
                            @error('logo_icon')
                                <span class="text-red-500 text-xs mt-1.5 block font-bold">⚠️ {{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Section 2: Hero Banner -->
                <div class="admin-card">
                    <div class="mb-6 border-b-4 border-black dark:border-white pb-4">
                        <h3 class="text-lg font-extrabold text-slate-800 dark:text-slate-100 flex items-center gap-2">
                            <span>✨</span> Hero Banner Section
                        </h3>
                        <p class="admin-subtitle">Atur kalimat promosi utama, judul, dan deskripsi singkat pada bagian atas landing page.</p>
                    </div>

                    <div class="space-y-6">
                        <div>
                            <label for="hero_tagline" class="admin-label">Tagline Banner Promosi <span class="text-red-500">*</span></label>
                            <input type="text" id="hero_tagline" name="hero_tagline" value="{{ old('hero_tagline', $settings['hero_tagline'] ?? '') }}" required class="admin-input @error('hero_tagline') !border-red-500 @enderror">
                            @error('hero_tagline')
                                <span class="text-red-500 text-xs mt-1.5 block font-bold">⚠️ {{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label for="hero_title" class="admin-label">Judul Utama (Header Title) <span class="text-red-500">*</span></label>
                            <input type="text" id="hero_title" name="hero_title" value="{{ old('hero_title', $settings['hero_title'] ?? '') }}" required class="admin-input @error('hero_title') !border-red-500 @enderror">
                            @error('hero_title')
                                <span class="text-red-500 text-xs mt-1.5 block font-bold">⚠️ {{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label for="hero_description" class="admin-label">Deskripsi Singkat Hunian <span class="text-red-500">*</span></label>
                            <textarea id="hero_description" name="hero_description" required class="admin-textarea @error('hero_description') !border-red-500 @enderror !h-28">{{ old('hero_description', $settings['hero_description'] ?? '') }}</textarea>
                            @error('hero_description')
                                <span class="text-red-500 text-xs mt-1.5 block font-bold">⚠️ {{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label for="hero_image" class="admin-label">Foto Background Hero (Faint/Samar)</label>
                            @if(!empty($settings['hero_image']))
                                <div class="mb-4">
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">Preview Saat Ini:</p>
                                    <div class="h-28 w-56 border-2 border-black dark:border-white overflow-hidden bg-yellow-50">
                                        @php
                                            $heroImg = $settings['hero_image'];
                                            $heroUrl = str_starts_with($heroImg, 'http') ? $heroImg : asset('storage/' . ltrim($heroImg, '/'));
                                        @endphp
                                        <img src="{{ $heroUrl }}" alt="Hero Background Preview" class="w-full h-full object-cover">
                                    </div>
                                </div>
                            @endif
                            <input type="file" id="hero_image" name="hero_image" accept="image/jpeg,image/png,image/jpg" class="admin-input !p-2 @error('hero_image') !border-red-500 @enderror">
                            <p class="text-xs text-slate-400 dark:text-slate-500 mt-2">Format: JPG, JPEG, PNG (Maksimal 2MB). Gambar ini akan dirender secara samar di latar belakang bagian hero.</p>
                            @error('hero_image')
                                <span class="text-red-500 text-xs mt-1.5 block font-bold">⚠️ {{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Section 3: Kontak Info -->
                <div class="admin-card">
                    <div class="mb-6 border-b-4 border-black dark:border-white pb-4">
                        <h3 class="text-lg font-extrabold text-slate-800 dark:text-slate-100 flex items-center gap-2">
                            <span>📞</span> Informasi Kontak Kost
                        </h3>
                        <p class="admin-subtitle">Kontak resmi yang akan dihubungi penyewa atau calon penyewa (tampil di footer & tombol WA).</p>
                    </div>

                    <div class="space-y-6">
                        <div>
                            <label for="contact_address" class="admin-label">Alamat Lengkap Kost <span class="text-red-500">*</span></label>
                            <textarea id="contact_address" name="contact_address" required class="admin-textarea @error('contact_address') !border-red-500 @enderror !h-24">{{ old('contact_address', $settings['contact_address'] ?? '') }}</textarea>
                            @error('contact_address')
                                <span class="text-red-500 text-xs mt-1.5 block font-bold">⚠️ {{ $message }}</span>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="contact_whatsapp" class="admin-label">Nomor WhatsApp Admin (Indonesia) <span class="text-red-500">*</span></label>
                                <input type="text" id="contact_whatsapp" name="contact_whatsapp" value="{{ old('contact_whatsapp', $settings['contact_whatsapp'] ?? '') }}" required placeholder="Contoh: 0812XXXXXXXX atau 62812XXXXXXXX" class="admin-input @error('contact_whatsapp') !border-red-500 @enderror">
                                @error('contact_whatsapp')
                                    <span class="text-red-500 text-xs mt-1.5 block font-bold">⚠️ {{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label for="contact_email" class="admin-label">Email Hubungi Kami <span class="text-red-500">*</span></label>
                                <input type="email" id="contact_email" name="contact_email" value="{{ old('contact_email', $settings['contact_email'] ?? '') }}" required class="admin-input @error('contact_email') !border-red-500 @enderror">
                                @error('contact_email')
                                    <span class="text-red-500 text-xs mt-1.5 block font-bold">⚠️ {{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 4: Google Maps Integration -->
                <div class="admin-card">
                    <div class="mb-6 border-b-4 border-black dark:border-white pb-4">
                        <h3 class="text-lg font-extrabold text-slate-800 dark:text-slate-100 flex items-center gap-2">
                            <span>📍</span> Integrasi Google Maps
                        </h3>
                        <p class="admin-subtitle">Masukkan kode HTML iframe Embed Google Maps untuk menampilkan lokasi kost Anda di Landing Page.</p>
                    </div>

                    <div class="space-y-6">
                        <div>
                            <label for="google_maps_embed" class="admin-label">Kode Iframe Google Maps (Embed HTML) <span class="text-red-500">*</span></label>
                            <textarea id="google_maps_embed" name="google_maps_embed" required class="admin-textarea @error('google_maps_embed') !border-red-500 @enderror !h-28 font-mono text-xs">{{ old('google_maps_embed', $settings['google_maps_embed'] ?? '') }}</textarea>
                            <p class="text-xs text-slate-400 dark:text-slate-500 mt-2">Dapatkan kode ini dari Google Maps -> Bagikan (Share) -> Sematkan peta (Embed a map) -> Salin HTML.</p>
                            @error('google_maps_embed')
                                <span class="text-red-500 text-xs mt-1.5 block font-bold">⚠️ {{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Section 5: Rekening Pembayaran Transfer Manual -->
                <div class="admin-card">
                    <div class="mb-6 border-b-4 border-black dark:border-white pb-4">
                        <h3 class="text-lg font-extrabold text-slate-800 dark:text-slate-100 flex items-center gap-2">
                            <span>💳</span> Rekening Pembayaran Transfer Manual
                        </h3>
                        <p class="admin-subtitle">Atur detail rekening bank untuk menerima pembayaran transfer manual dari penyewa.</p>
                    </div>

                    <div class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label for="bank_name" class="admin-label">Nama Bank <span class="text-red-500">*</span></label>
                                <input type="text" id="bank_name" name="bank_name" value="{{ old('bank_name', $settings['bank_name'] ?? '') }}" required placeholder="Contoh: Bank Mandiri, BCA, dll" class="admin-input @error('bank_name') !border-red-500 @enderror">
                                @error('bank_name')
                                    <span class="text-red-500 text-xs mt-1.5 block font-bold">⚠️ {{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label for="bank_account_number" class="admin-label">Nomor Rekening <span class="text-red-500">*</span></label>
                                <input type="text" id="bank_account_number" name="bank_account_number" value="{{ old('bank_account_number', $settings['bank_account_number'] ?? '') }}" required placeholder="Contoh: 123-456-7890" class="admin-input @error('bank_account_number') !border-red-500 @enderror">
                                @error('bank_account_number')
                                    <span class="text-red-500 text-xs mt-1.5 block font-bold">⚠️ {{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label for="bank_account_owner" class="admin-label">Nama Pemilik Rekening (Kost) <span class="text-red-500">*</span></label>
                                <input type="text" id="bank_account_owner" name="bank_account_owner" value="{{ old('bank_account_owner', $settings['bank_account_owner'] ?? '') }}" required placeholder="Contoh: Asri Boarding House" class="admin-input @error('bank_account_owner') !border-red-500 @enderror">
                                @error('bank_account_owner')
                                    <span class="text-red-500 text-xs mt-1.5 block font-bold">⚠️ {{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 6: Paket Promo & Diskon Kamar -->
                <div class="admin-card">
                    <div class="mb-6 border-b-4 border-black dark:border-white pb-4">
                        <h3 class="text-lg font-extrabold text-slate-800 dark:text-slate-100 flex items-center gap-2">
                            <span>🎁</span> Paket Promo & Diskon Kamar (Marketing)
                        </h3>
                        <p class="admin-subtitle">Konfigurasikan 3 paket penawaran khusus yang akan tampil sebagai kotak promo di halaman tipe kamar.</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8 border-b-2 border-dashed border-gray-300 dark:border-slate-700 pb-6">
                        <div>
                            <label for="promo_section_title" class="admin-label">Judul Bagian Promo <span class="text-red-500">*</span></label>
                            <input type="text" id="promo_section_title" name="promo_section_title" value="{{ old('promo_section_title', $settings['promo_section_title'] ?? '') }}" required class="admin-input @error('promo_section_title') !border-red-500 @enderror" placeholder="Contoh: Paket Promo Spesial">
                            @error('promo_section_title')
                                <span class="text-red-500 text-xs mt-1.5 block font-bold">⚠️ {{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label for="promo_section_subtitle" class="admin-label">Subjudul Bagian Promo <span class="text-red-500">*</span></label>
                            <input type="text" id="promo_section_subtitle" name="promo_section_subtitle" value="{{ old('promo_section_subtitle', $settings['promo_section_subtitle'] ?? '') }}" required class="admin-input @error('promo_section_subtitle') !border-red-500 @enderror" placeholder="Contoh: Pilih paket sewa terbaik untuk hemat lebih banyak!">
                            @error('promo_section_subtitle')
                                <span class="text-red-500 text-xs mt-1.5 block font-bold">⚠️ {{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="space-y-10">
                        @for ($i = 1; $i <= 3; $i++)
                            <div class="border-4 border-black dark:border-white p-6 bg-yellow-50 dark:bg-slate-800 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,1)]">
                                <h4 class="text-md font-extrabold text-black dark:text-white uppercase tracking-wider mb-4 flex items-center gap-2">
                                    <span class="bg-black text-white dark:bg-white dark:text-black w-6 h-6 flex items-center justify-center rounded-full text-xs font-black">{{ $i }}</span>
                                    Konfigurasi Paket {{ $i }}
                                </h4>

                                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                                    <div class="md:col-span-2">
                                        <label for="promo_pkg{{ $i }}_name" class="admin-label">Nama Paket <span class="text-red-500">*</span></label>
                                        <input type="text" id="promo_pkg{{ $i }}_name" name="promo_pkg{{ $i }}_name" value="{{ old('promo_pkg' . $i . '_name', $settings['promo_pkg' . $i . '_name'] ?? '') }}" required class="admin-input @error('promo_pkg' . $i . '_name') !border-red-500 @enderror" placeholder="Contoh: Paket Hemat 3 Bulan">
                                        @error('promo_pkg' . $i . '_name')
                                            <span class="text-red-500 text-xs mt-1.5 block font-bold">⚠️ {{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="promo_pkg{{ $i }}_type" class="admin-label">Tipe Sewa <span class="text-red-500">*</span></label>
                                        <select id="promo_pkg{{ $i }}_type" name="promo_pkg{{ $i }}_type" required class="admin-select @error('promo_pkg' . $i . '_type') !border-red-500 @enderror">
                                            @php $currentType = old('promo_pkg' . $i . '_type', $settings['promo_pkg' . $i . '_type'] ?? 'bulanan'); @endphp
                                            <option value="harian" {{ $currentType === 'harian' ? 'selected' : '' }}>Harian</option>
                                            <option value="mingguan" {{ $currentType === 'mingguan' ? 'selected' : '' }}>Mingguan</option>
                                            <option value="bulanan" {{ $currentType === 'bulanan' ? 'selected' : '' }}>Bulanan</option>
                                        </select>
                                        @error('promo_pkg' . $i . '_type')
                                            <span class="text-red-500 text-xs mt-1.5 block font-bold">⚠️ {{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label for="promo_pkg{{ $i }}_duration" class="admin-label">
                                                Durasi <span class="text-red-500">*</span>
                                                <span id="promo_pkg{{ $i }}_limit_label" class="text-xs text-slate-500 dark:text-slate-400 font-normal italic ml-1"></span>
                                            </label>
                                            <input type="number" id="promo_pkg{{ $i }}_duration" name="promo_pkg{{ $i }}_duration" value="{{ old('promo_pkg' . $i . '_duration', $settings['promo_pkg' . $i . '_duration'] ?? 1) }}" required min="1" max="30" class="admin-input @error('promo_pkg' . $i . '_duration') !border-red-500 @enderror">
                                            @error('promo_pkg' . $i . '_duration')
                                                <span class="text-red-500 text-xs mt-1.5 block font-bold">⚠️ {{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div>
                                            <label for="promo_pkg{{ $i }}_discount" class="admin-label">Diskon (%) <span class="text-red-500">*</span></label>
                                            <input type="number" step="0.01" id="promo_pkg{{ $i }}_discount" name="promo_pkg{{ $i }}_discount" value="{{ old('promo_pkg' . $i . '_discount', $settings['promo_pkg' . $i . '_discount'] ?? 0) }}" required min="0" max="100" class="admin-input @error('promo_pkg' . $i . '_discount') !border-red-500 @enderror">
                                            @error('promo_pkg' . $i . '_discount')
                                                <span class="text-red-500 text-xs mt-1.5 block font-bold">⚠️ {{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <label for="promo_pkg{{ $i }}_desc" class="admin-label">Deskripsi Singkat Paket <span class="text-red-500">*</span></label>
                                    <textarea id="promo_pkg{{ $i }}_desc" name="promo_pkg{{ $i }}_desc" required class="admin-textarea @error('promo_pkg' . $i . '_desc') !border-red-500 @enderror !h-16" placeholder="Contoh: Sewa 12 bulan sekaligus, diskon 1 bulan sewa penuh.">{{ old('promo_pkg' . $i . '_desc', $settings['promo_pkg' . $i . '_desc'] ?? '') }}</textarea>
                                    @error('promo_pkg' . $i . '_desc')
                                        <span class="text-red-500 text-xs mt-1.5 block font-bold">⚠️ {{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        @endfor
                    </div>
                </div>

                <!-- Section 7: Tentang Kami -->
                <div class="admin-card">
                    <div class="mb-6 border-b-4 border-black dark:border-white pb-4">
                        <h3 class="text-lg font-extrabold text-slate-800 dark:text-slate-100 flex items-center gap-2">
                            <span>⚡</span> Halaman Profil Tentang Kami
                        </h3>
                        <p class="admin-subtitle">Atur judul, deskripsi, visi, dan misi yang tampil di halaman Tentang Kami.</p>
                    </div>

                    <div class="space-y-6">
                        <div>
                            <label for="about_title" class="admin-label">Judul Utama Halaman Tentang Kami <span class="text-red-500">*</span></label>
                            <input type="text" id="about_title" name="about_title" value="{{ old('about_title', $settings['about_title'] ?? '') }}" required class="admin-input @error('about_title') !border-red-500 @enderror" placeholder="Contoh: Tentang Kami">
                            @error('about_title')
                                <span class="text-red-500 text-xs mt-1.5 block font-bold">⚠️ {{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label for="about_description" class="admin-label">Deskripsi Profil Kost <span class="text-red-500">*</span></label>
                            <textarea id="about_description" name="about_description" required class="admin-textarea @error('about_description') !border-red-500 @enderror !h-28">{{ old('about_description', $settings['about_description'] ?? '') }}</textarea>
                            @error('about_description')
                                <span class="text-red-500 text-xs mt-1.5 block font-bold">⚠️ {{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label for="about_visi" class="admin-label">Visi Kami <span class="text-red-500">*</span></label>
                            <textarea id="about_visi" name="about_visi" required class="admin-textarea @error('about_visi') !border-red-500 @enderror !h-24">{{ old('about_visi', $settings['about_visi'] ?? '') }}</textarea>
                            @error('about_visi')
                                <span class="text-red-500 text-xs mt-1.5 block font-bold">⚠️ {{ $message }}</span>
                            @enderror
                        </div>

                        <div class="border-t-2 border-dashed border-gray-300 dark:border-slate-700 pt-4 space-y-4">
                            <h4 class="text-sm font-extrabold text-black dark:text-white uppercase tracking-wider">Misi Kami (4 Poin Utama)</h4>
                            
                            @for ($m = 1; $m <= 4; $m++)
                                <div>
                                    <label for="about_misi_{{ $m }}" class="admin-label">Misi Poin {{ $m }} <span class="text-red-500">*</span></label>
                                    <input type="text" id="about_misi_{{ $m }}" name="about_misi_{{ $m }}" value="{{ old('about_misi_' . $m, $settings['about_misi_' . $m] ?? '') }}" required class="admin-input @error('about_misi_' . $m) !border-red-500 @enderror">
                                    @error('about_misi_' . $m)
                                        <span class="text-red-500 text-xs mt-1.5 block font-bold">⚠️ {{ $message }}</span>
                                    @enderror
                                </div>
                            @endfor
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end pt-3">
                    <button type="submit" class="admin-btn-primary !px-8 !py-3">
                        💾 Simpan Semua Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const durationLimits = {
                'harian': { max: {{ config('reservasi.max_durasi.harian', 30) }}, label: 'hari' },
                'mingguan': { max: {{ config('reservasi.max_durasi.mingguan', 8) }}, label: 'minggu' },
                'bulanan': { max: {{ config('reservasi.max_durasi.bulanan', 12) }}, label: 'bulan' }
            };

            for (let i = 1; i <= 3; i++) {
                const typeSelect = document.getElementById(`promo_pkg${i}_type`);
                const durationInput = document.getElementById(`promo_pkg${i}_duration`);
                const limitLabel = document.getElementById(`promo_pkg${i}_limit_label`);

                if (typeSelect && durationInput) {
                    const updateLimit = () => {
                        const type = typeSelect.value;
                        const limit = durationLimits[type] || { max: 30, label: 'unit' };
                        
                        durationInput.max = limit.max;
                        
                        const currentVal = parseInt(durationInput.value, 10);
                        if (!isNaN(currentVal) && currentVal > limit.max) {
                            durationInput.value = limit.max;
                        }

                        if (limitLabel) {
                            limitLabel.textContent = `(Maks: ${limit.max} ${limit.label})`;
                        }
                    };

                    typeSelect.addEventListener('change', updateLimit);
                    updateLimit();
                }
            }
        });
    </script>
</x-app-layout>
