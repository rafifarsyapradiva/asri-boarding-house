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
|  Paragraf ringkas tentang kendala      Paragraf pembagian ruang kerja pengguna,  |
|  32 kamar manual, tujuan riset,        alur pemesanan 5 tahap (DP 30% / penuh),  |
|  Laravel & MySQL, pendekatan R&D       pembayaran Virtual Account Midtrans,      |
|  model Waterfall, penguncian kamar     kuitansi PDF lunas, tagihan otomatis tgl  |
|  otomatis, verifikasi Midtrans,        1 via WhatsApp & email, denda tetap 5%,   |
|  kuitansi PDF, pengingat otomatis,     kelolosan 60 uji fungsi (100%), skor      |
|  60 uji fungsi (100%), skor penjaga    Bapak Asep 9,5/10, & rata-rata 8,0/10.    |
|  kost 9,5/10, serta rata-rata 8,0/10                                             |
|  dari lima penghuni mahasiswa.         +--------------------------------------+  |
|                                        |  Gambar 1 (Dasbor Pengelola & Kas)   |  |
|           LATAR BELAKANG               |  Lebar penuh kolom kanan (rasio 2:1) |  |
|  Paragraf arus perantau di Tembalang,  +--------------------------------------+  |
|  pengelolaan 32 kamar (VIP, Deluxe,     Gambar 1 Tampilan dasbor pengelola dan   |
|  Standar) memakai buku besar & WA               laporan kas                      |
|  pribadi, risiko pesanan ganda,                                                  |
|  tagih tanggal 10 yang telat, cek      +-----------------+  +-----------------+  |
|  transfer 3-4 hari, serta perlunya     | Gambar 2        |  | Gambar 3        |  |
|  aplikasi web terpusat.                | (Alur 5 Tahap)  |  | (Halaman Tagih) |  |
|                                        +-----------------+  +-----------------+  |
|         METODE PENELITIAN               Gambar 2 Tampilan    Gambar 3 Tampilan   |
|  Paragraf studi kasus di Asri           alur pemesanan       halaman tagihan     |
|  Boarding House Jl. Maera Sari No. 1,   kamar lima tahap     bulanan penghuni    |
|  wawancara penjaga kost 20 th & lima                                             |
|  penghuni, observasi, telaah arsip                 KESIMPULAN                    |
|  catatan sewa, tahapan Waterfall       Paragraf rangkuman tertibnya pengelolaan  |
|  (analisis, alur 3 peran, basis data   32 kamar tanpa pesanan ganda, kelolosan   |
|  MySQL, pembuatan web Laravel,         60 uji fungsi, skor lapangan 9,5/10 dan   |
|  60 skenario Black Box Testing, dan    8,0/10, serta saran lanjutan (WA H-3,     |
|  uji coba pemakaian skala 1-10).       panduan awal, kontrak sewa digital).      |
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
> Pengelolaan 32 kamar di Asri Boarding House Tembalang selama ini masih memakai buku tulis dan pesan WhatsApp pribadi. Cara manual tersebut kerap memicu pemesanan ganda saat dua calon penghuni memilih kamar yang sama. Jadwal tagih setiap tanggal 10 juga sering terlewat, sementara mencocokkan bukti transfer akhir bulan menyita waktu tiga sampai empat hari. Penelitian ini merancang dan membangun sistem manajemen kost berbasis web yang terhubung dengan layanan pembayaran daring agar pemesanan kamar hingga pencatatan kas berjalan tertib. Pengembangan aplikasi memakai kerangka kerja Laravel dan basis data MySQL dengan pendekatan penelitian pengembangan (R&D) model *Waterfall*. Sistem dibekali penguncian data kamar otomatis untuk mencegah bentrok jadwal, verifikasi pembayaran instan lewat Midtrans tanpa perlu kirim foto resi, penerbitan kuitansi PDF, serta pengingat tagihan otomatis melalui WhatsApp dan *email*. Seluruh 60 skenario pengujian fungsi (*Black Box Testing*) berjalan lancar 100%. Hasil evaluasi lapangan mencatat skor kepuasan 9,5 dari 10 dari penjaga kost karena rekap kas selesai dalam hitungan detik tanpa selisih uang, sementara lima mahasiswa penghuni memberi nilai rata-rata 8,0 dari 10.

