# Panduan Desain & Naskah Siap Pakai Poster Skripsi (Revisi A1)
## Konsep Tata Letak: Poster Akademik Dua Kolom Modern Kartu *Sans-Serif* dengan 4 Visual

**Judul Karya Ilmiah:** Sistem Informasi Manajemen Kost Terintegrasi Payment Gateway pada Asri Boarding House  
**Peneliti:** Rafif Arsya Pradiva (`22.N4.0014`)  
**Dosen Pembimbing:** Ir. Andre Kurniawan Pamudji, S.Kom., M.Ling. & Erdhi Widyarto Nugroho, S.T., M.T.  
**Program Studi:** Sistem Informasi, Fakultas Ilmu Komputer, Universitas Katolik Soegijapranata Semarang  
**Target Audiens Utama:** Mahasiswa Fakultas Ilmu Komputer (dalam dan luar kampus), dosen penguji, serta dosen pembimbing  
**Ukuran Cetak Resmi:** Kertas **A1 Potret (`594 x 841 mm`)**, 300 DPI (`7016 x 9933 px`), CMYK  
**Tautan Demo Aplikasi:** [https://asriboardinghouse.weatso.id/](https://asriboardinghouse.weatso.id/) *(referensi portofolio)*  
**Sumber Acuan Resmi:** `Skripsi/skripsi_v2_Rafif_Arsya_Pradiva_22.N4.0014.md` & `Skripsi/journal_inggris_Rafif_Arsya_Pradiva_22.N4.0014.md`

---

## 1. Evaluasi Draf Sebelumnya & Matriks Revisi Optimal

Revisi ini menjawab langsung catatan dosen pembimbing agar setiap subjudul cukup memuat **1 paragraf ringkas (2 hingga 4 kalimat *to the point*)**, menata **4 gambar** secara lega dan seimbang pada kertas **A1 (`594 x 841 mm`)**, serta mempertahankan gaya kartu putih modern *Sans-Serif* dari draf poster Anda agar nyaman dibaca mahasiswa Fakultas Ilmu Komputer, dosen penguji, dan dosen pembimbing dari jarak 1,5 hingga 2 meter:

| Unsur Poster | Kondisi Draf Sebelumnya | Rekomendasi Revisi Optimal (A1 `594 x 841 mm`) |
| :--- | :--- | :--- |
| **Kepadatan Naskah** | 5 hingga 8 kalimat per subjudul (total 34 kalimat, ~694 kata, 11 sampai 18 baris per kartu). | Dipangkas **47% kalimat / 48% kata** menjadi tepat **1 paragraf berisi 3 sampai 4 kalimat** per subjudul (total 18 kalimat, 362 kata, sekitar 6 sampai 9 baris lega per kartu). |
| **Gaya Bahasa** | Cukup informatif tetapi masih terasa panjang saat dipindai cepat pada pameran poster. | Ragam *hybrid* akademis-humanis yang luwes, mengalir, SPOK dinamis (*burstiness*), bersih dari kata rancu maupun kaku, bebas klise AI, dan bebas tanda baca *em-dash*. |
| **Komposisi 4 Gambar** | Baru memuat 3 tangkapan layar antarmuka di Kolom Kanan (`Gambar 1` sampai `Gambar 3`). | Ditata fokus dan lega: **1 diagram metode di Kolom Kiri** (`Gambar 1 Metode Waterfall` selebar penuh kartu) dan **3 antarmuka di Kolom Kanan** (`Gambar 2`, `Gambar 3`, `Gambar 4`). |
| **Kebersihan Gambar** | Bilah alamat serta *bookmark bar* Chrome masih tampak di bagian atas ketiga layar UI. | Seluruh gambar UI sudah dipotong bersih dan ditingkatkan ketajamannya dalam paket siap tempel `Skripsi/images/poster/` (`223` hingga `287 DPI` di A1). |
| **Header & Identitas** | Ikon kiri masih memakai ikon templat huruf "S", belum ada afiliasi prodi/fakultas, dan baris nama memakai strip panjang. | Memakai `ikon_asri_poster.svg` di kiri, `logo_unika.png` di kanan, nama `Rafif Arsya Pradiva | 22.N4.0014`, serta satu baris afiliasi prodi/fakultas untuk pengunjung luar kampus. |
| **Tipografi & Bobot Teks** | Teks bodi cenderung terlalu tebal (*SemiBold/Bold*) dan rapat sehingga mata cepat lelah. | Keluarga *Sans-Serif* modern (*Plus Jakarta Sans*, *Poppins*, *Montserrat*, atau *Canva Sans*) dengan bobot **Regular (`400`)** ukuran `22 pt`, *line-height* `1,48`, dan rata kiri (*Left Align*). |

---

## 2. Spesifikasi Ukuran, Grid Presisi A1 (`594 x 841 mm`), Warna & Tipografi

### 2.1 Parameter Fisik & Alokasi Tinggi Millimeter
- **Ukuran Kanvas Utama:** **A1 Potret (`594 x 841 mm`)**, rasio `1 : 1,414` (dapat diskalakan langsung ke A2 `420 x 594 mm` atau A3 `297 x 420 mm` bila dibutuhkan).
- **Resolusi & Profil Cetak:** **300 DPI (`7016 x 9933 px`)**, profil warna **CMYK** untuk hasil cetak tajam.
- **Batas Aman (*Safe Margin*):** `22 mm` di keempat tepi luar -> area kerja aktif **`550 x 797 mm`**.
- **Pembagian Area Header (`86 mm` + celah bawah `12 mm` = `98 mm`):**
  - Kolom kiri (`54 x 54 mm`): `ikon_asri_poster.svg`.
  - Kolom tengah (`420 mm`): Judul 3 baris kapital, baris nama + NIM, dan 1 baris afiliasi prodi/fakultas.
  - Kolom kanan (`54 x 54 mm`): `logo_unika.png`.
  - Penutup header: satu garis horizontal tipis `1,5 pt` (`#38BDF8`) selebar `550 mm`.
- **Pembagian Area Bodi Dua Kolom Simetris (Tinggi `699 mm`):**
  - Lebar Kolom Kiri `265 mm` + jarak tengah (*gutter*) `20 mm` + Lebar Kolom Kanan `265 mm` = `550 mm`.
  - *Padding* dalam kartu: `14 mm` kiri-kanan -> lebar efektif teks dan diagram penuh sebesar `237 mm`.

| Kolom Kiri (`265 mm`) | Alokasi Tinggi | Kolom Kanan (`265 mm`) | Alokasi Tinggi |
| :--- | :---: | :--- | :---: |
| **Kartu `ABSTRAK`** (4 kalimat, 71 kata) | `160 mm` | **Kartu `HASIL DAN PEMBAHASAN`** (4 kalimat, 95 kata + 3 layar UI) | `542 mm` |
| Celah antarkartu | `22 mm` | &emsp;Judul, *padding* atas & teks paragraf | *(`162 mm`)* |
| **Kartu `LATAR BELAKANG`** (4 kalimat, 73 kata) | `165 mm` | &emsp;Celah teks ke `Gambar 2` | *(`12 mm`)* |
| Celah antarkartu | `22 mm` | &emsp;`Gambar 2` (`216 x 94 mm`, rata tengah) + keterangan (`11 mm`) | *(`105 mm`)* |
| **Kartu `METODE PENELITIAN`** (3 kalimat, 59 kata + 1 diagram) | `330 mm` | &emsp;Celah ke `Gambar 3` | *(`14 mm`)* |
| &emsp;Judul, *padding* atas & teks paragraf | *(`127 mm`)* | &emsp;`Gambar 3` (`216 x 100 mm`, rata tengah) + keterangan (`11 mm`) | *(`111 mm`)* |
| &emsp;Celah teks ke `Gambar 1` | *(`20 mm`)* | &emsp;Celah ke `Gambar 4` | *(`14 mm`)* |
| &emsp;`Gambar 1` (`237 x 147 mm`, lebar penuh) + keterangan (`14 mm`) | *(`161 mm`)* | &emsp;`Gambar 4` (`216 x 97 mm`, rata tengah) + keterangan (`11 mm`) | *(`108 mm`)* |
| &emsp;*Padding* bawah kartu | *(`22 mm`)* | &emsp;*Padding* bawah kartu | *(`16 mm`)* |
| | | Celah antarkartu | `20 mm` |
| | | **Kartu `KESIMPULAN`** (3 kalimat, 64 kata) | `137 mm` |
| **Total Tinggi Kolom Kiri** | **`699 mm`** | **Total Tinggi Kolom Kanan** | **`699 mm`** |

### 2.2 Palet Warna Kartu Modern (Selaras Draf Aktual)
| Peran Elemen | Kode HEX | Karakter Visual | Penempatan pada Poster |
| :--- | :--- | :--- | :--- |
| **Canvas Base** | `#F4FAFE` | Putih Kebiruan Sangat Lembut | Latar dasar kanvas di belakang seluruh kartu agar tampilan segar dan tidak menyilaukan. |
| **Card Surface** | `#FFFFFF` | Putih Bersih | Isi kelima kartu subjudul agar kontras teks dan diagram selalu tajam saat dicetak. |
| **Card Border** | `#93C5FD` | Biru Langit Lembut | Garis tepi `1,5 pt` dengan sudut membulat (`border-radius 8 mm`) di sekeliling tiap kartu. |
| **Heading Accent** | `#0369A1` | Biru Akademik Tegas | Seluruh tajuk lima subjudul (`ABSTRAK` hingga `KESIMPULAN`). |
| **Header Divider** | `#38BDF8` | Biru Cerah Halus | Garis pembatas horizontal `1,5 pt` pemisah header dan area dua kolom. |
| **Primary Text** | `#1E293B` | Hitam Arang Nyaman | Judul utama, nama penulis, dan seluruh paragraf isi agar nyaman dibaca lama. |
| **Secondary Text** | `#475569` | Abu-Abu Gelap | Baris afiliasi prodi/fakultas di header serta keterangan `Gambar 1` sampai `Gambar 4`. |
| **Image Frame** | `#CBD5E1` | Bingkai Tipis Gambar | Garis tepi `1 pt` di sekeliling keempat gambar agar batas diagram putih tetap tegas di atas kartu putih. |

### 2.3 Hierarki Tipografi (Skala Cetak A1)
Gunakan satu keluarga huruf **Sans-Serif Modern** yang bersih (*Plus Jakarta Sans*, *Poppins*, *Montserrat*, atau *Canva Sans*):
- **Judul Utama Poster:** `44 hingga 46 pt`, *Bold* (`700`), kapital seluruhnya, rata tengah, *line-height* `1,15`, warna `#0F172A`.
- **Nama Penulis & NIM:** `24 hingga 26 pt`, *SemiBold* (`600`), *Title Case*, rata tengah (`Rafif Arsya Pradiva | 22.N4.0014`).
- **Baris Afiliasi (Prodi & Kampus):** `15 pt`, *Regular* (`400`), rata tengah, warna `#475569`.
- **Tajuk Lima Bagian (`ABSTRAK`, `LATAR BELAKANG`, dll.):** `25 hingga 26 pt`, *Bold* (`700`), kapital seluruhnya, rata tengah, warna `#0369A1`, jarak bawah `7 mm`.
- **Isi Paragraf Bodi:** `22 pt`, **Regular (`400`)**, warna `#1E293B`, *line-height* `1,48`, perataan **Rata Kiri (*Left Align*)** agar spasi antarkata merata tanpa rongga renggang dan tetap lega dibaca dari jarak 1,5 hingga 2 meter.
- **Keterangan Gambar (`Gambar 1` sampai `Gambar 4`):** `15 pt`, *Medium* (`500`), rata tengah tepat `3 mm` di bawah tiap bingkai gambar, warna `#334155`.

---

## 3. Cetak Biru Wireframe (Kanvas A1 `594 x 841 mm`)

```text
+----------------------------------------------------------------------------------+
| [IKON ASRI]        SISTEM INFORMASI MANAJEMEN KOST               [LOGO UNIKA]    |
| (54 x 54 mm)        TERINTEGRASI PAYMENT GATEWAY                 (54 x 54 mm)    |
|                      PADA ASRI BOARDING HOUSE                                    |
|                    Rafif Arsya Pradiva | 22.N4.0014                              |
|    Sistem Informasi, Fakultas Ilmu Komputer, Universitas Katolik Soegijapranata  |
|----------------------------------------------------------------------------------|
|  +------------------------------------+  +------------------------------------+  |
|  |              ABSTRAK               |  |       HASIL DAN PEMBAHASAN         |  |
|  |  1 paragraf (4 kalimat, 71 kata):  |  |  1 paragraf (4 kalimat, 95 kata):  |  |
|  |  kendala manual -> Laravel, MySQL, |  |  4 akses -> dasbor (Gambar 2) ->   |  |
|  |  Waterfall & Midtrans -> kunci     |  |  pesan 5 tahap (Gambar 3) ->       |  |
|  |  otomatis -> 60 uji & skor coba.   |  |  halaman tagihan & uji (Gambar 4). |  |
|  +------------------------------------+  |                                    |  |
|                                          |  +------------------------------+  |  |
|  +------------------------------------+  |  |  Gambar 2 (Dasbor & Kas)     |  |  |
|  |          LATAR BELAKANG            |  |  |  Crop bersih (216 x 94 mm)   |  |  |
|  |  1 paragraf (4 kalimat, 73 kata):  |  |  +------------------------------+  |  |
|  |  perantau Tembalang -> 32 kamar    |  |  Gambar 2 Tampilan dasbor pengelola|  |
|  |  manual -> tagih, denda & rekap    |  |           dan laporan kas          |  |
|  |  3-4 hari -> butuh web gateway.    |  |                                    |  |
|  +------------------------------------+  |  +------------------------------+  |  |
|                                          |  |  Gambar 3 (Alur 5 Tahap)     |  |  |
|  +------------------------------------+  |  |  Crop bersih (216 x 100 mm)  |  |  |
|  |         METODE PENELITIAN          |  |  +------------------------------+  |  |
|  |  1 paragraf (3 kalimat, 59 kata):  |  |  Gambar 3 Tampilan alur pemesanan  |  |
|  |  studi kasus & wawancara ->        |  |         kamar lima tahap           |  |
|  |  6 tahap Waterfall (Gambar 1) ->   |  |                                    |  |
|  |  60 uji Black Box & uji coba.      |  |  +------------------------------+  |  |
|  |                                    |  |  |  Gambar 4 (Halaman Tagihan)  |  |  |
|  |  +------------------------------+  |  |  |  Crop bersih (216 x 97 mm)   |  |  |
|  |  |                              |  |  |  +------------------------------+  |  |
|  |  |  Gambar 1 (Metode Waterfall) |  |  |  Gambar 4 Tampilan halaman tagihan |  |
|  |  |  Lebar penuh (237 x 147 mm)  |  |  |         bulanan penghuni           |  |
|  |  |                              |  |  +------------------------------------+  |
|  |  +------------------------------+  |                                          |
|  |      Gambar 1 Metode Waterfall     |  +------------------------------------+  |
|  |                                    |  |            KESIMPULAN              |  |
|  |                                    |  |  1 paragraf (3 kalimat, 64 kata):  |  |
|  |                                    |  |  32 kamar tertib & rekap detik ->  |  |
|  |                                    |  |  validasi uji & skor coba ->       |  |
|  +------------------------------------+  |  arah pengembangan berikutnya.     |  |
|                                          +------------------------------------+  |
+----------------------------------------------------------------------------------+
```

---

## 4. Naskah Siap Pakai Poster (Salin Langsung ke Canva atau Figma)

Seluruh naskah di bawah sudah dipangkas menjadi **1 paragraf berisi 3 sampai 4 kalimat per subjudul** (total 18 kalimat / 362 kata) dengan ragam bahasa *hybrid* akademis-humanis yang luwes bagi mahasiswa Fakultas Ilmu Komputer, dosen penguji, dan dosen pembimbing, bersih dari istilah rancu maupun kaku, tanpa frasa klise AI, serta tanpa tanda baca *em-dash*.

### BAGIAN HEADER (ATAS, RATA TENGAH)

**Judul Utama (3 Baris Kapital):**
```text
SISTEM INFORMASI MANAJEMEN KOST
TERINTEGRASI PAYMENT GATEWAY
PADA ASRI BOARDING HOUSE
```

**Nama & NIM Penulis:**
```text
Rafif Arsya Pradiva | 22.N4.0014
```

**Baris Afiliasi (Kecil di Bawah Nama):**
```text
Sistem Informasi, Fakultas Ilmu Komputer, Universitas Katolik Soegijapranata
```

---

### KOLOM KIRI

#### `ABSTRAK`
> Pencatatan 32 kamar di Asri Boarding House lewat buku tulis dan WhatsApp sering memicu pesanan ganda. Dengan metode *Waterfall*, riset ini membangun aplikasi web manajemen kost berbasis Laravel dan MySQL yang terhubung ke *payment gateway* Midtrans, WhatsApp, serta *email*. Begitu pembayaran terverifikasi, kamar terkunci otomatis dan kuitansi PDF langsung terbit. Seluruh 60 skenario uji fitur (*Black Box*) berjalan 100%, meraih skor 9,5/10 dari penjaga kost dan rata-rata 8,0/10 dari lima penghuni.

#### `LATAR BELAKANG`
> Mahasiswa perantau di Tembalang sering kesulitan mengecek kamar kosong tanpa datang langsung ke lokasi. Di Asri Boarding House, pencatatan 32 kamar tipe VIP, Deluxe, dan Standar pada buku tulis serta WhatsApp pribadi kerap terlambat diperbarui hingga memicu bentrok pesanan kamar. Urusan tagihan tanggal 10, denda 5%, hingga pencocokan kas akhir bulan bahkan menyita tiga sampai empat hari. Kondisi ini membutuhkan aplikasi web terpusat dengan *payment gateway* agar pemesanan dan pembukuan rapi tanpa selisih.

#### `METODE PENELITIAN`
> Studi kasus di Asri Boarding House ini menghimpun data melalui wawancara penjaga kost berpengalaman 20 tahun, lima penghuni aktif, observasi harian, serta pengecekan catatan kas. Aplikasi berbasis Laravel, MySQL, dan Midtrans kemudian dibangun secara berurutan mengikuti enam tahap metode *Waterfall* pada Gambar 1. Kelayakan aplikasi diuji memakai 60 skenario *Black Box* serta uji coba langsung berskala 1 sampai 10.

- **Penempatan Visual Kolom Kiri (Lebar Penuh Kartu di Bawah Paragraf `METODE PENELITIAN`):**
  1. **Diagram Metode (`237 x 147 mm`, rata tengah):** Pasang `Skripsi/images/poster/gambar_1_metode_waterfall.png` dan beri keterangan rata tengah di bawahnya: `Gambar 1 Metode Waterfall`

---

### KOLOM KANAN

#### `HASIL DAN PEMBAHASAN`
> Sistem membagi hak akses untuk pengunjung umum, calon penyewa, penghuni aktif, dan pengelola kost. Melalui dasbor pengelola (Gambar 2), status 32 kamar serta arus kas terpantau langsung sehingga tutup buku tuntas dalam hitungan detik. Calon penyewa memesan kamar lewat alur lima tahap (Gambar 3) dengan uang muka (DP) 30% atau pelunasan via Midtrans yang otomatis mengunci unit dan menerbitkan kuitansi PDF. Halaman tagihan penghuni (Gambar 4) menerbitkan rincian sewa tiap tanggal 1 dengan batas bayar tanggal 10, denda 5% bila melewati akhir bulan, serta pengingat WhatsApp dan *email*, sementara seluruh 60 uji fitur lulus 100%.

- **Penempatan Visual Kolom Kanan (Bertumpuk Vertikal Rata Tengah Selebar `216 mm` di Bawah Paragraf `HASIL DAN PEMBAHASAN`):**
  1. **Layar Pertama (`216 x 94 mm`):** Pasang `Skripsi/images/poster/gambar_2_dasbor_pengelola_kas.png` dan beri keterangan rata tengah di bawahnya: `Gambar 2 Tampilan dasbor pengelola dan laporan kas`
  2. **Layar Kedua (`216 x 100 mm`):** Pasang `Skripsi/images/poster/gambar_3_alur_pemesanan_5_tahap.png` dan beri keterangan rata tengah di bawahnya: `Gambar 3 Tampilan alur pemesanan kamar lima tahap`
  3. **Layar Ketiga (`216 x 97 mm`):** Pasang `Skripsi/images/poster/gambar_4_tagihan_bulanan_penghuni.png` dan beri keterangan rata tengah di bawahnya: `Gambar 4 Tampilan halaman tagihan bulanan penghuni`

#### `KESIMPULAN`
> Aplikasi web ini mencegah pesanan ganda pada 32 kamar sekaligus memangkas rekap kas bulanan menjadi hitungan detik. Pembayaran Midtrans, kuitansi PDF, serta pengingat WhatsApp dan *email* teruji lewat 60 skenario *Black Box* (100%), skor 9,5/10 dari pengelola, dan 8,0/10 dari lima penghuni. Pengembangan selanjutnya dapat menambahkan pengingat WhatsApp H-3 dengan tautan bayar langsung, panduan penghuni baru, penyempurnaan tampilan di ponsel, serta kontrak sewa digital.

---

## 5. Matriks Aset Gambar Siap Cetak A1 & Kode Ikon Aplikasi

### 5.1 Paket Gambar Bersih Siap Tempel (`Skripsi/images/poster/`)
Seluruh 4 gambar utama sudah diproses non-destruktif ke dalam folder `Skripsi/images/poster/` (berkas asli skripsi pada `Skripsi/images/` tetap utuh). Seluruh bilah Chrome pada `Gambar 2` hingga `Gambar 4` sudah dipotong presisi dan kepadatan pikselnya dioptimalkan agar sangat tajam pada ukuran cetak A1 (`223` sampai `287 DPI`):

| Posisi Poster | Keterangan Resmi | Berkas Siap Tempel (`Skripsi/images/...`) | Dimensi Siap Cetak | Estimasi DPI di A1 | Sumber Asli & Catatan |
| :--- | :--- | :--- | :---: | :---: | :--- |
| **Header Kiri** | Ikon Aplikasi Asri | `ikon_asri_poster.svg` | Vektor | Tak terbatas | Ukuran pasang `54 x 54 mm`; menggantikan ikon templat huruf "S". |
| **Header Kanan** | Logo Unika | `logo_unika.png` | `254 x 255 px` | ~`120 DPI` | Ukuran pasang `54 x 54 mm` sejajar vertikal dengan ikon kiri. |
| **`Gambar 1`** | `Gambar 1 Metode Waterfall` | `poster/gambar_1_metode_waterfall.png` | `2677 x 1658 px` | ~`287 DPI` | Dari `metode_sdlc_waterfall.png` (resmi `Gambar 3.1` skripsi, 6 tahap + umpan balik). |
| **`Gambar 2`** | `Gambar 2 Tampilan dasbor pengelola dan laporan kas` | `poster/gambar_2_dasbor_pengelola_kas.png` | `1897 x 823 px` | ~`223 DPI` | Dari `gambar_4_26_dashboard_admin.png`; bilah Chrome (`113 px`) sudah terpotong bersih. |
| **`Gambar 3`** | `Gambar 3 Tampilan alur pemesanan kamar lima tahap` | `poster/gambar_3_alur_pemesanan_5_tahap.png` | `2048 x 944 px` | ~`241 DPI` | Dari `antarmuka_stepper_reservasi_penyewa.png`; bebas bilah Chrome + *upscale* 2x. |
| **`Gambar 4`** | `Gambar 4 Tampilan halaman tagihan bulanan penghuni` | `poster/gambar_4_tagihan_bulanan_penghuni.png` | `1918 x 864 px` | ~`226 DPI` | Dari `gambar_4_38_tagihan_penyewa.png`; bilah Chrome (`73 px`) sudah terpotong bersih. |

**Catatan Opsi Tambahan yang Tersedia:**
- **Alternatif `Gambar 4` (Konsistensi Akun `Tyas`):** Tersedia juga `Skripsi/images/poster/gambar_4_alternatif_akun_tyas.png` (`2048 x 940 px`, ~`241 DPI`, bebas bilah Chrome) yang menampilkan 2 baris riwayat lunas atas nama akun `Tyas` sehingga seragam dengan akun `Tyas` pada `Gambar 3`.
- **Opsional Pembersihan *Tooltip* pada `Gambar 2`:** Pada tangkapan layar asli `gambar_4_26_dashboard_admin.png`, terdapat kotak *tooltip hover* putih kecil di kartu keempat (*Total Pengeluaran*). Jika ingin tampilan 100% tanpa *tooltip*, Anda dapat mengambil satu tangkapan layar ulang halaman `admin/dashboard` dengan kursor di luar area kartu lalu memotong bilah Chrome atasnya.

### 5.2 Detail Vektor Ikon Aplikasi Kiri Atas (`ikon_asri_poster.svg`)
Berkas vektor `Skripsi/images/ikon_asri_poster.svg` menggabungkan simbol rumah dan daun hijau `#10B981` dari `resources/views/components/application-logo.blade.php` ke dalam wadah kotak membulat bernuansa biru langit cerah agar seimbang dengan logo kuning Unika di sisi kanan. Berkas ini siap diimpor langsung ke Canva atau Figma:

```svg
<svg width="256" height="256" viewBox="0 0 256 256" xmlns="http://www.w3.org/2000/svg">
  <defs>
    <linearGradient id="bgSky" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" stop-color="#93D5F5" />
      <stop offset="100%" stop-color="#38A3DC" />
    </linearGradient>
  </defs>
  <rect x="8" y="8" width="240" height="240" rx="44" fill="url(#bgSky)" />
  <g transform="translate(36, 38) scale(4.5)">
    <path d="M20 3 L37 18 L33 18 L33 38 L7 38 L7 18 L3 18 Z"
          fill="none" stroke="#FFFFFF" stroke-width="3" stroke-linejoin="round" />
    <rect x="17" y="26" width="6" height="12" rx="1" fill="#FACC15" />
    <rect x="11.5" y="16" width="4.5" height="4.5" rx="0.8"
          fill="none" stroke="#FFFFFF" stroke-width="1.5" />
    <rect x="24" y="16" width="4.5" height="4.5" rx="0.8"
          fill="none" stroke="#FFFFFF" stroke-width="1.5" />
    <path d="M37 8 C29 8 27 16 27 16 C27 16 33 17 38 13 C42 10 41 8 37 8 Z" fill="#10B981" />
    <path d="M27 16 Q33 11 37 8" fill="none" stroke="#FFFFFF" stroke-width="1" stroke-linecap="round" />
  </g>
</svg>
```

---

## 6. Langkah Revisi Praktis pada Draf Poster Anda di Canva atau Figma

Ikuti urutan singkat ini untuk merevisi langsung proyek poster yang sudah Anda buat tanpa perlu membangun ulang dari awal:

1. **Kunci Ukuran Kanvas A1 & Panduan Grid:** Pastikan dimensi dokumen tepat **`594 x 841 mm`**. Pasang garis panduan (*ruler/guides*) pada *margin* `22 mm` di keempat tepi, serta dua kolom selebar `265 mm` dengan jarak tengah `20 mm`.
2. **Perbarui Header:**
   - Ganti ikon huruf "S" di sudut kiri atas memakai `Skripsi/images/ikon_asri_poster.svg` berukuran `54 x 54 mm` sejajar dengan `logo_unika.png`.
   - Ubah baris nama penulis menjadi `Rafif Arsya Pradiva | 22.N4.0014` (`24 pt` SemiBold), lalu tambahkan baris kecil `Sistem Informasi, Fakultas Ilmu Komputer, Universitas Katolik Soegijapranata` (`15 pt` Regular `#475569`) tepat di bawahnya.
3. **Ganti Seluruh 5 Teks Subjudul & Perbesar Tipografi Bodi:**
   - Salin kelima paragraf baru dari Bagian 4 ke masing-masing kartu (`ABSTRAK`, `LATAR BELAKANG`, `METODE PENELITIAN`, `HASIL DAN PEMBAHASAN`, `KESIMPULAN`).
   - Atur bobot huruf bodi menjadi **Regular (`400`)** ukuran **`22 pt`**, *line-height* `1,48`, warna `#1E293B`, dan ubah perataan menjadi **Rata Kiri (*Left Align*)**.
   - Sesuaikan tinggi kartu `ABSTRAK` (`~160 mm`) dan `LATAR BELAKANG` (`~165 mm`) dengan celah antarkartu `22 mm` agar ruang napas terasa lega.
4. **Lengkapi Kolom Kiri dengan `Gambar 1 Metode Waterfall`:**
   - Atur tinggi kartu `METODE PENELITIAN` menjadi `~330 mm`.
   - Unggah lalu masukkan `gambar_1_metode_waterfall.png` dari `Skripsi/images/poster/` selebar penuh area dalam kartu (`237 x 147 mm`). Beri garis tepi tipis `1 pt` (`#CBD5E1`) serta keterangan rata tengah `Gambar 1 Metode Waterfall`.
5. **Ganti Tiga Layar Kolom Kanan dengan Versi Bersih (`Gambar 2`, `Gambar 3`, `Gambar 4`):**
   - Ganti ketiga tangkapan layar lama pada kartu `HASIL DAN PEMBAHASAN` dengan `gambar_2_dasbor_pengelola_kas.png`, `gambar_3_alur_pemesanan_5_tahap.png`, dan `gambar_4_tagihan_bulanan_penghuni.png` (atau `gambar_4_alternatif_akun_tyas.png`) dari `Skripsi/images/poster/` selebar `216 mm` rata tengah masing-masing.
   - Beri bingkai tipis `1 pt` (`#CBD5E1`) dan perbarui keterangannya secara urut menjadi `Gambar 2`, `Gambar 3`, dan `Gambar 4`.
6. **Sejajarkan Tepi Bawah & Ekspor Cetak A1:**
   - Sesuaikan celah antargambar (`14 mm`) dan tinggi kartu `KESIMPULAN` (`~137 mm`) agar tepi bawah kartu `METODE PENELITIAN` (Kolom Kiri) dan kartu `KESIMPULAN` (Kolom Kanan) berhenti tepat sejajar di `Y = 819 mm`.
   - Lakukan pratinjau pembesaran `100%` untuk memastikan teks diagram dan tabel UI tajam, lalu ekspor ke **PDF Print** profil warna **CMYK** tanpa kompresi gambar.
