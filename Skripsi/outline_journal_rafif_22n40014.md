# OUTLINE DAN BEDAH LENGKAP NASKAH JURNAL INGGRIS
**Judul Artikel Jurnal:** BOARDING HOUSE MANAGEMENT INFORMATION SYSTEM INTEGRATED WITH PAYMENT GATEWAY AT ASRI BOARDING HOUSE  
**Penulis 1:** Rafif Arsya Pradiva (`22.N4.0014` / `rafif.arsya.pradiva@gmail.com`)  
**Penulis 2:** Andre Kurniawan Pamudji (`andre.kurniawan@unika.ac.id`)  
**Penulis 3:** Erdhi Widyarto Nugroho (`erdhi@unika.ac.id`)  
**Afiliasi:** Department of Information Systems, Faculty of Computer Science, Soegijapranata Catholic University, Semarang, Indonesia  
**Berkas Acuan Utama:** `Skripsi/journal_inggris_Rafif_Arsya_Pradiva_22.N4.0014.md`  

---

## TAHAP 1: PENGATURAN HALAMAN DAN TATA LETAK UMUM (*PAGE SETUP*)
Bagian pembuka naskah menetapkan aturan tata letak cetak standar jurnal ilmiah kampus:
1. **Ukuran Kertas:** A-4 (*portrait*, $21{,}0\text{ cm} \times 29{,}7\text{ cm}$).
2. **Batas Tepi (*Margin*):** $2{,}5\text{ cm}$ rata di sisi atas, bawah, kiri, dan kanan.
3. **Format Kolom Isi:** Dua kolom (*two columns*) dengan jarak antarkolom $0{,}5\text{ cm}$.
4. **Tipografi dan Spasi Utama:** Huruf *Times New Roman* ukuran `12 pt Regular` berspasi tunggal (`1.0`).
5. **Spesifikasi Ukuran Huruf Khusus:**
   * `16 pt Bold`: Judul utama artikel.
   * `12 pt Bold & Italic`: Subjudul *Abstract* dan label *Keywords*.
   * `12 pt Bold`: Isi paragraf abstrak beserta judul bab dan sub-bab.
   * `12 pt Regular`: Baris nama penulis, afiliasi, surel, daftar kata kunci, dan paragraf isi artikel.
   * `10 pt Bold`: Penomoran gambar (`Figure 1.` sampai `Figure 5.`).
   * `10 pt Regular`: Teks keterangan gambar berhuruf kapital rata tengah (*centered in Caps*) serta isi daftar pustaka (*References*).
   * `10 pt Italic`: Nama jurnal dan judul buku pada daftar pustaka berformat IEEE.
6. **Batas Panjang Naskah:** Maksimal **8 halaman** cetak A-4 dua kolom. Naskah terbaru memuat **5 kelompok *Figure* utama**, **0 tabel**, dan **2.615 kata total** dari seluruh teks tampil (38 kata kepala artikel, 171 kata bagian abstrak dan kata kunci, 393 kata Bab I, 311 kata Bab II termasuk keterangan `Figure 1`, 815 kata Bab III termasuk keterangan `Figure 2` sampai `Figure 5`, 176 kata Bab IV, 65 kata ucapan terima kasih, serta 646 kata daftar pustaka).

---

## TAHAP 2: BAGIAN KEPALA ARTIKEL (*HEADER*)
Seluruh elemen kepala artikel ditempatkan rata tengah (*center*) di bagian atas halaman pertama sebelum masuk ke area dua kolom:
1. **Judul Artikel (`16 pt Bold`):**  
   `BOARDING HOUSE MANAGEMENT INFORMATION SYSTEM INTEGRATED WITH PAYMENT GATEWAY AT ASRI BOARDING HOUSE`
2. **Baris Penulis (`12 pt Regular` dengan Superskrip):**  
   `¹Rafif Arsya Pradiva, ²Andre Kurniawan Pamudji, ³Erdhi Widyarto Nugroho`
3. **Baris Afiliasi Bahasa Inggris (2 Baris, `12 pt Regular`):**  
   * Baris 1: `Department of Information Systems, Faculty of Computer Science`
   * Baris 2: `Soegijapranata Catholic University, Semarang, Indonesia`
4. **Baris Alamat Surel (`12 pt Regular` dengan Superskrip):**  
   `¹rafif.arsya.pradiva@gmail.com, ²andre.kurniawan@unika.ac.id, ³erdhi@unika.ac.id`

---

