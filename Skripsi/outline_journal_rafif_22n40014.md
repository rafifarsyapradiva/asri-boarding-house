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
   * `12 pt Regular`: Nama penulis, afiliasi, surel, paragraf isi artikel, serta isi sel di dalam tabel.
   * `10 pt Bold`: Label penomoran tabel (`Table 1.`, `Table 2.`) dan penomoran gambar (`Figure 1.`, `Figure 2.`, dst.).
   * `10 pt Regular`: Judul keterangan tabel dalam huruf kapital (*in Caps*), judul keterangan gambar di bawah gambar (*centered in Caps*), dan daftar pustaka (*References*).
   * `10 pt Italic`: Nama jurnal dan judul buku pada daftar pustaka berformat IEEE.
6. **Batas Panjang Naskah:** Maksimal 8 sampai 10 halaman (mengakomodasi integrasi 7 kelompok *figure* visual utama dengan panjang aktual $\approx 8{,}5 - 9{,}5$ halaman cetak A-4 dua kolom atau $\approx 4.467$ kata total).

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
Bagian intisari ditulis dalam satu paragraf utuh sepanjang **179 kata** (di bawah batas maksimal 200 kata) dengan teks rata kanan-kiri (*justify*):
1. **Judul Abstrak (`12 pt Bold Italic`):** `Abstract`
2. **Isi Abstrak (`12 pt Bold`, 5 Komponen Utuh):**
   * *Kalimat 1 dan 2 (Introduction / Masalah Lapangan):* Pengelolaan 32 kamar sewa di Asri Boarding House selama ini bergantung pada buku besar kertas dan pesan singkat manual. Alur konvensional tersebut sering memicu benturan pemesanan kamar, keterlambatan penagihan sewa bulanan, serta selisih kas saat pemeriksaan bukti transfer bank yang memakan waktu berhari-hari.
   * *Kalimat 3 (Research Objectives / Tujuan Penelitian):* Penelitian ini bertujuan merancang dan membangun sistem informasi manajemen indekos berbasis web terintegrasi yang mengotomatisasi verifikasi pembayaran dan pengingat tagihan.
   * *Kalimat 4 dan 5 (Methods / Metode Pengembangan dan Arsitektur):* Pengembangan sistem mengikuti model *Waterfall* menggunakan *framework* Laravel dan basis data MySQL. Untuk mencegah alokasi kamar ganda saat transaksi bersamaan, lapisan basis data menerapkan *pessimistic row-level locking*, sedangkan transaksi keuangan dan pengingat tagihan dihubungkan ke gerbang pembayaran Midtrans Snap, WhatsApp API, dan layanan surel.
   * *Kalimat 6, 7, dan 8 (Results / Hasil Pengujian dan Evaluasi):* Pengujian fungsional pada 60 skenario *black-box* membuktikan seluruh modul berjalan stabil tanpa kegagalan transaksi maupun validasi. Evaluasi operasional bersama pengelola indekos menunjukkan waktu tutup buku bulanan turun dari beberapa hari menjadi laporan instan tanpa selisih kas, sementara wawancara bersama mahasiswa penghuni mengonfirmasi kepuasan yang tinggi.
   * *Kalimat 9 (Conclusions / Simpulan Manfaat):* Sistem ini menghapus *double booking*, menyederhanakan pembayaran sewa melalui *Virtual Account*, dan menyajikan kuitansi digital yang transparan bagi pengelola maupun penghuni.
3. **Kata Kunci (`12 pt Regular`, 5 Frasa Urut Abjad):**  
   *Keywords:* `Asri Boarding House, boarding house management system, Laravel, Midtrans payment gateway, pessimistic locking`

---

## TAHAP 4: BAB I. INTRODUCTION (*PENDAHULUAN*)
Bab pertama terdiri atas **5 paragraf** yang menguraikan latar belakang masalah, kajian pustaka (*state of the art*) dengan sitasi berurutan `[1]` sampai `[15]`, serta tujuan penelitian:

* **Paragraf 1 (Kebutuhan Hunian di Tembalang dan Risiko *Double Booking* di Asri Boarding House):**  
  Kawasan kampus Tembalang di Semarang menarik ribuan mahasiswa perantau setiap tahun akademik, namun cara promosi lama menyulitkan calon penyewa mengecek kamar kosong tanpa datang langsung ke lokasi `[1]`. Asri Boarding House di Jalan Maera Sari No. 1 Tembalang menghadapi kendala serupa pada 32 unit kamarnya yang terbagi atas tipe VIP, Deluxe, dan Standar. Pencatatan kamar yang masih ditulis manual di buku besar fisik membuat pengelola kesulitan memperbarui status keterisian dengan cepat, sehingga benturan pesanan ganda (*double booking*) kerap terjadi ketika beberapa peminat memesan kamar yang sama dalam waktu berdekatan `[2]`.
