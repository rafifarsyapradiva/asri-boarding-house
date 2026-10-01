## 4.2 Implementasi Sistem
Tahap implementasi merealisasikan rancangan sistem ke dalam kode program terstruktur berbasis framework Laravel 11. Implementasi berfokus pada ketahanan arsitektur, pemisahan logika bisnis yang tegas, keamanan data finansial, serta integrasi layanan eksternal.

### 4.2.1 Lingkungan Implementasi
Pengembangan dan pengoperasian sistem dibangun di atas spesifikasi lingkungan perangkat keras dan perangkat lunak yang terstandarisasi:
1. **Perangkat Keras (*Hardware*)**:
   * Lingkungan Pengembangan (*Development*): Laptop Workstation dengan spesifikasi Prosesor Multi-Core 2.3 GHz, RAM 16 GB DDR4, Penyimpanan Solid State Drive (SSD) NVMe 512 GB.
   * Lingkungan Produksi (*Production*): Cloud Shared Hosting Server Hostinger Enterprise yang berlokasi di Data Center Jakarta (Indonesia), ditenagai peladen web LiteSpeed Enterprise berspesifikasi 1 CPU Core vCPU, 1 GB RAM, dan penyimpanan berbasis Cloud NVMe berkecepatan tinggi.
2. **Perangkat Lunak (*Software Stack*)**:
   * Bahasa Pemrograman: PHP versi 8.2.12 dengan ekstensi `pdo_mysql`, `curl`, `openssl`, `mbstring`, `fileinfo`, dan `bcmath`.
   * Framework Backend: Laravel versi 11.x (arsitektur MVC modern).
   * Sistem Manajemen Basis Data: MySQL versi 8.0 dengan engine InnoDB, set karakter `utf8mb4`, dan kolasi `utf8mb4_unicode_ci`.
   * Antarmuka Frontend: Blade Templating Engine dipadukan dengan TailwindCSS versi 3.4 dan Alpine.js.
   * Pengelola Dependensi: Composer versi 2.7+ (backend) dan Node.js versi 18.x dengan npm (frontend asset bundling).
   * Bundler Aset: Vite versi 5.x.
3. **Layanan Pihak Ketiga (*Third-Party Cloud APIs*)**:
   * Gerbang Pembayaran: Midtrans Snap API v2 (Mode Sandbox & Mode Live Produksi).
   * Pesan Otomasi: Fonnte WhatsApp Gateway API v2.
   * Otentikasi Eksternal: Google Cloud Console OAuth 2.0 API via Laravel Socialite.
   * Layanan Surel: Hostinger SMTP Mail Server terenkripsi TLS pada port 587.

### 4.2.2 Perancangan Arsitektur Aplikasi
Sistem mengadopsi arsitektur tiga lapis (*3-Tier Architecture*) yang dipadukan dengan pola *Service Layer Decoupling* guna menghindari penumpukan kode pada pengontrol (*fat controllers*):
* **Presentation Tier**: Lapisan visual antarmuka pengguna dibangun menggunakan mesin templat Blade dan utilitas TailwindCSS. Seluruh komponen interaktif dirancang dengan bahasa desain Neo-Brutalisme terpadu, menerapkan kebijakan *Zero Native Browser Interaction* di mana kotak dialog peringatan bawaan peramban (`alert()` dan `confirm()`) digantikan oleh komponen dialog modal Neo-Brutalisme dinamis dan notifikasi toast mengambang.
* **Application Tier**: Menjadi pusat orkestrasi aturan bisnis kos. Pengontrol (*controller*) bertindak ramping hanya untuk memvalidasi permintaan HTTP dan mengembalikan respon, sementara pemrosesan logika bisnis didelegasikan kepada kelas layanan terisolasi pada direktori `app/Services/`:
  * `BillingService`: Mengelola kalkulasi tagihan bulanan massal, diskon durasi sewa tahunan, injeksi pelunasan uang muka (DP), dan evaluasi denda keterlambatan kalender.
  * `MidtransService`: Menangani pembentukan parameter transaksi Snap, pembangkitan *snap token*, dan verifikasi status transaksi.
  * `ReservasiService`: Menangani alur pemesanan unit kamar, perhitungan tanggal selesai sewa, dan penguncian jadwal hunian.
  * `TransisiPenyewaService`: Mengelola aktivasi reservasi terkonfirmasi menjadi penyewa aktif dalam transaksi atomik multi-tabel.
  * `FonnteService` & `NotifikasiService`: Mengisolasi integrasi komunikasi ke WhatsApp dan surel.
