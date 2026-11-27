# Panduan desain dan naskah siap pakai poster skripsi (A1)
## Tata letak dua kolom kartu modern dengan empat visual

**Judul Karya Ilmiah:** Sistem Informasi Manajemen Kost Terintegrasi Payment Gateway pada Asri Boarding House  
**Peneliti:** Rafif Arsya Pradiva (`22.N4.0014`)  
**Dosen Pembimbing:** Ir. Andre Kurniawan Pamudji, S.Kom., M.Ling. & Erdhi Widyarto Nugroho, S.T., M.T.  
**Program Studi:** Sistem Informasi, Fakultas Ilmu Komputer, Universitas Katolik Soegijapranata Semarang  
**Lokasi dan Fungsi Pajang:** Lingkungan Fakultas Ilmu Komputer dan Program Studi Sistem Informasi (*E-Commerce*), dirancang untuk waktu baca 1 sampai 2 menit dari jarak berdiri 1,5 sampai 2 meter  
**Pembaca Sasaran:** Mahasiswa dalam maupun luar kampus (baik mahasiswa luar fakultas yang awam istilah teknis IT maupun mahasiswa Fakultas Ilmu Komputer dan Sistem Informasi *E-Commerce*), dosen umum lintas bidang, dosen pembimbing, serta dosen penguji  
**Ukuran Cetak Resmi:** Kertas A1 potret (`594 x 841 mm`), 300 DPI (`7016 x 9933 px`), CMYK  
**Tautan Demo Aplikasi:** [https://asriboardinghouse.weatso.id/](https://asriboardinghouse.weatso.id/) *(referensi portofolio)*  
**Sumber Acuan:** `Skripsi/skripsi_v2_Rafif_Arsya_Pradiva_22.N4.0014.md` dan `Skripsi/journal_inggris_Rafif_Arsya_Pradiva_22.N4.0014.md`

---

## 1. Evaluasi poster saat ini dan perbaikan yang dibutuhkan

Tangkapan layar draf poster Anda saat ini sudah memuat empat gambar utama (diagram metode *Waterfall* beserta tiga layar antarmuka bebas bilah peramban). Namun, saat dipasang nyata di kanvas A1 (`594 x 841 mm`), teks 18 kalimat (316 kata) masih memadati kartu hingga 47 baris, lebar kedua kolom belum seimbang, dan ujung bawah kolom kiri menggantung jauh di atas kolom kanan. Sesuai masukan dosen pembimbing dan kebutuhan pajang lintas jurusan dari jarak 1,5 sampai 2 meter, kelima subjudul dipangkas menjadi tepat satu paragraf berisi 2 sampai 3 kalimat (total 14 kalimat, 263 kata) yang mengalir menceritakan perjalanan riset sekaligus merapikan proporsi grid dan tipografi.

| Unsur Poster | Kondisi pada Screenshot Saat Ini | Target Perbaikan (A1 `594 x 841 mm`) |
| :--- | :--- | :--- |
| Kepadatan naskah | Naskah 18 kalimat (316 kata) masih memakan 12 baris di `ABSTRAK`, 11 baris di `LATAR BELAKANG`, 7 baris di `METODE PENELITIAN`, 9 baris di `HASIL DAN PEMBAHASAN`, serta 8 baris di `KESIMPULAN` (total 47 baris). | Dipangkas menjadi 14 kalimat (263 kata; 2 sampai 3 kalimat per subjudul) dengan alur bercerita yang lugas sehingga setiap kartu cukup terisi 5 sampai 7 baris. |
| Lebar kolom dan keseimbangan bawah | Kolom kiri lebih sempit (`41%`, sekitar `243 mm`) dibanding kolom kanan (`54%`, sekitar `319 mm`); kartu `METODE PENELITIAN` berhenti di sekitar `Y = 790 mm`, sedangkan `KESIMPULAN` merosot ke `Y = 835 mm` (`6 mm` dari tepi bawah kertas). | Dua kolom dibuat simetris (`265 mm` kiri dan `265 mm` kanan) dengan total tinggi sama persis (`699 mm`), sehingga batas bawah kedua kolom berhenti sejajar di `Y = 819 mm`. |
| Tipografi dan perataan | Judul utama memakai huruf membulat bergaya spidol kasual, tajuk subjudul memakai huruf sempit berukuran kecil, dan teks bodi dicetak tebal (*Bold/SemiBold*) rata kiri-kanan (*Justify*) yang menyisakan celah antarkata renggang. | Satu keluarga *Sans-Serif* geometris (*Plus Jakarta Sans*, *Poppins*, *Montserrat*, atau *Canva Sans*); teks isi berbobot Regular (`400`), ukuran `22 pt`, *line-height* `1,48`, rata kiri (*Left Align*). |
| Margin, celah, dan *padding* | Margin luar tipis (`11` sampai `13 mm`), celah tengah kolom sempit (`7 mm`), dan *padding* kartu mepet (`6` sampai `8 mm`) sehingga tepi kiri-kanan `Gambar 1` nyaris menempel ke garis kartu. | Margin aman `22 mm` di keempat sisi, *gutter* tengah `20 mm`, jarak antarkartu seragam `24 mm`, *padding* kartu `14 mm`, dan jarak antarlayar UI kanan `18 mm`. |
| Header dan identitas | Sudut kiri atas masih memakai ikon huruf "S" bersirkuit, nama ditulis kapital penuh bertanda pisah (`RAFIF ARSYA PRADIVA - 22.N4.0014`), garis pembatas menembus tepi kertas, dan baris afiliasi belum ada. | Pasang `ikon_asri_poster.svg` di kiri, `logo_unika.png` di kanan, baris `Rafif Arsya Pradiva | 22.N4.0014`, baris afiliasi prodi dan kampus, serta garis pemisah selebar area aktif `550 mm`. |
| Bingkai dan detail 4 gambar | `Gambar 2` sampai `Gambar 4` belum berbingkai sehingga *sidebar* putih aplikasi menyatu ke latar kartu; `Gambar 2` masih memuat kotak *tooltip hover* kecil dan `Gambar 4` menampilkan akun `; Mikhayvx`. | Keempat gambar diberi garis tepi tipis `1 pt` (`#CBD5E1`); tersedia `gambar_4_alternatif_akun_tyas.png` untuk menyeragamkan akun `Tyas` dengan `Gambar 3` serta opsi tangkap ulang `Gambar 2` tanpa *tooltip*. |

---

## 2. Ukuran, grid presisi A1 (`594 x 841 mm`), warna, dan tipografi

### 2.1 Parameter fisik dan alokasi tinggi milimeter
- Ukuran kanvas cetak: kertas A1 potret (`594 x 841 mm`), rasio `1 : 1,414` (dapat diskalakan proporsional ke A2 `420 x 594 mm` atau A3 `297 x 420 mm` bila diperlukan).
- Resolusi dan warna cetak: 300 DPI (`7016 x 9933 px`), profil warna CMYK.
- Batas aman cetak (*safe margin*): `22 mm` di keempat sisi kanvas, menyisakan area aktif `550 x 797 mm` yang berakhir rapi pada `Y = 819 mm`.
- Area header (`86 mm` ditambah celah bawah `12 mm`, total `98 mm`):
  - Sisi kiri (`54 x 54 mm`): `ikon_asri_poster.svg`.
  - Tengah (`420 mm`): judul tiga baris kapital, baris nama beserta NIM, dan satu baris afiliasi prodi serta universitas.
  - Sisi kanan (`54 x 54 mm`): `logo_unika.png`.
  - Garis pembatas bawah header: garis horizontal `1,5 pt` (`#38BDF8`) tepat selebar area aktif `550 mm`.
- Area bodi dua kolom simetris (tinggi `699 mm`):
  - Lebar Kolom Kiri `265 mm`, celah tengah (*gutter*) `20 mm`, dan lebar Kolom Kanan `265 mm` (total lebar `550 mm`).
  - *Padding* kiri dan kanan di dalam kartu sebesar `14 mm` sehingga ruang bersih teks dan diagram penuh selebar `237 mm`.

| Kolom Kiri (`265 mm`) | Alokasi Tinggi | Kolom Kanan (`265 mm`) | Alokasi Tinggi |
| :--- | :---: | :--- | :---: |
| Kartu `ABSTRAK` (3 kalimat, 55 kata) | `150 mm` | Kartu `HASIL DAN PEMBAHASAN` (3 kalimat, 61 kata + 3 layar UI) | `541 mm` |
| Celah antarkartu | `24 mm` | &emsp;Judul, *padding* atas, dan teks paragraf | *(`133 mm`)* |
| Kartu `LATAR BELAKANG` (3 kalimat, 53 kata) | `150 mm` | &emsp;Celah teks ke `Gambar 2` | *(`18 mm`)* |
| Celah antarkartu | `24 mm` | &emsp;`Gambar 2` (`216 x 94 mm`, rata tengah) + keterangan (`11 mm`) | *(`105 mm`)* |
| Kartu `METODE PENELITIAN` (2 kalimat, 43 kata + 1 diagram) | `351 mm` | &emsp;Celah ke `Gambar 3` | *(`18 mm`)* |
| &emsp;Judul, *padding* atas, dan teks paragraf | *(`104 mm`)* | &emsp;`Gambar 3` (`216 x 100 mm`, rata tengah) + keterangan (`11 mm`) | *(`111 mm`)* |
| &emsp;Celah teks ke `Gambar 1` | *(`34 mm`)* | &emsp;Celah ke `Gambar 4` | *(`18 mm`)* |
| &emsp;`Gambar 1` (`237 x 147 mm`, lebar penuh) + keterangan (`14 mm`) | *(`161 mm`)* | &emsp;`Gambar 4` (`216 x 97 mm`, rata tengah) + keterangan (`11 mm`) | *(`108 mm`)* |
| &emsp;*Padding* bawah kartu | *(`52 mm`)* | &emsp;*Padding* bawah kartu | *(`30 mm`)* |
| | | Celah antarkartu | `24 mm` |
| | | Kartu `KESIMPULAN` (3 kalimat, 51 kata) | `134 mm` |
| **Total Tinggi Kolom Kiri** | **`699 mm`** | **Total Tinggi Kolom Kanan** | **`699 mm`** |

### 2.2 Palet warna kartu modern
| Peran Elemen | Kode HEX | Karakter Visual | Penempatan pada Poster |
| :--- | :--- | :--- | :--- |
| Canvas Base | `#F4FAFE` | Putih kebiruan muda | Latar dasar kanvas di belakang kelima kartu supaya batas tiap kotak tampak jelas. |
| Card Surface | `#FFFFFF` | Putih bersih | Permukaan kelima kartu subjudul demi kontras baca huruf dan gambar saat dicetak. |
| Card Border | `#93C5FD` | Biru langit muda | Garis tepi `1,5 pt` bersudut tumpul (`border-radius 8 mm`) di sekeliling tiap kartu. |
| Heading Accent | `#0369A1` | Biru akademik tegas | Tajuk lima subjudul (`ABSTRAK` sampai `KESIMPULAN`). |
| Header Divider | `#38BDF8` | Biru cerah tipis | Garis pemisah horizontal `1,5 pt` selebar `550 mm` di antara header dan area kolom. |
| Primary Text | `#1E293B` | Hitam arang | Judul utama poster, nama penulis, dan seluruh paragraf isi kartu. |
| Secondary Text | `#475569` | Abu-abu gelap | Baris afiliasi di header serta teks keterangan `Gambar 1` sampai `Gambar 4`. |
| Image Frame | `#CBD5E1` | Abu-abu tipis | Garis tepi `1 pt` pada keempat gambar agar sisi putih antarmuka terpisah dari latar kartu. |

### 2.3 Hierarki tipografi (skala cetak A1)
Pakai satu keluarga huruf *Sans-Serif* geometris (*Plus Jakarta Sans*, *Poppins*, *Montserrat*, atau *Canva Sans*) secara konsisten di seluruh kanvas untuk menggantikan font membulat pada draf sebelumnya:
- Judul utama poster: `44` sampai `46 pt`, *Bold* (`700`), kapital seluruhnya, rata tengah, *line-height* `1,15`, warna `#0F172A`.
- Nama penulis dan NIM: `24` sampai `26 pt`, *SemiBold* (`600`), *Title Case*, rata tengah (`Rafif Arsya Pradiva | 22.N4.0014`).
- Baris afiliasi prodi dan kampus: `15 pt`, Regular (`400`), rata tengah, warna `#475569`.
- Tajuk lima subjudul (`ABSTRAK`, `LATAR BELAKANG`, dan seterusnya): `25` sampai `26 pt`, *Bold* (`700`), kapital seluruhnya, rata tengah, warna `#0369A1`, jarak bawah `7 mm`.
- Paragraf isi: `22 pt`, Regular (`400`), warna `#1E293B`, *line-height* `1,48`, rata kiri (*Left Align*) sehingga spasi antarkata selalu rapat merata dan mudah dipindai dalam 1 sampai 2 menit dari jarak berdiri.
- Keterangan gambar (`Gambar 1` sampai `Gambar 4`): `15 pt`, *Medium* (`500`), rata tengah berjarak `3 mm` di bawah bingkai tiap gambar, warna `#334155`.

---

## 3. Cetak biru wireframe (kanvas A1 `594 x 841 mm`)

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
|  |  1 paragraf (3 kalimat, 55 kata):  |  |  1 paragraf (3 kalimat, 61 kata):  |  |
|  |  kendala manual 32 kamar; web      |  |  dasbor kas detik (Gambar 2);      |  |
|  |  Laravel, MySQL, Midtrans, PDF;    |  |  pesan 5 tahap DP 30% (Gambar 3);  |  |
|  |  60 uji 100%, skor 9,5 dan 8,0.    |  |  tagihan, denda, WA (Gambar 4).    |  |
|  +------------------------------------+  |                                    |  |
|                                          |  +------------------------------+  |  |
|  +------------------------------------+  |  |  Gambar 2 (Dasbor & Kas)     |  |  |
|  |          LATAR BELAKANG            |  |  |  Bingkai 1 pt (216 x 94 mm)  |  |  |
|  |  1 paragraf (3 kalimat, 53 kata):  |  |  +------------------------------+  |  |
|  |  calon penyewa luar kota; catat    |  |  Gambar 2 Tampilan dasbor pengelola|  |
|  |  buku tulis dan WA rawan bentrok;  |  |           dan laporan kas          |  |
|  |  tanggal 10, denda 5%, rekap lama. |  |                                    |  |
|  +------------------------------------+  |  +------------------------------+  |  |
|                                          |  |  Gambar 3 (Alur 5 Tahap)     |  |  |
|  +------------------------------------+  |  |  Bingkai 1 pt (216 x 100 mm) |  |  |
|  |         METODE PENELITIAN          |  |  +------------------------------+  |  |
|  |  1 paragraf (2 kalimat, 43 kata):  |  |  Gambar 3 Tampilan alur pemesanan  |  |
|  |  buku kas dan wawancara pengelola  |  |         kamar lima tahap           |  |
|  |  20 tahun dan 5 penghuni; 6 tahap  |  |                                    |  |
|  |  Waterfall (Gambar 1) dan 60 uji.  |  |  +------------------------------+  |  |
|  |                                    |  |  |  Gambar 4 (Halaman Tagihan)  |  |  |
|  |  +------------------------------+  |  |  |  Bingkai 1 pt (216 x 97 mm)  |  |  |
|  |  |                              |  |  |  +------------------------------+  |  |
|  |  |  Gambar 1 (Metode Waterfall) |  |  |  Gambar 4 Tampilan halaman tagihan |  |
|  |  |  Bingkai 1 pt (237 x 147 mm) |  |  |         bulanan penghuni           |  |
|  |  |                              |  |  +------------------------------------+  |
|  |  +------------------------------+  |                                          |
|  |      Gambar 1 Metode Waterfall     |  +------------------------------------+  |
|  |                                    |  |            KESIMPULAN              |  |
|  |                                    |  |  1 paragraf (3 kalimat, 51 kata):  |  |
|  |                                    |  |  32 kamar bebas bentrok, kas detik;|  |
|  |                                    |  |  skor 9,5 dan 8,0, 60 uji 100%;    |  |
|  +------------------------------------+  |  arah pengembangan tahap lanjut.   |  |
|                                          +------------------------------------+  |
+----------------------------------------------------------------------------------+
```

---

## 4. Naskah siap pakai poster (salin langsung ke Canva atau Figma)

Setiap subjudul di bawah terdiri dari satu paragraf berisi 2 sampai 3 kalimat (total 14 kalimat, 263 kata). Alur bahasanya merangkai persoalan nyata pengelolaan kost manual, cara kerja transaksi sewa daring, hingga hasil pengujiannya secara seimbang bagi pembaca lintas fakultas maupun penguji Sistem Informasi (*E-Commerce*), disertai rujukan berurutan ke `Gambar 1` sampai `Gambar 4`.

### Bagian header (atas, rata tengah)

Judul utama (tiga baris kapital):
```text
SISTEM INFORMASI MANAJEMEN KOST
TERINTEGRASI PAYMENT GATEWAY
PADA ASRI BOARDING HOUSE
```

Nama dan NIM penulis:
```text
Rafif Arsya Pradiva | 22.N4.0014
```

Baris afiliasi (kecil di bawah nama):
```text
Sistem Informasi, Fakultas Ilmu Komputer, Universitas Katolik Soegijapranata
```

---

### Kolom kiri

#### `ABSTRAK`
> Pencatatan manual 32 kamar di Asri Boarding House kerap memicu pesanan ganda bagi calon penyewa. Penelitian ini membangun aplikasi web Laravel dan MySQL terintegrasi *payment gateway* Midtrans agar transaksi sewa kamar hingga penerbitan kuitansi PDF berjalan otomatis. Seluruh 60 skenario *Black Box* lulus 100%, didukung skor 9,5/10 dari pengelola dan rata-rata 8,0/10 dari 5 penghuni.

#### `LATAR BELAKANG`
> Calon penyewa luar kota sering kesulitan mengecek kamar kosong dari jauh. Pengelola Asri Boarding House pun masih mencatat 32 kamar tipe VIP, Deluxe, dan Standar lewat buku tulis serta WhatsApp sehingga pesanan rawan bentrok. Pantauan jatuh tempo tanggal 10 beserta denda 5% mudah terlewat, sedangkan rekap kas bulanan menyita 3 sampai 4 hari.

#### `METODE PENELITIAN`
> Peneliti memetakan kebutuhan lapangan melalui observasi buku kas serta wawancara bersama pengelola berpengalaman 20 tahun dan 5 penghuni aktif. Proses pembangunan aplikasi mengikuti 6 tahap *Waterfall* pada Gambar 1 dan menempuh pengujian 60 skenario *Black Box* beserta penilaian lapangan berskala 1 sampai 10.

Penempatan visual Kolom Kiri (lebar penuh di bawah paragraf `METODE PENELITIAN`):
1. Diagram metode (`237 x 147 mm`, rata tengah, bingkai `1 pt #CBD5E1`): pasang `Skripsi/images/poster/gambar_1_metode_waterfall.png` dengan keterangan rata tengah di bawahnya: `Gambar 1 Metode Waterfall`

---

### Kolom kanan

#### `HASIL DAN PEMBAHASAN`
> Dasbor pengelola pada Gambar 2 memantau keterisian 32 kamar sekaligus menyajikan laporan kas dalam hitungan detik. Melalui alur lima tahap pada Gambar 3, pembayaran DP 30% atau lunas via Midtrans langsung mengunci kamar dan menyiapkan kuitansi PDF. Tiap tanggal 1, halaman pada Gambar 4 menerbitkan tagihan sewa berjatuh tempo tanggal 10 dengan denda 5% lintas bulan serta notifikasi WhatsApp dan *email*.

Penempatan visual Kolom Kanan (bertumpuk vertikal rata tengah selebar `216 mm` dengan celah seragam `18 mm` dan bingkai `1 pt #CBD5E1` di bawah paragraf `HASIL DAN PEMBAHASAN`):
1. Layar pertama (`216 x 94 mm`): pasang `Skripsi/images/poster/gambar_2_dasbor_pengelola_kas.png` dengan keterangan rata tengah di bawahnya: `Gambar 2 Tampilan dasbor pengelola dan laporan kas`
2. Layar kedua (`216 x 100 mm`): pasang `Skripsi/images/poster/gambar_3_alur_pemesanan_5_tahap.png` dengan keterangan rata tengah di bawahnya: `Gambar 3 Tampilan alur pemesanan kamar lima tahap`
3. Layar ketiga (`216 x 97 mm`): pasang `Skripsi/images/poster/gambar_4_tagihan_bulanan_penghuni.png` dengan keterangan rata tengah di bawahnya: `Gambar 4 Tampilan halaman tagihan bulanan penghuni`

#### `KESIMPULAN`
> Aplikasi ini meniadakan pesanan ganda di 32 kamar serta memangkas rekap kas dari 3 sampai 4 hari ke hitungan detik. Pengelola memberi skor 9,5/10 dan 5 penghuni memberi rata-rata 8,0/10 setelah 60 skenario *Black Box* lulus 100%. Pengembangan berikutnya mencakup tautan bayar WhatsApp H-3, panduan penghuni, tampilan ponsel, dan kontrak digital.

---

## 5. Matriks aset gambar siap cetak A1 dan vektor ikon aplikasi

### 5.1 Paket gambar siap tempel (`Skripsi/images/poster/`)
Keempat gambar utama disimpan di folder `Skripsi/images/poster/` supaya berkas gambar asli skripsi di `Skripsi/images/` tetap utuh. Bilah atas peramban pada `Gambar 2` sampai `Gambar 4` sudah dipotong bersih dengan kerapatan `223` sampai `287 DPI` pada ukuran cetak A1.

| Posisi Poster | Keterangan Resmi | Berkas Siap Tempel (`Skripsi/images/...`) | Dimensi Siap Cetak | Estimasi DPI di A1 | Sumber Asli dan Catatan |
| :--- | :--- | :--- | :---: | :---: | :--- |
| Header Kiri | Ikon Aplikasi Asri | `ikon_asri_poster.svg` | Vektor | Tak terbatas | Ukuran pasang `54 x 54 mm`; menggantikan ikon templat huruf "S". |
| Header Kanan | Logo Unika | `logo_unika.png` | `254 x 255 px` | Sekitar `120 DPI` | Ukuran pasang `54 x 54 mm` sejajar vertikal dengan ikon kiri. |
| `Gambar 1` | `Gambar 1 Metode Waterfall` | `poster/gambar_1_metode_waterfall.png` | `2677 x 1658 px` | Sekitar `287 DPI` | Dari `metode_sdlc_waterfall.png` (`Gambar 3.1` skripsi, 6 tahap beserta umpan balik). |
| `Gambar 2` | `Gambar 2 Tampilan dasbor pengelola dan laporan kas` | `poster/gambar_2_dasbor_pengelola_kas.png` | `1897 x 823 px` | Sekitar `223 DPI` | Dari `gambar_4_26_dashboard_admin.png`; bilah Chrome (`113 px`) sudah terpotong. |
| `Gambar 3` | `Gambar 3 Tampilan alur pemesanan kamar lima tahap` | `poster/gambar_3_alur_pemesanan_5_tahap.png` | `2048 x 944 px` | Sekitar `241 DPI` | Dari `antarmuka_stepper_reservasi_penyewa.png`; bebas bilah Chrome dan perbesaran 2x. |
| `Gambar 4` | `Gambar 4 Tampilan halaman tagihan bulanan penghuni` | `poster/gambar_4_tagihan_bulanan_penghuni.png` | `1918 x 864 px` | Sekitar `226 DPI` | Dari `gambar_4_38_tagihan_penyewa.png`; bilah Chrome (`73 px`) sudah terpotong. |

Catatan opsi perapihan gambar di disk:
- Penyeragaman akun `Tyas` pada `Gambar 4`: berkas `Skripsi/images/poster/gambar_4_alternatif_akun_tyas.png` (`2048 x 940 px`, sekitar `241 DPI`, ukuran pasang `216 x 99 mm`) menampilkan riwayat tagihan atas nama `Tyas` sehingga seragam dengan akun pada `Gambar 3`.
- Pembersihan *tooltip* pada `Gambar 2`: tangkapan layar `gambar_4_26_dashboard_admin.png` masih memuat kotak *tooltip hover* putih kecil di atas kartu keempat (*Total Pengeluaran*). Bila ingin tampilan dasbor bersih, ambil ulang tangkapan layar halaman `admin/dashboard` tanpa mengarahkan kursor ke kartu metrik lalu potong bilah atas perambannya.

### 5.2 Detail vektor ikon kiri atas (`ikon_asri_poster.svg`)
Berkas vektor `Skripsi/images/ikon_asri_poster.svg` memadukan simbol rumah dan daun hijau `#10B981` dari `resources/views/components/application-logo.blade.php` di atas bidang biru langit bersudut tumpul agar seimbang dengan logo kuning Unika di sisi kanan. Berkas ini siap diimpor langsung ke Canva maupun Figma:

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

## 6. Langkah revisi praktis pada draf poster di Canva atau Figma

Urutan pengerjaan di bawah langsung membenahi ketimpangan kolom, kepadatan huruf, dan kelengkapan identitas pada draf poster Anda saat ini tanpa perlu merombak kanvas dari nol:

1. Samakan lebar kedua kolom dan kunci margin A1: atur ukuran dokumen tepat `594 x 841 mm`, pasang garis bantu margin aman `22 mm` di keempat sisi, lalu ubah lebar kolom kiri dan kolom kanan yang semula timpang menjadi sama rata `265 mm` dengan *gutter* tengah `20 mm` serta *padding* dalam kartu `14 mm`.
2. Lengkapi identitas header dan ganti huruf judul:
   - Ganti ikon huruf "S" di pojok kiri atas dengan `Skripsi/images/ikon_asri_poster.svg` (`54 x 54 mm`) sejajar horizontal terhadap `logo_unika.png` (`54 x 54 mm`).
   - Ubah font judul utama dari huruf membulat kasual ke *Sans-Serif* tegas (*Plus Jakarta Sans* atau *Montserrat* *Bold* `44` sampai `46 pt`), ganti baris nama menjadi `Rafif Arsya Pradiva | 22.N4.0014` (`24 pt`, *SemiBold*), dan tambahkan baris afiliasi `Sistem Informasi, Fakultas Ilmu Komputer, Universitas Katolik Soegijapranata` (`15 pt`, Regular, `#475569`).
   - Pendekkan garis biru horizontal di bawah header agar tidak menembus pinggir kertas, tepat selebar area aktif `550 mm`.
3. Tempel kelima paragraf ringkas dan ubah perataan teks:
   - Ganti isi kartu `ABSTRAK`, `LATAR BELAKANG`, `METODE PENELITIAN`, `HASIL DAN PEMBAHASAN`, dan `KESIMPULAN` dengan naskah 263 kata pada Bagian 4.
   - Ubah seluruh paragraf isi dari huruf tebal rata kiri-kanan menjadi Regular (`400`) ukuran `22 pt`, *line-height* `1,48`, warna `#1E293B`, rata kiri (*Left Align*) supaya celah renggang antarkata hilang.
   - Sesuaikan tinggi kartu `ABSTRAK` dan `LATAR BELAKANG` masing-masing sekitar `150 mm` dengan celah antarkartu `24 mm`.
4. Longgarkan *padding* dan beri bingkai pada `Gambar 1 Metode Waterfall`:
   - Panjangkan kartu `METODE PENELITIAN` menjadi `351 mm` hingga menyentuh batas bawah `Y = 819 mm`.
   - Atur ukuran `Gambar 1` menjadi `237 x 147 mm` (menyisakan *padding* `14 mm` di sisi kiri dan kanan kartu, jarak atas `34 mm` dari paragraf, serta ruang bawah yang lega) lalu beri garis tepi `1 pt` (`#CBD5E1`).
5. Pasang bingkai pada tiga layar UI kanan dan rapikan jaraknya:
   - Tambahkan garis tepi `1 pt` (`#CBD5E1`) pada `Gambar 2`, `Gambar 3`, dan `Gambar 4` (selebar `216 mm`, rata tengah di dalam kartu `HASIL DAN PEMBAHASAN` setinggi `541 mm`) agar *sidebar* putih aplikasi tidak menyatu ke latar kartu.
   - Atur celah seragam `18 mm` di antara teks dan ketiga gambar. Bila ingin nama penghuni seragam memakai akun `Tyas`, tukar `Gambar 4` dengan `Skripsi/images/poster/gambar_4_alternatif_akun_tyas.png`.
6. Sejajarkan tepi bawah kedua kolom dan ekspor siap cetak:
   - Beri celah `24 mm` di bawah kartu `HASIL DAN PEMBAHASAN`, lalu atur tinggi kartu `KESIMPULAN` menjadi `134 mm` sehingga tepi bawahnya naik menjauhi pinggir kertas dan sejajar persis dengan kartu `METODE PENELITIAN` pada `Y = 819 mm`.
   - Tinjau ketajaman keempat gambar pada perbesaran `100%` sebelum mengekspor berkas PDF Print berprofil warna CMYK tanpa kompresi gambar.