* **Paragraf 2 (Kendala Penagihan Tanggal 10 dan Selisih Kas Tutup Buku Bulanan):**  
  Hambatan operasional juga mengganggu penagihan sewa bulanan karena pengingat jatuh tempo setiap tanggal 10 dan perhitungan denda masih dikirim satu per satu lewat pesan singkat pribadi. Rutinitas manual ini membuat jadwal penagihan kerap meleset dan tunggakan sewa menumpuk `[3]`. Di sisi pembukuan keuangan, pemeriksaan pembayaran yang masih mengandalkan tangkapan layar transfer bank dan kuitansi kertas memaksa pengelola menghabiskan waktu berhari-hari saat tutup buku akhir bulan untuk mencocokkan nota pengeluaran operasional dengan uang masuk, sehingga rentan menimbulkan selisih kas `[4]`.
* **Paragraf 3 (Landasan Teori SIM, B2C, UI/UX *Neo-Brutalism*, Laravel MVC, dan MySQL 3NF):**  
  Kajian literatur menunjukkan bahwa Sistem Informasi Manajemen (SIM) mampu memusatkan inventaris kamar, biodata penghuni, dan laporan kas untuk mendukung pengawasan manajerial `[5]`. Penerapan model web *Business-to-Consumer* (B2C) menghubungkan pemilik properti langsung dengan calon penyewa tanpa potongan komisi perantara `[6]`. Pada sisi antarmuka, sistem desain *Neo-Brutalism* berkontras tegas yang dipadukan dengan pengalaman pengguna (*user experience*) yang ringkas membantu pengunjung menyelesaikan transaksi tanpa kebingungan `[7]`. Di balik layar, *framework* Laravel berarsitektur *Model-View-Controller* (MVC) memisahkan logika bisnis dari lapisan tampilan `[8]`, sedangkan basis data MySQL InnoDB terstruktur Bentuk Normal Ketiga (3NF) menghilangkan duplikasi data sekaligus menegakkan penguncian baris berstandar ACID saat penulisan data serentak `[9]`.
* **Paragraf 4 (Kesenjangan Penelitian Terdahulu, Integrasi Otomasi Sistem, dan Metode *Waterfall*):**  
  Meskipun sejumlah penelitian terdahulu telah mendigitalisasi katalog indekos, sebagian besar masih mengandalkan pemeriksaan bukti transfer manual serta belum memadukan penguncian konkurensi basis data dengan penjadwal tagihan otomatis `[10]`. Integrasi gerbang pembayaran Midtrans melalui *webhook* berautentikasi SHA-512 memverifikasi pembayaran *Virtual Account* dan kanal digital secara otomatis tanpa cek mutasi bank manual `[11]`. Selain itu, konfigurasi *cron job* di *server* mengotomatisasi penerbitan tagihan bulanan berulang dan perhitungan denda keterlambatan secara idempoten `[12]`, diikuti pengiriman pengingat tagihan serta kuitansi resmi secara *real-time* melalui API WhatsApp Fonnte `[13]` dan *relay* surel SMTP berenkripsi TLS `[14]`. Metode *Waterfall* mengatur seluruh komponen tersebut melalui tahapan berurutan mulai dari analisis kebutuhan dan desain hingga pengkodean, pengujian, serta pemeliharaan `[15]`.
* **Paragraf 5 (Tiga Tujuan Penelitian):**  
  Untuk menjawab celah operasional dan teknis tersebut, penelitian ini menetapkan tiga tujuan utama:
  1. Merancang arsitektur sistem informasi manajemen indekos terintegrasi *payment gateway* di Asri Boarding House guna mengatasi benturan reservasi kamar, keterlambatan pembukuan manual, dan penagihan sewa yang terlambat.
  2. Mengimplementasikan aplikasi web menggunakan *framework* Laravel yang terhubung dengan gerbang pembayaran Midtrans, otomasi penagihan terjadwal, serta notifikasi WhatsApp dan surel.
  3. Mengevaluasi efektivitas sistem melalui pengujian fungsional dan wawancara bersama pengelola serta penghuni indekos.

---

## TAHAP 5: BAB II. METHOD (*METODE PENELITIAN*)
Bab kedua terdiri atas **4 paragraf** dan **1 tabel ringkasan (`Table 1`)** dengan urutan sitasi baru `[16]` sampai `[20]`:

* **Paragraf 1 (Lokasi, Desain Penelitian R&D + Studi Kasus, dan Kerangka Pikir):**  
  Penelitian dilaksanakan di Asri Boarding House, Jalan Maera Sari No. 1, Tembalang, Semarang, yang mengelola 32 kamar tipe VIP, Deluxe, dan Standar. Penelitian menerapkan pendekatan *Research and Development* (R&D) rekayasa perangkat lunak yang dipadukan dengan studi kasus tunggal melalui empat komponen berurutan dalam kerangka pikir: (1) *Input* (kendala buku besar manual dan kebutuhan operasional), (2) *Process* (perancangan sistem, pengodean, dan pengujian fungsional), (3) *Output* (aplikasi web manajemen indekos terverifikasi), dan (4) *Outcome* (pembukuan kas yang akurat serta pelunasan sewa yang praktis bagi pengelola dan penghuni) `[16]`.