* **Data Tier**: Persistensi data dikelola oleh MySQL 8.x melalui Laravel Eloquent ORM. Seluruh manipulasi data keuangan dikawal oleh transaksi basis data atomik (`DB::transaction`) dan penguncian tingkat baris (*pessimistic row locking*) menggunakan metode `lockForUpdate()` untuk mencegah kondisi balapan data (*race conditions*).

Arsitektur ini turut diperkuat oleh pola *Event-Listener-Observer*: *Event* `PembayaranBerhasil` dipicu saat transaksi Midtrans terkonfirmasi lunas, `PenyewaObserver` secara otomatis mengunci kamar menjadi terisi saat penyewa aktif dan menjaga kamar tetap terkunci pasca-checkout, serta `FasilitasObserver` membersihkan cache katalog publik saat data fasilitas diperbarui administrator.

### 4.2.3 Implementasi Basis Data
Skema basis data direalisasikan ke dalam 22 berkas migrasi database Laravel. Tabel 4.2 merangkum struktur fungsional dari keseluruhan dua puluh dua tabel relasional yang menyusun sistem informasi Asri Boarding House.

**Tabel 4.2** Struktur dan Fungsi 22 Tabel Basis Data Sistem Asri Boarding House

| No | Nama Tabel | Deskripsi Fungsional Entitas | Relasi Kunci / Batasan Integritas |
| :---: | :--- | :--- | :--- |
| 1 | `users` | Akun pengguna sistem (admin dan penyewa) dilengkapi kolom virtual `active_email`, `active_no_hp`, dan `active_nik`. | Tabel master akun; tanpa foreign key keluar. |
| 2 | `kamar` | Data 32 unit kamar kost (nomor kamar, lantai, tipe, tarif sewa) didukung kolom virtual `active_nomor_kamar`. | Relasi M:N dengan `fasilitas` via tabel pivot `kamar_fasilitas`. |
| 3 | `fasilitas` | Data master sarana dan prasarana kamar kost (nama, ikon, status aktif). | Relasi M:N dengan entitas `kamar`. |
| 4 | `kamar_fasilitas` | Tabel pivot perantara relasi banyak-ke-banyak kamar dan fasilitas. | FK: `kamar_id` → `kamar(id)`, `fasilitas_id` → `fasilitas(id)`. |
| 5 | `penyewa` | Data profil kontrak hunian penghuni aktif (tipe sewa, durasi, harga aktif, data wali). | FK: `user_id` → `users(id)`, `kamar_id` → `kamar(id)`. |
| 6 | `tagihan` | Tagihan sewa bulanan dan tagihan sisa uang muka penghuni kost. | FK: `penyewa_id` → `penyewa(id)`. UNIQUE: `(penyewa_id, periode_bulan, periode_tahun)`. |
| 7 | `pembayaran` | Jejak riwayat mutasi pembayaran tagihan (via Midtrans Snap maupun tunai terkonfirmasi). | FK: `tagihan_id` → `tagihan(id)`, `dikonfirmasi_oleh` → `users(id)`. |
| 8 | `reservasi` | Data pemesanan unit kamar oleh calon penghuni sebelum masa tinggal aktif. | FK: `user_id` → `users(id)`, `kamar_id` → `kamar(id)`. |
| 9 | `chat_messages` | Percakapan interaktif antara calon penyewa dan admin saat reservasi berstatus *pending*. | FK: `reservasi_id` → `reservasi(id)`, `sender_id` → `users(id)`. |
| 10 | `guest_chat_threads` | Utas percakapan pengunjung publik tanpa login dengan token sesi terenkripsi SHA-256. | Tabel master obrolan tamu; persistensi via cookie/localStorage. |
| 11 | `guest_chat_messages` | Pesan obrolan tamu publik dalam utas `guest_chat_threads`. | FK: `guest_chat_thread_id` → `guest_chat_threads(id)` ON DELETE CASCADE. |
| 12 | `keluhan` | Laporan keluhan fasilitas rusak dari penyewa aktif disertai unggahan foto bukti fisik. | FK: `penyewa_id` → `penyewa(id)` ON DELETE CASCADE. |
| 13 | `pengeluaran` | Pencatatan kas keluar operasional kost (token listrik, air, kebersihan, pemeliharaan). | Tabel master keuangan kas operasional. |
| 14 | `log_notifikasi` | Jejak audit pengiriman notifikasi otomatis WhatsApp (Fonnte) dan surel SMTP. | FK: `penyewa_id` → `penyewa(id)`, `tagihan_id` → `tagihan(id)`. |
| 15 | `pengumuman` | Siaran pesan massal (*broadcast*) pengumuman operasional dari administrator. | Tabel master komunikasi terpusat. |
| 16 | `notifikasi_khusus` | Catatan log peristiwa penting sistem (*system activity audit logger*). | FK: `user_id` → `users(id)` ON DELETE CASCADE. |
| 17 | `customer_reviews` | Data ulasan dan testimoni pengalaman tinggal penghuni kamar kost. | Tabel master ulasan publik. |
| 18 | `faqs` | Pertanyaan dan jawaban yang sering diajukan pada halaman publik. | Tabel master konten tanya-jawab. |
| 19 | `peraturan` | Tata tertib dan peraturan tata kelola hunian Asri Boarding House. | Tabel master tata tertib. |
| 20 | `galleries` | Dokumentasi galeri foto properti gedung dan kamar kost. | Tabel master multimedia. |
| 21 | `settings` | Pengaturan konfigurasi aplikasi dinamis (nama kos, kontak WA, rekening, promo). | Tabel master konfigurasi sistem. |
| 22 | `whatsapp_clicks` | Metrik analitik pelacakan jumlah klik pada tombol WhatsApp melayang landing page. | Tabel pencatatan konversi pemasaran. |