## TAHAP 3: ABSTRAK DAN KATA KUNCI (*ABSTRACT & KEYWORDS*)
Intisari naskah ditulis dalam satu paragraf rata kanan-kiri (*justify*) sepanjang **156 kata** (`7 kalimat`, aman di bawah batas 200 kata), tanpa sitasi, tanpa nama individu, dan tanpa tanda pisah panjang:
1. **Judul Abstrak (`12 pt Bold Italic`):** `Abstract`
2. **Alur Isi Abstrak (`12 pt Bold`, 7 Kalimat):**
   * *Kalimat 1 sampai 2 (Masalah Lapangan):* Penjaga kost (*the caretaker*) di Asri Boarding House mencatat 32 kamar secara manual. Karena staf melayani tanya jawab lewat percakapan pribadi dan menulis pembayaran di buku kertas, permintaan serentak kerap memicu pemesanan ganda (*double-book*) pada satu unit, pengingat sewa tanggal 10 sering terlewat, dan rekonsiliasi kas akhir bulan memakan waktu hingga empat hari.
   * *Kalimat 3 (Pendekatan & Tujuan Riset):* Studi *Research and Development* (R&D) ini membangun sistem informasi manajemen berbasis web melalui model *Waterfall*.
   * *Kalimat 4 sampai 5 (Arsitektur & Otomasi):* Laravel dan basis data MySQL bentuk Normal Ketiga (3NF) menjalankan penguncian baris pesimis (*pessimistic row locking*) agar dua peminat tidak dapat memesan unit yang sama sekaligus. Pembayaran non-tunai lunas otomatis di Midtrans Snap begitu *webhook* SHA-512 lolos verifikasi; *cron job* terjadwal di *server* juga menerbitkan tagihan bulanan, menambahkan denda keterlambatan lintas bulan, serta mengirim pemberitahuan WhatsApp dan surel SMTP.
   * *Kalimat 6 sampai 7 (Hasil Pengujian & Uji Pakai):* Seluruh 60 skenario uji *black-box* di enam modul dinyatakan lulus. Pada uji pakai langsung (*live trials*), penjaga kost memberi skor `9.5/10` sebab tutup buku akhir bulan tuntas dalam hitungan detik tanpa selisih kas, sedangkan lima mahasiswa penghuni mencatat rata-rata `8.0/10` untuk pembayaran *Virtual Account* dan unduhan kuitansi PDF.
3. **Kata Kunci (`12 pt Regular` dengan label `12 pt Bold Italic`, 5 Frasa Urut Abjad):**  
   *Keywords:* `Asri Boarding House, boarding house management system, Laravel, Midtrans payment gateway, pessimistic locking`

---

## TAHAP 4: BAB I. INTRODUCTION (*PENDAHULUAN*)
Bab pembuka terdiri atas **5 paragraf (16 kalimat, 393 kata termasuk subjudul)** dengan **13 sitasi berurutan `[1]` sampai `[13]`** tanpa pengulangan mundur dan tanpa nama orang. Struktur kalimatnya bervariasi untuk menjaga ritme baca: beberapa kalimat pendek berdiri sendiri tanpa sitasi sebagai pembuka gagasan, sementara Kalimat 10 merangkai sitasi `[8]` dan `[9]` serta Kalimat 14 menggabungkan `[12]` dan `[13]`.

* **Paragraf 1 (3 kalimat, 83 kata, Sitasi `[1]` sampai `[2]`):**
  * *Kalimat 1 (`[1]`):* Setiap tahun ajaran baru mendatangkan gelombang mahasiswa luar kota ke kawasan kampus Tembalang di Semarang, tetapi pemasaran dari mulut ke mulut masih memaksa peminat mengecek kamar kosong secara langsung di lokasi.
  * *Kalimat 2 (tanpa sitasi):* Asri Boarding House di Jalan Maera Sari No. 1 menghadapi kendala serupa pada 32 kamar tipe VIP, Deluxe, dan Standard miliknya.
  * *Kalimat 3 (`[2]`):* Penjaga kost mencatat hunian di buku besar kertas sembari membalas pertanyaan lewat obrolan pribadi, sehingga lambatnya pembaruan status sering memicu *double booking* saat dua peminat meminta kamar yang sama bersamaan.