* **Paragraf 2 (Sumber Data Primer dan Sekunder):**  
  Data primer dikumpulkan melalui wawancara mendalam tatap muka yang direkam dan ditranskripsi bersama dua kelompok informan inti, yaitu pengelola indekos terkait pembukuan harian dan pemantauan kamar, serta mahasiswa penghuni aktif terkait pengalaman pemesanan kamar dan pembayaran bulanan. Data sekunder dihimpun dari buku besar kertas internal, bukti transfer, nota pengeluaran operasional, daftar tarif 32 kamar, dokumentasi teknis API Midtrans, API WhatsApp Fonnte, dan Laravel, serta literatur ilmiah terkait `[17]`.
* **Paragraf 3 (Prosedur Pengembangan 5 Tahap Metode *Waterfall*):**  
  Pengembangan aplikasi menerapkan lima tahapan metode *Waterfall* sebagaimana dirangkum pada `Table 1`:
  1. *Analysis:* Memetakan aturan operasional dari wawancara lapangan.
  2. *Design:* Memodelkan batas hak akses tiga aktor (calon penyewa, administrator, dan penyewa aktif) menggunakan *Use Case Diagram*, *Activity Diagram*, dan *System Flowchart*, beserta skema ERD 3NF yang terbagi atas modul transaksi inti dan modul operasional pendukung `[18]`.
  3. *Coding / Implementation:* Membangun aplikasi di atas Laravel MVC dan MySQL InnoDB, mencegah benturan pesanan ganda dengan transaksi ACID dan *pessimistic row-level locking* (`lockForUpdate()`) `[19]`, serta mengintegrasikan token Midtrans Snap (masa aktif 24 jam), *webhook callback* SHA-512 (`hash_equals()`), *cron scheduler* tanggal 1 (`tagihan:generate-bulanan`) dan evaluasi denda harian (`tagihan:proses-keterlambatan`), API WhatsApp Fonnte, dan antrean surel SMTP/TLS `[20]`.
* **Tabel 1 (`Table 1. WATERFALL RESEARCH STAGES AND TESTING DESIGN`):**  
  Merangkum 5 tahapan *Waterfall* (*1. Analysis* `[17]`, *2. Design* `[18]`, *3. Coding* `[19]`, *4. Testing* `[10]`, dan *5. Rollout*) beserta cakupan prosedur dan pengujiannya.
* **Paragraf 4 (Metode Pengujian Data *Black-Box* 60 Skenario dan Evaluasi UAT):**  
  Tahap keempat memadukan verifikasi fungsional dengan evaluasi pengguna. Kebenaran fungsi diuji menggunakan *black-box testing* pada 60 skenario uji di enam domain operasional: (1) autentikasi, hak akses, dan profil pengguna (12 skenario); (2) portal publik, katalog kamar, dan obrolan tamu (10 skenario); (3) alur pemesanan lima langkah calon penyewa (10 skenario); (4) portal penyewa aktif, tagihan, dan keluhan fasilitas (9 skenario); (5) manajemen operasional dan keuangan administrator (13 skenario); serta (6) otomasi *task scheduler*, denda keterlambatan, dan API eksternal (6 skenario) `[10]`. Setelah aplikasi diunggah ke *hosting* produksi, efektivitas operasional dievaluasi melalui uji pakai berdampingan dan wawancara semi-terstruktur bersama pengelola indekos (Pak Asep) serta lima mahasiswa penghuni aktif pada skala penilaian 10 poin `[17]`.

---

## TAHAP 6: BAB III. RESULTS AND DISCUSSION (*HASIL DAN PEMBAHASAN*)
Bab ketiga menguraikan hasil implementasi sistem secara komprehensif dengan memadukan artefak visual arsitektur perangkat lunak, skema basis data, antarmuka pengguna, dan alur transaksi digital (**8 Paragraf Naratif + 7 Kelompok Figure Utama + Table 2**) pada Sub-bab A, serta analisis kualitatif wawancara dan perbandingan komparatif ilmiah (**3 Paragraf Diskusi**) pada Sub-bab B:

### Sub-bab A. RESULT (8 Paragraf Naratif, 7 Kelompok Figure Utama, dan Table 2)

* **Paragraf 1 (Pemodelan Arsitektur Hak Akses Multi-Peran & Pengantar Figure 1):**  
  Pemodelan arsitektur menetapkan batas wewenang yang tegas antara tiga peran pengguna utama: calon penyewa, penyewa aktif, dan administrator indekos, sebagaimana dipetakan pada `Figure 1`. Calon penyewa dapat menelusuri katalog 32 kamar, berkonsultasi via obrolan langsung (*live chat*), melengkapi verifikasi identitas, dan memulai alur reservasi 5 tahap. Di sisi lain, penyewa aktif memperoleh akses terdedikasi untuk tagihan sewa bulanan, riwayat pembayaran digital, serta tiket keluhan pemeliharaan. Administrator mengawasi seluruh siklus operasional properti, meliputi inventaris kamar, kontrak penyewa, pembukuan keuangan, hingga pemantauan biaya operasional.