*Sumber: Hasil rekayasa skema basis data penulis (2026)*

Sebagai penguatan integritas data, cuplikan berkas migrasi berikut menunjukkan penerapan *Virtual Generated Columns* untuk mencegah benturan *unique constraint* saat data dihapus lunak (*soft delete*):

```php
// Cuplikan Migrasi Tabel Users: Penerapan Virtual Generated Columns
Schema::create('users', function (Blueprint $table) {
    $table->id();
    $table->string('nama', 100);
    $table->string('email', 100);
    $table->string('no_hp', 20)->nullable();
    $table->string('nik', 16)->nullable();
    $table->softDeletes();
    $table->timestamps();

    // Kolom virtual yang hanya bernilai jika baris tidak di-softdelete
    $table->string('active_email')->virtualAs('CASE WHEN deleted_at IS NULL THEN email ELSE NULL END');
    $table->string('active_no_hp')->virtualAs('CASE WHEN deleted_at IS NULL THEN no_hp ELSE NULL END');
    $table->unique('active_email', 'uq_users_active_email');
    $table->unique('active_no_hp', 'uq_users_active_no_hp');
});
```

### 4.2.4 Implementasi Autentikasi dan Hak Akses
Sistem menegakkan isolasi peran pengguna secara berlapis:
1. **Pemisahan Tiga Portal Login**: Sistem mengisolasi antarmuka login ke dalam tiga rute berbeda: `/admin/login` untuk pengelola, `/penyewa/login` untuk penghuni aktif, dan `/reservasi/login` untuk calon penyewa baru. Setiap portal dikawal oleh middleware otorisasi `EnsureUserRole` yang segera membatalkan sesi dan mengembalikan respon HTTP 403 Forbidden jika pengguna mencoba melintasi batas portal yang bukan haknya.
2. **Integrasi Google OAuth 2.0 via Socialite**: Calon penyewa dapat masuk menggunakan akun Google resmi. Untuk menjamin kelengkapan data kontak, middleware `EnsureProfileIsComplete` dipasang sebagai penjaga gerbang (*interceptor*): saat calon penyewa baru berhasil login via Google dan kolom `no_hp` masih berformat sementara (`temp_...`), sistem secara otomatis mengarahkan navigasi ke `/profil/complete` guna mewajibkan pengisian nomor WhatsApp aktif sebelum diizinkan mengakses formulir pemesanan kamar.
3. **Mitigasi IDOR (*Insecure Direct Object Reference*)**: Akses terhadap entitas finansial privat seperti data invoice tagihan dan nota pembayaran dikunci pada tingkat pengontrol (*controller-level scoping*). Sistem mengikat kueri tagihan secara eksklusif ke instance pengguna yang terotentikasi aktif (`$request->user()->tenant->bills()->findOrFail($id)`). Upaya manipulasi ID tagihan pada URL oleh pengguna lain secara otomatis digagalkan dengan kode status HTTP 403.