* **Paragraf 2 (3 kalimat, 73 kata, Sitasi `[3]` sampai `[4]`):**
  * *Kalimat 4 (tanpa sitasi):* Penagihan sewa bulanan menambah pekerjaan manual pengelola.
  * *Kalimat 5 (`[3]`):* Selepas jatuh tempo tanggal 10, penjaga kost memeriksa kamar yang belum lunas secara manual, menghitung denda keterlambatan lintas bulan sebesar 5%, dan mengirim pesan ke tiap penghuni satu per satu, akibatnya pengingat jatuh tempo kerap terlewat dan tunggakan menumpuk.
  * *Kalimat 6 (`[4]`):* Tutup buku akhir bulan lalu menyita waktu tiga sampai empat hari karena staf mencocokkan catatan tunai, tangkapan layar transfer dari obrolan, serta nota pengeluaran kertas secara manual yang rawan menimbulkan salah hitung di buku kas.
* **Paragraf 3 (4 kalimat, 85 kata, Sitasi `[5]` sampai `[9]`):**
  * *Kalimat 7 (`[5]`):* Satu Sistem Informasi Manajemen (SIM) menyatukan inventaris kamar, kontrak sewa, dan buku kas ke dalam satu basis data.
  * *Kalimat 8 (`[6]`):* Pemilik properti selanjutnya dapat memasarkan unit kosong langsung kepada calon mahasiswa lewat portal web *Business-to-Consumer* (B2C) tanpa membayar komisi ke perantara pihak ketiga.
  * *Kalimat 9 (`[7]`):* Tata letak *Neo-Brutalism* berkontras tinggi memangkas keruwetan visual sewaktu *checkout*.
  * *Kalimat 10 (`[8]`, `[9]`):* Pada lapisan kode, pola *Model-View-Controller* (MVC) Laravel memisahkan aturan bisnis dari tampilan halaman `[8]`, dan skema MySQL InnoDB bentuk Normal Ketiga (3NF) mencegah duplikasi baris sekaligus menjaga riwayat tarif sewa `[9]`.
* **Paragraf 4 (4 kalimat, 80 kata, Sitasi `[10]` sampai `[13]`):**
  * *Kalimat 11 (tanpa sitasi):* Situs web indekos kebanyakan hanya berhenti sebatas katalog daring.
  * *Kalimat 12 (`[10]`):* Karena penghuni di situs semacam itu masih mengunggah bukti tangkapan layar transfer untuk verifikasi manual, integrasi *payment gateway* Midtrans dengan pemeriksaan *webhook* SHA-512 dibutuhkan agar transaksi *Virtual Account* dan QRIS lunas secara otomatis.
  * *Kalimat 13 (`[11]`):* *Cron job* di *server* menerbitkan tagihan bulanan setiap tanggal 1 serta memeriksa denda keterlambatan secara idempoten selepas tanggal 10.
  * *Kalimat 14 (`[12]`, `[13]`):* Penghuni menerima peringatan WhatsApp Fonnte `[12]` dan kuitansi resmi melalui surel SMTP `[13]` segera setelah tagihan terbit atau lunas.
* **Paragraf 5 (2 kalimat, 70 kata, Tanpa Sitasi):**
  * *Kalimat 15 sampai 16 (tanpa sitasi):* Berangkat dari masalah operasional tersebut, studi ini menetapkan tiga tujuan: (1) merancang arsitektur sistem informasi manajemen indekos berbasis web untuk Asri Boarding House, (2) membangun platform di Laravel dan MySQL dengan penguncian baris pesimis, *webhook* pembayaran Midtrans, otomasi penagihan berbasis *cron*, serta peringatan WhatsApp dan surel, dan (3) mengevaluasi keandalan fungsional serta penerimaan pengguna melalui pengujian *black-box* dan wawancara lapangan bersama penjaga kost dan mahasiswa penghuni.

---

## TAHAP 5: BAB II. METHOD (*METODE PENELITIAN*)
Bab kedua disusun dalam **3 paragraf naratif (15 kalimat, 311 kata termasuk subjudul dan keterangan `Figure 1`)** serta **1 bagan alir (`Figure 1`)** tanpa tabel. Urutan rujukan melanjutkan Bab I secara ketat dari **`[14]` sampai `[20]`** tanpa nama individu:

* **Paragraf 1 (5 kalimat, 94 kata, Sitasi `[14]` sampai `[15]`):**
  * *Kalimat 1 (tanpa sitasi):* Kerja lapangan berlangsung di Asri Boarding House, Tembalang, Semarang.
  * *Kalimat 2 (`[14]`):* Penelitian ini menerapkan metode rekayasa perangkat lunak *Research and Development* (R&D) pada studi kasus tunggal 32 kamar agar hambatan buku besar manual digantikan oleh aplikasi web terverifikasi untuk pembukuan harian.
  * *Kalimat 3 sampai 4 (tanpa sitasi):* Wawancara tatap muka bersama penjaga kost dan mahasiswa penghuni aktif memasok data primer pada tahap pengumpulan kebutuhan awal maupun evaluasi pasca-peluncuran; setiap sesi direkam secara audio lalu ditranskripsikan kata demi kata (*verbatim*).
  * *Kalimat 5 (`[15]`):* Buku kas fisik, slip transfer bank, catatan pengeluaran operasional, lembar tarif kamar, manual resmi API, dan studi terpublikasi menjadi rujukan sekunder.
* **Paragraf 2 & `Figure 1` (3 kalimat, 60 kata, Sitasi `[16]`):**
  * *Kalimat 6 sampai 7 (tanpa sitasi):* `Figure 1` memetakan siklus hidup perangkat lunak *Waterfall*; kebutuhan sistem tidak bergeser di tengah pembangunan karena Asri Boarding House sudah memiliki aturan pemesanan dan tanggal tagihan bulanan yang tetap.
  * *Kalimat 8 (`[16]`):* Pekerjaan rekayasa pun berjalan berurutan melewati enam fase *Waterfall* (*requirement gathering and analysis*, *design*, *implementation*, *integration and testing*, *verification*, serta *operation and maintenance*), di mana setiap fase harus lolos tinjauan sebelum fase berikutnya dimulai.
  * **`Figure 1` (`Figure 1. WATERFALL SYSTEM DEVELOPMENT MODEL`):**  
    Berkas `images/metode_sdlc_waterfall.png` dengan lebar `width="95%"` (`alt="Waterfall System Development Model"`), menampilkan diagram enam tahapan SDLC *Waterfall* berbahasa Inggris.
* **Paragraf 3 (7 kalimat, 149 kata, Sitasi `[17]` sampai `[20]`):**
  * *Kalimat 9 (`[17]`):* Pada tahap desain, diagram *Unified Modeling Language* (UML) menetapkan aturan akses bagi calon penyewa, penyewa aktif, dan administrator; skema *Entity Relationship Diagram* (ERD) MySQL InnoDB bentuk Normal Ketiga (3NF) turut memisahkan tabel inti sewa dan pembayaran dari log operasional.
  * *Kalimat 10 (tanpa sitasi):* Rute dan *controller* sisi *backend* berjalan di atas Laravel MVC.
  * *Kalimat 11 (`[18]`):* Di dalam MySQL, transaksi ACID menerapkan penguncian baris pesimis `lockForUpdate()` supaya pengajuan *checkout* serentak tidak dapat mengklaim kamar yang sama.
  * *Kalimat 12 (`[19]`):* Midtrans Snap menerbitkan token *checkout* 24 jam dan memeriksa tanda tangan *webhook* SHA-512 yang masuk lewat `hash_equals()`, sedangkan *cron job* di *server* menangani tagihan tanggal 1, pemeriksaan denda pasca-tanggal 10, pemberitahuan WhatsApp Fonnte, serta antrean surel SMTP.
  * *Kalimat 13 sampai 14 (tanpa sitasi):* Enam puluh skenario uji *black-box* menguji keenam modul (*authentication and profile*, *public catalog*, *five-step booking*, *tenant portal*, *admin operations*, dan *background schedulers*), kemudian diikuti uji pakai langsung di *server* produksi.
  * *Kalimat 15 (`[20]`):* Penjaga kost dan lima mahasiswa penghuni aktif menjalankan transaksi nyata pada situs sebelum memberikan penilaian lewat wawancara semi-terstruktur berskala 10 poin.

---

## TAHAP 6: BAB III. RESULTS AND DISCUSSION (*HASIL DAN PEMBAHASAN*)
Bab ketiga memuat **8 paragraf naratif (815 kata termasuk judul bab, sub-bab, dan keterangan gambar)** beserta **4 kelompok gambar inti (`Figure 2` sampai `Figure 5`)**, tanpa tabel UAT, tanpa sitasi, dan tanpa penyebutan nama pribadi.