* **Figure 1 (`Figure 1. USE CASE DIAGRAM OF ASRI BOARDING HOUSE MANAGEMENT INFORMATION SYSTEM`):**  
  *Berkas Gambar:* `images/gambar_4_1_use_case_en.png` (lebar tampilan `88%`).  
  *Deskripsi Visual:* Diagram *Use Case* berstandar UML yang memetakan relasi 3 aktor utama (*Admin*, *Prospective Tenant*, dan *Active Tenant*) terhadap 14 *use case* fungsional inti berspesifikasi bahasa Inggris.

* **Paragraf 2 (Normalisasi Basis Data Relasional 3NF Dua Modul & Pengantar Figure 2):**  
  Guna mendukung operasi multi-peran tanpa redundansi data, basis data MySQL InnoDB dinormalisasi ke Bentuk Normal Ketiga (3NF) dan dikelompokkan ke dalam dua modul yang saling terhubung sebagaimana disajikan pada `Figure 2` `[18]`. Modul inti hunian dan keuangan (`Figure 2a`) menghubungkan enam tabel fundamental: `users`, `kamar`, `penyewa`, `reservasi`, `tagihan`, dan `pembayaran`. Melalui relasi *one-to-many*, tabel `penyewa` mengunci *snapshot* tarif sewa yang disepakati, tanggal masuk, batas akhir sewa, dan kontak darurat wali agar data kontrak historis tetap konsisten kendati tarif master kamar mengalami perubahan harga. Sementara itu, modul operasional pendukung (`Figure 2b`) mengelola penanganan keluhan fasilitas (`keluhan`), pencatatan biaya operasional (`pengeluaran`), log audit notifikasi (`log_notifikasi`), utas pesan konsultasi tamu (`guest_chat_threads` dan `chat_messages`), ulasan kepuasan penghuni (`customer_reviews`), tata tertib indekos (`peraturan`), serta pusat bantuan tanya-jawab (`faqs`).
* **Figure 2 (`Figure 2. ENTITY RELATIONSHIP DIAGRAM (3NF): (a) CORE RESIDENCY AND FINANCIAL MODULE; (b) SUPPORTING OPERATIONAL MODULE`):**  
  *Berkas Gambar:* `images/gambar_4_2_a_erd_modul_inti_en.png` (lebar `48%`) dan `images/gambar_4_2_b_erd_modul_pendukung_en.png` (lebar `48%`).  
  *Deskripsi Visual:* Diagram relasi entitas 3NF bersanding (*side-by-side*) yang memperlihatkan kardinalitas relasi, kunci primer (*PK*), kunci asing (*FK*), serta tipe data berbahasa Inggris yang presisi.

* **Paragraf 3 (Alur Algoritma Reservasi, Webhook Midtrans, dan Cron Scheduler & Pengantar Figure 3):**  
  `Figure 3` mengilustrasikan alur logika menyeluruh (*flowchart*) yang mengatur pemesanan kamar, verifikasi pembayaran otomatis, dan penerbitan tagihan terjadwal. Ketika calon penyewa memilih unit kosong, mengunggah kartu identitas, dan memulai proses pembayaran, sistem mengeksekusi transaksi basis data ACID disertai *pessimistic row-level locking* (`lockForUpdate()`) dan validasi bentrok jadwal guna menggagalkan pesanan ganda pada detik yang sama `[19]`. Begitu *gateway* Midtrans mengirim *webhook callback* SHA-512 yang divalidasi via `hash_equals()`, sistem secara otomatis mengubah status tagihan menjadi lunas, meningkatkan peran akun menjadi penyewa aktif, serta menerbitkan kuitansi digital via WhatsApp dan surel `[20]`. Untuk penyewa aktif, penjadwal *cron job* di *server* menerbitkan tagihan bulanan berulang setiap tanggal 1 (`tagihan:generate-bulanan`) dan mengevaluasi denda tunggakan secara idempoten setelah tanggal 10 (`tagihan:proses-keterlambatan`) dengan proteksi tumpang-tindih eksekusi (`withoutOverlapping()`) `[12]`.
* **Figure 3 (`Figure 3. END-TO-END RESERVATION, PAYMENT GATEWAY WEBHOOK, AND AUTOMATED BILLING FLOWCHART`):**  
  *Berkas Gambar:* `images/gambar_4_6_flowchart_sistem_en.png` (lebar `72%`).  
  *Deskripsi Visual:* Diagram alir sistem (*system flowchart*) komprehensif yang memetakan cabang keputusan calon penyewa, verifikasi kriptografis token Midtrans Snap, mutasi hak akses penyewa, hingga otomatisasi denda sewa lintas bulan.

* **Paragraf 4 (Implementasi Antarmuka Neo-Brutalism & Katalog Publik 32 Kamar & Pengantar Figure 4):**  
  Implementasi sistem menghasilkan 36 antarmuka web responsif berbalut gaya desain *Neo-Brutalism* berkontras tinggi `[7]`. Pada portal publik (`Figure 4`), calon penyewa dapat menyaring 32 unit kamar indekos berdasarkan kategori VIP, Deluxe, dan Standar, sembari memeriksa status ketersediaan *real-time*, ukuran luas kamar, rincian fasilitas, serta tarif sewa bulanan tanpa harus datang langsung ke lokasi properti.