#### `LATAR BELAKANG`
> Setiap awal tahun kuliah, kawasan Tembalang, Semarang, selalu ramai oleh mahasiswa perantau yang mencari tempat tinggal. Di Asri Boarding House, pengelola mengurus 32 kamar tipe VIP, Deluxe, dan Standar dengan buku besar serta WhatsApp pribadi. Akibatnya, peminat dari luar kota sulit mengetahui sisa kamar kosong secara cepat. Ketika dua orang memesan kamar yang sama dalam waktu berdekatan, jadwal sewa pun mudah bentrok. Di sisi keuangan, penjaga kost harus menagih penghuni satu per satu setiap tanggal 10 dan mengecek ratusan bukti transfer selama tiga sampai empat hari di akhir bulan. Melihat kendala tersebut, dibutuhkan aplikasi web terpusat agar pemesanan kamar, penagihan bulanan, serta pencatatan kas berjalan praktis dan bebas selisih.

#### `METODE PENELITIAN`
> Penelitian studi kasus ini dilakukan di Asri Boarding House, Jalan Maera Sari Nomor 1, Tembalang, Semarang. Data dikumpulkan lewat wawancara langsung bersama penjaga kost berpengalaman 20 tahun dan lima mahasiswa penghuni aktif, ditambah pengamatan aktivitas harian serta penelaahan buku catatan sewa, daftar tarif, dan nota pengeluaran. Pembangunan web mengikuti metode *Waterfall* melalui tahapan analisis kebutuhan, perancangan alur tiga peran pengguna (calon penyewa, penghuni aktif, dan pengelola) beserta struktur basis data, pembuatan aplikasi memakai Laravel dan MySQL yang terhubung ke pembayaran Midtrans serta pesan otomatis, pengujian fungsi sistem, hingga evaluasi lapangan. Kelayakan fitur diuji melalui 60 skenario *Black Box Testing*, lalu dilanjutkan uji coba langsung bersama pengelola dan penghuni dengan penilaian skala 1 sampai 10.

---

### KOLOM KANAN

#### `HASIL DAN PEMBAHASAN`
> Aplikasi web yang dihasilkan membagi ruang akses secara jelas untuk pengunjung umum, calon penyewa, penghuni aktif, dan pengelola. Peminat dari luar kota bisa memeriksa ketersediaan 32 kamar, mengisi identitas KTP 16 digit, lalu menyelesaikan pemesanan lima tahap dengan pilihan uang muka 30% atau bayar penuh melalui *Virtual Account* Midtrans. Begitu pembayaran diterima, sistem mengaktifkan hunian dan menerbitkan kuitansi PDF berstempel lunas secara otomatis. Untuk penghuni aktif, tagihan terbit otomatis setiap tanggal 1 disertai pengingat WhatsApp dan *email*, masa kelonggaran sampai akhir bulan berjalan setelah jatuh tempo tanggal 10, serta denda tetap 5% satu kali bila menunggak ke bulan berikutnya. Seluruh 60 skenario uji fungsi lolos 100%, sementara uji coba lapangan mencatat skor 9,5/10 dari penjaga kost (Bapak Asep) karena tutup buku menyusut ke hitungan detik tanpa selisih kas, diikuti nilai rata-rata 8,0/10 dari lima mahasiswa berbagai kampus.

- **Penempatan Visual Hasil (`Gambar 1`, `Gambar 2`, `Gambar 3`):**
  1. **Baris Atas (Lebar Penuh Kolom Kanan):** Pasang `gambar_4_26_dashboard_admin.png` dan beri keterangan rata tengah di bawahnya: `Gambar 1 Tampilan dasbor pengelola dan laporan kas`
  2. **Baris Bawah Kiri (48% Kolom Kanan):** Pasang `antarmuka_stepper_reservasi_penyewa.png` dan beri keterangan rata tengah di bawahnya: `Gambar 2 Tampilan alur pemesanan kamar lima tahap`
  3. **Baris Bawah Kanan (48% Kolom Kanan):** Pasang `gambar_4_38_tagihan_penyewa.png` dan beri keterangan rata tengah di bawahnya: `Gambar 3 Tampilan halaman tagihan bulanan penghuni`

#### `KESIMPULAN`
> Penerapan sistem manajemen kost berbasis Laravel, MySQL, dan gerbang pembayaran Midtrans berhasil menertibkan pengelolaan 32 kamar di Asri Boarding House. Fitur penguncian jadwal otomatis mampu mencegah pesanan ganda, sementara pembayaran daring dan kuitansi PDF membuat pencatatan uang sewa jauh lebih transparan. Pengiriman tagihan rutin tanggal 1 lewat WhatsApp serta *email* juga membantu penghuni membayar tepat waktu tanpa penagihan manual satu per satu. Seluruh 60 skenario pengujian fungsi lulus 100%, diikuti penilaian positif dari penjaga kost (9,5 dari 10) dan lima penghuni aktif (rata-rata 8,0 dari 10). Pengembangan berikutnya dapat menambahkan pengingat WhatsApp H-3 sebelum jatuh tempo beserta tautan bayar langsung, panduan awal pemakaian bagi penghuni baru, serta fitur kontrak sewa digital.

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