### Sub-bab A. RESULT (5 Paragraf, 24 Kalimat, serta `Figure 2` sampai `Figure 5`)
1. **Paragraf 1 & `Figure 2` (6 kalimat, 124 kata):**  
   Pengguna masuk melalui tiga tingkat hak akses: calon penyewa menelusuri kamar, mengobrol langsung (*live chat*) dengan staf, mengunggah kartu identitas, dan mengirim pemesanan lima langkah; penyewa aktif mengecek tagihan bulanan, mengunduh kuitansi, serta melapor kerusakan; sedangkan administrator mengelola stok kamar, kontrak sewa, biaya harian, dan buku kas. `Figure 2` menampilkan enam tabel transaksi inti MySQL InnoDB 3NF (`users`, `kamar`, `penyewa`, `reservasi`, `tagihan`, dan `pembayaran`). Di dalam tabel `penyewa`, tiap kontrak menyimpan *snapshot* tetap berisi tarif sewa yang disepakati, tanggal masuk, masa berakhir sewa, dan kontak orang tua agar kenaikan harga kamar di masa depan tidak mengubah kontrak yang sedang berjalan. Delapan tabel pendukung menampung keluhan perawatan (`keluhan`), biaya operasional (`pengeluaran`), log notifikasi (`log_notifikasi`), obrolan langsung (`guest_chat_threads` dan `chat_messages`), ulasan penghuni (`customer_reviews`), tata tertib kost (`peraturan`), serta tanya jawab umum (`faqs`).
   * **Spesifikasi `Figure 2`:** Berkas `images/gambar_4_2_a_erd_modul_inti_en.png` (`width="95%"`, `alt="Core Residency and Financial 3NF Entity Relationship Diagram"`), keterangan `Figure 2. CORE RESIDENCY AND FINANCIAL 3NF ENTITY RELATIONSHIP DIAGRAM`.
2. **Paragraf 2 & `Figure 3` (5 kalimat, 103 kata):**  
   Pada alur `Figure 3`, seorang peminat memilih unit kosong lalu mengunggah KTP 16 digit NIK untuk mengirim pemesanan. Pengajuan serentak tidak bisa memesan unit yang sama. Laravel membungkus setiap pengajuan ke dalam transaksi basis data ACID dengan penguncian baris `lockForUpdate()` serta pemeriksaan tumpang-tindih tanggal (*date-overlap checks*). Begitu peminat membayar, Midtrans mengirimkan *webhook* HTTP bertanda tangan SHA-512 yang diperiksa *server* menggunakan `hash_equals()`; *callback* yang sah langsung menandai tagihan lunas, mengaktifkan akun penyewa, dan mengirim kuitansi digital melalui WhatsApp dan surel. Tagihan bulanan berikutnya keluar lewat *cron job* setiap tanggal 1, disusul pemeriksaan idempoten `withoutOverlapping()` yang menambahkan denda keterlambatan selepas tanggal 10.
   * **Spesifikasi `Figure 3`:** Berkas `images/gambar_4_6_flowchart_sistem_en.png` (`width="95%"`, `alt="End-to-End Reservation, Payment Gateway Webhook, and Billing Flowchart"`), keterangan `Figure 3. END-TO-END RESERVATION, PAYMENT GATEWAY WEBHOOK, AND BILLING FLOWCHART`.
3. **Paragraf 3 & `Figure 4` (4 kalimat, 77 kata):**  
   Calon mahasiswa menjelajahi 36 halaman responsif bergaya *Neo-Brutalism* untuk seluruh 32 kamar VIP, Deluxe, dan Standard yang dilengkapi penyaring ketersediaan langsung, ukuran ruang (*dimensions*), fasilitas, dan tarif bulanan. Di dalam layar pemesanan (`Figure 4a`), formulir lima langkah mengumpulkan unggahan identitas, mengonfirmasi rincian lewat *live chat*, serta mencatat opsi bayar lunas (*full payment*) atau uang muka (*down payment*) sebelum membuka jendela sembul Midtrans Snap (`Figure 4b`) yang menerbitkan nomor *Virtual Account* atau kode QRIS berdurasi 24 jam.
   * **Spesifikasi `Figure 4` (2 gambar bertumpuk vertikal):**  
     `(a)` `images/gambar_4_21_pembayaran_5_tahap_reservasi_en.png` (`width="95%"`, `alt="Five-Step Reservation Workspace"`) dan `(b)` `images/gambar_4_22_payment_gateway_midtrans.png` (`width="75%"`, `alt="Midtrans Snap Virtual Account Modal"`), keterangan `Figure 4. ONLINE BOOKING WORKFLOW: (a) FIVE-STEP RESERVATION WORKSPACE; (b) MIDTRANS SNAP VIRTUAL ACCOUNT MODAL`.
