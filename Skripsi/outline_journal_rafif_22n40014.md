# OUTLINE DAN BEDAH LENGKAP NASKAH JURNAL INGGRIS
**Judul Artikel Jurnal:** BOARDING HOUSE MANAGEMENT INFORMATION SYSTEM INTEGRATED WITH PAYMENT GATEWAY AT ASRI BOARDING HOUSE  
**Penulis 1:** Rafif Arsya Pradiva (`22.N4.0014` / `22n40014@student.unika.ac.id`)  
**Penulis 2:** Andre Kurniawan Pamudji (`andre.kurniawan@unika.ac.id`)  
**Penulis 3:** Erdhi Widyarto Nugroho (`erdhi@unika.ac.id`)  
**Afiliasi:** Department of Information Systems, Faculty of Computer Science, Soegijapranata Catholic University, Semarang, Indonesia  
**Berkas Acuan Utama:** `Skripsi/journal_inggris_Rafif_Arsya_Pradiva_22.N4.0014.md`  

---

## TAHAP 1: PENGATURAN HALAMAN DAN TATA LETAK UMUM (*PAGE SETUP*)
Bagian pembuka dokumen menetapkan aturan tata letak cetak standar jurnal ilmiah kampus:
1. **Ukuran Kertas:** A-4 (*portrait*, $21{,}0\text{ cm} \times 29{,}7\text{ cm}$).
2. **Batas Tepi (*Margin*):** $2{,}5\text{ cm}$ secara merata di sisi atas, bawah, kiri, dan kanan.
3. **Format Kolom Isi:** Dua kolom (*two columns*) dengan jarak pemisah antarkolom sebesar $0{,}5\text{ cm}$.
4. **Tipografi dan Spasi Utama:** Huruf *Times New Roman* ukuran `12 pt Regular` dengan spasi tunggal (`1.0`).
5. **Spesifikasi Ukuran Huruf Khusus:**
   * `16 pt Bold`: Judul utama artikel.
   * `12 pt Bold & Italic`: Judul sub-bagian *Abstract* dan label *Keywords*.
   * `12 pt Bold`: Isi paragraf abstrak serta judul bab dan sub-bab.
   * `12 pt Regular`: Nama penulis, afiliasi, surel, paragraf isi artikel.
   * `10 pt Bold`: Label penomoran gambar (`Figure 1.` sampai `Figure 5.`).
   * `10 pt Regular`: Judul keterangan gambar di bawah gambar (*centered in Caps*) dan daftar pustaka (*References*).
   * `10 pt Italic`: Nama jurnal dan judul buku pada daftar pustaka berformat IEEE.
6. **Batas Panjang Naskah:** **Maksimal 8 halaman** cetak A-4 dua kolom (memuat **5 kelompok *Figure* utama**, **0 tabel**, dan **2.720 kata total** inklusif judul, abstrak, isi, keterangan gambar, dan daftar pustaka; terverifikasi aman di bawah batas maksimal 8 halaman).

---

## TAHAP 2: BAGIAN KEPALA ARTIKEL (*HEADER*)
Ditempatkan rata tengah (*center*) di bagian paling atas halaman pertama sebelum masuk ke area dua kolom:
1. **Judul Artikel (`16 pt Bold`):**  
   `BOARDING HOUSE MANAGEMENT INFORMATION SYSTEM INTEGRATED WITH PAYMENT GATEWAY AT ASRI BOARDING HOUSE`
2. **Baris Penulis (`12 pt Regular` dengan Superskrip):**  
   `¹Rafif Arsya Pradiva, ²Andre Kurniawan Pamudji, ³Erdhi Widyarto Nugroho`
3. **Baris Afiliasi Bahasa Inggris (2 Baris, `12 pt Regular`):**  
   * Baris 1: `Department of Information Systems, Faculty of Computer Science`
   * Baris 2: `Soegijapranata Catholic University, Semarang, Indonesia`
4. **Baris Alamat Surel (`12 pt Regular` dengan Superskrip):**  
   `¹22n40014@student.unika.ac.id, ²andre.kurniawan@unika.ac.id, ³erdhi@unika.ac.id`