* **Figure 4 (`Figure 4. PUBLIC ROOM UNIT CATALOG WITH REAL-TIME OCCUPANCY STATUS AND TIER FILTERS`):**  
  *Berkas Gambar:* `images/gambar_4_9_tipe_kamar_kost_en.png` (lebar `88%`).  
  *Deskripsi Visual:* Tangkapan layar antarmuka katalog publik indekos dengan kartu kamar *Neo-Brutalism*, lencana status okupansi (*Occupied* / *Available*), spesifikasi fasilitas kamar, dan tombol tindakan pemesanan langsung.

* **Paragraf 5 (Alur Pemesanan Online 5 Tahap & Modal Midtrans Snap & Pengantar Figure 5):**  
  Setelah menentukan unit kamar kosong dan lolos verifikasi 16 digit NIK, calon penyewa diarahkan menuju ruang kerja pemesanan lima langkah yang terintegrasi langsung dengan jendela sembulan (*modal*) Midtrans Snap (`Figure 5`). Pelacak reservasi (`Figure 5a`) memandu calon penghuni menyelesaikan tahapan *booking*, konfirmasi kesiapan fisik kamar via obrolan, hingga pemilihan skema pembayaran penuh maupun uang muka (DP). Pada saat bersamaan, gerbang pembayaran Midtrans Snap (`Figure 5b`) menghasilkan kode bayar *Virtual Account* atau QRIS berdurasi 24 jam sehingga meniadakan keharusan konfirmasi manual bukti transfer bank.
* **Figure 5 (`Figure 5. ONLINE ROOM BOOKING WORKFLOW: (a) FIVE-STEP RESERVATION WORKSPACE; (b) MIDTRANS SNAP VIRTUAL ACCOUNT MODAL`):**  
  *Berkas Gambar:* `images/gambar_4_21_pembayaran_5_tahap_reservasi_en.png` (lebar `60%`) dan `images/gambar_4_22_payment_gateway_midtrans.png` (lebar `36%`).  
  *Deskripsi Visual:* Tampilan bersanding (*side-by-side*) alur transaksi: ruang kerja reservasi lima langkah calon penghuni di sisi kiri dan *pop-up modal* pembayaran *Virtual Account* resmi Midtrans di sisi kanan.

* **Paragraf 6 (Portal Back-Office Administrator: Dasbor Eksekutif & Modul Laporan Kas & Pengantar Figure 6):**  
  Bagi pengelola properti, portal administrator mengintegrasikan operasional harian dan pembukuan kas ke dalam dasbor eksekutif dan modul laporan keuangan bulanan (`Figure 6`). Dasbor eksekutif (`Figure 6a`) memantau metrik bisnis melalui kartu KPI *real-time*—terdiri atas total 32 kamar, kamar tersedia, pendapatan bulan berjalan, pengeluaran operasional, laba bersih, tagihan belum lunas, dan pemesanan masuk—disertai grafik tren keuangan 12 bulan dan diagram donat okupansi kamar. Sementara itu, modul pelaporan keuangan (`Figure 6b`) merekonsiliasi seluruh arus kas masuk dari tagihan sewa lunas dengan pengeluaran operasional secara otomatis untuk menghitung saldo kas bersih akhir, lengkap dengan tombol ekspor dokumen PDF dan Excel dalam satu kali klik.
* **Figure 6 (`Figure 6. ADMINISTRATOR BACK-OFFICE PORTAL: (a) REAL-TIME EXECUTIVE DASHBOARD; (b) AUTOMATED FINANCIAL RECONCILIATION AND EXPORT MODULE`):**  
  *Berkas Gambar:* `images/gambar_4_26_dashboard_admin_en.png` (lebar `48%`) dan `images/gambar_4_33_manajemen_laporan_pemasukan_en.png` (lebar `48%`).  
  *Deskripsi Visual:* Tampilan antarmuka *back-office* pengelola: dasbor metrik bisnis dengan grafik interaktif di sisi kiri serta modul filter dan rekonsiliasi arus kas masuk-keluar di sisi kanan.

* **Paragraf 7 (Portal Penagihan Sewa Penghuni & Kuitansi Pembayaran Digital PDF A5 & Pengantar Figure 7):**  
  Di sisi penghuni, penyewa aktif dapat memantau kewajiban sewa bulanan dan mengunduh bukti pelunasan resmi secara mandiri melalui portal penagihan penyewa sebagaimana disajikan pada `Figure 7`. Halaman tagihan (`Figure 7a`) merangkum nominal tunggakan berjalan, riwayat tagihan terbayar, dan total akumulasi pengeluaran sewa. Setiap transaksi yang terverifikasi lunas secara otomatis menerbitkan dokumen kuitansi resmi berukuran A5 lanskap format PDF (`Figure 7b`) berstempel digital *PAID* dan referensi *invoice* Midtrans sebagai bukti bayar transparan untuk dilaporkan kepada orang tua mahasiswa.