### 4.2.5 Implementasi Fitur Calon Penyewa
Modul calon penyewa menyediakan fitur penelusuran unit kamar dan pemesanan secara mandiri:
1. **Katalog Kamar Dinamis Berstatus Real-Time**: Landing page menampilkan grid 32 unit kamar dengan penanda status dinamis. Kamar berstatus kosong menampilkan tombol "Pesan Unit" yang membuka formulir reservasi, sedangkan kamar terisi menyembunyikan formulir dan menampilkan tombol "Tanya WA" yang langsung membuka WhatsApp admin dengan format pesan yang telah disiapkan.
2. **Alur Pemesanan Bertahap (*5-Step Horizontal Stepper*)**: Calon penyewa diarahkan melalui lima tahapan pemesanan: (1) Verifikasi Unit Kamar, (2) Biodata Diri & NIK 16 Digit, (3) Pilihan Tipe Sewa (Harian/Mingguan/Bulanan) beserta kalkulasi diskon sewa tahunan otomatis, (4) Pilihan Skema Pembayaran (DP 30% atau Lunas 100%), dan (5) Pembayaran Online via Midtrans Snap.
3. **Kanal Komunikasi Ganda**: Disediakan *Guest Chat* publik tanpa login dengan persistensi token sesi di peramban, serta modul *Live Chat* pra-pembayaran yang aktif selama status reservasi *pending*. Utas percakapan ditarik secara berkala per 4 detik melalui AJAX polling yang dioptimalkan menggunakan teknik *eager loading* relasi `latestChatMessage` guna menghindari permasalahan kueri berulang (*N+1 queries problem*).

### 4.2.6 Implementasi Fitur Penyewa Aktif
Modul penyewa aktif pada rute `/penyewa/dashboard` menyediakan layanan mandiri (*self-service*) komprehensif bagi penghuni kamar:
1. **Dasbor Tagihan dan Pelunasan Mandiri**: Penyewa dapat melihat kartu ringkasan kontrak sewa dan kisi tagihan bulanan. Tombol "Bayar Sekarang" memunculkan popup modal Midtrans Snap v2 untuk pelunasan secara daring melalui Bank BCA Virtual Account.
2. **Kuitansi Pembayaran Digital Format A5 (*Zero Server Overhead*)**: Bukti pembayaran resmi format A5 berstempel digital dicetak dan diunduh langsung di sisi peramban klien menggunakan pustaka `html2pdf.js`. Implementasi ini mengeliminasi kebutuhan kompilasi PDF di peladen serta mengurangi kebutuhan ruang penyimpanan pada peladen hosting.
3. **Modul Pengaduan Keluhan Fasilitas Rusak**: Penyewa dapat melaporkan kerusakan sarana kamar (seperti lampu mati atau kran air bocor) secara terstruktur melalui formulir keluhan disertai unggahan foto bukti fisik. Sistem memvalidasi ekstensi berkas (.jpg, .jpeg, .png) dan membatasi ukuran berkas maksimal 2 MB.
4. **Pemulihan Kata Sandi Mandiri**: Penyewa yang lupa kata sandi dapat meminta tautan reset kata sandi terenkripsi melalui rute `/penyewa/password/reset` yang dikirimkan secara otomatis ke alamat surel terdaftar melalui protokol SMTP.

### 4.2.7 Implementasi Fitur Admin
Panel kontrol administrator pada rute `/admin/dashboard` mengadopsi tata letak modern bernuansa *OLED Black Dark Mode* untuk mencegah kelelahan mata administrator (Bapak Asep, usia 48 tahun) saat memantau operasional dalam durasi lama:
1. **Dasbor Statistik dan Akuntansi Mikro (*Micro-Accounting*)**: Tiga kartu ringkasan keuangan utama (Total Pemasukan, Total Pengeluaran, dan Laba Bersih) dikalkulasi secara real-time melalui kueri agregasi basis data, menggantikan pencatatan buku besar konvensional guna mengamankan kapasitas perputaran pendapatan bruto maksimum kos sebesar Rp28.500.000 per bulan dari risiko salah hitung.
2. **Pendaftaran Penyewa Baru Jalur Tamu Datang Langsung (*Walk-in*)**: Memfasilitasi pendaftaran manual bagi calon penghuni yang datang langsung ke lokasi kost tanpa reservasi web. Administrator menginput biodata, memilih unit kamar kosong, mencatat uang jaminan deposit sewa, dan mengunggah bukti setoran tunai/transfer.
3. **Konfirmasi Pembayaran Kas/Tunai**: Administrator dapat mengubah status tagihan menjadi lunas dengan satu kali klik melalui tombol "Konfirmasi Tunai" pada menu tagihan, yang secara otomatis mencatat mutasi pemasukan kas dan menerbitkan kuitansi resmi.
4. **Kebijakan Isolasi Kamar Pasca-Checkout**: Ketika penyewa menyelesaikan masa tinggal (*checkout*), sistem secara sengaja tidak mengubah status kamar menjadi tersedia; kamar tetap berstatus terkunci merah `terisi` hingga administrator melakukan pemeriksaan fisik kamar secara langsung dan menekan tombol pelepasan kamar menjadi `tersedia`. Kebijakan ini berhasil mencegah terjadinya pemesanan ganda (*double booking*) pada kamar yang belum siap huni.
5. **Kalender Kontrol Visual Hunian (UC-19) & Broadcast WhatsApp (UC-20)**: Administrator dapat memantau jadwal kedatangan, durasi sewa, dan tanggal kepulangan seluruh 32 unit kamar dalam tampilan kalender visual interaktif, serta menyiarkan pengumuman massal atau darurat ke nomor WhatsApp seluruh penghuni aktif dalam satu klik.