---

## TAHAP 3: ABSTRAK DAN KATA KUNCI (*ABSTRACT & KEYWORDS*)
Bagian intisari ditulis dalam satu paragraf utuh sepanjang **155 kata** (di bawah batas maksimal 200 kata) dengan teks rata kanan-kiri (*justify*), **tanpa sitasi**, **tanpa nama narasumber**, dan **tanpa tanda *em-dash***:
1. **Judul Abstrak (`12 pt Bold Italic`):** `Abstract`
2. **Isi Abstrak (`12 pt Bold`, 5 Komponen Utuh):**
   * *Masalah Lapangan:* Pengelolaan harian 32 kamar sewa di Asri Boarding House masih bergantung pada buku besar kertas dan pesan manual, memicu benturan pesanan kamar, keterlambatan penagihan sewa, serta selisih kas saat audit transfer bank berhari-hari.
   * *Tujuan Penelitian:* Merancang dan membangun sistem informasi manajemen indekos berbasis web yang mengotomatisasi verifikasi pembayaran dan pengingat tagihan penghuni.
   * *Metode & Arsitektur:* Pengembangan mengikuti model *Waterfall* dengan Laravel dan MySQL. Lapisan basis data menerapkan *pessimistic row-level locking* untuk mencegah reservasi ganda saat transaksi bersamaan, serta terhubung dengan Midtrans Snap, WhatsApp API, dan layanan surel.
   * *Hasil Pengujian & Evaluasi:* Pengujian 60 skenario *black-box* berjalan stabil tanpa galat transaksi atau validasi. Evaluasi pasca-peluncuran bersama pengelola indekos membuktikan tutup buku akhir bulan turun dari beberapa hari menjadi laporan instan tanpa selisih kas, serta wawancara penghuni mengonfirmasi kepuasan tinggi atas pembayaran *Virtual Account* dan kuitansi digital.
3. **Kata Kunci (`12 pt Regular`, 5 Frasa Urut Abjad):**  
   *Keywords:* `Asri Boarding House, boarding house management system, Laravel, Midtrans payment gateway, pessimistic locking`

---

## TAHAP 4: BAB I. INTRODUCTION (*PENDAHULUAN*)
Bab pertama terdiri atas **5 paragraf** dengan **13 kalimat rujukan berurutan `[1]` sampai `[13]`** (1 sitasi per kalimat secara naik teratur) dan **1 kalimat penutup tujuan penelitian tanpa sitasi**, bebas penyebutan nama orang:

* **Paragraf 1 (Kebutuhan Hunian Tembalang & Risiko *Double Booking*, Sitasi `[1]`–`[2]`):**
  * *Kalimat 1 (`[1]`):* Arus kedatangan mahasiswa ke kawasan kampus Tembalang Semarang mendorong permintaan kamar sewa setiap tahun, tetapi promosi konvensional menyulitkan calon penyewa mengecek kamar kosong dari jarak jauh.
  * *Kalimat 2 (`[2]`):* Asri Boarding House di Jalan Maera Sari No. 1 mengelola 32 kamar tipe VIP, Deluxe, dan Standard pada buku besar kertas, sehingga pembaruan status terlambat dan memicu *double booking* saat dua peminat memesan kamar yang sama dalam waktu berdekatan.
* **Paragraf 2 (Kendala Penagihan Tanggal 10 & Selisih Kas Tutup Buku, Sitasi `[3]`–`[4]`):**
  * *Kalimat 3 (`[3]`):* Penagihan bulanan terkendala karena pengingat jatuh tempo tanggal 10 dan perhitungan denda masih dikirim satu per satu lewat pesan pribadi, membuat jadwal tagih sering terlewat dan tunggakan menumpuk.
  * *Kalimat 4 (`[4]`):* Pencocokan tangkapan layar transfer bank dan kuitansi tunai di akhir bulan memaksa pengelola menghabiskan waktu berhari-hari dan membuat catatan kas rentan salah hitung.