* **Figure 7 (`Figure 7. TENANT BILLING AND PROOF OF SETTLEMENT: (a) RENTAL INVOICE HISTORY PORTAL; (b) AUTOMATED A5 DIGITAL PAYMENT RECEIPT (PDF)`):**  
  *Berkas Gambar:* `images/gambar_4_38_tagihan_penyewa_en.png` (lebar `56%`) dan `images/gambar_4_42_nota_pdf_en.png` (lebar `40%`).  
  *Deskripsi Visual:* Tampilan portal penagihan mandiri penyewa aktif dengan tabel status pembayaran di sisi kiri dan dokumen kuitansi PDF resmi A5 bertanda cap lunas hijau di sisi kanan.

* **Paragraf 8 (Hasil Pengujian Fungsional 60 Skenario Black-Box 100% Lulus & Pengantar Table 2):**  
  Verifikasi fungsional pada seluruh 60 skenario *black-box* di enam domain operasional mencapai tingkat kelulusan 100% tanpa galat validasi maupun transaksi `[10]`. Seluruh penyaring formulir, validasi 16 digit NIK, karantina kamar pasca-*checkout*, unduhan kuitansi PDF, serta ekspor laporan PDF dan Excel berjalan sesuai spesifikasi. Selanjutnya, *User Acceptance Testing* (UAT) yang dilaksanakan melalui uji pakai berdampingan dan wawancara tatap muka yang direkam secara audio bersama pengelola senior serta lima mahasiswa penghuni aktif menghasilkan skor kepuasan tinggi sebagaimana dirangkum pada `Table 2` `[17]`.
* **Tabel 2 (`Table 2. USER ACCEPTANCE EVALUATION SCORES AND KEY FINDINGS`):**  
  Menyajikan skor dan temuan utama dari rekaman suara wawancara 6 informan:
  1. **Mr. Asep (Manager):** `9.5 / 10` (Tutup buku turun dari 3 sampai 4 hari menjadi hitungan detik; nol selisih kas; kunci karantina pasca-*checkout* mencegah pemesanan prematur).
  2. **Arjun (UNDIP Tenant):** `8.5 / 10` (Katalog *Neo-Brutalism* jelas; pelunasan *Virtual Account* BCA cepat; kuitansi PDF A5 praktis untuk dikirim ke orang tua).
  3. **Farrel (USM Tenant):** `7.0 / 10` (Pemesanan jarak jauh lewat ponsel Android mudah; bebas antre ATM malam hari; menyarankan aplikasi *mobile* khusus dan kontrak sewa digital).
  4. **Iman (UDINUS Tenant):** `9.0 / 10` (Verifikasi pembayaran instan mencegah sengketa kuitansi hilang; riwayat bulanan rapi; menyarankan optimasi *server* di tanggal puncak jatuh tempo).
  5. **Ammar (PKN STAN Tenant):** `7.5 / 10` (Transaksi lancar di peramban Safari iPhone; bayar sewa saat libur dari luar kota mudah; menyarankan tautan pembayaran langsung di pesan WhatsApp).
  6. **Sindhu (UI Tenant):** `8.0 / 10` (Harga transparan; menghilangkan rasa sungkan saat serah terima uang tunai; menyarankan panduan *onboarding* awal).

### Sub-bab B. DISCUSSION (3 Paragraf Pembahasan Kualitatif & Komparasi Ilmiah)
* **Paragraf 1 (Pembahasan Temuan Kualitatif Wawancara Pengelola Senior / Pak Asep):**  
  Temuan kualitatif dari rekaman suara wawancara mengonfirmasi bahwa perpaduan verifikasi pembayaran otomatis dan kendali konkurensi basis data secara langsung mengurai hambatan pengelolaan indekos manual `[5]`. Dari sudut pandang manajemen, pengelola senior (Pak Asep) memberikan skor $9{,}5/10$ dan menekankan bahwa rekonsiliasi buku besar otomatis memangkas tutup buku akhir bulan dari tiga atau empat hari pencocokan nota kertas menjadi ekspor PDF dan Excel instan tanpa selisih kas. Di dalam mesin penagihan, penjadwal harian menerapkan masa tenggang dalam bulan berjalan setelah jatuh tempo tanggal 10 dan baru mengenakan denda *rollover* 5% secara idempoten apabila tagihan belum lunas melewati pergantian bulan kalender `[12]`. Selain itu, kunci karantina kamar pasca-*checkout* mencegah unit yang baru dikosongkan langsung tampil di katalog publik sebelum pembersihan dan pengecekan fisik selesai.
* **Paragraf 2 (Pembahasan Temuan Kualitatif Wawancara Lima Mahasiswa Penghuni):**  
  Dari sudut pandang penghuni, rekaman suara wawancara bersama lima mahasiswa menghasilkan rata-rata kepuasan $8{,}0/10$. Para penghuni menegaskan bahwa katalog layanan mandiri B2C `[6]` dan antarmuka *Neo-Brutalism* berkontras tinggi `[7]` memudahkan mereka memeriksa fasilitas, memesan kamar dari kampung halaman, serta membayar sewa bulanan lewat *Virtual Account* di peramban Android maupun iPhone tanpa harus mengantre di ATM pada malam hari. Unduhan kuitansi PDF ukuran A5 lanskap juga memberi bukti bayar resmi untuk diteruskan kepada orang tua sekaligus menghapus sengketa bukti kertas yang terselip. Masukan konstruktif dari wawancara tersebut, meliputi pengingat H-3 jatuh tempo disertai tautan bayar langsung di WhatsApp, panduan visual *onboarding*, aplikasi *mobile* khusus, serta kontrak sewa digital, memberikan acuan jelas untuk peningkatan sistem ke depan.
