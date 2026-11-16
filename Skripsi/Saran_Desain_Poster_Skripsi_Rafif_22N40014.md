# Panduan Desain & Naskah Siap Pakai Poster Skripsi
## Konsep Tata Letak: Poster Akademik Dua Kolom Simetris (Pendekatan ATM)

**Judul Karya Ilmiah:** Sistem Informasi Manajemen Kost Terintegrasi Payment Gateway pada Asri Boarding House  
**Peneliti:** Rafif Arsya Pradiva (`22.N4.0014`)  
**Dosen Pembimbing:** Ir. Andre Kurniawan Pamudji, S.Kom., M.Ling. & Erdhi Widyarto Nugroho, S.T., M.T.  
**Program Studi:** Sistem Informasi, Fakultas Ilmu Komputer, Universitas Katolik Soegijapranata Semarang  
**Tautan Demo Aplikasi:** [https://asriboardinghouse.weatso.id/](https://asriboardinghouse.weatso.id/) *(referensi portofolio)*  
**Sumber Acuan Resmi:** `Skripsi/skripsi_v2_Rafif_Arsya_Pradiva_22.N4.0014.md` & `Skripsi/journal_inggris_Rafif_Arsya_Pradiva_22.N4.0014.md`

---

## 1. Matriks ATM (Amati, Tiru, Modifikasi) dari Poster Referensi

Rancangan ini mengadaptasi struktur poster akademik Fakultas Ilmu Komputer Universitas Katolik Soegijapranata agar tampil bersih, mudah dipahami semua kalangan kampus (mahasiswa lintas jurusan, dosen, maupun pengunjung umum), serta nyaman dibaca dari jarak 1 hingga 1,5 meter:

| Unsur Desain | Amati (Contoh Referensi) | Tiru (Struktur Inti) | Modifikasi (Adaptasi Asri Boarding House) |
| :--- | :--- | :--- | :--- |
| **Ukuran & Rasio Kanvas** | Dimensi contoh `591 x 835 px` (rasio `0,708` atau `1 : 1,414` seri ISO A potret). | Mengunci rasio potret seri ISO A (`1 : 1,414`). | Ditetapkan pada **A1 Potret (`594 x 841 mm`)**, 300 DPI (`7016 x 9933 px`), kompatibel 1:1 untuk A2 (`420 x 594 mm`) dan A3 (`297 x 420 mm`). |
| **Latar Belakang** | Kanvas biru langit pucat berhias gelombang lembut transparan tanpa bingkai kotak teks. | Teks ditempatkan langsung di atas kanvas pastel lembut tanpa kotak kartu dan tanpa banner gelap atas-bawah. | Memakai gelombang biru langit sangat muda (`#F4FAFE`, `#E2F1FA`, `#CEE7F6`) yang menyatu dengan logo kuning Unika serta tampilan antarmuka web. |
| **Header 3 Slot** | Kiri: ikon aplikasi kotak membulat. Tengah: judul kapital tebal + nama & NIM. Kanan: logo Unika. Bawah header: garis pemisah tipis. | Susunan simetris `[Ikon App]` \| `[Judul + Nama NIM]` \| `[Logo Unika]` beserta satu garis pemisah horizontal tipis (`1,5 pt`). | Kiri memakai ikon kotak membulat Asri Boarding House (rumah putih + daun hijau `#10B981`); kanan memakai `logo_unika.png` (`254 x 255 px`). |
| **Struktur Bodi** | Dua kolom seimbang (`50% : 50%`). Kiri: `ABSTRAK`, `LATAR BELAKANG`, `METODE PENELITIAN`. Kanan: `HASIL DAN PEMBAHASAN` + 3 gambar + `KESIMPULAN`. | Mempertahankan tepat 5 bagian inti dan keseimbangan tinggi ujung bawah kolom kiri serta kanan. | Menjaga gaya bahasa populer yang mengalir tanpa istilah koding yang rumit, serta menampilkan ringkasan angka langsung lewat dasbor pengelola pada `Gambar 1`. |
| **Galeri Hasil** | 1 gambar lebar penuh (`Gambar 1`) di atas, lalu 2 gambar sejajar (`Gambar 2` dan `Gambar 3`) di bawahnya, seluruhnya diberi keterangan rata tengah. | Susunan `1 + 2` dengan keterangan `Gambar 1`, `Gambar 2`, dan `Gambar 3` rata tengah di bawah tiap bingkai. | Memilih 3 cuplikan antarmuka berasio seragam (`~2 : 1`) beresolusi tinggi dengan bilah penanda peramban dipotong rapi agar presisi saat dicetak. |
| **Tipografi & Perataan** | Huruf *Serif* klasik, tajuk bagian kapital tebal rata tengah, isi berupa paragraf rata kiri-kanan (*justify*) tanpa daftar poin. | Judul bagian rata tengah kapital tebal dan isi paragraf *justify* yang mengalir utuh. | Mengatur skala huruf paragraf pada `19-21 pt` dengan jarak baris `1,38` agar tetap tajam dan nyaman dibaca saat pameran. |

---

## 2. Spesifikasi Ukuran, Tata Letak, Warna & Tipografi (Skala Cetak A1)

### 2.1 Parameter Fisik & Grid Kanvas
- **Ukuran Kanvas Utama:** **A1 Potret (`594 x 841 mm`)** (dapat langsung diperkecil proporsional ke **A2 `420 x 594 mm`** atau **A3 `297 x 420 mm`** tanpa mengubah tata letak karena memiliki rasio identik `1 : 1,414`).
- **Resolusi & Profil Cetak:** **300 DPI (`7016 x 9933 px`)**, profil warna **CMYK** untuk hasil cetak tajam.
- **Batas Aman (*Safe Margin*):** `22 hingga 25 mm` dari keempat tepi luar kertas.
- **Pembagian Grid:**
  - **Area Header (`~12%` tinggi kanvas):** Tiga kolom horizontal (`16%` Ikon Kiri, `68%` Judul & Identitas Tengah, `16%` Logo Unika Kanan), ditutup garis pemisah horizontal tipis sepanjang lebar area isi.
  - **Area Bodi (`~88%` tinggi kanvas):** Dua kolom simetris masing-masing selebar `48%` dengan jarak antarkolom (*gutter*) sebesar `4%` (`~22 mm`).

### 2.2 Palet Warna Kanvas & Elemen
| Peran Elemen | Kode HEX | Karakter Visual | Penempatan pada Poster |
| :--- | :--- | :--- | :--- |
| **Canvas Base** | `#F4FAFE` | Putih Kebiruan Sangat Lembut | Dasar kanvas agar teks hitam memiliki kontras tertinggi saat dicetak. |
| **Soft Wave Layer 1** | `#E2F1FA` | Biru Pastel Halus | Lapisan gelombang besar beropasitas `55-70%` di sisi kiri atas, tengah kanan, dan sudut bawah. |
| **Soft Wave Layer 2** | `#CEE7F6` | Biru Langit Lembut | Lapisan gelombang sekunder melengkung diagonal di sudut kanan atas dan kiri bawah (`opasitas 45-60%`). |
| **Accent Glow** | `#B8DCF2` | Aksen Bayangan Gelombang | Pita lengkung tipis di tepi luar kanvas sebagai pemanis ruang kosong. |
| **Primary Text** | `#111827` | Hitam Arang Tajam | Judul utama, nama penulis, tajuk lima bagian, dan seluruh teks paragraf. |
| **Header Divider** | `#94A3B8` | Abu-Abu Baja Halus | Garis pembatas tipis (`1,5 pt`) pemisah antara header dan area bodi dua kolom. |
| **Image Border & Caption** | `#CBD5E1` / `#1E293B` | Bingkai Tipis & Keterangan | Garis tepi `1 pt` (`#CBD5E1`) di sekeliling cuplikan antarmuka dan teks keterangan (`#1E293B`). |

### 2.3 Hierarki Tipografi (Skala Cetak A1)
Gunakan satu keluarga huruf **Serif Klasik** yang konsisten (*Times New Roman*, *Georgia*, atau *Noto Serif*) agar menyatu dengan gaya poster ilmiah referensi:
- **Judul Utama Poster:** `46 hingga 52 pt`, *Bold*, huruf kapital seluruhnya, rata tengah, *line-height* `1,20`.
- **Nama Penulis & NIM:** `28 hingga 32 pt`, *Regular*, huruf *Title Case*, rata tengah (`Rafif Arsya Pradiva 22.N4.0014`, dikunci ringkas satu baris seperti contoh gambar; tambahkan baris `16 pt` berwarna `#334155` hanya bila diwajibkan aturan cetak prodi).
- **Tajuk Lima Bagian (`ABSTRAK`, `LATAR BELAKANG`, dll.):** `26 hingga 30 pt`, *Bold*, huruf kapital seluruhnya, rata tengah, jarak bawah `6-8 mm`.
- **Isi Paragraf Bodi:** `19 hingga 21 pt`, *Regular*, perataan **Justify** (rata kiri-kanan), *line-height* `1,38`, tanpa daftar poin (*bullet*) agar membentuk blok bacaan yang rapi.
- **Keterangan Gambar (`Gambar 1`, `Gambar 2`, `Gambar 3`):** `15 hingga 16 pt`, *Regular*, rata tengah tepat di bawah masing-masing gambar.

---

## 3. Cetak Biru Wireframe (Kanvas A1)

```text
+----------------------------------------------------------------------------------+
| [IKON ASRI]        SISTEM INFORMASI MANAJEMEN KOST               [LOGO UNIKA]    |
| (Rounded Sq)        TERINTEGRASI PAYMENT GATEWAY                                 |
|                      PADA ASRI BOARDING HOUSE                                    |
|                                                                                  |
|                     Rafif Arsya Pradiva 22.N4.0014                               |
|----------------------------------------------------------------------------------|
|                                                                                  |
|               ABSTRAK                       HASIL DAN PEMBAHASAN                 |
|  Paragraf ringkas tentang kendala      Paragraf empat ruang akses pengguna,      |
|  sewa 32 kamar manual, solusi web      dasbor pengelola & rekap kas (Gambar 1),  |
|  kost terintegrasi Midtrans, metode    alur pesan 5 tahap & VA Midtrans          |
|  Waterfall dengan Laravel & MySQL,     (Gambar 2), tagihan tgl 1, jatuh tempo    |
|  kunci status kamar otomatis,          tgl 10 & denda 5% (Gambar 3), kuitansi    |
|  kuitansi PDF, pengingat tagihan,      PDF lunas, 60 uji fungsi (100%), serta    |
|  60 uji fungsi (100%), skor penjaga    skor 9,5/10 dan rata-rata 8,0/10.         |
|  kost 9,5/10, serta rata-rata 8,0/10                                             |
|  dari lima mahasiswa penghuni.         +--------------------------------------+  |
|                                        |  Gambar 1 (Dasbor Pengelola & Kas)   |  |
|           LATAR BELAKANG               |  Lebar penuh kolom kanan (rasio 2:1) |  |
|  Paragraf arus perantau di Tembalang,  +--------------------------------------+  |
|  pengelolaan 32 kamar (VIP, Deluxe,     Gambar 1 Tampilan dasbor pengelola dan   |
|  Standar) memakai buku tulis & WA               laporan kas                      |
|  pribadi, risiko pesanan ganda,                                                  |
|  tagih tgl 10 & denda 5% manual,       +-----------------+  +-----------------+  |
|  rekap kas 3-4 hari, serta perlunya    | Gambar 2        |  | Gambar 3        |  |
|  aplikasi web terpusat.                | (Alur 5 Tahap)  |  | (Halaman Tagih) |  |
|                                        +-----------------+  +-----------------+  |
|         METODE PENELITIAN               Gambar 2 Tampilan    Gambar 3 Tampilan   |
|  Paragraf riset pengembangan & studi    alur pemesanan       halaman tagihan     |
|  kasus di Asri Boarding House,          kamar lima tahap     bulanan penghuni    |
|  wawancara penjaga kost 20 th & lima                                             |
|  penghuni, pengamatan, telaah arsip,               KESIMPULAN                    |
|  serta alur Waterfall (analisis,       Paragraf tertibnya sewa 32 kamar tanpa    |
|  rancangan 3 peran, pembuatan web      pesanan ganda, rekap kas hitungan detik,  |
|  Laravel & MySQL terhubung Midtrans,   kelulusan 60 uji fitur (100%), skor       |
|  60 uji Black Box, dan uji coba        9,5/10 dan 8,0/10, serta saran lanjutan   |
|  lapangan berskala 1 sampai 10).       (WA H-3, panduan awal, kontrak digital).  |
+----------------------------------------------------------------------------------+
```

---

## 4. Naskah Siap Pakai Poster (Salin Langsung ke Canva atau Figma)

Seluruh naskah di bawah memakai ragam bahasa hibrida (akademis dan humanis) yang ramah bagi seluruh pembaca kampus tanpa istilah teknis yang menyulitkan, sekaligus menjaga panjang kolom kiri dan kolom kanan agar sejajar di tepi bawah kanvas.

### BAGIAN HEADER (ATAS, RATA TENGAH)

**Judul Utama (3 Baris Kapital):**
```text
SISTEM INFORMASI MANAJEMEN KOST
TERINTEGRASI PAYMENT GATEWAY
PADA ASRI BOARDING HOUSE
```

**Nama & NIM Penulis:**
```text
Rafif Arsya Pradiva 22.N4.0014
```

---

### KOLOM KIRI

#### `ABSTRAK`
> Urusan sewa 32 kamar di Asri Boarding House Tembalang selama ini masih dicatat manual lewat buku tulis dan WhatsApp pribadi. Akibatnya, dua peminat kerap memesan kamar yang sama karena data ketersediaan terlambat diperbarui. Penagihan sewa setiap tanggal 10 pun rawan terlewat, sedangkan rekap bukti transfer akhir bulan memakan waktu hingga empat hari. Penelitian ini membangun sistem informasi manajemen kost berbasis web yang terintegrasi dengan gerbang pembayaran Midtrans untuk menertibkan pemesanan kamar hingga pembukuan kas. Aplikasi dikembangkan melalui tahapan metode *Waterfall* menggunakan kerangka kerja Laravel dan basis data MySQL. Saat pemesanan berlangsung, sistem otomatis mengunci status kamar pilihan agar tidak terjadi bentrok jadwal antarpeminat. Pembayaran daring pun terverifikasi instan tanpa kirim foto resi, diikuti penerbitan kuitansi PDF serta pengingat tagihan otomatis via WhatsApp dan *email*. Seluruh 60 skenario pengujian fungsi aplikasi berjalan 100% tanpa kendala. Pada uji pemakaian langsung, penjaga kost memberikan nilai 9,5 dari 10 karena rekap kas tuntas dalam hitungan detik tanpa selisih, sedangkan lima mahasiswa penghuni memberikan rata-rata 8,0 dari 10.

#### `LATAR BELAKANG`
> Kawasan pendidikan Tembalang di Semarang selalu dipadati mahasiswa perantau setiap tahun ajaran baru, tetapi informasi kost yang masih mengandalkan kabar dari mulut ke mulut mengharuskan mereka survei langsung ke lokasi. Kendala serupa terjadi di Asri Boarding House saat mengelola 32 kamar tipe VIP, Deluxe, dan Standar. Penjaga kost masih mencatat hunian di buku tulis sembari membalas pesan calon penyewa satu per satu lewat WhatsApp pribadi. Perubahan status kamar pun sering terlambat tercatat. Saat dua peminat memesan kamar yang sama dalam waktu berdekatan, bentrok jadwal sewa sulit dihindari. Urusan penagihan setiap tanggal 10 dan perhitungan denda keterlambatan 5% juga masih dikerjakan manual sehingga pengingat bayar kerap terlewat. Di akhir bulan, penjaga kost bahkan membutuhkan tiga sampai empat hari untuk mencocokkan uang tunai, foto bukti transfer penghuni, serta tumpukan nota belanja operasional. Kondisi ini menuntut hadirnya aplikasi web manajemen kost yang terhubung dengan pembayaran otomatis (*payment gateway*) agar pemesanan kamar, penagihan rutin, dan pembukuan kas berjalan tertib tanpa selisih.

#### `METODE PENELITIAN`
> Penelitian dan pengembangan berbasis studi kasus ini dilaksanakan di Asri Boarding House, Jalan Maera Sari Nomor 1, Tembalang, Semarang. Pengumpulan data dilakukan melalui wawancara langsung bersama penjaga kost berpengalaman 20 tahun dan lima mahasiswa penghuni aktif, disertai pengamatan harian serta telaah catatan sewa, daftar tarif, dan nota pengeluaran. Pengembangan aplikasi web mengikuti metode *Waterfall* yang diawali analisis kebutuhan, perancangan alur kerja tiga peran pengguna, serta penyusunan struktur basis data. Sistem kemudian dibangun menggunakan kerangka kerja Laravel dan basis data MySQL yang dihubungkan langsung dengan gerbang pembayaran Midtrans serta pengingat WhatsApp dan *email*. Tahap akhir mencakup pengujian *Black Box* pada 60 skenario fitur hingga uji coba pemakaian bersama pengelola dan penghuni kost menggunakan penilaian skala 1 sampai 10.

---

### KOLOM KANAN

#### `HASIL DAN PEMBAHASAN`
> Aplikasi web yang dibangun memisahkan ruang akses bagi pengunjung umum, calon penyewa, penghuni aktif, dan pengelola kost. Melalui dasbor pengelola (`Gambar 1`), keterisian 32 kamar, belanja operasional, hingga laporan kas terpantau langsung dalam satu layar sehingga tutup buku bulanan selesai dalam hitungan detik tanpa selisih. Calon penyewa dari luar kota dapat mengecek kamar kosong, mengisi NIK 16 digit beserta kontak wali, lalu mengikuti alur pemesanan lima tahap (`Gambar 2`). Pembayaran uang muka 30% maupun pelunasan penuh diproses lewat *Virtual Account* Midtrans yang otomatis mengunci kamar pilihan dan menerbitkan kuitansi PDF berstempel lunas. Bagi penghuni aktif, tagihan sewa terbit otomatis setiap tanggal 1 pada portal mandiri (`Gambar 3`) diiringi pengingat WhatsApp serta *email*. Batas bayar ditetapkan setiap tanggal 10 dengan kelonggaran hingga akhir bulan berjalan, sedangkan tunggakan yang melewati pergantian bulan dikenai denda tetap 5% satu kali. Pengujian 60 skenario fungsi sistem berhasil 100%, didukung nilai kepuasan 9,5 dari 10 oleh penjaga kost dan rata-rata 8,0 dari 10 oleh lima mahasiswa penghuni aktif.

- **Penempatan Visual Hasil (`Gambar 1`, `Gambar 2`, `Gambar 3`):**
  1. **Baris Atas (Lebar Penuh Kolom Kanan):** Pasang `gambar_4_26_dashboard_admin.png` dan beri keterangan rata tengah di bawahnya: `Gambar 1 Tampilan dasbor pengelola dan laporan kas`
  2. **Baris Bawah Kiri (48% Kolom Kanan):** Pasang `antarmuka_stepper_reservasi_penyewa.png` dan beri keterangan rata tengah di bawahnya: `Gambar 2 Tampilan alur pemesanan kamar lima tahap`
  3. **Baris Bawah Kanan (48% Kolom Kanan):** Pasang `gambar_4_38_tagihan_penyewa.png` dan beri keterangan rata tengah di bawahnya: `Gambar 3 Tampilan halaman tagihan bulanan penghuni`

#### `KESIMPULAN`
> Kehadiran aplikasi web manajemen kost berhasil menertibkan pengelolaan 32 kamar di Asri Boarding House. Penguncian kamar otomatis saat pemesanan berlangsung mampu menutup celah pesanan ganda, sedangkan pembayaran daring Midtrans dan kuitansi PDF membuat pencatatan sewa lebih transparan. Pengingat tagihan rutin setiap tanggal 1 melalui WhatsApp serta *email* juga membantu penghuni membayar tepat waktu, sekaligus memangkas rekap kas bulanan menjadi hitungan detik. Secara teknis, seluruh 60 skenario pengujian fitur berjalan 100% tanpa kendala. Manfaat ini tercermin dari nilai kepuasan 9,5 dari 10 oleh penjaga kost serta rata-rata 8,0 dari 10 oleh lima mahasiswa penghuni. Ke depannya, sistem dapat dilengkapi pengingat WhatsApp H-3 bertautan bayar langsung, panduan awal bagi penghuni baru, perapian tampilan ponsel, serta kontrak sewa digital.

---

## 5. Matriks Aset Gambar & Kode Ikon Aplikasi

### 5.1 Daftar Berkas Gambar Resmi (`Skripsi/images/`)
Seluruh berkas di bawah ini telah diverifikasi langsung keberadaan serta resolusinya agar tajam saat dicetak pada ukuran A1:

| Posisi pada Poster | Nama Berkas (`Skripsi/images/`) | Dimensi Asli | Panduan Penempatan & Keterangan |
| :--- | :--- | :--- | :--- |
| **Header Kanan** | `logo_unika.png` | `254 x 255 px` | Letakkan di sudut kanan atas sejajar vertikal dengan judul dan ikon kiri, ukuran cetak sekitar `45 x 45 mm`. |
| **Header Kiri** | `ikon_asri_poster.svg` | Vektor (`256 x 256 px`) | Berkas SVG siap pakai sudah tersedia di `Skripsi/images/ikon_asri_poster.svg`; tempatkan sejajar dengan `logo_unika.png` berukuran `45 x 45 mm`. |
| **`Gambar 1` (Lebar Penuh)** | `gambar_4_26_dashboard_admin.png` | `1897 x 936 px` | Potong bilah alamat dan *bookmark bar* Chrome. Menampilkan 8 kartu ringkasan, grafik pemasukan 12 bulan, serta status 32 kamar. |
| **`Gambar 2` (Kiri Bawah)** | `antarmuka_stepper_reservasi_penyewa.png` | `1024 x 512 px` | Potong bilah peramban. Menampilkan alur pemesanan 5 tahap terkonfirmasi beserta rincian kamar. |
| **`Gambar 3` (Kanan Bawah)** | `gambar_4_38_tagihan_penyewa.png` | `1918 x 937 px` | Potong bilah peramban. Menampilkan kartu ringkasan dan riwayat tagihan sewa berstatus LUNAS via Midtrans. |

**Opsi Gambar Pengganti yang Tetap Tersedia:**
- Jika ingin menampilkan dasbor pengelola versi tema gelap pada `Gambar 1`, gunakan `antarmuka_dasbor_admin_keuangan.png` (`1024 x 504 px`).
- Jika ingin menonjolkan etalase publik pada salah satu slot bawah, gunakan `antarmuka_katalog_kamar_publik.png` (`1024 x 510 px`).
- Jika ingin menonjolkan bukti pembayaran instan, `gambar_4_22_payment_gateway_midtrans.png` (`414 x 627 px`) atau `gambar_4_42_nota_pdf.png` (`532 x 514 px`) dapat dipasang dengan penyesuaian bingkai potong agar tingginya tetap seragam.

### 5.2 Detail Vektor Ikon Aplikasi Kiri Atas (`ikon_asri_poster.svg`)
Berkas vektor `Skripsi/images/ikon_asri_poster.svg` menggabungkan simbol rumah dan daun hijau `#10B981` dari `resources/views/components/application-logo.blade.php` ke dalam wadah kotak membulat bernuansa biru langit cerah agar seimbang dengan logo kuning Unika di sisi kanan. Berkas ini sudah siap diimpor langsung ke Canva atau Figma dengan struktur kode berikut:

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

## 6. Langkah Eksekusi Praktis di Canva atau Figma

1. **Siapkan Kanvas A1:** Buat dokumen baru ukuran khusus **`594 x 841 mm`** dengan batas panduan (*margin*) **`22 mm`** di keempat sisi.
2. **Bangun Latar Gelombang Pastel:** Isi latar dengan warna `#F4FAFE`, lalu tumpuk 3 hingga 4 bentuk lengkung organik memakai warna `#E2F1FA` dan `#CEE7F6` pada opasitas `45-65%` di sudut kanan atas, sisi kiri tengah, serta sudut bawah kanvas.
3. **Susun Area Header & Pembatas:**
   - Tempatkan `ikon_asri_poster.svg` di sudut kiri atas dan `logo_unika.png` di sudut kanan atas pada tinggi sejajar (`45 mm`).
   - Ketik judul 3 baris beserta `Rafif Arsya Pradiva 22.N4.0014` di kolom tengah dengan perataan *Center*.
   - Tarik satu garis horizontal tipis (`1,5 pt`, warna `#94A3B8`) di bawah blok header.
4. **Tata Bodi Dua Kolom Seimbang:**
   - Aktifkan panduan dua kolom masing-masing `48%` dengan jarak tengah (*gutter*) `4%`.
   - Salin teks `ABSTRAK`, `LATAR BELAKANG`, dan `METODE PENELITIAN` ke kolom kiri; salin teks `HASIL DAN PEMBAHASAN` serta `KESIMPULAN` ke kolom kanan.
   - Atur seluruh tajuk bagian dengan *Bold ALL CAPS Center* (`26-30 pt`) dan isi paragraf dengan perataan **Justify** (`19-21 pt`, *line-height* `1,38`).
5. **Pasang Tiga Tangkapan Layar (`Gambar 1, 2, 3`):**
   - Potong bilah alamat Chrome pada ketiga gambar terlebih dahulu.
   - Tempatkan `Gambar 1` tepat selebar kolom kanan (`~260 mm`), lalu sejajarkan `Gambar 2` dan `Gambar 3` berdampingan di bawahnya dengan jarak pemisah sekitar `8-10 mm`. Beri garis tepi `1 pt` (`#CBD5E1`) agar batas gambar tetap tegas di atas latar biru pucat.
6. **Sejajarkan Tepi Bawah & Ekspor Cetak:**
   - Geser jarak antarbagian tipis-tipis (`1-2 mm`) sampai baris terakhir `METODE PENELITIAN` dan baris terakhir `KESIMPULAN` berhenti di ketinggian yang seimbang.
   - Ekspor ke format **PDF Print** dengan profil warna **CMYK** resolusi tinggi (tanpa *compress* gambar agar antarmuka aplikasi tetap terbaca tajam).