* **Paragraf 3 (Landasan Konsep SIM, B2C, Neo-Brutalism, Laravel MVC, MySQL 3NF, Sitasi `[5]`–`[9]`):**
  * *Kalimat 5 (`[5]`):* Penerapan Sistem Informasi Manajemen (SIM) menyatukan stok kamar, kontrak penghuni, dan laporan arus kas dalam satu pusat data untuk kendali operasional lebih cepat.
  * *Kalimat 6 (`[6]`):* Portal web *Business-to-Consumer* (B2C) menghubungkan pemilik properti langsung dengan calon penyewa tanpa potongan komisi perantara.
  * *Kalimat 7 (`[7]`):* Pada lapisan antarmuka, sistem desain *Neo-Brutalism* berkontras tegas dengan petunjuk navigasi yang jelas membantu pengunjung menuntaskan reservasi tanpa kebingungan.
  * *Kalimat 8 (`[8]`):* Di sisi *backend*, pola *Model-View-Controller* (MVC) Laravel memisahkan aturan bisnis dari templat tampilan agar mudah dirawat.
  * *Kalimat 9 (`[9]`):* Penggabungan dengan basis data MySQL InnoDB bentuk Normal Ketiga (3NF) menghapus duplikasi data dan menjaga keutuhan relasi riwayat penyewa.
* **Paragraf 4 (Celah Riset & Otomasi Pembayaran, Penjadwal, WhatsApp, Email, Sitasi `[10]`–`[13]`):**
  * *Kalimat 10 (`[10]`):* Banyak katalog indekos berbasis web terdahulu masih mengandalkan unggah bukti transfer manual, sedangkan integrasi *payment gateway* Midtrans dengan tanda tangan *webhook* SHA-512 memverifikasi pembayaran *Virtual Account* secara otomatis.
  * *Kalimat 11 (`[11]`):* Penjadwalan *cron job* di *server* mengambil alih penerbitan tagihan bulanan berulang dan pengecekan denda secara idempoten tanpa pencatatan manual operator.
  * *Kalimat 12 (`[12]`):* Pengingat tagihan dan konfirmasi pembayaran otomatis terkirim langsung melalui API WhatsApp Fonnte.
  * *Kalimat 13 (`[13]`):* Kuitansi resmi dan pembaruan akun turut dikirim secara paralel melalui *relay* surel SMTP terenkripsi TLS untuk pengarsipan permanen.
* **Paragraf 5 (Tiga Tujuan Penelitian, Tanpa Sitasi):**
  * Merumuskan tiga tujuan utama penelitian: (1) merancang arsitektur sistem informasi manajemen indekos terintegrasi *payment gateway* di Asri Boarding House guna menghentikan benturan kamar dan keterlambatan pembukuan, (2) membangun aplikasi web pada Laravel dengan integrasi Midtrans, penjadwalan tagihan otomatis, serta notifikasi WhatsApp dan surel, dan (3) mengevaluasi kinerja sistem melalui pengujian fungsional dan wawancara pemangku kepentingan.

---

## TAHAP 5: BAB II. METHOD (*METODE PENELITIAN*)
Bab kedua terdiri atas **3 paragraf naratif** dan **1 gambar bagan (`Figure 1`)** sebagai pengganti tabel, melanjutkan urutan sitasi ketat **`[14]` sampai `[20]`** tanpa pengulangan mundur dan tanpa penyebutan nama orang:

* **Paragraf 1 (Lokasi, Pendekatan R&D + Studi Kasus, dan Sumber Data, Sitasi `[14]`–`[15]`):**
  * *Kalimat 1 (`[14]`):* Studi lapangan berlangsung di Asri Boarding House, Jalan Maera Sari No. 1, Tembalang, Semarang, menerapkan pendekatan rekayasa perangkat lunak R&D dan studi kasus tunggal dengan kerangka empat fase: *input* (kendala buku besar manual 32 kamar), *process* (perancangan, pengkodean, dan pengujian), *output* (platform manajemen web terverifikasi), dan *outcome* (pembukuan akurat serta pembayaran sewa digital yang praktis).
  * *Kalimat 2 (`[15]`):* Data primer bersumber dari wawancara tatap muka terekam bersama pengelola indekos (*the resident manager*) dan penghuni aktif, sedangkan data sekunder meliputi buku besar internal, bukti transfer, catatan biaya, daftar tarif kamar, manual teknis API, dan literatur terdahulu.