4. **Paragraf 4 & `Figure 5` (5 kalimat, 102 kata):**  
   Penjaga kost memantau operasional harian melalui portal administrator pada `Figure 5`. Di dasbor utama (`Figure 5a`), penghitung langsung di seluruh 32 kamar memperlihatkan rasio hunian, pemasukan bulanan, biaya operasional, laba bersih, tagihan belum lunas, dan reservasi tertunda di samping grafik arus kas 12 bulan serta diagram donat status kamar. Buku besar PDF dan Excel dapat diekspor dalam satu klik dari halaman pembukuan (`Figure 5b`) yang mencocokkan pembayaran sewa lunas terhadap pengeluaran harian. Sementara itu, penyewa aktif mengecek riwayat tagihan mereka di portal penghuni dan mengunduh kuitansi PDF A5 horizontal berstempel *PAID* beserta ID transaksi resmi Midtrans.
   * **Spesifikasi `Figure 5` (2 gambar bertumpuk vertikal):**  
     `(a)` `images/gambar_4_26_dashboard_admin_en.png` (`width="95%"`, `alt="Live Executive Dashboard"`) dan `(b)` `images/gambar_4_33_manajemen_laporan_pemasukan_en.png` (`width="95%"`, `alt="Financial Reconciliation and Export Module"`), keterangan `Figure 5. ADMINISTRATOR PORTAL: (a) LIVE EXECUTIVE DASHBOARD; (b) FINANCIAL RECONCILIATION AND EXPORT MODULE`.
5. **Paragraf 5 (4 kalimat, 69 kata):**  
   Seluruh 60 skenario uji *black-box* lulus. Di keenam modul fungsional, penyaring kelas kamar, pemeriksaan NIK 16 digit, kunci karantina kamar pasca-*checkout*, pembuat kuitansi PDF, dan ekspor buku besar berjalan tanpa kegagalan validasi maupun transaksi. Wawancara lapangan terrekam berskala 10 poin turut mengonfirmasi sambutan positif pasca-peluncuran: penjaga kost memberi nilai `9.5/10`, sedangkan lima mahasiswa penghuni aktif mencatatkan rata-rata `8.0/10` dengan rentang skor individu dari `7.0` sampai `9.0`.

### Sub-bab B. DISCUSSION (3 Paragraf, 14 Kalimat)
* **Paragraf 1 (Dampak Operasional Pengelola, 5 kalimat, 102 kata):**  
  Wawancara pasca-peluncuran memperlihatkan bagaimana *webhook* pembayaran otomatis dan kunci basis data tingkat baris menuntaskan kemacetan kerja harian di Asri Boarding House. Selisih kas turun ke titik nol. Penjaga kost tidak perlu lagi menghabiskan tiga atau empat hari untuk mencocokkan tangkapan layar percakapan dengan catatan kertas sebab transaksi lunas langsung masuk ke ekspor PDF dan Excel instan. Aturan sewa juga menyesuaikan arus kas mahasiswa: penghuni mendapat masa tenggang dalam bulan berjalan selepas tanggal 10, dan sistem baru menambahkan denda idempoten 5% apabila saldo belum lunas menyeberang ke bulan berikutnya. Kamar yang baru dikosongkan pun tetap tersembunyi dalam status karantina pasca-*checkout* hingga staf selesai membersihkan dan memeriksa unit.
* **Paragraf 2 (Pengalaman Mahasiswa Penghuni & Usulan Pengembangan, 5 kalimat, 103 kata):**  
  Mahasiswa luar kota terbantu saat membandingkan fasilitas kamar di katalog *Neo-Brutalism* berkontras tinggi dan memesan unit dari kampung halaman. Pembayaran *Virtual Account* lewat peramban Android maupun iPhone mengakhiri kebiasaan pergi ke ATM larut malam. Orang tua juga menerima bukti bayar yang jelas karena penghuni langsung meneruskan kuitansi PDF A5 horizontal ke rumah alih-alih menyimpan slip kertas yang gampang terselip. Selama wawancara muncul lima permintaan praktis untuk pembaruan berikutnya: pengingat WhatsApp bertautan *checkout* langsung tiga hari sebelum jatuh tempo, panduan singkat pengguna, aplikasi *mobile*, penyetelan kinerja *server* pada hari puncak penagihan, dan perjanjian sewa digital.
