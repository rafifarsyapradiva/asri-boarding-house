# Panduan Desain & Naskah Siap Pakai Poster Skripsi
## Format Adaptasi Templat: Modern Academic Research Poster (Teal & Navy Modular)

**Karya Ilmiah:** Rancang Bangun Sistem Informasi Manajemen Rumah Kost Terintegrasi Payment Gateway Midtrans (Studi Kasus: Asri Boarding House Semarang)  
**Peneliti:** Rafif Arsya Pradiva (NIM: 22.N4.0014)  
**Dosen Pembimbing:** Andre Kurniawan Pamudji, S.Kom., M.Kom. & Erdhi Widyarto Nugroho, S.T., M.T.  
**Program Studi:** Sistem Informasi, Fakultas Ilmu Komputer, Universitas Katolik Soegijapranata Semarang  
**Tautan Produksi:** [https://asriboardinghouse.weatso.id/](https://asriboardinghouse.weatso.id/)  

---

## 1. Spesifikasi Teknis & Panduan Tata Letak Sesuai Templat

Dokumen ini disusun khusus untuk meniru secara presisi komposisi visual dari templat poster referensi:
- **Header:** Judul tebal bergaris bawah tebal di kiri, bersanding dengan *Author Badge* ber-avatar di kanan.
- **Top Dark Banner:** Kapsul latar gelap berisi intisari penelitian dengan lencana ikon buku lingkaran.
- **Main Body 2-Kolom Modular:**
  - Kolom Kiri: 3 kartu vertikal berbingkai halus (`INTRODUCTION`, `OBJECTIVE`, `METHODOLOGY`).
  - Kolom Kanan: 2 kartu vertikal lebih lebar (`RESULT`, `ANALYSIS`) yang dilengkapi cuplikan visual antarmuka/grafik di sudut bawahnya.
- **Data Strip (3 Kartu Grafik Sejajar):** Berisi *Bar Chart* (Demografi Kamar), *Donut Chart* (Sebaran Omzet), dan *Line Chart* (Efisiensi Waktu Rekonsiliasi).
- **Bottom Dark Banner:** Kapsul penutup berlatar gelap berisi `CONCLUSION` dengan lencana ikon lampu dan QR Code *live demo*.
- **Ornamen Grafis:** Aksen pola titik-titik (*dot matrix grid*) di sudut kanan-atas dan kiri-bawah kanvas.

---

### 1.1 Parameter Desain Fisik & Cetak
- **Ukuran Kanvas Standar:** **A1 Portrait (594 × 841 mm)** (atau A0 Portrait: 841 × 1189 mm).
- **Resolusi Cetak:** 300 DPI (7016 × 9933 px untuk A1).
- **Profil Warna:** CMYK (untuk hasil cetak warna pekat dan tidak pudar).
- **Batas Aman (*Safe Margin*):** 20 mm dari tepi luar kanvas.
- **Jari-Jari Lengkungan Sudut (*Border Radius*):**
  - Kartu Standar: 12–16 px.
  - Dark Banner Kapsul (Atas & Bawah): 20–24 px.

---

### 1.2 Palet Warna Heksadesimal Templat
| Peran Desain | Kode Warna HEX | Karakter Visual | Penempatan pada Poster |
| :--- | :--- | :--- | :--- |
| **Dark Petrol Navy** | `#0B253A` / `#0D2F44` | Gelap, Elegan, Berwibawa | Latar kapsul abstrak atas & kesimpulan bawah, teks judul besar, dan garis tebal header. |
| **Vibrant Cyan/Teal** | `#00A896` / `#20A39E` | Segar, Modern, Fokus Teknologi | Lingkaran ikon bab, garis aksen judul kartu, batang grafik, dan warna irisan chart utama. |
| **Soft Mint Accent** | `#52D1C5` / `#7FE3D9` | Terang, Komplementer | Irisan kedua grafik lingkaran, batang sekunder, dan aksen garis tren. |
| **Card Surface** | `#FFFFFF` | Putih Bersih | Latar belakang seluruh kartu modular di zona tengah dan zona grafik. |
| **Card Border** | `#CBD5E1` / `#94A3B8` | Garis Halus (1–1.5 pt) | Bingkai luar setiap kartu modular agar tampak rapi dan terstruktur. |
| **Canvas Background** | `#F8FAFC` / `#F1F5F9` | Putih Abu Lembut | Warna dasar kanvas luar; memberi kontras lembut pada kartu putih. |
| **Dot Grid Ornament** | `#0D2F44` (Opasitas 30%) | Pola Titik Modern | Aksen dekoratif di sudut kanan atas dan kiri bawah poster. |

---

### 1.3 Skala Ukuran Huruf (Font Hierarchy untuk Ukuran A1)
- **Judul Utama Poster:** 64–76 pt, *Extra Bold / Black*, huruf kapital rapat (*Condensed Sans*), misal: *Montserrat*, *Plus Jakarta Sans*, *Oswald*, atau *Inter*.
- **Nama Penulis pada Kartu Kanan Atas:** 24–28 pt, *Bold*.
- **Nama Universitas & Dosen:** 14–16 pt, *Medium*.
- **Teks Dark Banner (Abstrak):** 16–18 pt, *Medium / Italic*, putih bersih.
- **Judul Bagian Kartu (Header Poin):** 18–22 pt, *Bold*, huruf kapital (*Uppercase*) dengan garis tipis di sampingnya.
- **Teks Tubuh Kartu (Body Text):** 14–16 pt, *Regular*, spasi baris (*line-height*) 1.45.
- **Label & Nilai Grafik:** 12–14 pt, *Bold / Semi-bold*.

---

## 2. Cetak Biru Wireframe (1-to-1 dengan Templat Referensi)

```
[• • • • •] (Dot Grid Top-Right)
+--------------------------------------------------------------------------------------------------+
|                                                                                                  |
|   SISTEM INFORMASI MANAJEMEN                               +---------------------------------+   |
|   ASRI BOARDING HOUSE SEMARANG                             |  (o)  RAFIF ARSYA PRADIVA       |   |
|   TERINTEGRASI PAYMENT GATEWAY                             |       Universitas Katolik       |   |
|   ==============================================           |       Soegijapranata Semarang   |   |
|   (Thick Horizontal Bar)                                   +---------------------------------+   |
|                                                                                                  |
|   +------------------------------------------------------------------------------------------+   |
|   |  ((Buku))  Penelitian ini merancang dan menguji Sistem Informasi Manajemen Asri          |   |
|   |            Boarding House (32 kamar, omzet Rp28,5 jt/bln) berbasis Laravel 11 dan       |   |
|   |            Midtrans Snap v2 untuk menuntaskan tunggakan sewa, selisih pembukuan manual   |   |
|   |            dari 3-5 hari menjadi instan, dan tabrakan reservasi serentak.                |   |
|   +------------------------------------------------------------------------------------------+   |
|                                                                                                  |
|   +---------------------------------------+  +-----------------------------------------------+   |
|   | [Icon] INTRODUCTION                   |  | [Icon] RESULT                                 |   |
|   | - Fasilitas 32 kamar, omzet Rp28,5 jt |  | - Stepper booking 5 tahap mandiri             |   |
|   | - 20 th pembukuan nota kertas manual  |  | - Midtrans Snap BCA VA + SHA-512              |   |
|   | - 3 Kerentanan: tunggakan, selisih    |  | - Cron billing tgl 1 (00:05 WIB), denda 5%    |   |
|   |   kas, dan double-booking mahasiswa   |  | - Kuitansi digital A5 di peramban             |   |
|   |                                       |  |                      [Thumbnail Antarmuka UI] |   |
|   +---------------------------------------+  +-----------------------------------------------+   |
|   | [Icon] OBJECTIVE                      |  | [Icon] ANALYSIS                               |   |
|   | - Rancang bangun sistem web terpadu   |  | - 100% lolos 60 uji black-box & 510 PHPUnit   |   |
|   | - Mitigasi risiko operasional harian  |  | - Isolasi keamanan RBAC & proteksi IDOR       |   |
|   | - Evaluasi adopsi pengelola & tenant  |  | - Skor Pengelola: 9,5 / 10 (Zero Variance)    |   |
|   +---------------------------------------+  | - Skor Mahasiswa: 8,00 / 10 (Sewa Ringkas)    |   |
|   | [Icon] METHODOLOGY                    |  |                      [Mini Grafik Skor/Gauge] |   |
|   | - SDLC Waterfall: Analisis - Rilis    |  +-----------------------------------------------+   |
|   | - Laravel 11 MVC + MySQL 8.0 3NF (22) |                                                      |
|   | - Virtual Columns: Soft-Delete Unicity|                                                      |
|   | - Pessimistic Lock: lockForUpdate()   |                                                      |
|   +---------------------------------------+                                                      |
|                                                                                                  |
|   +------------------------------------------------------------------------------------------+   |
|   |  +--------------------+      +--------------------+      +--------------------+          |   |
|   |  |  INVENTARIS KAMAR  |      |   SEBARAN OMZET    |      | EFISIENSI REKAP KAS|          |   |
|   |  |    (BAR CHART)     |      |   (DONUT CHART)    |      |    (LINE CHART)    |          |   |
|   |  | VIP: 6 unit        |      | Std: Rp17,25 jt    |      | 3-5 Hari -> Instan |          |   |
|   |  | Dlx: 3 unit        |      | VIP: Rp8,4 jt      |      | Selisih Kas: 0%    |          |   |
|   |  | Std: 23 unit       |      | Dlx: Rp2,85 jt     |      | Double-Book: 0%    |          |   |
|   |  +--------------------+      +--------------------+      +--------------------+          |   |
|   +------------------------------------------------------------------------------------------+   |
|                                                                                                  |
|   +------------------------------------------------------------------------------------------+   |
|   |  ((Lampu))  CONCLUSION                                                 [ QR CODE ]       |   |
|   |             Sistem terbukti mengeliminasi selisih kas menjadi instan,  Scan Live Demo    |   |
|   |             meniadakan bentrok kamar via pessimistic locking, serta    asriboardinghouse |   |
|   |             meningkatkan efisiensi kerja. Roadmap: QRIS & IoT Lock.    .weatso.id        |   |
|   +------------------------------------------------------------------------------------------+   |
|                                                                                                  |
+--------------------------------------------------------------------------------------------------+
[• • • • •] (Dot Grid Bottom-Left)
```

---

## 3. Naskah Siap Pakai Poster (Ready-to-Paste Copywriting)

Naskah di bawah ini telah diselaraskan dengan ke-6 pilar gaya penulisan: ragam *hybrid* akademis-humanis, bebas klise AI, padat to-the-point, berirama dinamis (*burstiness*), terminologi tepat, dan parafrase murni.

```markdown
<!-- SLIDE / BAGIAN HEADER POSTER -->
```

### BAGIAN 1: HEADER UTAMA & KARTU PENELITI

#### Teks Judul Utama (Sisi Kiri Atas):
**SISTEM INFORMASI MANAJEMEN ASRI BOARDING HOUSE BERBASIS LARAVEL 11 & MIDTRANS PAYMENT GATEWAY**

*(Garis Pembatas Bawah: Garis tebal horizontal sepanjang teks judul, ketebalan 4–6 pt, warna Deep Petrol Navy `#0B253A`).*

#### Kartu Peneliti / Author Badge (Sisi Kanan Atas):
- **Ikon Profil:** Avatar siluet dalam lingkaran berwarna toska (`#00A896`).
- **Nama Peneliti:** **RAFIF ARSYA PRADIVA**
- **NIM:** 22.N4.0014
- **Institusi:** Program Studi Sistem Informasi — Fakultas Ilmu Komputer  
  Universitas Katolik Soegijapranata Semarang
- **Dosen Pembimbing:** Andre Kurniawan Pamudji, S.Kom., M.Kom. | Erdhi Widyarto Nugroho, S.T., M.T.

---

### BAGIAN 2: TOP DARK BANNER (RINGKASAN EKSEKUTIF / PROLOG)

*(Format: Kotak kapsul melengkung dengan latar Deep Petrol Navy `#0B253A`, teks putih bersih).*

- **Ikon Sisi Kiri:** Lingkaran garis putih berisi ikon buku akademis / gedung hunian.
- **Naskah Teks:**  
  *Penelitian ini merancang, membangun, dan mengevaluasi Sistem Informasi Manajemen terintegrasi pada Asri Boarding House Semarang (32 kamar terbagi atas kelas VIP, Deluxe, dan Standard dengan basis omzet Rp28.500.000/bulan). Sistem mentransformasikan tata kelola sewa konvensional berbasis nota kertas menuju platform digital nirgesekan melalui integrasi Midtrans BCA Virtual Account, autonotifikasi tagihan WhatsApp, dan penguncian basis data anti-bentrok kamar.*

---

### BAGIAN 3: BODI UTAMA (2-KOLOM MODULAR GRID)

#### KOLOM KIRI (3 Kartu Bersusun)

##### Kartu 1: `[Ikon Dokumen] INTRODUCTION`
> Asri Boarding House mengelola 32 kamar di Tembalang, Semarang. Selama dua dekade, operasional bertumpu pada satu pengelola senior menggunakan buku nota karbon, kalkulator manual, dan pesan WhatsApp informal.  
> 
> Pola konvensional ini memicu tiga kerapuhan kronis:
> 1. **Tunggakan Sewa Tak Terlacak:** Penagihan bersandar pada ingatan pengelola tanpa eskalasi resmi ke nomor kontak wali mahasiswa.
> 2. **Keterlambatan Pembukuan Kas:** Rekonsiliasi bulanan menyita 3 hingga 5 hari kerja dengan risiko tinggi selisih hitung uang fisik.
> 3. **Pemesanan Ganda (*Double-Booking*):** Permintaan kamar memuncak pada tahun ajaran baru; calon penghuni walk-in dan pemesan daring kerap berebut unit yang sama.

##### Kartu 2: `[Ikon Target] OBJECTIVE`
> 1. **Rekayasa Sistem Terintegrasi:** Mengembangkan platform web Laravel 11 & MySQL 8.0 3NF yang menyatukan katalog digital, pembayaran nirkas Midtrans Snap BCA VA, dan bot WhatsApp Fonnte.
> 2. **Resolusi Tiga Hambatan Pokok:** Menjamin penagihan disiplin tiap awal bulan, mewujudkan rekonsiliasi kas instan tanpa selisih (*zero cash variance*), serta memastikan peniadaan bentrok jadwal kamar (*zero double-booking*).
> 3. **Evaluasi Empiris Lapangan:** Mengukur tingkat kepuasan operasional pengelola properti dan kemudahan alur sewa bagi mahasiswa penghuni aktif di lingkungan produksi riil.

##### Kartu 3: `[Ikon Roda Gigi / Alur] METHODOLOGY`
> - **Model Pengembangan:** Siklus Linear-Sekuensial Waterfall (Analisis Kebutuhan $\rightarrow$ Pemodelan UML $\rightarrow$ Pengodean MVC $\rightarrow$ Pengujian Bertingkat $\rightarrow$ Rilis Produksi).
> - **Arsitektur Perangkat Lunak:** Laravel 11 (PHP 8.2 runtime) dengan pemisahan domain logika ke dalam Service Classes (*BillingService*, *MidtransService*, *ReservasiService*).
> - **Integritas Basis Data (MySQL 8.0 InnoDB 3NF):**  
>   - *Virtual Generated Columns* (`active_email`, `active_nik`, `active_nomor_kamar`) untuk menjamin keunikan data saat *soft-delete*.
>   - *Pessimistic Row Locking* (`Kamar::lockForUpdate()`) di dalam transaksi atomik database (`DB::transaction`) guna memblokir pemesanan serentak pada detik yang sama.

---

#### KOLOM KANAN (2 Kartu Dinamis + Cuplikan Visual)

##### Kartu 1: `[Ikon Grafik Naiknya Inovasi] RESULT`
> Sistem menghadirkan empat otomasi operasional utama:
> - **Pemesanan Mandiri 5 Langkah:** Calon penghuni memeriksa unit secara *real-time*, memvalidasi format NIK 16 digit, dan memilih pembayaran lunas atau DP 30%.
> - **Penyelesaian Nirkas Midtrans Snap v2:** Pembayaran BCA Virtual Account berbatas 24 jam dengan verifikasi *webhook* kriptografis SHA-512 untuk menangkal bukti transfer manipulatif.
> - **Mesin Penagihan Mandiri (*Cron Billing Engine*):** Tagihan terbit otomatis setiap tanggal 1 pukul 00:05 WIB via WhatsApp. Sistem memberlakukan toleransi intra-bulan Rp0 serta denda kalender 5% yang dihitung secara *idempotent* bila melewati batas bulan.
> - **Kuitansi Sah Sisi Klien:** Dokumen format A5 dirangkum seketika di peramban via *html2pdf.js*, menjamin *zero server storage overhead*.
> 
> *(Letakkan gambar cuplikan `antarmuka_stepper_reservasi_penyewa.png` atau `antarmuka_dasbor_admin_keuangan.png` di sudut kanan bawah kartu ini).*

##### Kartu 2: `[Ikon Analisis Kacamata Pembesar] ANALYSIS`
> **Validasi Fungsional & Ketahanan Akses:**
> - **100% Lolos Uji Black-Box:** Tuntas melintasi 60 skenario behavioral tanpa kegagalan logika.
> - **510 Automated Integration Tests:** Memenuhi 2.211 asersi PHPUnit, menolak mutlak serangan manipulasi IDOR dan pembobolan hak akses (RBAC).
> 
> **Evaluasi Empiris Pengguna Ganda (*Dual-Stakeholder*):**
> - **Pengelola Senior (Bapak Asep, 48 th, 20 th masa kerja):** Memberikan nilai kepuasan operasional **9,5 / 10**. Waktu tutup buku kas bulanan dipangkas dari 3–5 hari kerja menjadi instan dengan *zero cash variance* atas omzet Rp28,5 juta.
> - **Mahasiswa Penghuni Aktif (5 Responden Lintas Kampus):** Memberikan rata-rata skor **8,00 / 10**. Responden menegaskan platform sangat mempermudah rutinitas indekos, menghapus antrean ATM malam hari, dan menyediakan kuitansi digital akuntabel bagi orang tua.
> 
> *(Letakkan grafik mini / badge skor kepuasan 9,5 dan 8,00 di sudut kanan bawah kartu ini).*

---

### BAGIAN 4: STRIP VISUALISASI DATA (3 KARTU GRAFIK SEJAJAR)

*(Format: Satu kontainer putih memanjang dengan 3 kolom grafik simetris).*

#### Grafik 1: `INVENTARIS & TARIF KAMAR` (Bar Chart)
Grafik batang vertikal menunjukkan komposisi 32 unit kamar sewa:
- **Kelas Standard:** 23 Unit | Tarif: Rp750.000 / bln (Batang tertinggi - warna Toska Muda `#52D1C5`)
- **Kelas VIP:** 6 Unit | Tarif: Rp1.400.000 / bln (Batang sedang - warna Toska Gelap `#00A896`)
- **Kelas Deluxe:** 3 Unit | Tarif: Rp950.000 / bln (Batang terpendek - warna Navy `#0B253A`)
- *Keterangan:* Kapasitas total 32 unit kamar terkelola penuh secara terpusat.

#### Grafik 2: `DISTRIBUSI OMZET BULANAN` (Donut / Pie Chart)
Grafik lingkaran menunjukkan kontribusi dari total basis omzet kotor **Rp28.500.000 / bulan**:
- **Standard (23 Unit):** Rp17.250.000 (**60,5%**) — Sektor terbesar (Warna Cyan `#00A896`)
- **VIP (6 Unit):** Rp8.400.000 (**29,5%**) — Sektor menengah (Warna Navy `#0B253A`)
- **Deluxe (3 Unit):** Rp2.850.000 (**10,0%**) — Sektor pelengkap (Warna Mint `#7FE3D9`)
- *Label Tengah Donut:* **Rp28,5 Juta** (Gross Revenue Baseline).

#### Grafik 3: `EFISIENSI WAKTU PEMBUKUAN KAS` (Line / Comparison Chart)
Grafik kurva penurunan drastis durasi rekonsiliasi kas operasional kost:
- **Sebelum Sistem (Manual Nota Karbon):** 3 – 5 Hari Kerja (4.320 – 7.200 Menit)
- **Sesudah Sistem (Digital Terintegrasi):** **0 Detik / Instan (< 1 Detik)**
- **Parameter Kunci Tercapai:**  
  - Selisih Kas: **0% (Zero Cash Variance)**  
  - Kasus Pemesanan Ganda: **0 Kasus (Zero Double-Booking)**

---

### BAGIAN 5: BOTTOM DARK BANNER (KESIMPULAN, TAUTAN PRODUKSI, & QR CODE)

*(Format: Kotak kapsul melengkung dengan latar Deep Petrol Navy `#0B253A`, teks putih bersih).*

- **Ikon Sisi Kiri:** Lingkaran garis putih berisi ikon bola lampu pijar (*lightbulb*).
- **Judul Bagian:** **CONCLUSION**
- **Naskah Teks:**  
  *Sistem Informasi Manajemen Asri Boarding House membuktikan bahwa integrasi payment gateway nirkas yang dikombinasikan dengan penguncian basis data pesimistik dan otomasi penagihan WhatsApp sukses melenyapkan selisih kas pembukuan, mencegah tabrakan reservasi serentak, serta menyederhanakan alur sewa bagi mahasiswa. Pengembangan masa depan diarahkan pada adopsi QRIS/E-Wallet terpadu dan integrasi perangkat keras IoT Smart Door Lock berbasis PIN dinamis.*
- **Sisi Kanan:**  
  - **Gambar QR Code:** Mengarah ke tautan `https://asriboardinghouse.weatso.id/`
  - **Teks Pendamping:**  
    *Scan Live Demo*  
    *asriboardinghouse.weatso.id*

---

## 4. Tabel Data Mentah untuk Pembuat Grafik (Canva / Figma / Excel)

Salin data di bawah ini langsung ke tabel data grafik (*chart data spreadsheet*) pada aplikasi desain Anda:

### 4.1 Data Bar Chart (Inventaris Kamar & Tarif)
| Tipe Kamar | Jumlah Unit (Kapasitas) | Tarif Sewa Per Bulan (IDR) |
| :--- | :---: | :---: |
| Standard | 23 | Rp750.000 |
| VIP | 6 | Rp1.400.000 |
| Deluxe | 3 | Rp950.000 |

### 4.2 Data Donut Chart (Sebaran Omzet Bulanan)
| Kelas Kamar | Nilai Omzet Kotor (IDR) | Persentase Kontribusi (%) |
| :--- | :---: | :---: |
| Standard | Rp17.250.000 | 60,5% |
| VIP | Rp8.400.000 | 29,5% |
| Deluxe | Rp2.850.000 | 10,0% |

### 4.3 Data Line / Efisiensi Chart (Perbandingan Sebelum vs Sesudah)
| Indikator Kinerja | Metode Konvensional (Sebelum) | Sistem Baru (Sesudah) |
| :--- | :---: | :---: |
| Durasi Rekonsiliasi Kas | 72 Jam (3–5 Hari Kerja) | 0 Jam (Instan / < 1 Detik) |
| Selisih Kas Bulanan | Rawan Selisih Nota Kertas | 0% (Zero Variance Terverifikasi) |
| Risiko Double-Booking | Sering Terjadi Saat Intake | 0 Kasus (Pessimistic Lock) |

---

## 5. Matriks Penempatan Gambar Aset Proyek (`Skripsi/images/`)

| Komponen Poster | Berkas Aset di `Skripsi/images/` | Rekomendasi Format & Posisi |
| :--- | :--- | :--- |
| **Header Samping** | `logo_unika.png` | Format PNG transparan, letakkan di samping kiri judul poster (tinggi proporsional ± 50 mm). |
| **Kartu RESULT** | `antarmuka_stepper_reservasi_penyewa.png` | Cuplikan antarmuka *mobile viewport*, beri bingkai tipis dengan bayangan lembut (*drop shadow*) di sudut kanan bawah kartu. |
| **Kartu ANALYSIS** | `antarmuka_dasbor_admin_keuangan.png` (atau mini-gauge skor 9.5 & 8.00) | Mockup kartu ringkas menunjukkan angka kepuasan 9,5 dan 8,00. |
| **Kartu METHODOLOGY** | `flowchart_transisi_reservasi.png` (opsional diagram alir) | Diagram alir transisi reservasi mini jika ingin memperkuat visual rekayasa atomik. |
| **Banner CONCLUSION** | QR Code Generator menuju `https://asriboardinghouse.weatso.id/` | Kotak putih kontras tajam ukuran 45 × 45 mm di sudut kanan banner penutup. |

---

## 6. Panduan Eksekusi Desain di Canva atau Figma

1. **Membuat Kanvas:**
   - Di Canva/Figma, buat dokumen khusus ukuran **594 × 841 mm** (atau rasio 1 : 1.414).
2. **Membuat Grid 2 Kolom:**
   - Kolom kiri: lebar sekitar 42% dari lebar bodi.
   - Kolom kanan: lebar sekitar 54% dari lebar bodi.
   - Jarak antar-kolom (*gutter*): 4%.
3. **Membuat Bentuk Kartu (*Rounded Cards*):**
   - Gunakan bentuk persegi panjang dengan *Corner Radius* 14 px.
   - Warna isi: Putih (`#FFFFFF`), Garis tepi (*Stroke*): 1.5 px warna `#CBD5E1`.
4. **Membuat Dark Banner Kapsul:**
   - Gunakan bentuk persegi panjang dengan *Corner Radius* 22 px.
   - Warna isi: `#0B253A`.
5. **Membuat Ornamen Dot Matrix:**
   - Di Canva, cari elemen "Dot Grid Matrix" atau "Halftone Dots". Warnai dengan `#0D2F44` dengan transparansi/opasitas 30%. Tempatkan di pojok kanan atas dan kiri bawah.
6. **Ekspor Akhir:**
   - Pilih format **PDF Print** (Cetak PDF).
   - Centang opsi **Crop marks and bleed** jika percetakan Anda memintanya.
   - Pastikan opsi profil warna disetel ke **CMYK** sebelum dikirim ke percetakan.