* **Paragraf 2 & `Figure 1` (Model Waterfall, Sitasi `[16]`):**
  * *Kalimat 3 (`[16]`):* Pengembangan aplikasi mengikuti siklus hidup perangkat lunak *Waterfall* sekuensial pada `Figure 1`, bergerak berurutan melalui *Requirement gathering and analysis*, *Design*, *Implementation*, *Integration & testing*, *Verification*, dan *Operation & maintenance*.
  * **`Figure 1` (`Figure 1. WATERFALL SYSTEM DEVELOPMENT MODEL`):**  
    *Berkas Gambar:* `images/metode_sdlc_waterfall.png` (lebar `95%` kolom), diagram SDLC Waterfall berbahasa Inggris 6 tahapan dengan panah umpan balik.
* **Paragraf 3 (Desain, Implementasi ACID/Midtrans/Cron, Pengujian & Evaluasi, Sitasi `[17]`–`[20]`):**
  * *Kalimat 4 (`[17]`):* Tahap desain memetakan batas akses tiga aktor (calon penyewa, administrator, penyewa aktif) melalui diagram UML dan skema ERD MySQL InnoDB 3NF untuk tabel transaksi inti serta operasional pendukung.
  * *Kalimat 5 (`[18]`):* Tahap implementasi berjalan di atas Laravel MVC dan MySQL InnoDB menggunakan transaksi ACID dengan *pessimistic row-level locking* (`lockForUpdate()`) guna mencegah benturan reservasi pada kamar yang sama di waktu bersamaan.
  * *Kalimat 6 (`[19]`):* Orkestrasi pembayaran mengintegrasikan token Midtrans Snap (masa berlaku 24 jam) dan verifikasi *webhook* SHA-512 (`hash_equals()`), dipadukan perintah *cron* bulanan tanggal 1 (`tagihan:generate-bulanan`), pengecekan denda harian (`tagihan:proses-keterlambatan`), peringatan WhatsApp Fonnte, serta antrean surel SMTP.
  * *Kalimat 7 (`[20]`):* Tahap integrasi, pengujian, dan verifikasi menguji 60 skenario *black-box* pada enam domain operasional (12 kasus autentikasi dan profil, 10 portal publik, 10 reservasi 5 tahap, 9 portal penyewa, 13 operasi admin, dan 6 penjadwal dan API), dilanjutkan pasca-peluncuran produksi dengan uji pakai berdampingan dan wawancara semi-terstruktur skala 10 poin bersama pengelola indekos serta lima mahasiswa penghuni aktif.

---

## TAHAP 6: BAB III. RESULTS AND DISCUSSION (*HASIL DAN PEMBAHASAN*)
Bab ketiga menyajikan implementasi inti dengan **gambar esensial saja (4 kelompok *Figure*, `Figure 2` sampai `Figure 5`)**, **tanpa Tabel UAT**, **tanpa sitasi rujukan**, dan **tanpa penyebutan nama orang**:

### Sub-bab A. RESULT (5 Paragraf Naratif dan 4 Kelompok Figure Inti)
* **Paragraf 1 (Pemisahan 3 Peran & Normalisasi Basis Data 3NF & `Figure 2`):**  
  Menguraikan batas akses calon penyewa, penyewa aktif, dan administrator, serta normalisasi MySQL InnoDB 3NF yang mengelompokkan 6 tabel transaksi inti (`users`, `kamar`, `penyewa`, `reservasi`, `tagihan`, `pembayaran`) dengan *snapshot* tarif kontrak historis pada `penyewa`, dilengkapi deskripsi tabel operasional pendukung (`keluhan`, `pengeluaran`, `log_notifikasi`, `guest_chat_threads`, `chat_messages`, `customer_reviews`, `peraturan`, `faqs`).
