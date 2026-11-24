# Panduan desain dan naskah siap pakai poster skripsi (A1)
## Tata letak dua kolom kartu modern dengan empat visual

**Judul Karya Ilmiah:** Sistem Informasi Manajemen Kost Terintegrasi Payment Gateway pada Asri Boarding House  
**Peneliti:** Rafif Arsya Pradiva (`22.N4.0014`)  
**Dosen Pembimbing:** Ir. Andre Kurniawan Pamudji, S.Kom., M.Ling. & Erdhi Widyarto Nugroho, S.T., M.T.  
**Program Studi:** Sistem Informasi, Fakultas Ilmu Komputer, Universitas Katolik Soegijapranata Semarang  
**Pembaca Sasaran:** Mahasiswa Fakultas Ilmu Komputer (dalam dan luar kampus), dosen penguji, serta dosen pembimbing  
**Ukuran Cetak Resmi:** Kertas A1 potret (`594 x 841 mm`), 300 DPI (`7016 x 9933 px`), CMYK  
**Tautan Demo Aplikasi:** [https://asriboardinghouse.weatso.id/](https://asriboardinghouse.weatso.id/) *(referensi portofolio)*  
**Sumber Acuan:** `Skripsi/skripsi_v2_Rafif_Arsya_Pradiva_22.N4.0014.md` dan `Skripsi/journal_inggris_Rafif_Arsya_Pradiva_22.N4.0014.md`

---

## 1. Evaluasi poster saat ini dan perbaikan yang dibutuhkan

Draf poster yang Anda susun sudah memakai empat gambar utama (diagram *Waterfall* dan tiga layar antarmuka), menggunakan tangkapan layar bebas bilah peramban, dan menyejajarkan ujung bawah kedua kolom. Revisi kali ini memadatkan lagi kelima subjudul menjadi satu paragraf berisi 3 sampai 4 kalimat langsung ke inti, memperbaiki susunan tipografi, memperluas ruang kosong di kanvas A1 (`594 x 841 mm`), serta merapikan identitas header agar nyaman dibaca dari jarak 1,5 sampai 2 meter.

| Unsur Poster | Kondisi pada Draf Saat Ini | Target Perbaikan (A1 `594 x 841 mm`) |
| :--- | :--- | :--- |
| Kepadatan naskah | Sudah 3 sampai 4 kalimat per subjudul, tetapi beberapa kalimat masih mencapai 25 sampai 34 kata sehingga kartu terisi 9 sampai 11 baris. | Dipadatkan dari 362 kata menjadi 316 kata (18 kalimat, 3 sampai 4 kalimat per subjudul) dengan variasi kalimat pendek dan sedang sehingga tiap kartu cukup terisi 5 sampai 8 baris. |
| Tipografi dan perataan | Teks bodi memakai bobot terlalu tebal (*SemiBold/Bold*) dengan perataan kiri-kanan (*Justify*) yang meninggalkan celah antarkata tidak merata. | Keluarga *Sans-Serif* modern (*Plus Jakarta Sans*, *Poppins*, *Montserrat*, atau *Canva Sans*) berbobot Regular (`400`), ukuran `22 pt`, *line-height* `1,48`, dan perataan rata kiri (*Left Align*). |
| Margin dan ruang kosong | Margin luar, celah tengah kolom (*gutter*), jarak antarkartu, dan ruang dalam kartu tampak sempit; `Gambar 1` hampir menempel ke tepi kartu. | Margin aman `22 mm`, *gutter* `20 mm`, jarak antarkartu `20` sampai `22 mm`, *padding* kartu `14 mm`, serta celah seragam `16 mm` di antara tiga layar UI kanan. |
| Bingkai 4 gambar | `Gambar 2` sampai `Gambar 4` belum memakai bingkai tipis sehingga *sidebar* putih aplikasi menyatu dengan permukaan kartu putih. | Seluruh empat gambar diberi garis tepi `1 pt` (`#CBD5E1`) agar batas tiap antarmuka dan diagram tegas di atas kartu putih. |
| Header dan identitas | Sudut kiri atas masih memakai ikon huruf "S", nama penulis masih berhuruf kapital penuh dengan tanda pisah panjang, dan baris prodi belum ada. | Memasang `ikon_asri_poster.svg` di kiri, `logo_unika.png` di kanan, baris nama `Rafif Arsya Pradiva | 22.N4.0014`, serta satu baris afiliasi prodi dan kampus. |
| Detail kecil antarmuka | `Gambar 2` masih memuat kotak *tooltip hover* putih di kartu pengeluaran; `Gambar 4` memakai akun `; Mikhayvx` yang berbeda dari akun `Tyas` pada `Gambar 3`. | Tersedia `gambar_4_alternatif_akun_tyas.png` bila ingin menyeragamkan akun `Tyas` pada `Gambar 3` dan `Gambar 4`, serta opsi tangkap ulang `Gambar 2` tanpa *tooltip*. |

---

## 2. Ukuran, grid presisi A1 (`594 x 841 mm`), warna, dan tipografi

### 2.1 Parameter fisik dan alokasi tinggi millimeter
- Ukuran kanvas utama: A1 potret (`594 x 841 mm`), rasio `1 : 1,414` (dapat diskalakan langsung ke A2 `420 x 594 mm` atau A3 `297 x 420 mm` jika dibutuhkan).
- Resolusi dan profil cetak: 300 DPI (`7016 x 9933 px`), profil warna CMYK.
- Batas aman (*safe margin*): `22 mm` di keempat sisi sehingga area kerja aktif berukuran `550 x 797 mm` (berakhir pada `Y = 819 mm`).
- Area header (`86 mm` ditambah celah bawah `12 mm`, total `98 mm`):
  - Sisi kiri (`54 x 54 mm`): `ikon_asri_poster.svg`.
  - Tengah (`420 mm`): judul tiga baris kapital, baris nama dan NIM, serta satu baris afiliasi prodi dan kampus.
  - Sisi kanan (`54 x 54 mm`): `logo_unika.png`.
  - Garis pemisah header: satu garis horizontal `1,5 pt` (`#38BDF8`) selebar `550 mm`.
- Area bodi dua kolom simetris (tinggi `699 mm`):
  - Lebar Kolom Kiri `265 mm`, jarak tengah (*gutter*) `20 mm`, dan lebar Kolom Kanan `265 mm` (total `550 mm`).
  - *Padding* kiri dan kanan di dalam kartu sebesar `14 mm`, menghasilkan lebar efektif teks dan diagram penuh `237 mm`.

| Kolom Kiri (`265 mm`) | Alokasi Tinggi | Kolom Kanan (`265 mm`) | Alokasi Tinggi |
| :--- | :---: | :--- | :---: |
| Kartu `ABSTRAK` (4 kalimat, 69 kata) | `160 mm` | Kartu `HASIL DAN PEMBAHASAN` (4 kalimat, 78 kata + 3 layar UI) | `542 mm` |
| Celah antarkartu | `22 mm` | &emsp;Judul, *padding* atas, dan teks paragraf | *(`148 mm`)* |
| Kartu `LATAR BELAKANG` (4 kalimat, 69 kata) | `165 mm` | &emsp;Celah teks ke `Gambar 2` | *(`16 mm`)* |
| Celah antarkartu | `22 mm` | &emsp;`Gambar 2` (`216 x 94 mm`, rata tengah) + keterangan (`11 mm`) | *(`105 mm`)* |
| Kartu `METODE PENELITIAN` (3 kalimat, 47 kata + 1 diagram) | `330 mm` | &emsp;Celah ke `Gambar 3` | *(`16 mm`)* |
| &emsp;Judul, *padding* atas, dan teks paragraf | *(`116 mm`)* | &emsp;`Gambar 3` (`216 x 100 mm`, rata tengah) + keterangan (`11 mm`) | *(`111 mm`)* |
| &emsp;Celah teks ke `Gambar 1` | *(`26 mm`)* | &emsp;Celah ke `Gambar 4` | *(`16 mm`)* |
| &emsp;`Gambar 1` (`237 x 147 mm`, lebar penuh) + keterangan (`14 mm`) | *(`161 mm`)* | &emsp;`Gambar 4` (`216 x 97 mm`, rata tengah) + keterangan (`11 mm`) | *(`108 mm`)* |
| &emsp;*Padding* bawah kartu | *(`27 mm`)* | &emsp;*Padding* bawah kartu | *(`22 mm`)* |
| | | Celah antarkartu | `20 mm` |
| | | Kartu `KESIMPULAN` (3 kalimat, 53 kata) | `137 mm` |
| **Total Tinggi Kolom Kiri** | **`699 mm`** | **Total Tinggi Kolom Kanan** | **`699 mm`** |

### 2.2 Palet warna kartu modern
| Peran Elemen | Kode HEX | Karakter Visual | Penempatan pada Poster |
| :--- | :--- | :--- | :--- |
| Canvas Base | `#F4FAFE` | Putih kebiruan muda | Latar dasar kanvas di belakang kelima kartu agar batas tiap kotak tetap terlihat jelas. |
| Card Surface | `#FFFFFF` | Putih bersih | Permukaan kelima kartu subjudul untuk menjaga kontras huruf dan diagram saat dicetak. |
| Card Border | `#93C5FD` | Biru langit muda | Garis tepi `1,5 pt` dengan sudut membulat (`border-radius 8 mm`) di sekeliling tiap kartu. |
| Heading Accent | `#0369A1` | Biru akademik tegas | Tajuk lima subjudul (`ABSTRAK` sampai `KESIMPULAN`). |
| Header Divider | `#38BDF8` | Biru cerah tipis | Garis pemisah horizontal `1,5 pt` antara header dan area dua kolom. |
| Primary Text | `#1E293B` | Hitam arang | Judul utama, nama penulis, dan seluruh paragraf isi. |
| Secondary Text | `#475569` | Abu-abu gelap | Baris afiliasi di header serta keterangan `Gambar 1` sampai `Gambar 4`. |
| Image Frame | `#CBD5E1` | Abu-abu tipis | Garis tepi `1 pt` di sekeliling keempat gambar supaya sisi putih antarmuka tidak menyatu ke latar kartu. |

### 2.3 Hierarki tipografi (skala cetak A1)
Gunakan satu keluarga huruf *Sans-Serif* modern (*Plus Jakarta Sans*, *Poppins*, *Montserrat*, atau *Canva Sans*) secara seragam:
- Judul utama poster: `44` sampai `46 pt`, *Bold* (`700`), kapital seluruhnya, rata tengah, *line-height* `1,15`, warna `#0F172A`.
- Nama penulis dan NIM: `24` sampai `26 pt`, *SemiBold* (`600`), *Title Case*, rata tengah (`Rafif Arsya Pradiva | 22.N4.0014`).
- Baris afiliasi (prodi dan kampus): `15 pt`, Regular (`400`), rata tengah, warna `#475569`.
- Tajuk lima subjudul (`ABSTRAK`, `LATAR BELAKANG`, dan seterusnya): `25` sampai `26 pt`, *Bold* (`700`), kapital seluruhnya, rata tengah, warna `#0369A1`, jarak bawah `7 mm`.
- Paragraf isi: `22 pt`, Regular (`400`), warna `#1E293B`, *line-height* `1,48`, perataan rata kiri (*Left Align*) agar spasi antarkata merata tanpa rongga lebar dan mudah dipindai dari jarak 1,5 sampai 2 meter.
- Keterangan gambar (`Gambar 1` sampai `Gambar 4`): `15 pt`, *Medium* (`500`), rata tengah tepat `3 mm` di bawah bingkai tiap gambar, warna `#334155`.

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
|  |  1 paragraf (4 kalimat, 69 kata):  |  |  1 paragraf (4 kalimat, 78 kata):  |  |
|  |  kendala manual; Laravel, MySQL,   |  |  4 akses dan dasbor (Gambar 2);    |  |
|  |  Waterfall, Midtrans; kunci kamar  |  |  pesan 5 tahap (Gambar 3);         |  |
|  |  otomatis; 60 uji dan skor coba.   |  |  tagihan (Gambar 4); 60 uji 100%.  |  |
|  +------------------------------------+  |                                    |  |
|                                          |  +------------------------------+  |  |
|  +------------------------------------+  |  |  Gambar 2 (Dasbor & Kas)     |  |  |
|  |          LATAR BELAKANG            |  |  |  Bingkai 1 pt (216 x 94 mm)  |  |  |
|  |  1 paragraf (4 kalimat, 69 kata):  |  |  +------------------------------+  |  |
|  |  perantau Tembalang; 32 kamar      |  |  Gambar 2 Tampilan dasbor pengelola|  |
|  |  manual; tanggal 10, denda 5%,     |  |           dan laporan kas          |  |
|  |  rekap 3-4 hari; butuh web kas.    |  |                                    |  |
|  +------------------------------------+  |  +------------------------------+  |  |
|                                          |  |  Gambar 3 (Alur 5 Tahap)     |  |  |
|  +------------------------------------+  |  |  Bingkai 1 pt (216 x 100 mm) |  |  |
|  |         METODE PENELITIAN          |  |  +------------------------------+  |  |
|  |  1 paragraf (3 kalimat, 47 kata):  |  |  Gambar 3 Tampilan alur pemesanan  |  |
|  |  wawancara, observasi, buku kas;   |  |         kamar lima tahap           |  |
|  |  6 tahap Waterfall (Gambar 1);     |  |                                    |  |
|  |  60 uji Black Box dan uji coba.    |  |  +------------------------------+  |  |
|  |                                    |  |  |  Gambar 4 (Halaman Tagihan)  |  |  |
|  |  +------------------------------+  |  |  |  Bingkai 1 pt (216 x 97 mm)  |  |  |
|  |  |                              |  |  |  +------------------------------+  |  |
|  |  |  Gambar 1 (Metode Waterfall) |  |  |  Gambar 4 Tampilan halaman tagihan |  |
|  |  |  Bingkai 1 pt (237 x 147 mm) |  |  |         bulanan penghuni           |  |
|  |  |                              |  |  +------------------------------------+  |
|  |  +------------------------------+  |                                          |
|  |      Gambar 1 Metode Waterfall     |  +------------------------------------+  |
|  |                                    |  |            KESIMPULAN              |  |
|  |                                    |  |  1 paragraf (3 kalimat, 53 kata):  |  |
|  |                                    |  |  32 kamar tertib dan rekap detik;  |  |
|  |                                    |  |  validasi 60 uji dan skor coba;    |  |
|  +------------------------------------+  |  empat arah pengembangan lanjut.   |  |
|                                          +------------------------------------+  |
+----------------------------------------------------------------------------------+
```

---

## 4. Naskah siap pakai poster (salin langsung ke Canva atau Figma)

Tiap subjudul di bawah terdiri dari satu paragraf berisi 3 sampai 4 kalimat (total 18 kalimat, 316 kata) dengan alur langsung ke inti dan rujukan berurutan dari `Gambar 1` sampai `Gambar 4`.

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
> Pengelolaan 32 kamar di Asri Boarding House memakai buku catatan dan WhatsApp sering memicu pesanan ganda. Penelitian ini membangun aplikasi web berbasis Laravel dan MySQL lewat metode *Waterfall*, terintegrasi dengan *payment gateway* Midtrans serta notifikasi WhatsApp dan *email*. Begitu pembayaran terverifikasi, kamar terkunci otomatis dan kuitansi PDF langsung terbit. Seluruh 60 skenario *Black Box* lulus 100%, dengan skor uji lapangan 9,5/10 dari pengelola dan rata-rata 8,0/10 dari lima penghuni.

#### `LATAR BELAKANG`
> Mahasiswa perantau di Tembalang sulit mengecek kamar kosong dari jauh. Pengelola Asri Boarding House masih mencatat 32 kamar tipe VIP, Deluxe, dan Standar di buku tulis dan WhatsApp sehingga bentrok pesanan kerap terjadi. Pantauan jatuh tempo tanggal 10 dan denda 5% pun mudah terlewat, sementara rekap kas bulanan menyita tiga sampai empat hari. Asri Boarding House memerlukan aplikasi web terpusat dengan *payment gateway* agar reservasi dan pembukuan berjalan otomatis.

#### `METODE PENELITIAN`
> Data dihimpun lewat wawancara pengelola berpengalaman 20 tahun dan lima penghuni aktif, disertai observasi harian serta telaah buku kas. Pembangunan aplikasi Laravel, MySQL, dan Midtrans mengikuti enam tahap *Waterfall* pada Gambar 1. Seluruh fitur diuji dengan 60 skenario *Black Box* dan uji lapangan berskala 1 sampai 10.

Penempatan visual Kolom Kiri (lebar penuh di bawah paragraf `METODE PENELITIAN`):
1. Diagram metode (`237 x 147 mm`, rata tengah, bingkai `1 pt #CBD5E1`): pasang `Skripsi/images/poster/gambar_1_metode_waterfall.png` dengan keterangan rata tengah di bawahnya: `Gambar 1 Metode Waterfall`

---

### Kolom kanan

#### `HASIL DAN PEMBAHASAN`
> Aplikasi memisahkan akses pengunjung, calon penyewa, penghuni, dan pengelola, dengan dasbor (Gambar 2) yang memantau 32 kamar serta rekap kas dalam hitungan detik. Lewat alur lima tahap (Gambar 3), pembayaran DP 30% atau lunas via Midtrans langsung mengunci kamar dan menghasilkan kuitansi PDF. Halaman tagihan (Gambar 4) menerbitkan rincian tiap tanggal 1, jatuh tempo tanggal 10, denda 5% bila lewat akhir bulan, serta pengingat WhatsApp dan *email*. Seluruh 60 skenario uji *Black Box* pada enam modul lulus 100%.

Penempatan visual Kolom Kanan (bertumpuk vertikal rata tengah selebar `216 mm` dengan celah seragam `16 mm` dan bingkai `1 pt #CBD5E1` di bawah paragraf `HASIL DAN PEMBAHASAN`):
1. Layar pertama (`216 x 94 mm`): pasang `Skripsi/images/poster/gambar_2_dasbor_pengelola_kas.png` dengan keterangan rata tengah di bawahnya: `Gambar 2 Tampilan dasbor pengelola dan laporan kas`
2. Layar kedua (`216 x 100 mm`): pasang `Skripsi/images/poster/gambar_3_alur_pemesanan_5_tahap.png` dengan keterangan rata tengah di bawahnya: `Gambar 3 Tampilan alur pemesanan kamar lima tahap`
3. Layar ketiga (`216 x 97 mm`): pasang `Skripsi/images/poster/gambar_4_tagihan_bulanan_penghuni.png` dengan keterangan rata tengah di bawahnya: `Gambar 4 Tampilan halaman tagihan bulanan penghuni`

#### `KESIMPULAN`
> Sistem ini menghilangkan pesanan ganda pada 32 kamar dan menuntaskan rekap kas dalam hitungan detik. Keandalannya teruji lewat 60 skenario *Black Box* (100%), skor 9,5/10 dari pengelola, dan rata-rata 8,0/10 dari lima penghuni. Pengembangan berikutnya dapat menambah pengingat WhatsApp H-3 bertautan bayar langsung, panduan penghuni baru, penyempurnaan tampilan ponsel, serta kontrak sewa digital.

---

## 5. Matriks aset gambar siap cetak A1 dan vektor ikon aplikasi

### 5.1 Paket gambar siap tempel (`Skripsi/images/poster/`)
Keempat gambar utama disimpan terpisah di folder `Skripsi/images/poster/` sehingga berkas asli skripsi di `Skripsi/images/` tetap utuh. Bilah peramban pada `Gambar 2` sampai `Gambar 4` sudah dipotong bersih dengan kepadatan piksel `223` sampai `287 DPI` pada ukuran cetak A1.

| Posisi Poster | Keterangan Resmi | Berkas Siap Tempel (`Skripsi/images/...`) | Dimensi Siap Cetak | Estimasi DPI di A1 | Sumber Asli dan Catatan |
| :--- | :--- | :--- | :---: | :---: | :--- |
| Header Kiri | Ikon Aplikasi Asri | `ikon_asri_poster.svg` | Vektor | Tak terbatas | Ukuran pasang `54 x 54 mm`; menggantikan ikon templat huruf "S". |
| Header Kanan | Logo Unika | `logo_unika.png` | `254 x 255 px` | ~`120 DPI` | Ukuran pasang `54 x 54 mm` sejajar vertikal dengan ikon kiri. |
| `Gambar 1` | `Gambar 1 Metode Waterfall` | `poster/gambar_1_metode_waterfall.png` | `2677 x 1658 px` | ~`287 DPI` | Dari `metode_sdlc_waterfall.png` (resmi `Gambar 3.1` skripsi, enam tahap dan umpan balik). |
| `Gambar 2` | `Gambar 2 Tampilan dasbor pengelola dan laporan kas` | `poster/gambar_2_dasbor_pengelola_kas.png` | `1897 x 823 px` | ~`223 DPI` | Dari `gambar_4_26_dashboard_admin.png`; bilah Chrome (`113 px`) sudah terpotong. |
| `Gambar 3` | `Gambar 3 Tampilan alur pemesanan kamar lima tahap` | `poster/gambar_3_alur_pemesanan_5_tahap.png` | `2048 x 944 px` | ~`241 DPI` | Dari `antarmuka_stepper_reservasi_penyewa.png`; bebas bilah Chrome dan *upscale* 2x. |
| `Gambar 4` | `Gambar 4 Tampilan halaman tagihan bulanan penghuni` | `poster/gambar_4_tagihan_bulanan_penghuni.png` | `1918 x 864 px` | ~`226 DPI` | Dari `gambar_4_38_tagihan_penyewa.png`; bilah Chrome (`73 px`) sudah terpotong. |

Catatan opsi tambahan di disk:
- Opsi seragam akun `Tyas` untuk `Gambar 4`: berkas `Skripsi/images/poster/gambar_4_alternatif_akun_tyas.png` (`2048 x 940 px`, ~`241 DPI`, ukuran pasang `216 x 99 mm`) menampilkan dua riwayat tagihan lunas atas nama `Tyas`, sama seperti akun pada `Gambar 3`.
- Pembersihan opsional *tooltip* pada `Gambar 2`: tangkapan layar `gambar_4_26_dashboard_admin.png` masih memperlihatkan kotak *tooltip hover* putih kecil di kartu keempat (*Total Pengeluaran*). Jika ingin halaman dasbor bersih tanpa *tooltip*, ambil ulang tangkapan layar `admin/dashboard` dengan kursor di luar area kartu lalu potong bagian atas perambannya.

### 5.2 Detail vektor ikon kiri atas (`ikon_asri_poster.svg`)
Berkas vektor `Skripsi/images/ikon_asri_poster.svg` memadukan simbol rumah dan daun hijau `#10B981` dari `resources/views/components/application-logo.blade.php` di dalam kotak membulat biru langit agar seimbang dengan logo kuning Unika di sisi kanan. Berkas ini dapat langsung diimpor ke Canva atau Figma:

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

Urutan di bawah langsung menargetkan perbaikan yang masih tampak pada draf poster Anda saat ini tanpa perlu membangun tata letak dari awal:

1. Kunci kanvas A1 dan lebarkan panduan grid: pastikan ukuran dokumen tepat `594 x 841 mm`, pasang garis bantu margin luar `22 mm` di keempat sisi, atur dua kolom selebar `265 mm` dengan *gutter* `20 mm`, dan beri *padding* dalam kartu `14 mm`.
2. Rapikan header poster:
   - Ganti ikon huruf "S" di kiri atas memakai `Skripsi/images/ikon_asri_poster.svg` berukuran `54 x 54 mm` sejajar vertikal dengan `logo_unika.png`.
   - Ubah baris nama penulis dari huruf kapital bertanda pisah panjang menjadi `Rafif Arsya Pradiva | 22.N4.0014` (`24 pt`, *SemiBold*), lalu tambahkan baris afiliasi `Sistem Informasi, Fakultas Ilmu Komputer, Universitas Katolik Soegijapranata` (`15 pt`, Regular, `#475569`) tepat di bawahnya.
3. Tempel kelima paragraf revisi dan perbaiki bobot huruf:
   - Ganti isi kelima kartu (`ABSTRAK`, `LATAR BELAKANG`, `METODE PENELITIAN`, `HASIL DAN PEMBAHASAN`, `KESIMPULAN`) memakai naskah pada Bagian 4.
   - Ubah teks bodi dari *SemiBold/Bold* rata kiri-kanan menjadi Regular (`400`) ukuran `22 pt`, *line-height* `1,48`, warna `#1E293B`, dengan perataan rata kiri (*Left Align*).
   - Atur tinggi `ABSTRAK` sekitar `160 mm` dan `LATAR BELAKANG` sekitar `165 mm` dengan jarak antarkartu `22 mm`.
4. Beri ruang napas dan bingkai pada `Gambar 1 Metode Waterfall`:
   - Atur tinggi kartu `METODE PENELITIAN` sekitar `330 mm` agar jarak di atas (`26 mm`) dan di bawah (`27 mm`) `Gambar 1` seimbang.
   - Perkecil sedikit lebar `Gambar 1` agar masuk tepat selebar area dalam kartu (`237 x 147 mm`, menyisakan *padding* `14 mm` di kiri dan kanan), lalu tambahkan garis tepi tipis `1 pt` (`#CBD5E1`).
5. Tambah bingkai dan lebarkan celah tiga layar UI di Kolom Kanan:
   - Beri garis tepi `1 pt` (`#CBD5E1`) pada `Gambar 2`, `Gambar 3`, dan `Gambar 4` (masing-masing selebar `216 mm`, rata tengah) agar *sidebar* putih aplikasi terpisah jelas dari latar kartu.
   - Atur jarak antargambar seragam `16 mm` dengan *padding* bawah kartu `22 mm`. Jika ingin akun pada `Gambar 3` dan `Gambar 4` seragam memakai `Tyas`, tukar `Gambar 4` memakai `Skripsi/images/poster/gambar_4_alternatif_akun_tyas.png`.
6. Sejajarkan batas bawah kolom dan ekspor cetak A1:
   - Sesuaikan celah ke kartu `KESIMPULAN` (`20 mm`) dan tinggi kartu `KESIMPULAN` (`137 mm`) supaya tepi bawah Kolom Kiri dan Kolom Kanan berhenti sejajar di `Y = 819 mm`.
   - Periksa ketajaman diagram dan tabel antarmuka pada pratinjau `100%`, lalu ekspor ke PDF Print berprofil warna CMYK tanpa kompresi gambar.
