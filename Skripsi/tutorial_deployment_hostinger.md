# PANDUAN DEPLOYMENT LARAVEL 12 KE HOSTINGER SHARED HOSTING
### Projek: Asri Boarding House (Sistem Informasi Kost)
---

Dokumen ini menjelaskan langkah-langkah detail untuk melakukan deployment projek **Asri Boarding House** dari lingkungan lokal (XAMPP/localhost) ke layanan **Hostinger Shared Hosting** (misal: Paket Premium/Business Hosting) untuk keperluan demo sidang skripsi.

Projek ini menggunakan beberapa fitur Laravel yang memerlukan konfigurasi khusus pada shared hosting:
1. **PHP ^8.2** (Persyaratan Laravel 12).
2. **Vite + Tailwind CSS** (Penyusunan asset front-end).
3. **Task Scheduling** (Untuk denda keterlambatan, pembuatan tagihan bulanan otomatis, pembatalan reservasi otomatis).
4. **Queue Database** (`QUEUE_CONNECTION=database` untuk pengiriman email & notifikasi secara background).
5. **Integrasi Pihak Ketiga** (Midtrans, Fonnte WhatsApp, & Gmail SMTP).

---

## DAFTAR ISI
1. [Persiapan di Komputer Lokal (Localhost)](#1-persiapan-di-komputer-lokal-localhost)
2. [Konfigurasi Awal di Hostinger hPanel](#2-konfigurasi-awal-di-hostinger-hpanel)
3. [Metode Upload & Penataan Direktori (Keamanan .env)](#3-metode-upload--penataan-direktori-keamanan-env)
4. [Konfigurasi Environment (`.env`) di Server](#4-konfigurasi-environment-env-di-server)
5. [Membuat Storage Symlink (Storage Link)](#5-membuat-storage-symlink-storage-link)
6. [Konfigurasi Cron Jobs & Queue Worker (SANGAT PENTING)](#6-konfigurasi-cron-jobs--queue-worker-sangat-penting)
7. [Tips Persiapan Demo Sidang Skripsi](#7-tips-persiapan-demo-sidang-skripsi)

---

## 1. PERSIAPAN DI KOMPUTER LOKAL (LOCALHOST)

Sebelum mengunggah file ke hosting, Anda harus mempersiapkan file-file projek agar siap berjalan di lingkungan produksi (*production*).

### A. Kompilasi Assets (CSS & JS)
Hostinger Shared Hosting umumnya tidak memiliki Node.js secara default atau membatasi penggunaan RAM sehingga Anda **tidak bisa** menjalankan `npm run build` di server hosting.
1. Buka terminal di folder projek lokal Anda (`c:\xampp\htdocs\asri-boarding-house`).
2. Jalankan perintah kompilasi asset:
   ```bash
   npm run build
   ```
3. Perintah ini akan menghasilkan folder `public/build` yang berisi file CSS dan JS terkompresi. Folder `public/build` ini **wajib** ikut diunggah ke hosting.

### B. Bersihkan Cache Lokal
Bersihkan semua log, sesi, dan cache konfigurasi agar tidak terbawa ke hosting yang dapat menyebabkan error pathing:
```bash
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
```

### C. Kompresi Projek menjadi File `.zip`
Kompres seluruh isi folder projek Anda ke dalam file `.zip` (misal: `project.zip`).
> [!IMPORTANT]
> **Opsi Pengemasan Folder `vendor` & `node_modules`:**
> - **Folder `node_modules`**: **JANGAN IKUT DI-ZIP** (ukurannya sangat besar dan tidak digunakan di server produksi setelah di-build).
> - **Folder `vendor`**: 
>   - **Opsi A (Direkomendasikan - Pakai SSH)**: Jangan sertakan folder `vendor`. Kita akan melakukan `composer install --no-dev -o` nanti lewat terminal SSH Hostinger. Ini membuat ukuran file ZIP jauh lebih kecil (~10 MB) dan proses upload lebih cepat.
>   - **Opsi B (Alternatif Tanpa SSH - Upload Langsung)**: Jika Anda tidak ingin menggunakan SSH, Anda **harus** menyertakan folder `vendor` di dalam file ZIP. Ukuran ZIP akan membengkak (~80-100 MB) namun proses instalasi di hosting lebih mudah karena tinggal ekstrak dan langsung jalan.

---

## 2. KONFIGURASI AWAL DI HOSTINGER HPANEL

### A. Atur Versi PHP
Laravel 12 membutuhkan minimal PHP versi 8.2.
1. Login ke **Hostinger hPanel**.
2. Masuk ke menu dashboard hosting Anda, cari menu **PHP Configuration** (Konfigurasi PHP) di kolom *Advanced*.
3. Pilih versi **PHP 8.2** atau **PHP 8.3** (disarankan PHP 8.2 agar sesuai dengan spesifikasi lokal).
4. Klik **Save** (Simpan).
5. Masuk ke tab **PHP Extensions**, pastikan ekstensi berikut telah tercentang/aktif: `ctype`, `curl`, `dom`, `fileinfo`, `filter`, `hash`, `mbstring`, `openssl`, `pcre`, `pdo`, `pdo_mysql`, `session`, `tokenizer`, `xml`, `zip`, `bcmath`.

### B. Buat Database MySQL Baru
1. Cari menu **MySQL Databases** di hPanel.
2. Buat database baru dengan memasukkan:
   - **MySQL Database name**: misal `asri_db` (nama akhir akan menjadi `uXXXXXX_asri_db`).
   - **MySQL Username**: misal `admin_asri` (nama akhir akan menjadi `uXXXXXX_admin_asri`).
   - **Password**: Buat password yang kuat dan catat!
3. Klik **Create** (Buat).

### C. Import Database
1. Setelah database dibuat, klik tombol **Enter phpMyAdmin** di sebelah database tersebut.
2. Di dalam phpMyAdmin Hostinger, pilih tab **Import**.
3. Pilih file database cadangan Anda yang ada di folder root projek: `asri_kost_db.sql`.
4. Klik **Go** / **Import** di bagian bawah dan tunggu sampai proses selesai.

---

## 3. METODE UPLOAD & PENATAAN DIREKTORI (KEAMANAN .env)

Secara bawaan, direktori publik Hostinger adalah `/home/uXXXXXX/domains/namadomain.com/public_html`.
> [!CAUTION]
> **Bahaya Menaruh Seluruh File Laravel di `public_html`:**
> Jika Anda mengunggah seluruh folder Laravel langsung di dalam `public_html`, file konfigurasi sensitif seperti `.env` (yang berisi password database, kunci Midtrans, dll.) dapat diakses oleh publik secara langsung melalui browser (contoh: `namadomain.com/.env`). Ini adalah celah keamanan fatal yang sering menjadi poin kritik dosen penguji sidang skripsi.

### Cara Penataan yang Aman (Metode Struktur Terpisah)

Mari kita pisahkan **Core Files Laravel** (kode program) dengan **Public Files** (file yang boleh diakses publik).

```
Struktur Direktori Server:
/home/uXXXXXX/
 ├── domains/
 │    └── namadomain.com/
 │         ├── asri-core/         <-- Letakkan semua file Laravel di sini (kecuali folder public)
 │         └── public_html/       <-- Letakkan ISI dari folder 'public' Laravel di sini
```

#### Langkah-langkah Penerapan:
1. Buka **File Manager** di Hostinger hPanel.
2. Masuk ke direktori domain Anda: `/domains/namadomain.com/`.
3. Buat folder baru dengan nama `asri-core` sejajar dengan folder `public_html`.
4. Upload file `project.zip` yang sudah Anda buat tadi ke dalam folder `asri-core`.
5. Ekstrak file `project.zip` di dalam folder `asri-core` tersebut.
6. Masuk ke folder `asri-core/public/`. Pilih **semua** file dan folder di dalamnya (termasuk `index.php`, `.htaccess`, folder `build`, dll.), lalu lakukan operasi **Move** (Pindah) ke folder utama **`public_html`** (satu tingkat di luar `asri-core`).
7. Sekarang, folder `asri-core/public` akan kosong, dan semua file publik berada di `public_html`.

#### Mengubah Path Autoload di `public_html/index.php`:
Karena file inti projek dipindahkan ke dalam folder `asri-core`, Anda harus menyesuaikan path di file `public_html/index.php` agar dapat memanggil file bootstrap Laravel di folder `asri-core`.

1. Buka file `public_html/index.php` melalui File Manager Editor di Hostinger.
2. Cari baris berikut (biasanya di baris ~47 dan ~51 untuk Laravel modern):
   ```php
   // BARIS ASLI:
   require __DIR__.'/../vendor/autoload.php';
   ```
   Ubah menjadi:
   ```php
   // BARIS BARU (Mengarahkan ke folder asri-core):
   require __DIR__.'/../asri-core/vendor/autoload.php';
   ```
3. Cari baris berikut:
   ```php
   // BARIS ASLI:
   $app = require_once __DIR__.'/../bootstrap/app.php';
   ```
   Ubah menjadi:
   ```php
   // BARIS BARU (Mengarahkan ke folder asri-core):
   $app = require_once __DIR__.'/../asri-core/bootstrap/app.php';
   ```
4. Simpan file `index.php`.

*(Catatan: Jika Anda memilih Opsi A di Tahap 1C (tanpa menyertakan vendor saat upload), pastikan Anda melakukan koneksi SSH ke server hosting, navigasi ke `/domains/namadomain.com/asri-core` dan jalankan perintah `composer install --no-dev -o` terlebih dahulu sebelum mencoba membuka website).*

---

## 4. KONFIGURASI ENVIRONMENT (`.env`) DI SERVER

File `.env` di hosting terletak di dalam folder `/domains/namadomain.com/asri-core/.env`. (Salin dari `.env.example` jika belum ada).

Buka file `.env` tersebut dan sesuaikan konfigurasinya dengan kondisi server hosting:

```env
# Ubah ke mode production dan matikan debug untuk keamanan sidang
APP_NAME="Asri Boarding House"
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:omfj5jlAYs2YdIG5L8dY5oRQ3BAuwxxvH4DU1UAPNr4= # Pastikan sama dengan local Anda

# Ubah ke domain asli Anda (Gunakan HTTPS jika SSL sudah aktif di Hostinger)
APP_URL=https://namadomain.com

# Konfigurasi Database Hostinger Baru Anda
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=uXXXXXX_asri_db      # Nama database dari hPanel
DB_USERNAME=uXXXXXX_admin_asri   # Username database dari hPanel
DB_PASSWORD=password_database_anda # Password database yang Anda buat

# Konfigurasi Queue & Session
QUEUE_CONNECTION=database
SESSION_DRIVER=file
SESSION_LIFETIME=120
SESSION_SECURE_COOKIE=true       # Ubah ke true jika website sudah menggunakan HTTPS (sangat disarankan)

# === MIDTRANS (Gunakan sandbox terlebih dahulu untuk demo sidang) ===
MIDTRANS_SERVER_KEY=Mid-server-your_sandbox_server_key
MIDTRANS_CLIENT_KEY=Mid-client-your_sandbox_client_key
MIDTRANS_IS_PRODUCTION=false

# === FONNTE WHATSAPP ===
FONNTE_TOKEN=fonnte-token-anda

# === EMAIL SMTP GMAIL ===
# Masukkan konfigurasi email pengiriman tagihan & notifikasi
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_ENCRYPTION=tls
MAIL_USERNAME=dev.asrikost@gmail.com
MAIL_PASSWORD="app-password-gmail-anda"  # Gunakan App Password dari Google Account, bukan password email biasa
MAIL_FROM_ADDRESS=dev.asrikost@gmail.com
MAIL_FROM_NAME="Asri Boarding House"

# === GOOGLE OAUTH / SOCIALITE ===
GOOGLE_CLIENT_ID=google-client-id-anda.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=GOCSPX-secret-google-anda
```

> [!NOTE]
> **Konfigurasi Penting untuk Google Login (OAuth):**
> Jika Anda menggunakan fitur Login dengan Akun Google (Socialite), link callback redirect URI akan dibuat dinamis berdasarkan variabel `APP_URL`. 
> Anda **wajib** menambahkan URL live Anda di **Google Cloud Console -> API & Services -> Credentials**:
> * **Authorized redirect URIs**: `https://namadomain.com/auth/google/callback` (Ganti dengan nama domain asli Anda).


---

## 5. MEMBUAT STORAGE SYMLINK (STORAGE LINK)

Laravel menyimpan file dinamis (seperti foto bukti pembayaran, foto profil, dokumen kamar) di direktori `storage/app/public`. Agar file ini dapat diakses secara publik, diperlukan link simbolis (*symbolic link*) dari `storage` ke folder publik.

### Opsi A: Menggunakan SSH (Cara Paling Mudah jika SSH Aktif)
1. Buka menu **SSH Access** di hPanel Hostinger dan aktifkan akses SSH.
2. Gunakan aplikasi terminal (seperti PuTTY atau Command Prompt bawaan Windows).
3. Hubungkan ke server dengan kredensial SSH yang tertera di hPanel:
   ```bash
   ssh username@ip_address -p port_number
   ```
4. Masuk ke direktori core Laravel Anda:
   ```bash
   cd domains/namadomain.com/asri-core
   ```
5. Karena kita memisahkan folder core (`asri-core`) dan folder public (`public_html`), perintah bawaan `php artisan storage:link` akan membuat symlink di `asri-core/public/storage`. Kita perlu membuat symlink kustom secara manual yang mengarah ke `public_html/storage`.
6. Hapus folder `storage` yang mungkin tidak sengaja terbuat di `public_html` terlebih dahulu:
   ```bash
   rm -rf ../public_html/storage
   ```
7. Buat symlink manual dengan perintah:
   ```bash
   ln -s /home/uXXXXXX/domains/namadomain.com/asri-core/storage/app/public /home/uXXXXXX/domains/namadomain.com/public_html/storage
   ```

### Opsi B: Menggunakan Web Route (Alternatif Tanpa SSH)
Jika Anda tidak bisa mengakses terminal SSH, Anda bisa memicu pembuatan symlink melalui script PHP sementara di browser.

1. Buka file `/domains/namadomain.com/asri-core/routes/web.php` menggunakan File Manager Editor.
2. Tambahkan rute sementara di bagian paling bawah file:
   ```php
   use Illuminate\Support\Facades\Artisan;
   use Illuminate\Support\Facades\File;

   Route::get('/buat-symlink-skripsi', function () {
       $target = storage_path('app/public');
       $shortcut = '/home/uXXXXXX/domains/namadomain.com/public_html/storage'; // GANTI uXXXXXX dengan username Hostinger Anda
       
       // Hapus jika jalan pintas lama ada
       if (file_exists($shortcut)) {
           @unlink($shortcut);
       }
       
       // Buat symlink menggunakan fungsi PHP symlink()
       if (symlink($target, $shortcut)) {
           return "Symlink berhasil dibuat! Folder upload gambar sudah terhubung.";
       } else {
           return "Gagal membuat symlink.";
       }
   });
   ```
3. Akses URL tersebut melalui browser: `https://namadomain.com/buat-symlink-skripsi`
4. Jika muncul pesan "Symlink berhasil dibuat!", segera hapus atau komentari baris kode tersebut di `routes/web.php` demi keamanan sistem Anda.

---

## 6. KONFIGURASI CRON JOBS & QUEUE WORKER (SANGAT PENTING)

Projek Asri Boarding House memiliki fitur otomatisasi krusial yang berjalan di latar belakang (scheduler dan antrean). Jika langkah ini dilewati, sistem tidak akan pernah membuat tagihan bulanan atau mendeteksi keterlambatan.

### A. Konfigurasi Task Scheduler (Cron Job Utama)
Task Scheduler bertugas memicu event bulanan dan harian yang didefinisikan di `routes/console.php` (seperti pembuatan tagihan otomatis, pembatalan reservasi kadaluarsa, prunning chat, dll.).

1. Masuk ke hPanel Hostinger -> cari **Cron Jobs** di bagian *Advanced*.
2. Isi form pembuatan Cron Job baru:
   - **Type**: Custom
   - **Common settings**: Pilih **Once Per Minute (* * * * *)** (agar scheduler Laravel dapat memeriksa tugas setiap menit).
   - **Command**:
     ```bash
     /usr/bin/php8.2 /home/uXXXXXX/domains/namadomain.com/asri-core/artisan schedule:run >> /dev/null 2>&1
     ```
     > *Catatan penting:*
     > - Ganti `uXXXXXX` dengan username direktori Hostinger Anda (tertera di File Manager / Dashboard hPanel).
     > - Ganti `/usr/bin/php8.2` dengan path PHP yang sesuai di Hostinger. Umumnya Hostinger menggunakan path binary `/usr/bin/php8.2` atau cukup ditulis `php` jika global path sudah terkonfigurasi.
3. Klik **Save** (Simpan).

### B. Konfigurasi Queue Worker (Untuk Notifikasi & Email)
Projek Anda dikonfigurasi dengan `QUEUE_CONNECTION=database` di `.env`. Artinya, saat sistem mengirim email kuitansi atau notifikasi WhatsApp, Laravel tidak langsung memprosesnya saat itu juga (agar web terasa sangat cepat), melainkan menyimpannya ke tabel `jobs`.
Tabel `jobs` ini memerlukan worker yang berjalan terus menerus untuk mengeksekusinya.

Pada VPS, kita biasa menggunakan *Supervisor* agar worker berjalan non-stop. Namun pada Shared Hosting Hostinger, kita tidak memiliki akses Supervisor. Solusi terbaiknya adalah dengan membuat Cron Job tambahan untuk memproses antrean secara berkala.

1. Buat **Cron Job Baru** di menu hPanel Cron Jobs.
2. Pilih frekuensi waktu: **Once Per Minute (* * * * *)** atau **Every 5 Minutes (*/5 * * * *)**.
3. Masukkan perintah berikut:
   ```bash
   /usr/bin/php8.2 /home/uXXXXXX/domains/namadomain.com/asri-core/artisan queue:work --stop-when-empty >> /dev/null 2>&1
   ```
   - Parameter `--stop-when-empty` sangat penting di shared hosting. Perintah ini akan memproses semua antrean email/WhatsApp yang tertunda hingga kosong, kemudian aplikasinya langsung menutup diri (berhenti). Dengan cara ini, resource RAM hosting Anda tidak akan bocor atau terkena suspend karena menjalankan proses background terus menerus.

---

## 7. TIPS PERSIAPAN DEMO SIDANG SKRIPSI

Saat sidang skripsi, kelancaran sistem adalah prioritas utama. Berikut adalah tips agar demo aplikasi Anda berjalan mulus di hadapan dosen penguji:

1. **Gunakan Akun Midtrans Sandbox**:
   Jangan beralih ke mode Production Midtrans saat sidang skripsi kecuali diminta. Gunakan mode Sandbox agar Anda bisa mendemokan pembayaran dengan menggunakan kartu kredit virtual atau simulator bank transfer Midtrans (tanpa mengeluarkan uang asli). Tunjukkan status pembayaran otomatis berubah menjadi "Lunas" setelah transaksi disimulasikan sukses.
2. **Aktifkan Fitur WhatsApp Fonnte dengan Kuota Terisi**:
   Jika skripsi Anda berfokus pada "Notifikasi Real-time WhatsApp", pastikan nomor pengirim WhatsApp Anda dalam keadaan aktif (terkoneksi di panel Fonnte) dan kuota pesan Anda terisi agar pesan notifikasi tagihan atau konfirmasi reservasi benar-benar masuk ke HP penguji/dosen saat demo.
3. **Simulasi Jam Sistem untuk Pengujian**:
   Dosen sering bertanya: *"Bagaimana cara sistem tahu kalau tagihan bulanan dibuat tiap tanggal 1?"* atau *"Bagaimana sistem membatalkan reservasi yang lewat 24 jam?"*.
   - **Jawaban Anda**: "Sistem menggunakan Laravel Scheduler yang dijalankan setiap menit oleh Cron Job Hostinger. Scheduler memicu perintah di `routes/console.php`. Untuk reservasi kadaluarsa, query akan membatalkan status jika sudah lewat 24 jam."
   - **Trik Demo Cepat**: Jika ingin mendemokan proses keterlambatan tanpa menunggu 24 jam atau menunggu tanggal 1, Anda dapat masuk ke phpMyAdmin Hostinger dan ubah tanggal `created_at` sebuah data reservasi/tagihan secara manual menjadi beberapa hari ke belakang, lalu jalankan Command Artisan secara manual lewat SSH atau dengan bantuan rute web pemicu sementara.
4. **Keamanan Folder Terpisah**:
   Bila penguji menanyakan tentang keamanan sistem, tunjukkan bahwa file konfigurasi `.env` Anda diletakkan di luar folder `public_html` (`asri-core`), sehingga jika ada seseorang mencoba mengakses `http://domainanda.com/.env`, server akan mengembalikan error 404 (Not Found), bukan mendownload file konfigurasi database Anda. Ini nilai plus yang sangat besar untuk aspek keamanan sistem informasi skripsi Anda.
5. **Cek Log Jika Terjadi Kendala**:
   Jika website tiba-tiba mengalami error *500 Internal Server Error* saat demo atau pengujian:
   - Jangan panik.
   - Buka File Manager Hostinger, buka file `/domains/namadomain.com/asri-core/storage/logs/laravel.log`.
   - Scroll ke bagian paling bawah untuk melihat pesan error yang sebenarnya dan baris kode yang menyebabkannya. Dosen akan sangat terkesan jika Anda mampu melakukan *debugging* mandiri berbasis log selama sidang berlangsung.

---

## 8. KONFIGURASI WEBHOOK & CALLBACK API (MIDTRANS & CHAT)

Aplikasi ini memiliki beberapa API (Application Programming Interface) internal dan eksternal yang harus dipastikan berjalan dengan baik di server produksi Hostinger:

### A. Webhook Callback Midtrans (Otomatis & Dinamis)
Untuk mendeteksi pembayaran lunas dari Midtrans secara otomatis (Server-to-Server), Midtrans mengirimkan request HTTP POST ke server Anda. 
- **Endpoint Tagihan Reguler**: `https://namadomain.com/api/midtrans/callback`
- **Endpoint Reservasi Kamar**: `https://namadomain.com/api/midtrans/callback-reservasi`

> [!TIP]
> **Fitur Dynamic Override yang Sudah Diterapkan:**
> Saya telah memperbarui [MidtransService.php](file:///c:/xampp/htdocs/asri-boarding-house/app/Services/MidtransService.php) dengan menambahkan header `X-Override-Notification`. Dengan fitur ini:
> 1. Sistem akan secara otomatis mengirimkan URL callback yang dinamis menyesuaikan nama domain aktif Anda (HTTPS secara otomatis) ke server Midtrans setiap kali transaksi baru dibuat.
> 2. Anda **tidak perlu lagi mengonfigurasi "Payment Notification URL" secara manual** di dashboard Midtrans Merchant. Semuanya akan langsung diarahkan ke backend website Anda baik saat di sandbox maupun production secara otomatis.

### B. Chat & Public API (Sanctum & Rate Limiter)
Website ini menyediakan API chat untuk guest (`/api/guest-chat/*`) yang diatur oleh Laravel Rate Limiter (Throttle) untuk menghindari spamming chat dari pengguna tidak dikenal.
- Pastikan modul web server **mod_rewrite** aktif di hosting Anda (di Hostinger sudah aktif secara default) agar route `/api/...` tidak menghasilkan error 404. File `.htaccess` yang Anda pindahkan ke `public_html` sudah mencakup pengaturan ini secara otomatis.