* **`Figure 2` (`Figure 2. CORE RESIDENCY AND FINANCIAL 3NF ENTITY RELATIONSHIP DIAGRAM`):**  
  *Berkas Gambar:* `images/gambar_4_2_a_erd_modul_inti_en.png` (lebar `84%`).
* **Paragraf 2 (Alur Logika Reservasi, Webhook Midtrans & Billing Otomatis & `Figure 3`):**  
  Mengulas alur end-to-end dari pemilihan kamar kosong, validasi 16 digit NIK, transaksi ACID `lockForUpdate()`, verifikasi tanda tangan SHA-512 (`hash_equals()`) pada webhook Midtrans, promosi peran otomatis, notifikasi WhatsApp dan surel, hingga penjadwal *cron* tanggal 1 dan pasca-tanggal 10 dengan `withoutOverlapping()`.
* **`Figure 3` (`Figure 3. END-TO-END RESERVATION, PAYMENT GATEWAY WEBHOOK, AND BILLING FLOWCHART`):**  
  *Berkas Gambar:* `images/gambar_4_6_flowchart_sistem_en.png` (lebar `95%` kolom).
* **Paragraf 3 (Antarmuka Katalog 32 Kamar, Alur Pemesanan 5 Langkah & Modal Midtrans & `Figure 4`):**  
  Merangkum 36 antarmuka web responsif bergaya *Neo-Brutalism* untuk filter 32 kamar (VIP, Deluxe, Standard) beserta alur reservasi 5 langkah dan modal pembayaran *Virtual Account*/QRIS Midtrans Snap berdurasi 24 jam.
* **`Figure 4` (`Figure 4. ONLINE BOOKING WORKFLOW: (a) FIVE-STEP RESERVATION WORKSPACE; (b) MIDTRANS SNAP VIRTUAL ACCOUNT MODAL`):**  
  *Berkas Gambar:* `images/gambar_4_21_pembayaran_5_tahap_reservasi_en.png` (`58%`) dan `images/gambar_4_22_payment_gateway_midtrans.png` (`35%`).
* **Paragraf 4 (Dasbor Admin, Rekonsiliasi Keuangan & Kuitansi PDF A5 Penghuni & `Figure 5`):**  
  Menjelaskan dasbor KPI pengelola (32 total unit, rasio okupansi, pendapatan, pengeluaran, laba bersih, tagihan belum lunas, reservasi masuk, grafik tren 12 bulan, diagram donat status kamar), modul rekonsiliasi keuangan dengan ekspor PDF dan Excel satu klik, serta portal tagihan penghuni yang menerbitkan kuitansi PDF A5 horizontal berstempel *PAID* dan ID transaksi Midtrans.
* **`Figure 5` (`Figure 5. ADMINISTRATOR PORTAL: (a) LIVE EXECUTIVE DASHBOARD; (b) FINANCIAL RECONCILIATION AND EXPORT MODULE`):**  
  *Berkas Gambar:* `images/gambar_4_26_dashboard_admin_en.png` (`48%`) dan `images/gambar_4_33_manajemen_laporan_pemasukan_en.png` (`48%`).
* **Paragraf 5 (Hasil Pengujian Black-Box 60 Skenario & Ringkasan Evaluasi UAT dalam Narasi):**  
  Menyatakan kelulusan 100% pada 60 skenario *black-box* di enam domain serta merangkum langsung hasil uji pakai dan wawancara tanpa tabel dan tanpa nama individu: pengelola senior memberi skor `9.5/10`, sementara lima mahasiswa penghuni aktif mencatat rata-rata `8.0/10` (rentang skor individu `7.0` hingga `9.0`).

### Sub-bab B. DISCUSSION (3 Paragraf Pembahasan Tanpa Sitasi & Tanpa Nama)
* **Paragraf 1 (Dampak Operasional bagi Manajemen):**  
  Membahas temuan wawancara bahwa rekonsiliasi otomatis memangkas tutup buku akhir bulan dari tiga atau empat hari menjadi ekspor PDF dan Excel instan tanpa selisih kas, efektivitas masa tenggang dalam bulan berjalan sebelum denda *rollover* 5% idempoten di bulan berikutnya, serta fungsi kunci karantina kamar pasca-*checkout* sebelum pembersihan selesai.
