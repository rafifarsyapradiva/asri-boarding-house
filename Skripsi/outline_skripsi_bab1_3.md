# OUTLINE NASKAH SKRIPSI (BAB 1 SAMPAI BAB 3)

**Judul Tugas Akhir:** Sistem Informasi Manajemen Kost Terintegrasi *Payment Gateway* pada Asri Boarding House  
**Berkas Sumber:** [`Skripsi/skripsi_Rafif_Arsya_Pradiva_22.N4.0014.md`](file:///c:/xampp/htdocs/asri-boarding-house/Skripsi/skripsi_Rafif_Arsya_Pradiva_22.N4.0014.md)  
**Program Studi:** Sistem Informasi, Fakultas Ilmu Komputer  
**Institusi:** Universitas Katolik Soegijapranata Semarang  
**Tahun:** 2026  

---

## 1. Identitas dan Parameter Utama Penelitian

| Komponen | Deskripsi Faktual |
| :--- | :--- |
| **Objek Penelitian** | Asri Boarding House, Jalan Maera Sari Nomor 1, Tembalang, Kota Semarang |
| **Kapasitas Hunian** | 32 unit kamar sewa (Tipe VIP, Deluxe, dan Standar) |
| **Sasaran Pengguna** | Pengelola kost, penjaga kost, calon penghuni, dan penghuni aktif |
| **Fokus Permasalahan** | Keterbatasan promosi sewa jarak jauh, benturan pemesanan ganda (*double booking*), penagihan jatuh tempo manual tanggal 10, serta rekonsiliasi kas fisik yang memakan waktu berhari-hari |
| **Solusi yang Dibangun** | Sistem informasi manajemen kost berbasis web dengan integrasi gerbang pembayaran digital, penjadwalan tagihan otomatis, serta pengingat notifikasi multi-kanal |
| **Arsitektur & Teknologi** | Framework Laravel (arsitektur MVC dan *Service Layer*), MySQL Database (InnoDB, transaksi ACID, 3NF), Midtrans API (*Sandbox* dan *Production*), Fonnte WhatsApp API, serta SMTP Mail Gateway |
| **Metodologi Pengembangan** | *Research and Development* (R&D) dipadukan dengan SDLC model *Waterfall* (5 fase bertahap) |
| **Metode Evaluasi Data** | Analisis deskriptif kualitatif melalui wawancara mendalam bersama penjaga kost dan penghuni kost |

---

## 2. Struktur Hierarkis Naskah (Bab 1 - Bab 3)

```
BAB 1 PENDAHULUAN
├── 1.1 Latar Belakang
│   ├── Paragraf 1: Konteks kawasan Tembalang, kebutuhan hunian sewa, keterbatasan promosi, urgensi sistem reservasi daring [1]
│   ├── Paragraf 2: Profil Asri Boarding House (32 kamar), kelemahan pencatatan buku besar fisik, risiko kamar terpesan ganda [2]
│   ├── Paragraf 3: Kendala penagihan bulanan tanggal 10, pesan manual satu per satu, penumpukan tunggakan, solusi pengawasan otomatis [3]
│   ├── Paragraf 4: Kompleksitas rekonsiliasi bukti transfer dan nota kas, selisih pembukuan fisik, solusi otomasi payment gateway [4]
│   └── Paragraf 5: Sintesis solusi SIM kost terpadu, Midtrans, notifikasi WhatsApp dan email, serta penetapan judul penelitian [5]
├── 1.2 Rumusan Masalah
│   ├── Butir 1: Perancangan arsitektur sistem informasi manajemen kost terintegrasi payment gateway
│   ├── Butir 2: Implementasi kerangka kerja Laravel, Midtrans, cron job penagihan, dan notifikasi multi-kanal
│   └── Butir 3: Analisis efektivitas sistem melalui wawancara evaluasi bersama penjaga dan penghuni kost
└── 1.3 Tujuan Penelitian
    ├── Butir 1: Merancang arsitektur sistem sebagai solusi kendala reservasi, pembukuan, dan penagihan
    ├── Butir 2: Mengimplementasikan sistem menggunakan Laravel, Midtrans, penjadwalan otomatis, dan notifikasi
    └── Butir 3: Menganalisis hasil penerapan dan efektivitas sistem pada operasional harian

BAB 2 TINJAUAN PUSTAKA
├── 2.1 Sistem Informasi Manajemen (Definisi terstruktur, single source of truth, pencegahan data tercecer, transparansi kas) [6], [7]
├── 2.2 E-Commerce B2C (Model interaksi langsung tanpa perantara, katalog mandiri, pemangkasan rantai sewa, reservasi jarak jauh) [8], [9]
├── 2.3 Web Application (Arsitektur client-server, protokol HTTP/HTTPS, thin client, pembaruan terpusat, struktur modular) [10], [11]
├── 2.4 UI & UX (Visual Neo-Brutalism kontras tinggi, garis tepi tegas, keterbacaan status, susunan alur kerja UX ringkas) [12], [13]
├── 2.5 Laravel (Arsitektur MVC, Eloquent ORM, Blade template, Service Layer, proteksi keamanan CSRF dan SQL injection) [14], [15]
├── 2.6 RDBMS (Tabel relasional dua dimensi, primary key, foreign key, kepatuhan ACID, normalisasi skema hingga 3NF) [16], [17]
├── 2.7 MySQL Database (RDBMS open-source, arsitektur multithread, mesin InnoDB, row-level locking, optimasi indeks) [18], [19]
├── 2.8 Payment Gateway (Layanan Midtrans, multi-kanal pembayaran, ruang kerja Sandbox dan Production, validasi webhook SHA-512) [20], [21]
├── 2.9 WhatsApp Gateway API (Pengiriman pesan otomatis via HTTP POST, autentikasi token, keunggulan tingkat keterbacaan pesan instan) [22], [23]
├── 2.10 Email Gateway API (Protokol SMTP berenkripsi TLS, fungsi dokumen resmi kuitansi/tagihan, pencegahan folder spam) [24], [25]
└── 2.11 Cron Job (Penjadwal tugas latar belakang Unix, eksekusi periodik perintah konsol, sifat idempoten penagihan otomatis) [26], [27]

BAB 3 METODE PENELITIAN
├── 3.1 Objek Penelitian (Lokasi geografis, kapasitas 32 unit, profil kamar, fokus alur pemesanan, penagihan, dan pembukuan)
├── 3.2 Jenis Penelitian (Pendekatan R&D rekayasa perangkat lunak [28], perpaduan studi kasus tunggal dan analisis kualitatif)
├── 3.3 Sumber Data
│   ├── Data Primer: Keterangan lisan wawancara mendalam penjaga kost dan penghuni kost
│   └── Data Sekunder: Buku besar fisik, arsip bukti transfer, nota pengeluaran, dokumentasi teknis API, dan literatur akademik
├── 3.4 Teknik Pengumpulan Data (Wawancara mendalam tatap muka [29], perekaman audio, transkripsi tekstual, fokus materi dua narasumber)
├── 3.5 Metode Pengembangan Sistem (Waterfall SDLC [30]: Analisis Kebutuhan, Perancangan, Implementasi, Pengujian, serta Pemeliharaan; Gambar 3.1)
└── 3.6 Kerangka Pemikiran (Alur Input, Process, Output, Outcome sistem informasi manajemen kost; Gambar 3.2)
```

---

## 3. Rincian Narasi dan Pokok Pikiran Bab 1 (Pendahuluan)

### 1.1 Latar Belakang
* **Paragraf 1 (Konteks Lapangan & Akses Informasi):**  
  * *Inti Pembahasan:* Kawasan pendidikan Tembalang di Semarang terus menerima mahasiswa perantau sehingga permintaan hunian sewa tetap tinggi. Cara promosi konvensional menyulitkan pencarian kamar dari luar kota.  
  * *Poin Teknis:* Calon penyewa harus datang langsung untuk memastikan ketersediaan unit. Kehadiran sistem informasi berbasis web memotong kendala jarak dan merapikan alur reservasi sejak awal [1].
* **Paragraf 2 (Profil Objek & Kendala Inventaris):**  
  * *Inti Pembahasan:* Asri Boarding House mengelola 32 kamar sewa dengan tiga pilihan tipe (VIP, Deluxe, dan Standar). Pendataan kamar masih mengandalkan buku besar fisik.  
  * *Poin Teknis:* Penjaga kost kesulitan memperbarui ketersediaan unit secara cepat. Kondisi ini kerap memicu benturan pesanan ganda (*double booking*) saat kamar yang sama dipesan oleh beberapa orang secara bersamaan [2].
* **Paragraf 3 (Kendala Penagihan & Jadwal Pembayaran):**  
  * *Inti Pembahasan:* Siklus penagihan sewa jatuh tempo setiap tanggal 10. Pengingat sewa dan perhitungan denda keterlambatan masih dikirim secara manual lewat pesan pribadi satu per satu.  
  * *Poin Teknis:* Penagihan manual sering terlewat oleh penjaga kost. Tunggakan sewa pun menumpuk. Fitur pengawasan jadwal otomatis pada sistem informasi diperlukan agar siklus pembayaran bulanan berjalan tertib [3].
* **Paragraf 4 (Kendala Rekonsiliasi Kas & Celah Pembukuan):**  
  * *Inti Pembahasan:* Pembayaran sewa masih diverifikasi lewat tangkapan layar transfer bank dan setoran uang tunai. Pemeriksaan fisik ini menghabiskan waktu berhari-hari saat penutupan buku akhir bulan.  
  * *Poin Teknis:* Pencocokan nota keluar-masuk secara manual rawan menimbulkan selisih kas fisik. Penerapan gerbang pembayaran (*payment gateway*) menutup risiko tersebut melalui pencatatan transaksi non-tunai yang terverifikasi otomatis [4].
* **Paragraf 5 (Solusi Terintegrasi & Penetapan Fokus):**  
  * *Inti Pembahasan:* Diperlukan sistem terpusat yang menggabungkan fitur reservasi kamar daring, penjadwalan tagihan rutin, serta gerbang pembayaran digital Midtrans.  
  * *Poin Teknis:* Sistem diperkuat pengingat tagihan otomatis via WhatsApp dan *email* agar informasi tersampaikan secara *real-time* [5]. Solusi ini diwujudkan melalui penelitian berjudul *Sistem Informasi Manajemen Kost Terintegrasi Payment Gateway pada Asri Boarding House*.

### 1.2 Rumusan Masalah
1. Bagaimana merancang arsitektur Sistem Informasi Manajemen Kost Terintegrasi *Payment Gateway* pada Asri Boarding House sebagai solusi atas kendala pemesanan unit, pencatatan pembukuan manual, dan keterlambatan penagihan sewa?
2. Bagaimana mengimplementasikan sistem tersebut menggunakan kerangka kerja Laravel yang dipadukan dengan gerbang pembayaran Midtrans, penjadwalan tagihan otomatis berkala, serta pengiriman notifikasi melalui WhatsApp dan *email*?
3. Bagaimana hasil penerapan dan efektivitas sistem berdasarkan evaluasi wawancara bersama penjaga kost serta penghuni kost pada operasional harian Asri Boarding House?

### 1.3 Tujuan Penelitian
1. Merancang arsitektur Sistem Informasi Manajemen Kost Terintegrasi *Payment Gateway* pada Asri Boarding House sebagai solusi terpadu atas kendala pemesanan unit, pencatatan pembukuan manual, dan keterlambatan penagihan sewa.
2. Mengimplementasikan sistem tersebut menggunakan kerangka kerja Laravel yang dipadukan dengan gerbang pembayaran Midtrans, penjadwalan tagihan otomatis berkala, serta pengiriman notifikasi melalui WhatsApp dan *email*.
3. Menganalisis hasil penerapan dan efektivitas sistem berdasarkan evaluasi wawancara bersama penjaga kost serta penghuni kost pada operasional harian Asri Boarding House.

---

## 4. Rincian Narasi dan Pokok Pikiran Bab 2 (Tinjauan Pustaka)

### 2.1 Sistem Informasi Manajemen
* **Konsep:** Penggabungan terstruktur antara perangkat keras, perangkat lunak, prosedur operasional, dan sumber daya manusia untuk mengolah data transaksi mentah menjadi informasi manajerial yang siap pakai [6].
* **Fungsi pada Sistem:** Menjadi basis data tunggal (*single source of truth*) yang menghimpun inventaris 32 unit kamar, profil penghuni, riwayat pembayaran sewa, dan arus kas harian.
* **Dampak Operasional:** Menghilangkan pencatatan ganda di buku besar, mencegah benturan pesanan kamar, serta menyajikan laporan keuangan secara transparan bagi pengelola [7].

### 2.2 E-Commerce B2C
* **Konsep:** Transaksi perdagangan elektronik *Business-to-Consumer* yang menghubungkan langsung pemilik jasa dengan pengguna akhir tanpa perantara pihak ketiga [8].
* **Fungsi pada Sistem:** Calon penghuni dapat melihat katalog kamar, mengecek ketersediaan unit kosong secara mandiri, dan menyelesaikan reservasi kapan pun dari jarak jauh.
* **Dampak Operasional:** Memotong biaya komisi agen properti, memberikan keleluasaan pengaturan tarif bagi pengelola, serta mempercepat proses pemesanan unit baru [9].

### 2.3 Web Application
* **Konsep:** Aplikasi perangkat lunak yang beroperasi dengan arsitektur *client-server* melalui protokol HTTP atau HTTPS di jaringan internet [10].
* **Fungsi pada Sistem:** Peramban pada komputer atau ponsel cerdas berfungsi sebagai *thin client* tanpa perlu proses instalasi aplikasi tambahan. Logika bisnis dijalankan secara terpusat pada *web server*.
* **Dampak Operasional:** Pembaruan sistem dapat dilakukan langsung di *server* pusat tanpa mengonfigurasi perangkat pengguna satu per satu, sehingga waktu tanggap transaksi tetap cepat dan konsisten [11].

### 2.4 UI & UX
* **Konsep Antarmuka (UI):** Penerapan gaya visual *Neo-Brutalism* yang mengandalkan garis tepi tebal, bayangan tegas kontras tinggi, dan tata letak geometris sederhana [12].
* **Fungsi Antarmuka:** Setiap tombol tindakan, status kamar, dan formulir data terlihat kontras sehingga pengguna mengenali fungsi navigasi secara intuitif.
* **Konsep Pengalaman Pengguna (UX):** Penyusunan alur pemesanan dan pembayaran dalam langkah-langkah ringkas. Informasi disajikan secara bertingkat agar pengguna dapat menuntaskan transaksi tanpa kebingungan [13].

### 2.5 Laravel
* **Konsep:** Kerangka kerja pengembangan web berbasis PHP yang menggunakan pola arsitektur *Model-View-Controller* (MVC) [14].
* **Fungsi pada Sistem:** Memisahkan lapisan data (*Model* berbasis Eloquent ORM), tampilan (*View* berbasis mesin Blade), dan kendali alur (*Controller*). Logika bisnis yang rumit dipisahkan ke dalam lapisan layanan (*Service Layer*) [15].
* **Aspek Keamanan:** Fitur keamanan bawaan melindungi sistem dari ancaman pemalsuan permintaan antar-situs (*Cross-Site Request Forgery* atau CSRF) dan injeksi SQL.

### 2.6 RDBMS
* **Konsep:** Sistem manajemen basis data relasional yang menata data dalam tabel dua dimensi yang saling terhubung melalui relasi *primary key* dan *foreign key* [16].
* **Fungsi pada Sistem:** Memenuhi standar transaksi ACID (*Atomicity, Consistency, Isolation, Durability*) untuk menjaga keabsahan setiap pencatatan transaksi sewa.
* **Normalisasi Data:** Skema basis data dirancang melalui tahapan normalisasi hingga Bentuk Normal Ketiga (3NF) untuk menghilangkan duplikasi data dan mencegah anomali saat manipulasi rekaman [17].

### 2.7 MySQL Database
* **Konsep:** Perangkat lunak RDBMS sumber terbuka yang menggunakan standar bahasa *Structured Query Language* (SQL) dengan dukungan arsitektur *multithread* [18].
* **Fungsi pada Sistem:** Penggunaan mesin penyimpanan (*storage engine*) InnoDB menyediakan penguncian tingkat baris (*row-level locking*) untuk mencegah benturan data saat beberapa pengguna mengakses unit kamar yang sama pada waktu bersamaan.
* **Dampak Operasional:** Dukungan indeks data dan integritas relasional menjaga respons kueri tetap cepat dan stabil saat menangani rekapitulasi data pembayaran [19].

### 2.8 Payment Gateway
* **Konsep:** Layanan perantara pembayaran digital yang menghubungkan aplikasi web secara langsung dengan infrastruktur perbankan dan dompet digital [20].
* **Fungsi pada Sistem:** Memanfaatkan layanan Midtrans untuk memfasilitasi transaksi melalui *Virtual Account* berbagai bank, QRIS, dan dompet elektronik tanpa memerlukan banyak rekening bank terpisah.
* **Mekanisme Teknis:** Pengujian fitur dilakukan pada mode *Sandbox* sebelum diterapkan ke mode *Production*. Status transaksi diverifikasi secara otomatis menggunakan *webhook callback* yang dilindungi validasi kriptografi *signature key* SHA-512 [21].

### 2.9 WhatsApp Gateway API
* **Konsep:** Layanan antarmuka pemrograman aplikasi untuk mengirimkan notifikasi pesan instan secara otomatis ke nomor telepon pengguna melalui protokol HTTP POST berautentikasi token [22].
* **Fungsi pada Sistem:** Mengirimkan pesan konfirmasi reservasi dan pengingat tagihan bulanan langsung ke nomor WhatsApp penghuni kost.
* **Dampak Operasional:** Memiliki tingkat keterbacaan (*open rate*) yang lebih tinggi dibanding media konvensional sehingga meminimalkan alasan lupa bayar dari penghuni kost [23].

### 2.10 Email Gateway API
* **Konsep:** Pengiriman dokumen administrasi digital melalui jaringan internet menggunakan protokol *Simple Mail Transfer Protocol* (SMTP) dengan enkripsi TLS (*Transport Layer Security*) [24].
* **Fungsi pada Sistem:** Berfungsi sebagai saluran pengiriman dokumen resmi seperti kuitansi pelunasan, berkas tagihan digital (*invoice*), dan tautan verifikasi akun.
* **Dampak Operasional:** Memastikan kuitansi tersimpan permanen sebagai arsip bukti pembayaran yang sah, dengan konfigurasi *relay* yang mencegah surat masuk ke folder *spam* [25].

### 2.11 Cron Job
* **Konsep:** Utilitas penjadwal tugas berbasis waktu pada sistem operasi Unix yang mengeksekusi instruksi program secara otomatis di latar belakang [26].
* **Fungsi pada Sistem:** Cukup satu pengatur waktu (*daemon cron*) pada sistem operasi yang diaktifkan untuk memicu fitur *task scheduling* Laravel. Perintah konsol berjalan otomatis untuk memeriksa tanggal jatuh tempo, menghitung denda, dan menerbitkan tagihan [27].
* **Prinsip Idempoten:** Skrip penjadwal dirancang idempoten agar eksekusi perintah yang berulang tidak menerbitkan tagihan ganda pada siklus bulan yang sama.

---

## 5. Rincian Narasi dan Pokok Pikiran Bab 3 (Metode Penelitian)

### 3.1 Objek Penelitian
* **Lokasi & Karakteristik:** Asri Boarding House, beralamat di Jalan Maera Sari Nomor 1, Tembalang, Kota Semarang.
* **Kapasitas Unit:** Memiliki total 32 unit kamar tidur sewa yang terbagi ke dalam tipe VIP, Deluxe, dan Standar.
* **Ruang Lingkup Operasional:** Penelitian membatasi kajian pada alur reservasi kamar, siklus penagihan rutin tanggal 10, serta rekonsiliasi pembukuan kas yang sebelumnya dicatat secara fisik pada buku besar.

### 3.2 Jenis Penelitian
* **Pendekatan R&D:** Menerapkan metode Penelitian dan Pengembangan (*Research and Development*) dengan fokus pada rekayasa perangkat lunak [28]. Tujuannya adalah menghasilkan produk aplikasi web yang bekerja fungsional di lapangan.
* **Metode Studi Kasus:** Dipadukan dengan studi kasus tunggal deskriptif kualitatif untuk memetakan alur kerja riil, mengidentifikasi kendala pembukuan, serta mengevaluasi penerimaan sistem setelah dioperasikan.

### 3.3 Sumber Data
* **Data Primer:** Diperoleh melalui wawancara mendalam langsung bersama narasumber lapangan di Asri Boarding House:
  * *Penjaga kost:* Sumber data alur kerja harian, proses pendataan kamar, penagihan sewa, dan kendala rekapitulasi buku kas fisik.
  * *Penghuni kost:* Sumber data pengalaman pemesanan unit, kendala pelunasan sewa bulanan, dan kebutuhan bukti kuitansi yang sah.
* **Data Sekunder:** Dihimpun dari dokumen pendukung:
  * Arsip buku besar operasional, bukti transfer pembayaran, nota kuitansi, dan daftar tarif 32 kamar.
  * Dokumentasi teknis API Midtrans, gateway WhatsApp, dan kerangka kerja Laravel.
  * Literatur akademik pendukung perancangan sistem informasi dan integrasi gerbang pembayaran.

### 3.4 Teknik Pengumpulan Data
* **Metode Wawancara Mendalam:** Dilakukan secara tatap muka dengan bantuan rekaman audio yang kemudian disalin ke dalam transkrip tertulis [29].
* **Fokus Pembahasan Wawancara:**
  * *Penjaga kost:* Evaluasi pembukuan kas, pemantauan status 32 unit kamar, kepraktisan penerbitan tagihan berkala, dan efisiensi waktu kerja dibanding pencatatan manual.
  * *Penghuni kost:* Pengalaman memilih kamar daring, kejelasan rincian tagihan bulanan, kemudahan transaksi lewat *payment gateway*, dan keterbacaan kuitansi digital.

### 3.5 Metode Pengembangan Sistem
* **Model Waterfall (SDLC):** Menerapkan siklus hidup pengembangan perangkat lunak berurutan [30] dengan acuan visual pada Gambar 3.1:
  1. *Requirement Gathering and Analysis:* Menghimpun dan memvalidasi kebutuhan operasional indekos melalui wawancara mendalam.
  2. *Design:* Merancang antarmuka aplikasi, diagram alur sistem, serta skema basis data relasional.
  3. *Implementation:* Menulis kode program dengan framework Laravel dan menghubungkan API gerbang pembayaran Midtrans.
  4. *Integration & Testing:* Menguji fungsi aplikasi melalui pengujian kotak hitam (*black box testing*) untuk memastikan kelancaran transaksi.
  5. *Operation & Maintenance:* Melakukan pemasangan aplikasi pada peladen web (*server hosting*), pemantauan operasional berkala, dan perbaikan kendala teknis.

### 3.6 Kerangka Pemikiran
* **Alur Logika Penelitian (R&D):** Berpijak pada skema Gambar 3.2 yang membagi proses ke dalam empat tahapan berurutan:
  * *Masukan (Input):* Kondisi operasional indekos, kelemahan sistem manual buku besar, potensi benturan pesanan, serta spesifikasi kebutuhan sistem.
  * *Proses (Process):* Perancangan sistem berbasis web, pengodean dengan kerangka kerja Laravel, integrasi Midtrans dan notifikasi, serta validasi fungsi sistem.
  * *Keluaran (Output):* Sistem informasi manajemen kost terintegrasi *payment gateway* yang berfungsi stabil dan siap pakai.
  * *Dampak (Outcome):* Pembukuan keuangan menjadi tertib, penagihan bulanan tepat waktu, risiko salah catat kas hilang, serta proses pemesanan unit berlangsung efisien.

---

## 6. Matriks Pemetaan Referensi Ilmiah IEEE ([1] - [30])

Tabel berikut menunjukkan keterkaitan antara referensi pustaka IEEE dengan subbab pembahasan pada Bab 1 sampai Bab 3:

| Nomor Sitasi | Sumber Publikasi / Jurnal | Topik Inti Referensi | Penempatan Subbab |
| :---: | :--- | :--- | :---: |
| **[1]** | PESHUM J. Pendidikan, Sos. dan Hum. (2025) | Sistem informasi pemesanan kost berbasis web dengan Laravel | 1.1 |
| **[2]** | J. Sist. Inf. dan Sains Teknol. (2021) | Rancang bangun sistem informasi sewa rumah kost berbasis web | 1.1 |
| **[3]** | JSiI (Jurnal Sist. Informasi) (2024) | Sistem monitoring rumah kos dan pembayaran berbasis web | 1.1 |
| **[4]** | REMIK Ris. dan E-Jurnal Manaj. Inform. Komput. (2025) | Sistem penyewaan berbasis web terintegrasi payment gateway Midtrans | 1.1 |
| **[5]** | J. Tekno Kompak (2021) | Aplikasi penerimaan dan pengeluaran kas berbasis WhatsApp gateway | 1.1 |
| **[6]** | J. Manaj. Sist. Inf. (2022) | Peran SIM organisasi dalam pengambilan keputusan manajemen | 2.1 |
| **[7]** | JUSIM (Jurnal Sist. Inf. Musirawas) (2025) | Sistem informasi manajemen kos berbasis web | 2.1 |
| **[8]** | J. Nas. Pendidik. Tek. Inform. JANAPATI (2022) | Analisis perancangan e-business B2C untuk transaksi mandiri | 2.2 |
| **[9]** | J. Kepariwisataan Indones. (2023) | Pemanfaatan e-commerce dan property management system perhotelan | 2.2 |
| **[10]** | J. Tek. Komput. (2021) | Rancang bangun sistem informasi berbasis web arsitektur client-server | 2.3 |
| **[11]** | J. Teknol. dan Sist. Inf. (2020) | Sistem informasi berbasis web dengan arsitektur modular | 2.3 |
| **[12]** | J. Teknol. Terpadu (2023) | Desain antarmuka sistem akses digital dan keterbacaan elemen | 2.4 |
| **[13]** | J. Nas. Teknol. dan Sist. Inf. (2023) | Perancangan antarmuka UI/UX berbasis kebutuhan pengguna | 2.4 |
| **[14]** | J. Teknol. dan Sist. Inf. Bisnis (2024) | Pengembangan website dengan arsitektur MVC pada framework Laravel | 2.5 |
| **[15]** | SISKOMTI J. Sist. Inf. Komput. dan Teknol. Inf. (2023) | Implementasi konsep MVC dan struktur modular pada Laravel | 2.5 |
| **[16]** | MASIP J. Manaj. Adm. Bisnis dan Publik Terapan (2023) | Peran basis data relasional RDBMS pada sistem informasi manajemen | 2.6 |
| **[17]** | Computing Insight J. Comput. Sci. (2024) | Normalisasi basis data perancangan sistem penyewaan | 2.6 |
| **[18]** | Router J. Tek. Inform. dan Terap. (2025) | Implementasi basis data relasional MySQL | 2.7 |
| **[19]** | INSOLOGI J. Sains dan Teknol. (2024) | Kinerja pengindeksan data dan respons basis data MySQL | 2.7 |
| **[20]** | J. Algoritma (2024) | Implementasi payment gateway Midtrans pada aplikasi web | 2.8 |
| **[21]** | INFOTECH J. Inform. dan Teknol. (2024) | Perancangan dan integrasi sistem payment gateway Midtrans | 2.8 |
| **[22]** | J. Tek. Mesin, Elektro dan Ilmu Komput. (2025) | Penerapan API WhatsApp gateway untuk pengingat otomatis | 2.9 |
| **[23]** | J. Ilm. Sist. Inf. (2025) | Pemanfaatan gerbang komunikasi WhatsApp API terprogram | 2.9 |
| **[24]** | TeknoIS J. Ilm. Teknol. Inf. dan Sains (2022) | Implementasi Simple Mail Transfer Protocol pada mail gateway | 2.10 |
| **[25]** | Decode J. Pendidik. Teknol. Inf. (2025) | Sistem informasi monitoring dengan fitur notifikasi email resmi | 2.10 |
| **[26]** | Sainteknol J. Sains dan Teknol. (2025) | Penerapan cron job untuk pemrosesan data otomatis di server | 2.11 |
| **[27]** | JSITIK J. Sist. Inf. dan Teknol. Inf. Komput. (2026) | Otomasi notifikasi berbasis web terjadwal | 2.11 |
| **[28]** | J. Informatics Manag. Inf. Technol. (2025) | Pengembangan sistem informasi menggunakan metode R&D | 3.2 |
| **[29]** | Alfabeta Bandung (2022) | Metode penelitian kualitatif, kuantitatif, dan R&D | 3.4 |
| **[30]** | REMIK Ris. dan E-Jurnal Manaj. Inform. Komput. (2023) | Analisis metode pengembangan sistem informasi model Waterfall | 3.5 |

---

## 7. Panduan Kepatuhan Penulisan Naskah

Untuk memastikan naskah tugas akhir tetap memenuhi standar akademik tinggi dan terhindar dari catatan koreksi, setiap penyusunan teks wajib mematuhi parameter berikut:

1. **Ragam Bahasa Hybrid (Akademis dan Humanis):**  
   Gunakan struktur kalimat aktif yang lugas. Hindari susunan pasif yang berbelit-belit. Paparkan fakta operasional secara wajar layaknya komunikasi profesional di tempat kerja.
2. **Kerapian Ritme Kalimat (*Burstiness*):**  
   Kombinasikan kalimat penjelas dengan kalimat pendek penegas (antara 8 sampai 12 kata). Kalimat pendek ini berfungsi sebagai jeda napas yang memperjelas arah argumen paragraf.
3. **Pemberantasan Jejak Kata Klise AI (*Anti-Slop*):**  
   Hindari formula kalimat penutup paragraf yang monoton dan berlebihan. Jangan gunakan kata sambung repetitif, pengantar pembuka zaman yang klise, kata pujian berlebihan terhadap teknologi, maupun rumus kalimat buatan mesin.
4. **Larangan Tanda Pisah Em-Dash:**  
   Seluruh berkas teks dilarang memuat karakter tanda pisah panjang em-dash. Gunakan tanda koma, tanda titik, tanda kurung, atau struktur kalimat bertingkat yang rapi sebagai gantinya.
5. **Penghilangan Nama Individu pada Bab 1 sampai Bab 3:**  
   Gunakan sebutan peran fungsional secara konsisten (*penjaga kost*, *pengelola*, *calon penghuni*, *penghuni kost*). Kutipan pustaka wajib menggunakan nomor sitasi numerik IEEE `[1]` hingga `[30]` tanpa menyisipkan nama penulis di dalam narasi kalimat.