### 4.2.8 Implementasi Integrasi Payment Gateway
Integrasi gerbang pembayaran Midtrans Snap API v2 dibangun secara modular melalui kelas `MidtransService` dan dua pengendali webhook terpisah:
1. **Pembangkitan Snap Token Transaksi**: Sistem membangkitkan parameter transaksi aman (`order_id`, `gross_amount`, `customer_details`, dan batas kedaluwarsa 24 jam) ke Midtrans Cloud, kemudian menerima *snap token* yang dirender pada antarmuka pengguna dalam bentuk pop-up modal.
2. **Pemisahan Jalur Webhook Callback**: Penanganan callback dibagi menjadi dua pengontrol terisolasi: `MidtransCallbackController` yang menangani tagihan bulanan penyewa aktif, serta `MidtransReservasiCallbackController` yang menangani reservasi awal calon penyewa.
3. **Verifikasi Keamanan Tanda Tangan Digital SHA-512**: Setiap permintaan notifikasi webhook yang masuk diverifikasi keasliannya dengan mencocokkan signature key SHA-512 yang dihitung secara matematis di sisi peladen:
   $$\text{Signature} = \text{SHA512}(\text{order\_id} + \text{status\_code} + \text{gross\_amount} + \text{ServerKey})$$
   Jika signature tidak cocok, permintaan segera ditolak dengan kode status HTTP 403 Forbidden guna menangkal serangan manipulasi data transaksi (*fraud spoofing*).
4. **Penanganan Idempotensi Transaksi**: Kueri pembaruan status pembayaran tagihan dibungkus dalam blok `DB::transaction` dengan instruksi penguncian baris `lockForUpdate()`. Jika notifikasi callback dengan status `settlement` diterima lebih dari satu kali untuk `order_id` yang sama, sistem mendeteksi bahwa tagihan telah berstatus lunas dan mengabaikan eksekusi pencatatan kas kedua, menjamin mutasi saldo kas tidak tercatat ganda.

### 4.2.9 Implementasi Notifikasi Email SMTP
Layanan surel diintegrasikan menggunakan driver SMTP standar Laravel yang terhubung ke server surat Hostinger pada port aman 587 dengan enkripsi Transport Layer Security (TLS):
* Kelas Mailable `ResetPasswordNotification` menangani pengiriman tautan token pemulihan kata sandi akun penyewa dengan batas waktu kedaluwarsa token selama 60 menit.
* Pengiriman surel diproses melalui antrean latar belakang (*Laravel Queue*) agar latensi jaringan peladen surat tidak menghambat responsivitas interaksi pengguna pada peramban.

### 4.2.10 Implementasi Notifikasi WhatsApp FONNTE
Integrasi otomasi pesan WhatsApp diimplementasikan melalui kelas `FonnteService` yang berkomunikasi dengan RESTful API Gateway Fonnte:
* **Pengiriman Invoice Bulanan Otomatis**: Dikirimkan setiap tanggal 1 awal bulan berisikan rincian tagihan pokok dan batas jatuh tempo tanggal 10.
* **Pengingat Jatuh Tempo (*Payment Reminder*)**: Dikirimkan secara persuasif menjelang dan setelah tanggal 10 bagi penyewa yang belum menyelesaikan kewajiban sewa.
* **Notifikasi Denda Flat Kalender 5%**: Dikirimkan secara otomatis pada tanggal 1 awal bulan berikutnya saat tagihan bulan lalu resmi menyeberang bulan kalender.
* **Eskalasi Penunggakan ke Nomor Wali**: Pesan otomatis dikirimkan ke nomor WhatsApp wali/orang tua penyewa ketika penunggakan tagihan memasuki bulan kalender kedua.
* **Audit Trail Notifikasi**: Setiap eksekusi pengiriman pesan dicatat pada tabel `log_notifikasi` berisikan waktu kirim, ID penerima, isi pesan, serta status terkirim (*success*) atau gagal (*failed*) untuk memudahkan pemantauan operasional.