* **Paragraf 2 (Pengalaman Penghuni & Masukan Lanjutan):**  
  Membahas kemudahan katalog layanan mandiri dan antarmuka *Neo-Brutalism* bagi penghuni untuk memesan dari kampung halaman, membayar via *Virtual Account* di ponsel Android maupun iPhone tanpa mengantre ATM malam hari, memanfaatkan kuitansi PDF A5 untuk laporan ke orang tua, serta merangkum masukan fitur lanjutan (pengingat WhatsApp H-3 bertautan bayar langsung, panduan *onboarding* singkat, aplikasi *mobile*, optimasi kapasitas di tanggal puncak penagihan, dan kontrak sewa digital).
* **Paragraf 3 (Perbandingan Teknis & Keunggulan Arsitektur):**  
  Membandingkan secara teknis-konseptual keunggulan penguncian `lockForUpdate()` plus karantina pasca-*checkout* atas portal kost manual berbasis unggah bukti transfer, keunggulan webhook SHA-512 otomatis dibanding cek mutasi manual, serta cakupan siklus sewa multi-bulan dibanding integrasi pembayaran ritel sekali beli.

---

## TAHAP 7: BAB IV. CONCLUSION (*KESIMPULAN*)
Bab keempat terdiri atas **3 paragraf kesimpulan murni** yang langsung menjawab tiga tujuan penelitian, **tanpa daftar saran bernomor 1 sampai 5**, **tanpa sitasi**, dan **tanpa penyebutan nama orang**:

1. **Paragraf 1 (Jawaban Tujuan 1 - Perancangan Arsitektur):** Arsitektur sistem Asri Boarding House memadukan pemodelan UML terstruktur dengan basis data MySQL InnoDB 3NF yang memisahkan transaksi inti dan operasional pendukung, menyatukan pengelolaan 32 kamar serta buku kas di bawah tiga batasan peran untuk menggantikan buku kertas dan mencegah *double booking*.
2. **Paragraf 2 (Jawaban Tujuan 2 - Implementasi & Pengujian Fungsional):** Aplikasi web memadukan *framework* Laravel MVC, penguncian baris pesimis (`lockForUpdate()`), dan *callback* pembayaran Midtrans Snap tervalidasi SHA-512, dilengkapi *cron job* otomatis untuk penerbitan tagihan bulanan, denda idempoten, serta notifikasi via API WhatsApp Fonnte dan surel SMTP yang lulus di seluruh 60 skenario *black-box*.
3. **Paragraf 3 (Jawaban Tujuan 3 - Evaluasi Lapangan):** Evaluasi pemangku kepentingan mencatat skor kepuasan `9.5/10` dari pengelola indekos karena tutup buku bulanan menjadi instan tanpa selisih kas, sedangkan lima mahasiswa penghuni mencatat rata-rata `8.0/10` atas kepraktisan reservasi jarak jauh, pembayaran *Virtual Account*, dan kuitansi PDF A5.

---

## TAHAP 8: ACKNOWLEDGMENT (*UCAPAN TERIMA KASIH*)
Bagian ini ditempatkan setelah kesimpulan dengan **judul tanpa nomor bab (*unnumbered heading*)** dalam **1 paragraf** tanpa penyebutan nama narasumber:
* Penulis menyampaikan terima kasih kepada Program Studi Sistem Informasi, Fakultas Ilmu Komputer, Universitas Katolik Soegijapranata Semarang atas bimbingan akademik dan dukungan riset selama penelitian berlangsung, serta apresiasi kepada pemilik properti, pengelola indekos (*the resident manager*), dan para mahasiswa penghuni Asri Boarding House di Tembalang atas kerja sama terbuka selama wawancara lapangan dan uji coba sistem.

---