* **Paragraf 3 (Keunggulan Teknis atas Portal Indekos Dasar, 4 kalimat, 78 kata):**  
  Situs web indekos dasar masih meminta penghuni mengunggah bukti transfer manual dan membiarkan konkurensi basis data tanpa pelindung. Pada sistem ini, transaksi ACID dengan penguncian baris `lockForUpdate()` dan karantina pasca-*checkout* menghentikan pemesanan ganda di ke-32 kamar sewaktu *checkout* bersamaan; *callback* Midtrans Snap yang diverifikasi lewat pemeriksaan SHA-512 `hash_equals()` turut memperbarui buku besar tanpa audit rekening koran manual. Rangkaian kerja tersebut mencakup seluruh masa sewa multi-bulan karena *webhook* pembayaran terverifikasi langsung terhubung ke penjadwal *cron* bulanan, peringatan WhatsApp, dan antrean surel SMTP.

---

## TAHAP 7: BAB IV. CONCLUSION (*KESIMPULAN*)
Bab keempat terdiri atas **3 paragraf kesimpulan (9 kalimat, 176 kata termasuk judul bab; tiap paragraf memuat tepat 3 kalimat dan 58 kata)** yang langsung menjawab tiga tujuan penelitian, tanpa daftar saran bernomor, tanpa sitasi, dan tanpa nama orang:

1. **Paragraf 1 (Jawaban Tujuan 1, Arsitektur & Basis Data):** Arsitektur web yang dibangun bagi Asri Boarding House menggantikan buku hunian kertas di seluruh 32 kamar. Aturan akses UML memisahkan calon penyewa, penyewa aktif, dan administrator, sementara basis data MySQL InnoDB 3NF membagi enam tabel inti sewa dan pembayaran dari delapan tabel operasional sehingga pemesanan ganda dicegah langsung di tingkat basis data.
2. **Paragraf 2 (Jawaban Tujuan 2, Implementasi & Pengujian Fungsional):** Laravel MVC memadukan transaksi ACID dan penguncian baris pesimis `lockForUpdate()` dengan *webhook* pembayaran Midtrans Snap berverifikasi SHA-512. *Cron job* di *server* menangani administrasi berulang secara otomatis: tagihan baru keluar setiap tanggal 1, denda keterlambatan lintas bulan 5% yang idempoten berjalan selepas tanggal 10, dan pemberitahuan pembayaran menjangkau penghuni lewat WhatsApp Fonnte serta antrean surel SMTP. Seluruh 60 pengujian *black-box* di enam modul lulus penuh.
3. **Paragraf 3 (Jawaban Tujuan 3, Evaluasi Lapangan):** Pada evaluasi lapangan, penjaga kost memberi skor `9.5/10` seiring turunnya waktu tutup buku akhir bulan dari empat hari menjadi ekspor instan tanpa selisih kas. Sementara itu, lima mahasiswa penghuni aktif mencatat rata-rata `8.0/10` untuk pemesanan mandiri jarak jauh, *checkout* *Virtual Account*, dan unduhan kuitansi PDF A5. Capaian ini membuktikan bahwa sistem menyelesaikan hambatan administrasi harian di Asri Boarding House.

---

## TAHAP 8: ACKNOWLEDGMENT (*UCAPAN TERIMA KASIH*)
Bagian ini diletakkan setelah kesimpulan dengan **judul tanpa nomor bab (*unnumbered heading*)** dalam **1 paragraf (3 kalimat aktif, 65 kata termasuk judul bagian)** tanpa penyebutan nama pribadi:
* Penulis mengucapkan terima kasih kepada Program Studi Sistem Informasi, Fakultas Ilmu Komputer, Universitas Katolik Soegijapranata, Semarang, yang telah membimbing dan mendukung penelitian ini. Rasa terima kasih juga disampaikan kepada pemilik properti dan penjaga kost (*the caretaker*) Asri Boarding House di Tembalang karena telah membagikan buku besar harian serta menguji platform web. Apresiasi khusus ditujukan kepada para mahasiswa penghuni aktif atas masukan jujur mereka selama uji coba lapangan.

---

## TAHAP 9: REFERENCES (*DAFTAR PUSTAKA*)
Ditempatkan di bagian paling akhir dengan **judul tanpa nomor bab (*unnumbered heading*)**, diformat dalam ukuran `10 pt Regular` dan `10 pt Italic` berstandar IEEE (**646 kata termasuk judul bagian**). Memuat **20 referensi terurut `[1]` sampai `[20]`** yang muncul tepat satu kali secara berurutan di Bab I (`[1]` sampai `[13]`) dan Bab II (`[14]` sampai `[20]`):