* **Paragraf 3 (Perbandingan Komparatif terhadap Penelitian Terdahulu):**  
  Dibandingkan dengan sistem indekos terdahulu oleh Cornellya dan Afriyadi `[1]` serta Nizar `[2]` yang masih mengandalkan unggah bukti transfer manual tanpa penguncian tingkat baris, implementasi ini memadukan transaksi ACID dengan `lockForUpdate()` dan penahanan karantina kamar pasca-*checkout* `[19]` sehingga menjamin nol *double booking* di seluruh 32 kamar. Selain itu, apabila platform pemantauan oleh Malaikosa dan Mokola `[3]` masih memerlukan pemeriksaan pembayaran manual, integrasi Midtrans Snap dengan validasi tanda tangan SHA-512 melalui `hash_equals()` memperbarui catatan buku besar secara otomatis begitu pembayaran lunas `[11]`. Lebih jauh lagi, berbeda dengan integrasi pembayaran ritel sekali beli `[4]`, penggabungan *webhook* pembayaran dengan *cron job* terjadwal `[12]`, API WhatsApp Fonnte `[13]`, dan antrean surel SMTP mengotomatisasi seluruh siklus masa sewa lintas bulan `[14]`.

---

## TAHAP 7: BAB IV. CONCLUSION (*KESIMPULAN DAN PROSPEK PENGEMBANGAN*)
Bab keempat terdiri atas **3 paragraf kesimpulan** (masing-masing tiga kalimat singkat *to the point* yang menjawab tiga tujuan penelitian) serta **5 poin rekomendasi pengembangan** tanpa sitasi baru:

* **Tiga Paragraf Kesimpulan (Menjawab Tiga Tujuan Penelitian):**
  1. **Paragraf 1 (Jawaban Tujuan 1 - Perancangan Arsitektur):** Arsitektur sistem Asri Boarding House menggunakan *use case diagram*, ERD, *activity diagram*, dan *flowchart*. Basis data MySQL 3NF dan tiga peran pengguna menyatukan pengelolaan 32 unit kamar beserta catatan kas dalam satu platform, sehingga mencegah *double booking* sekaligus menggantikan buku besar kertas.
  2. **Paragraf 2 (Jawaban Tujuan 2 - Implementasi Laravel, Midtrans, Penjadwalan, dan Pengujian):** Aplikasi web memadukan *framework* Laravel, penguncian baris basis data (`lockForUpdate`), dan integrasi pembayaran Midtrans Snap. Penjadwal tugas (*cron job*) otomatis menerbitkan tagihan bulanan, menghitung denda keterlambatan, dan mengirim pengingat via API WhatsApp Fonnte serta surel SMTP, yang seluruhnya berjalan stabil pada 60 skenario *black-box*.
  3. **Paragraf 3 (Jawaban Tujuan 3 - Evaluasi Wawancara Pengelola & Penghuni):** Evaluasi wawancara bersama pengelola indekos mencatat skor kepuasan $9{,}5/10$ karena tutup buku bulanan selesai secara instan tanpa selisih uang, sedangkan lima mahasiswa penghuni memberikan rata-rata skor $8{,}0/10$ atas kepraktisan reservasi jarak jauh, pembayaran *Virtual Account*, dan kuitansi PDF. Sistem ini terbukti mempermudah operasional harian Asri Boarding House.
* **Lima Poin Prospek Pengembangan Sistem (1 Kalimat Pengantar + 5 Poin Singkat *To the Point*):**
  1. Integrasi perangkat keras dapat menghubungkan kunci pintu pintar (*smart door lock*) IoT dan meteran listrik digital di tiap kamar langsung ke data kontrak aktif.
  2. Arsitektur platform dapat diperluas menjadi sistem multi-cabang untuk mengelola inventaris kamar dan keuangan di cabang indekos baru.
  3. Notifikasi WhatsApp sebaiknya dilengkapi pengingat H-3 jatuh tempo beserta tautan pembayaran langsung (*direct payment link*) menuju halaman tagihan.
  4. Rilis selanjutnya dapat menghadirkan aplikasi *mobile* Android dan iOS yang disertai tur panduan visual (*onboarding tour*) bagi penghuni baru.
  5. Layanan portal dapat menyertakan fitur kontrak sewa digital (*e-contract*) dan *chatbot* otomatis untuk melayani calon penyewa selama 24 jam.

---