## TAHAP 9: REFERENCES (*DAFTAR PUSTAKA*)
Ditempatkan di bagian paling akhir dengan **judul tanpa nomor bab (*unnumbered heading*)**, diformat dalam ukuran `10 pt Regular` dan `10 pt Italic` berstandar IEEE. Memuat **20 referensi terurut `[1]` s.d. `[20]`** yang muncul tepat satu kali secara berurutan hanya di Bab I (`[1]`–`[13]`) dan Bab II (`[14]`–`[20]`):

1. **`[1]` A. Cornellya & H. Afriyadi (2025):** Jurnal *PESHUM*, perancangan sistem informasi pemesanan kos berbasis web menggunakan Laravel.
2. **`[2]` C. Nizar (2021):** Jurnal *SISTEK*, rancang bangun sistem informasi sewa rumah kost (E-Kost) berbasis *website*.
3. **`[3]` E. J. Malaikosa & P. Mokola (2024):** Jurnal *JSiI*, sistem informasi monitoring rumah kos dan pembayarannya berbasis web.
4. **`[4]` R. Sutisna & F. Aziz (2025):** Jurnal *REMIK*, integrasi Midtrans sebagai *payment gateway* pada sistem penyewaan berbasis *website*.
5. **`[5]` A. Syahputra, R. Wiranti, & W. Astita (2022):** Jurnal *JMASIF*, peran Sistem Informasi Manajemen organisasi dalam pengambilan keputusan.
6. **`[6]` I. A. Nurhasanah et al. (2022):** Jurnal *JANAPATI*, analisis perancangan *E-Business* B2C (*Business-to-Consumer*).
7. **`[7]` A. R. Setiawan et al. (2023):** *Jurnal Teknologi Terpadu*, *design system* pada perancangan antarmuka perangkat lunak sistem akses digital.
8. **`[8]` L. Rahmawati & Sumarsono (2024):** Jurnal *JTEKSIS*, desain pengembangan *website* dengan arsitektur *Model View Controller* pada Laravel.
9. **`[9]` D. F. M. Dina (2024):** Jurnal *Computing Insight*, normalisasi *database* (3NF) pada rancangan sistem penyewaan.
10. **`[10]` M. Nurhayati & R. Setiawan (2024):** *Jurnal Algoritma*, implementasi *payment gateway* Midtrans pada aplikasi berbasis web.
11. **`[11]` A. Wibowo et al. (2025):** Jurnal *Sainteknol*, implementasi *cron job* untuk pemrosesan data otomatis pada infrastruktur *cloud*.
12. **`[12]` I. M. B. Waton, A. N. Weking, & B. Deta (2025):** *Jurnal Teknik Mesin, Elektro dan Ilmu Komputer*, penerapan API WhatsApp Fonnte untuk pengingat berbasis web.
13. **`[13]` M. A. Syahputra & R. A. Putri (2025):** Jurnal *Decode*, sistem informasi berbasis web dengan fitur notifikasi *email* (SMTP).
14. **`[14]` M. W. Mauludy & Fathahillah (2025):** Jurnal *JIMAT*, pengembangan sistem informasi menggunakan metode *Research and Development* (R&D).
15. **`[15]` Sugiyono (2022):** Buku *Metode Penelitian Kuantitatif, Kualitatif, dan R&D* (Alfabeta, Bandung).
16. **`[16]` E. Rahmi, E. Yumami, & N. Hidayasari (2023):** Jurnal *REMIK*, analisis metode pengembangan sistem informasi berbasis *website* (*Waterfall* SLR).
17. **`[17]` Y. Adisa & M. I. P. Nasution (2023):** Jurnal *MASIP*, konsep dan peran sistem manajemen basis data relasional pada SIM.
18. **`[18]` J. V. Dachi & J. Suhada (2025):** Jurnal *Router*, implementasi basis data MySQL dan transaksi ACID.
19. **`[19]` R. C. Buwono, R. Kartadie, & M. K. Ramadhan (2024):** Jurnal *INFOTECH*, perancangan dan implementasi sistem *payment gateway* Midtrans.
20. **`[20]` J. K. Aditya & A. T. Hidayat (2025):** Jurnal *JUSIM*, sistem informasi manajemen kos berbasis web menggunakan metode *Blackbox*.