1. **`[1]` A. Cornellya & H. Afriyadi (2025):** Jurnal *PESHUM*, perancangan sistem informasi pemesanan pada Kost Tya berbasis web menggunakan *framework* Laravel.
2. **`[2]` C. Nizar (2021):** Jurnal *SISTEK*, rancang bangun sistem informasi sewa rumah kost (E-Kost) berbasis *website*.
3. **`[3]` E. J. Malaikosa & P. Mokola (2024):** Jurnal *JSiI*, sistem informasi monitoring rumah kos dan pembayarannya berbasis web menggunakan metode *Rapid Application Development*.
4. **`[4]` R. Sutisna & F. Aziz (2025):** Jurnal *REMIK*, perancangan sistem penyewaan alat *event* berbasis *website* dengan integrasi *payment gateway* Midtrans.
5. **`[5]` A. Syahputra, R. Wiranti, & W. Astita (2022):** Jurnal *JMASIF*, peran Sistem Informasi Manajemen organisasi dalam pengambilan keputusan.
6. **`[6]` I. A. Nurhasanah, M. Brilliant, K. Reni, & A. Mulyanto (2022):** Jurnal *JANAPATI*, analisis perancangan *E-Business* B2C (*Business to Consumer*) untuk digitalisasi pengembangan UMKM.
7. **`[7]` A. R. Setiawan, M. Asfi, A. Sevtiana, S. Pranata, & W. E. Septian (2023):** *Jurnal Teknologi Terpadu*, penerapan *design system* pada perancangan antarmuka perangkat lunak sistem akses digital.
8. **`[8]` L. Rahmawati & Sumarsono (2024):** Jurnal *JTEKSIS*, desain pengembangan *website* berarsitektur *Model View Controller* pada *framework* Laravel.
9. **`[9]` D. F. M. Dina (2024):** Jurnal *Computing Insight*, normalisasi basis data pada rancangan sistem penyewaan buku berbayar.
10. **`[10]` M. Nurhayati & R. Setiawan (2024):** *Jurnal Algoritma*, implementasi *payment gateway* Midtrans pada aplikasi toko emas berbasis web.
11. **`[11]` A. Wibowo, A. Fathimah, D. Waskito, & R. A. Aprilianto (2025):** Jurnal *Sainteknol*, implementasi *cron job* untuk pemrosesan dan transfer data otomatis di infrastruktur *cloud*.
12. **`[12]` I. M. B. Waton, A. N. Weking, & B. Deta (2025):** *Jurnal Teknik Mesin, Elektro dan Ilmu Komputer*, penerapan API WhatsApp Fonnte pada sistem pengingat jadwal bimbingan tugas akhir berbasis web.
13. **`[13]` M. A. Syahputra & R. A. Putri (2025):** Jurnal *Decode*, sistem informasi monitoring pembelajaran berbasis web dengan fitur notifikasi *email*.
14. **`[14]` M. W. Mauludy & Fathahillah (2025):** Jurnal *JIMAT*, pengembangan sistem informasi pengarsipan *file* menggunakan metode *Research and Development* (R&D).
15. **`[15]` Sugiyono (2022):** Buku *Metode Penelitian Kuantitatif, Kualitatif, dan R&D* (Bandung: Alfabeta).
16. **`[16]` E. Rahmi, E. Yumami, & N. Hidayasari (2023):** Jurnal *REMIK*, kajian *Systematic Literature Review* atas metode pengembangan sistem informasi berbasis *website*.
17. **`[17]` Y. Adisa & M. I. P. Nasution (2023):** Jurnal *MASIP*, konsep dan peran sistem manajemen basis data relasional pada Sistem Informasi Manajemen.
18. **`[18]` J. V Dachi & J. Suhada (2025):** Jurnal *Router*, implementasi basis data sederhana menggunakan MySQL/PostgreSQL sebagai rujukan fondasi transaksi relasional.
19. **`[19]` R. C. Buwono, R. Kartadie, & M. K. Ramadhan (2024):** Jurnal *INFOTECH*, perancangan dan implementasi sistem *payment gateway* Midtrans untuk UMKM Batik Lurik.
20. **`[20]` J. K. Aditya & A. T. Hidayat (2025):** Jurnal *JUSIM*, sistem informasi manajemen kos berbasis web menggunakan metode pengujian *Blackbox* pada Kos Putri Humba.
