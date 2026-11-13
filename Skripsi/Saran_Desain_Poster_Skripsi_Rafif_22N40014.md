# Panduan Desain & Naskah Siap Pakai Poster Skripsi
## Format Tata Letak: Poster Riset Akademik Modular (Teal & Navy)

**Judul Karya Ilmiah:** Sistem Informasi Manajemen Kost Terintegrasi Payment Gateway pada Asri Boarding House  
**Peneliti:** Rafif Arsya Pradiva (NIM: 22.N4.0014)  
**Dosen Pembimbing:** Ir. Andre Kurniawan Pamudji, S.Kom., M.Ling. & Erdhi Widyarto Nugroho, S.T., M.T.  
**Program Studi:** Sistem Informasi, Fakultas Ilmu Komputer, Universitas Katolik Soegijapranata Semarang  
**Tautan Demo Aplikasi:** [https://asriboardinghouse.weatso.id/](https://asriboardinghouse.weatso.id/) *(pastikan tautan masih aktif sebelum QR Code dicetak)*  
**Sumber Acuan Resmi:** `Skripsi/skripsi_v2_Rafif_Arsya_Pradiva_22.N4.0014.md` & `Skripsi/journal_inggris_Rafif_Arsya_Pradiva_22.N4.0014.md`

---

## 1. Spesifikasi Teknis & Tata Letak Poster Kampus (Ukuran A1)

Poster ini dirancang agar nyaman dibaca oleh mahasiswa dan dosen di area Fakultas Ilmu Komputer dari jarak 1 hingga 1,5 meter dalam waktu pemindaian 15 sampai 30 detik:

- **Header Atas:** Logo Unika di sisi kiri mendampingi judul tebal bergaris bawah tegas, sedangkan kartu identitas peneliti dan pembimbing diletakkan di sisi kanan.
- **Banner Ringkasan Atas:** Kapsul berlatar gelap berisi inti masalah dan hasil utama sistem, disertai ikon buku di sisi kiri.
- **Bodi Utama 2 Kolom Modular:**
  - Kolom Kiri (42%): 3 kartu bersusun (`PENDAHULUAN`, `TUJUAN`, `METODOLOGI`).
  - Kolom Kanan (54%): 2 kartu lebih lebar (`HASIL SISTEM` dilengkapi cuplikan antarmuka, dan `PENGUJIAN & EVALUASI`).
- **Strip Visualisasi Data (3 Panel Sejajar):** Berisi *Bar Chart* (Komposisi 32 Unit Kamar), *Donut Chart* (Cakupan 60 Skenario *Black-Box Testing* yang Lolos 100%), serta *Horizontal Bar Chart* dan Lencana Dampak (Skor UAT Lapangan & Efisiensi Tutup Buku).
- **Banner Penutup Bawah:** Kapsul gelap berisi `KESIMPULAN`, pengembangan lanjutan, serta QR Code demo aplikasi di sisi kanan.
- **Ornamen Grafis:** Pola titik (*dot matrix grid*) tipis di sudut kanan atas dan kiri bawah kanvas sebagai penyeimbang ruang kosong.

---

### 1.1 Parameter Fisik & Cetak
- **Ukuran Kanvas:** **A1 Potret (594 x 841 mm)** (dapat diskalakan ke A0: 841 x 1189 mm).
- **Resolusi Cetak:** 300 DPI (7016 x 9933 px untuk A1).
- **Profil Warna:** CMYK untuk hasil cetak pekat.
- **Batas Aman (*Safe Margin*):** Minimal 20 mm dari seluruh tepi luar kanvas.
- **Radius Sudut (*Border Radius*):**
  - Kartu Bodi & Grafik: 12 sampai 16 px.
  - Banner Kapsul Atas & Bawah: 20 sampai 24 px.

---

### 1.2 Palet Warna Templat
| Peran Desain | Kode HEX | Karakter Visual | Penempatan pada Poster |
| :--- | :--- | :--- | :--- |
| **Dark Petrol Navy** | `#0B253A` / `#0D2F44` | Gelap, Tegas, Kontras | Latar banner atas dan bawah, judul utama, serta garis tebal header. |
| **Vibrant Teal** | `#00A896` / `#20A39E` | Modern, Segar, Fokus Sistem | Lencana ikon bagian, aksen garis judul kartu, dan batang grafik utama. |
| **Soft Mint** | `#52D1C5` / `#7FE3D9` | Terang, Komplementer | Irisan sekunder grafik dan aksen penanda sorotan data. |
| **Card Surface** | `#FFFFFF` | Putih Bersih | Latar seluruh kartu konten dan panel grafik agar tulisan mudah terbaca. |
| **Card Border** | `#CBD5E1` / `#94A3B8` | Bingkai Halus (1,5 pt) | Garis tepi pembatas antarkartu. |
| **Canvas Background** | `#F8FAFC` / `#F1F5F9` | Abu-Abu Sangat Lembut | Dasar kanvas luar untuk memisahkan kartu putih secara rapi. |
| **Dot Grid Ornament** | `#0D2F44` (Opasitas 30%) | Aksen Minimalis | Dekorasi sudut kanan atas dan kiri bawah kanvas. |

---

### 1.3 Hierarki Ukuran Huruf (Skala Cetak A1)
- **Judul Utama Poster:** 64 hingga 74 pt, *Extra Bold / Black*, huruf kapital (*Plus Jakarta Sans*, *Montserrat*, atau *Inter*).
- **Nama Penulis pada Kartu Kanan Atas:** 24 hingga 28 pt, *Bold*.
- **Program Studi, Fakultas, & Dosen Pembimbing:** 14 hingga 16 pt, *Medium*.
- **Teks Banner Atas & Bawah:** 16 hingga 18 pt, *Regular / Medium*, warna putih bersih.
- **Tajuk Kartu Bagian:** 18 hingga 22 pt, *Bold*, huruf kapital.
- **Isi Teks Kartu:** 14 hingga 16 pt, *Regular*, spasi baris (*line-height*) 1,45.
- **Label & Nilai Grafik:** 12 hingga 14 pt, *Bold / Semi-bold*.

---

## 2. Cetak Biru Wireframe (Kanvas A1)

```
                                                              [o o o o o] (Dot Grid)
+----------------------------------------------------------------------------------+
| [LOGO] SISTEM INFORMASI MANAJEMEN KOST             +---------------------------+ |
| UNIKA  TERINTEGRASI PAYMENT GATEWAY                | (o) RAFIF ARSYA PRADIVA   | |
|        PADA ASRI BOARDING HOUSE                    |     NIM: 22.N4.0014       | |
| ==========================================         |     SI - FIKOM Unika      | |
| (Garis Tebal Navy)                                 |     Pembimbing 1 & 2      | |
|                                                    +---------------------------+ |
| +------------------------------------------------------------------------------+ |
| | [Ikon] Mengelola 32 kamar dengan buku tulis dan WhatsApp pribadi memicu      | |
| |        bentrok sewa serta menyita 3-4 hari untuk tutup buku. Aplikasi web    | |
| |        Laravel 12 & MySQL ini menyatukan reservasi, penguncian kamar,        | |
| |        Midtrans Snap, dan pengingat otomatis: 60 uji black-box lolos 100%.   | |
| +------------------------------------------------------------------------------+ |
|                                                                                  |
| +----------------------------------+  +----------------------------------------+ |
| | [Ikon] PENDAHULUAN              |  | [Ikon] HASIL SISTEM                    | |
| | - 32 kamar (20 th manual)        |  | - 36 halaman Neo-Brutalism & OLED      | |
| | - Double booking saat TA baru    |  | - Reservasi 5 tahap + karantina kamar  | |
| | - Tagih tgl 10 kerap terlewat    |  | - Midtrans BCA VA + SHA-512 + Nota A5  | |
| | - Tutup buku 3-4 hari kerja      |  | - Cron tgl 1 (00:05 WIB) + denda 5%    | |
| +----------------------------------+  |         [Mockup Stepper & Dasbor OLED] | |
| | [Ikon] TUJUAN                    |  +----------------------------------------+ |
| | - Arsitektur 3 peran terpadu     |  | [Ikon] PENGUJIAN & EVALUASI            | |
| | - Web Laravel 12 + Midtrans Snap |  | - 60 uji Black-Box (6 domain): 100%    | |
| | - Uji Black-Box & evaluasi nyata |  | - Penjaga kost (Bpk Asep): 9,5 / 10    | |
| +----------------------------------+  |   Tutup buku 3-4 hari -> hitungan detik| |
| | [Ikon] METODOLOGI                |  | - 5 penghuni aktif: rata-rata 8,0 / 10 | |
| | - R&D + Waterfall 6 tahap        |  |   (UNDIP, USM, UDINUS, PKN STAN, UI)   | |
| | - UML 3 aktor & MySQL 3NF        |  +----------------------------------------+ |
| | - Service Layer + lockForUpdate()|                                             |
| +----------------------------------+                                             |
|                                                                                  |
| +------------------------------------------------------------------------------+ |
| | +--------------------+   +--------------------+   +------------------------+ | |
| | | KOMPOSISI 32 KAMAR |   | 60 UJI BLACK-BOX   |   | SKOR UAT & DAMPAK      | | |
| | |    (BAR CHART)     |   |   (DONUT CHART)    |   | (HORIZONTAL BAR + KPI) | | |
| | | Standar : 23 unit  |   | Total: 60 Skenario |   | Asep: 9,5 | Mhs: 8,0   | | |
| | | VIP     : 6 unit   |   | Status: 100% Lolos |   | Rekap: 3-4 hr -> Detik | | |
| | | Deluxe  : 3 unit   |   | 6 Domain Sistem    |   | Selisih Kas: 0%        | | |
| | +--------------------+   +--------------------+   +------------------------+ | |
| +------------------------------------------------------------------------------+ |
|                                                                                  |
| +------------------------------------------------------------------------------+ |
| | [Ikon] KESIMPULAN                                              [ QR CODE ]   | |
| |        Kontrol 3 peran, lockForUpdate(), dan Midtrans Snap     Pindai Demo   | |
| |        menertibkan reservasi, tagihan, dan kas 32 kamar.       asriboarding  | |
| |        Lanjutan: WA H-3, e-contract, & multi-cabang.           house.weatso  | |
| +------------------------------------------------------------------------------+ |
+----------------------------------------------------------------------------------+
[o o o o o] (Dot Grid)
```

---

## 3. Naskah Siap Pakai Poster (Bahasa Indonesia, Ringkas untuk Kanvas A1)

### BAGIAN 1: HEADER & KARTU IDENTITAS

#### Judul Utama (Kiri Atas):
**SISTEM INFORMASI MANAJEMEN KOST TERINTEGRASI PAYMENT GATEWAY PADA ASRI BOARDING HOUSE**

*(Garis Pembatas Bawah: Garis horizontal tebal 4 sampai 6 pt warna `#0B253A`).*

#### Kartu Penulis (Kanan Atas):
- **Ikon Profil:** Siluet dalam lingkaran warna `#00A896`.
- **Peneliti:** **RAFIF ARSYA PRADIVA**
- **NIM:** 22.N4.0014
- **Institusi:** Program Studi Sistem Informasi, Fakultas Ilmu Komputer, Universitas Katolik Soegijapranata Semarang
- **Pembimbing:** Ir. Andre Kurniawan Pamudji, S.Kom., M.Ling. | Erdhi Widyarto Nugroho, S.T., M.T.

---

### BAGIAN 2: BANNER RINGKASAN ATAS

*(Kotak kapsul `#0B253A`, teks putih, ikon buku di kiri).*

> Mengelola 32 kamar lewat buku tulis dan WhatsApp pribadi kerap memicu bentrok sewa serta membuat tutup buku menyita 3 sampai 4 hari. Aplikasi Laravel 12 dan MySQL InnoDB ini menyatukan reservasi daring, penguncian kamar otomatis, Midtrans Snap, serta pengingat terjadwal: 60 uji *black-box* lolos 100% dan rekap kas rampung dalam hitungan detik.

---

### BAGIAN 3: BODI UTAMA (2 KOLOM)

#### KOLOM KIRI

##### 1. PENDAHULUAN
> Mahasiswa perantau terus memadati Tembalang setiap tahun ajaran baru. Selama 20 tahun, Asri Boarding House mencatat 32 kamar (6 VIP, 3 Deluxe, 23 Standar) secara manual, memicu tiga kendala:
> - **Pemesanan ganda:** Pembaruan status kamar terlambat dan peminat luar kota sulit mengecek unit kosong.
> - **Penagihan terlewat:** Pengingat tanggal 10 dan hitungan denda dikirim satu per satu lewat pesan pribadi.
> - **Rekap kas lambat:** Mencocokkan tunai dan bukti transfer akhir bulan memakan 3 sampai 4 hari.

##### 2. TUJUAN
> 1. Merancang arsitektur sistem kost terpadu untuk admin, calon penyewa, dan penghuni aktif.
> 2. Membangun aplikasi web Laravel 12 dengan penguncian baris basis data, Midtrans Snap, *cron job* tagihan, serta notifikasi WhatsApp dan email.
> 3. Menguji keandalan fungsi lewat *black-box testing* dan mengevaluasi kepuasan pengguna langsung di lapangan.

##### 3. METODOLOGI
> Studi kasus R&D ini mengikuti model *Waterfall* (analisis → desain → implementasi → pengujian → verifikasi → operasi).
> - **UML & MySQL InnoDB (3NF):** Memetakan tiga peran pengguna serta basis data Modul Inti (6 tabel transaksi plus *snapshot* tarif kontrak) dan Modul Pendukung.
> - **Laravel 12 Service Layer:** Logika bisnis terpisah di balik `RoleMiddleware`, memakai transaksi ACID dan `Kamar::lockForUpdate()` untuk mencegah pemesanan ganda.

---

#### KOLOM KANAN

##### 4. HASIL SISTEM
> Tersedia **36 halaman web responsif** bergaya *Neo-Brutalism* plus mode gelap *OLED* admin:
> - **Reservasi 5 Tahap & Karantina:** Pemesan memvalidasi NIK 16 digit lalu memilih DP 30% atau lunas 100%, selaras dengan input *walk-in*. Pasca-*checkout*, kamar terkunci merah hingga inspeksi kebersihan tuntas.
> - **Midtrans Snap & Nota A5:** *Virtual Account* BCA (24 jam) diverifikasi *webhook* SHA-512 idempoten (`hash_equals()`), langsung menghasilkan nota A5 berstempel **LUNAS** via `html2pdf.js`.
> - **Cron Job & Notifikasi:** Faktur terbit otomatis tiap tanggal 1 pukul 00:05 WIB via WhatsApp Fonnte dan antrean email SMTP (`ShouldQueue`). Lewat tanggal 10 bebas denda selama bulan berjalan, lalu kena denda *flat* 5% satu kali lintas bulan.
>
> *(Tempatkan cuplikan `antarmuka_stepper_reservasi_penyewa.png` dan `antarmuka_dasbor_admin_keuangan.png` berbingkai peramban di bagian bawah kartu ini).*

##### 5. PENGUJIAN & EVALUASI
> - **60 Skenario Black-Box (100% Lolos):** Uji enam domain (autentikasi, katalog, reservasi, portal penyewa, admin, dan penjadwal API) berjalan tanpa galat.
> - **Penjaga Kost (Bapak Asep, 20 th): 9,5 / 10.** Tutup buku menyusut dari 3 sampai 4 hari ke hitungan detik via ekspor PDF/Excel tanpa selisih kas.
> - **Lima Penghuni Mahasiswa: Rata-rata 8,0 / 10.** Arjun (UNDIP 8,5), Iman (UDINUS 9,0), Sindhu (UI 8,0), Ammar (PKN STAN 7,5), dan Farrel (USM 7,0) menilai reservasi jarak jauh dan bayar *Virtual Account* jauh lebih praktis.

---

### BAGIAN 4: STRIP VISUALISASI DATA (3 PANEL SEJAJAR)

*(Gunakan satu kontainer putih memanjang berisi 3 panel grafik simetris).*

#### Grafik 1: `KOMPOSISI 32 UNIT KAMAR` (Bar Chart)
Menampilkan sebaran unit fisik Asri Boarding House:
- **Standar:** 23 unit (71,9%) | Warna Mint `#52D1C5` *(tarif referensi UI: Rp750.000/bln)*
- **VIP:** 6 unit (18,8%) | Warna Teal `#00A896` *(tarif referensi UI: Rp1.400.000/bln)*
- **Deluxe:** 3 unit (9,4%) | Warna Navy `#0B253A` *(tarif referensi UI: Rp950.000/bln)*

#### Grafik 2: `CAKUPAN 60 UJI BLACK-BOX` (Donut Chart)
Menampilkan pembagian 60 skenario uji yang seluruhnya berhasil 100%:
- **Admin & Operasional (Domain V):** 13 skenario (21,7%)
- **Autentikasi & Profil (Domain I):** 12 skenario (20,0%)
- **Portal Publik & Guest Chat (Domain II):** 10 skenario (16,7%)
- **Simulasi & Reservasi 5 Tahap (Domain III):** 10 skenario (16,7%)
- **Portal Penyewa Aktif & Keluhan (Domain IV):** 9 skenario (15,0%)
- **Otomasi Cron, Denda & API (Domain VI):** 6 skenario (10,0%)
- *Label Tengah Donut:* **60 / 60 (100% Lolos)**

#### Grafik 3: `SKOR EVALUASI UAT & DAMPAK` (Horizontal Bar Chart + 3 Lencana KPI)
Menampilkan penilaian langsung 6 narasumber di lapangan (skala 1 sampai 10):
- **Bapak Asep (Penjaga Kost):** 9,5
- **Iman (UDINUS):** 9,0
- **Arjun (UNDIP):** 8,5
- **Sindhu (UI):** 8,0
- **Ammar (PKN STAN):** 7,5
- **Farrel (USM):** 7,0
- *Garis Rata-Rata 5 Mahasiswa:* **8,0 / 10**
- *Tiga Kotak Angka Dampak di Bawah Grafik:*
  - Tutup Buku: **3 sampai 4 Hari → Hitungan Detik**
  - Selisih Kas: **0%**
  - Bentrok Kamar: **0 Kasus**

---

### BAGIAN 5: BANNER PENUTUP BAWAH

*(Kotak kapsul `#0B253A`, teks putih, ikon lampu di kiri, QR Code di kanan).*

- **Judul:** **KESIMPULAN**
- **Naskah Teks:**
  > Kontrol tiga peran, penguncian baris `lockForUpdate()`, dan integrasi Midtrans Snap menertibkan reservasi, penagihan otomatis, serta kas 32 kamar tanpa bentrok unit maupun selisih uang. Pengembangan lanjutan mengarah pada pengingat WhatsApp H-3 bertautan langsung, panduan visual awal portal, *caching* awal bulan, kontrak sewa digital, serta dukungan multi-cabang dan *smart lock*.
- **Sisi Kanan (QR Code):**
  - Tautan tujuan: `https://asriboardinghouse.weatso.id/`
  - Teks pendamping: **Pindai Demo Aplikasi** (`asriboardinghouse.weatso.id`)

---

## 4. Tabel Data Mentah untuk Canva, Figma, atau Excel

### 4.1 Data Grafik 1 (Komposisi 32 Unit Kamar)
| Tipe Kamar | Jumlah Unit | Persentase (%) | Tarif Referensi Aplikasi / Bulan |
| :--- | :---: | :---: | :---: |
| Standar | 23 | 71,9% | Rp750.000 |
| VIP | 6 | 18,8% | Rp1.400.000 |
| Deluxe | 3 | 9,4% | Rp950.000 |

### 4.2 Data Grafik 2 (Sebaran 60 Skenario Black-Box Testing)
| Domain Pengujian | Jumlah Skenario | Persentase (%) | Hasil |
| :--- | :---: | :---: | :---: |
| Domain V: Admin & Operasional | 13 | 21,7% | 100% Berhasil |
| Domain I: Autentikasi & Profil | 12 | 20,0% | 100% Berhasil |
| Domain II: Portal Publik & Guest Chat | 10 | 16,7% | 100% Berhasil |
| Domain III: Simulasi & Reservasi | 10 | 16,7% | 100% Berhasil |
| Domain IV: Portal Penyewa & Keluhan | 9 | 15,0% | 100% Berhasil |
| Domain VI: Cron Job, Denda & API | 6 | 10,0% | 100% Berhasil |

### 4.3 Data Grafik 3 (Skor Evaluasi Lapangan Skala 1 sampai 10)
| Narasumber | Peran / Asal Kampus | Skor | Catatan Utama |
| :--- | :--- | :---: | :--- |
| Bapak Asep | Penjaga Kost (20 th pengalaman) | 9,5 | Tutup buku menyusut ke hitungan detik, tanpa selisih kas |
| Iman | Penghuni Aktif (UDINUS) | 9,0 | Mutasi otomatis mencegah sengketa nota hilang |
| Arjun | Penghuni Aktif (UNDIP) | 8,5 | Katalog jelas dan nota A5 rapi untuk orang tua |
| Sindhu | Penghuni Aktif (UI) | 8,0 | Verifikasi langsung tanpa tunggu balasan manual |
| Ammar | Penghuni Aktif (PKN STAN) | 7,5 | Lancar di Safari iPhone saat bayar dari luar kota |
| Farrel | Penghuni Aktif (USM) | 7,0 | Praktis dari HP tanpa antre ATM malam hari |
| **Rata-Rata Mahasiswa** | **5 Responden Penghuni Aktif** | **8,0** | **Memudahkan reservasi hingga bayar rutin** |

---

## 5. Matriks Penempatan Aset Gambar (`Skripsi/images/`)

Seluruh berkas di bawah ini telah diperiksa langsung agar sesuai dengan isi `skripsi_v2` dan proporsional di kanvas A1:

| Posisi di Poster | Nama Berkas di `Skripsi/images/` | Panduan Penempatan |
| :--- | :--- | :--- |
| **Header Kiri** | `logo_unika.png` | Letakkan di sebelah kiri judul utama dengan tinggi proporsional sekitar 45 sampai 50 mm. |
| **Kartu HASIL SISTEM (Utama)** | `antarmuka_stepper_reservasi_penyewa.png` | Tangkapan layar *desktop* peramban menunjukkan reservasi 5 tahap lunas. Beri bingkai tipis dan bayangan halus di area kanan bawah kartu. |
| **Kartu HASIL SISTEM (Pendamping)** | `antarmuka_dasbor_admin_keuangan.png` | Tangkapan layar *desktop* dasbor admin mode gelap *OLED*. Dapat disandingkan kecil bertumpuk (*overlapping mockup*) bersama gambar stepper. |
| **Kartu HASIL SISTEM (Alternatif)** | `gambar_4_42_nota_pdf.png` atau `gambar_4_22_payment_gateway_midtrans.png` | Opsi pengganti jika ingin menonjolkan wujud nota A5 berstempel **LUNAS** atau *pop-up* BCA *Virtual Account*. |
| **Kartu METODOLOGI (Opsional)** | `metode_sdlc_waterfall.png` | Gunakan bila masih ada ruang kosong; jauh lebih ringkas dan terbaca dibanding diagram alir vertikal panjang. |
| **Banner KESIMPULAN** | QR Code Generator ke `https://asriboardinghouse.weatso.id/` | Buat berlatar kotak putih kontras tajam ukuran 45 x 45 mm di sudut kanan banner penutup. |

> **Catatan Penghindaran Gambar:** Jangan memasang `keluaran_terminal_pengujian_phpunit_passed.png` (karena menampilkan angka `715 passed / 2793 assertions` yang berbeda dari data pengujian di `skripsi_v2`) maupun `flowchart_transisi_reservasi.png` (rasio vertikal ~1:3 terlalu memanjang sehingga teksnya sulit terbaca di ukuran A1).

---

## 6. Panduan Eksekusi di Canva atau Figma

1. **Siapkan Kanvas A1:** Atur ukuran khusus **594 x 841 mm** dengan batas panduan (*margin*) 20 mm di tiap sisi.
2. **Buat Grid 2 Kolom:** Kolom kiri selebar 42%, kolom kanan selebar 54%, dan jarak antarkolom (*gutter*) 4%.
3. **Susun Kartu & Banner:**
   - Kartu konten: persegi panjang putih `#FFFFFF`, *corner radius* 14 px, garis tepi 1,5 px `#CBD5E1`.
   - Banner atas & bawah: persegi panjang `#0B253A`, *corner radius* 22 px, teks putih `#FFFFFF`.
4. **Tempelkan Cuplikan UI Lanskap:** Potong bagian *bookmark bar* Chrome pada gambar `antarmuka_stepper_reservasi_penyewa.png` dan `antarmuka_dasbor_admin_keuangan.png` sebelum dipasang agar terlihat seperti *mockup* aplikasi yang bersih.
5. **Uji Pracetak:**
   - Pindai QR Code memakai kamera ponsel untuk memastikan tautan terbuka lancar.
   - Ekspor dalam format **PDF Print** profil **CMYK** dengan resolusi tinggi (centang *crop marks and bleed* bila diminta percetakan).