## TAHAP 8: ACKNOWLEDGMENT (*UCAPAN TERIMA KASIH*)
Bagian ini ditempatkan setelah kesimpulan dengan **judul tanpa nomor bab (*unnumbered heading*)** dalam **1 paragraf**:
* Penulis menyampaikan terima kasih kepada Program Studi Sistem Informasi, Fakultas Ilmu Komputer, Universitas Katolik Soegijapranata Semarang atas bimbingan akademik dan fasilitas penelitian selama studi berlangsung, serta apresiasi khusus kepada pemilik, pengelola indekos (Pak Asep), dan para mahasiswa penghuni Asri Boarding House di Tembalang atas kerja sama terbuka selama wawancara lapangan dan evaluasi sistem.

---

## TAHAP 9: REFERENCES (*DAFTAR PUSTAKA*)
Ditempatkan di bagian paling akhir dengan **judul tanpa nomor bab (*unnumbered heading*)**, diformat dalam ukuran `10 pt Regular` dan `10 pt Italic` berstandar IEEE. Memuat **20 referensi terkurasi (`[1]` s.d. `[20]`)** yang muncul secara berurutan di dalam naskah (**19 artikel jurnal ilmiah**, di mana **18 jurnal diterbitkan dalam 5 tahun terakhir 2022 sampai 2025**, ditambah **1 buku metodologi penelitian tahun 2022**):

1. **`[1]` A. Cornellya & H. Afriyadi (2025):** Jurnal *PESHUM*, mengulas perancangan sistem informasi pemesanan kos berbasis web menggunakan *framework* Laravel.
2. **`[2]` C. Nizar (2021):** Jurnal *SISTEK*, mengulas rancang bangun sistem informasi sewa rumah kost (E-Kost) berbasis *website*.
3. **`[3]` E. J. Malaikosa & P. Mokola (2024):** Jurnal *JSiI*, mengulas sistem informasi monitoring rumah kos dan pembayarannya berbasis web.
4. **`[4]` R. Sutisna & F. Aziz (2025):** Jurnal *REMIK*, mengulas integrasi Midtrans sebagai *payment gateway* pada sistem penyewaan berbasis *website*.
5. **`[5]` A. Syahputra, R. Wiranti, & W. Astita (2022):** Jurnal *JMASIF*, mengulas peran Sistem Informasi Manajemen organisasi dalam pengambilan keputusan.
6. **`[6]` I. A. Nurhasanah et al. (2022):** Jurnal *JANAPATI*, mengulas analisis perancangan *E-Business* B2C (*Business-to-Consumer*).
7. **`[7]` A. R. Setiawan et al. (2023):** *Jurnal Teknologi Terpadu*, mengulas *design system* pada perancangan antarmuka perangkat lunak sistem akses digital.
8. **`[8]` L. Rahmawati & Sumarsono (2024):** Jurnal *JTEKSIS*, mengulas desain pengembangan *website* dengan arsitektur *Model View Controller* pada *framework* Laravel.
9. **`[9]` D. F. M. Dina (2024):** Jurnal *Computing Insight*, mengulas normalisasi *database* (3NF) pada rancangan sistem penyewaan.
10. **`[10]` J. K. Aditya & A. T. Hidayat (2025):** Jurnal *JUSIM*, mengulas sistem informasi manajemen kos berbasis web menggunakan metode *Blackbox*.
11. **`[11]` M. Nurhayati & R. Setiawan (2024):** *Jurnal Algoritma*, mengulas implementasi *payment gateway* Midtrans pada aplikasi berbasis web.
12. **`[12]` A. Wibowo et al. (2025):** Jurnal *Sainteknol*, mengulas implementasi *cron job* untuk pemrosesan data otomatis pada infrastruktur *cloud*.
13. **`[13]` I. M. B. Waton, A. N. Weking, & B. Deta (2025):** *Jurnal Teknik Mesin, Elektro dan Ilmu Komputer*, mengulas penerapan API WhatsApp Fonnte untuk sistem pengingat berbasis web.
14. **`[14]` M. A. Syahputra & R. A. Putri (2025):** Jurnal *Decode*, mengulas sistem informasi berbasis web dengan fitur notifikasi *email* (SMTP).
15. **`[15]` E. Rahmi, E. Yumami, & N. Hidayasari (2023):** Jurnal *REMIK*, mengulas analisis metode pengembangan sistem informasi berbasis *website* (metode *Waterfall*).
16. **`[16]` M. W. Mauludy & Fathahillah (2025):** Jurnal *JIMAT*, mengulas pengembangan sistem informasi menggunakan metode *Research and Development* (R&D).
17. **`[17]` Sugiyono (2022):** Buku *Metode Penelitian Kuantitatif, Kualitatif, dan R&D* (Alfabeta, Bandung).
18. **`[18]` Y. Adisa & M. I. P. Nasution (2023):** Jurnal *MASIP*, mengulas konsep dan peran sistem manajemen basis data relasional (RDBMS) pada SIM.
19. **`[19]` J. V. Dachi & J. Suhada (2025):** Jurnal *Router*, mengulas implementasi basis data menggunakan MySQL dan transaksi ACID.
20. **`[20]` R. C. Buwono, R. Kartadie, & M. K. Ramadhan (2024):** Jurnal *INFOTECH*, mengulas perancangan dan implementasi sistem *payment gateway* Midtrans.
