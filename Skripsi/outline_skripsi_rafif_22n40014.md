# OUTLINE & BEDAH LENGKAP NASKAH SKRIPSI
**Judul Skripsi:** SISTEM INFORMASI MANAJEMEN KOST TERINTEGRASI PAYMENT GATEWAY PADA ASRI BOARDING HOUSE  
**Penulis:** Rafif Arsya Pradiva  
**NIM:** 22.N4.0014  
**Program Studi / Konsentrasi:** Sistem Informasi / E-Commerce  
**Fakultas / Universitas:** Fakultas Ilmu Komputer / Universitas Katolik Soegijapranata Semarang (2026)  
**Dosen Pembimbing 1:** Ir. Andre Kurniawan Pamudji, S.Kom., M.Ling.  
**Dosen Pembimbing 2:** Erdhi Widyarto Nugroho, S.T., M.T.  

---

## RINGKASAN BAGIAN AWAL (PRA-BAB)
Sebelum masuk ke Bab 1, naskah memuat komponen administratif dan ringkasan eksekutif penelitian:
1. **Halaman Sampul & Judul:** Memuat identitas judul, penulis (`Rafif Arsya Pradiva - 22.N4.0014`), dan institusi.
2. **Halaman Pengesahan (10 April 2026):** Persetujuan Dosen Pembimbing 1, Dosen Pembimbing 2, dan Kaprodi Sistem Informasi (`Dr. Albertus Dwiyoga Widiantoro, S.Kom., M.Kom.`).
3. **Halaman Pernyataan Keaslian & Persetujuan Publikasi:** Komitmen bebas plagiasi dan pemberian Hak Bebas Royalti Non-eksklusif kepada Unika Soegijapranata.
4. **Kata Pengantar:**
   - **Paragraf 1:** Ungkapan syukur atas terselesaikannya skripsi sebagai syarat gelar Sarjana Komputer.
   - **Paragraf 2 & Daftar Poin (1–8):** Ucapan terima kasih kepada Dekan, Kaprodi, Dosen Pembimbing 1 & 2, staf pengajar, orang tua/keluarga, rekan mahasiswa, dan pihak pendukung lainnya.
   - **Paragraf 3:** Penutup dan keterbukaan terhadap saran membangun.
5. **Abstrak / Abstract:**
   - **Paragraf 1 (Masalah & Metode Solusi):** Menguraikan kendala operasional 32 unit kamar Asri Boarding House (buku besar fisik & pesan singkat manual yang memicu benturan reservasi, penagihan terlewat, dan selisih kas akibat rekonsiliasi manual berhari-hari), serta solusi rancang bangun sistem berbasis web dengan metode *Waterfall* menggunakan Laravel, MySQL (*pessimistic row-level locking*), Midtrans Snap, serta pengingat otomatis WhatsApp dan *email*.
   - **Paragraf 2 (Hasil Pengujian & Evaluasi):** Menyajikan hasil pengujian 60 skenario *black box* yang seluruhnya stabil tanpa galat, evaluasi bersama pengelola yang membuktikan waktu tutup buku terpangkas dari berhari-hari menjadi instan tanpa selisih kas, serta wawancara penghuni yang menegaskan kepuasan tinggi atas pemesanan jarak jauh, pembayaran *Virtual Account*, dan kuitansi digital.
   - **Kata Kunci:** Asri Boarding House, gerbang pembayaran Midtrans, Laravel, penguncian pesimis, sistem informasi manajemen kost.

---

## BAB 1: PENDAHULUAN

Bab ini memaparkan alasan pemilihan topik, permasalahan nyata di Asri Boarding House, rumusan masalah, serta tujuan penelitian yang ingin dicapai.

### 1.1 Latar Belakang (5 Paragraf)
* **Paragraf 1 (Fenomena Umum & Kebutuhan Reservasi Web):**  
  Membahas kepadatan mahasiswa perantau setiap tahun di kawasan pendidikan Tembalang, Kota Semarang, yang meningkatkan kebutuhan hunian sewa yang praktis dan transparan dari luar kota. Dijelaskan bahwa media promosi konvensional menyulitkan pengecekan sisa kamar kosong tanpa datang ke lokasi, sehingga diperlukan sistem pemesanan berbasis web untuk mengatasi kendala jarak dan merapikan pencatatan reservasi sejak awal `[1]`.
* **Paragraf 2 (Masalah Pencatatan Kamar & *Double Booking* di Objek Penelitian):**  
  Mengerucut pada objek penelitian Asri Boarding House Tembalang yang memiliki 32 unit kamar sewa (tipe VIP, Deluxe, dan Standar). Seluruh pendataan kamar masih dicatat pada buku besar fisik sehingga penjaga kost kesulitan memperbarui status keterisian dengan cepat, yang berakibat sering terjadinya benturan pemesanan ganda (*double booking*) ketika beberapa calon penghuni memesan kamar yang sama dalam waktu berdekatan `[2]`.
* **Paragraf 3 (Masalah Penagihan Sewa Bulanan & Denda Manual):**  
  Mengangkat kendala operasional pada penagihan sewa bulanan, di mana pengingat jatuh tempo setiap tanggal 10 beserta perhitungan dendanya masih dikirim lewat pesan pribadi satu per satu. Cara manual ini membuat jadwal penagihan sering terlewat dan memicu penumpukan tunggakan, padahal pengawasan jadwal otomatis pada sistem informasi dapat menertibkan siklus tagihan dan menekan keterlambatan `[3]`.
* **Paragraf 4 (Masalah Verifikasi Pembayaran & Rekonsiliasi Kas Akhir Bulan):**  
  Menjelaskan kerumitan pembukuan karena verifikasi pembayaran masih mengandalkan bukti *screenshot* transfer bank dan setoran tunai. Saat tutup buku akhir bulan, penjaga kost membutuhkan waktu berhari-hari untuk mencocokkan nota pengeluaran dan bukti kas masuk. Hal ini dapat diatasi dengan integrasi gerbang pembayaran digital (*payment gateway*) agar verifikasi transaksi non-tunai berjalan otomatis dan menutup celah selisih kas fisik `[4]`.
* **Paragraf 5 (Sintesis Solusi Sistem yang Dibangun):**  
  Merangkum kebutuhan Asri Boarding House akan sistem manajemen terpusat yang memadukan reservasi daring, penjadwalan tagihan otomatis, gerbang pembayaran Midtrans, serta pengingat tagihan via WhatsApp dan *email* secara *real-time* `[5]`. Paragraf ini menegaskan pembangunan "Sistem Informasi Manajemen Kost Terintegrasi *Payment Gateway* pada Asri Boarding House" untuk mewujudkan tata kelola yang rapi, efisien, dan bebas selisih kas.

### 1.2 Rumusan Masalah (1 Paragraf Pengantar + 3 Poin)
* **Paragraf Pengantar:** Menyatakan bahwa rumusan masalah disusun berdasarkan latar belakang permasalahan yang telah diuraikan.
* **Poin 1 (Perancangan Arsitektur):** Bagaimana merancang arsitektur Sistem Informasi Manajemen Kost Terintegrasi *Payment Gateway* pada Asri Boarding House sebagai solusi atas kendala pemesanan unit, pencatatan pembukuan manual, dan keterlambatan penagihan sewa?
* **Poin 2 (Implementasi Teknis):** Bagaimana mengimplementasikan sistem tersebut menggunakan kerangka kerja Laravel yang dipadukan dengan gerbang pembayaran Midtrans, penjadwalan tagihan otomatis berkala, serta pengiriman notifikasi melalui WhatsApp dan *email*?
* **Poin 3 (Evaluasi Efektivitas):** Bagaimana hasil penerapan dan efektivitas sistem berdasarkan evaluasi wawancara bersama penjaga kost serta penghuni kost pada operasional harian Asri Boarding House?

### 1.3 Tujuan Penelitian (1 Paragraf Pengantar + 3 Poin)
* **Paragraf Pengantar:** Menyatakan bahwa tujuan penelitian berpijak langsung pada rumusan masalah yang telah ditetapkan.
* **Poin 1 (Tujuan Perancangan):** Merancang arsitektur Sistem Informasi Manajemen Kost Terintegrasi *Payment Gateway* pada Asri Boarding House sebagai solusi terpadu atas kendala pemesanan unit, pencatatan pembukuan manual, dan keterlambatan penagihan sewa.
* **Poin 2 (Tujuan Implementasi):** Mengimplementasikan sistem tersebut menggunakan kerangka kerja Laravel yang dipadukan dengan gerbang pembayaran Midtrans, penjadwalan tagihan otomatis berkala, serta pengiriman notifikasi melalui WhatsApp dan *email*.
* **Poin 3 (Tujuan Evaluasi):** Menganalisis hasil penerapan dan efektivitas sistem berdasarkan evaluasi wawancara bersama penjaga kost serta penghuni kost pada operasional harian Asri Boarding House.

---

## BAB 2: TINJAUAN PUSTAKA

Bab ini menguraikan 11 landasan teori dan teknologi pendukung yang menjadi fondasi pembangunan sistem informasi manajemen kost.

### 2.1 Sistem Informasi Manajemen (2 Paragraf)
* **Paragraf 1 (Definisi & Fungsi Manajerial SIM):** Menjelaskan konsep SIM sebagai perpaduan perangkat keras, perangkat lunak, prosedur kerja, dan SDM untuk mengolah data transaksi mentah menjadi informasi terstruktur yang siap pakai guna membantu evaluasi kerja dan pengambilan keputusan manajerial `[6]`.
* **Paragraf 2 (Peran SIM dalam Pengelolaan Indekos):** Menguraikan fungsi SIM sebagai pusat data tunggal indekos (menyimpan inventaris kamar, identitas penghuni, riwayat pembayaran, dan kas operasional) untuk mencegah salah catat di buku besar, menghindarkan *double booking*, memantau masa sewa, dan menyajikan laporan keuangan transparan `[7]`.

### 2.2 E-Commerce B2C (2 Paragraf)
* **Paragraf 1 (Konsep Dasar B2C):** Membahas model perdagangan elektronik *Business-to-Consumer* (B2C) yang mempertemukan penyedia jasa langsung dengan konsumen akhir melalui internet, memotong jalur distribusi konvensional, serta memberikan kejelasan harga dan alur pesan mandiri kapan saja `[8]`.
* **Paragraf 2 (Penerapan B2C pada Bisnis Indekos):** Menjelaskan manfaat situs web B2C pada penyewaan indekos yang menghubungkan pengelola langsung dengan calon penghuni tanpa perantara pihak ketiga (bebas potongan komisi agen), sekaligus memudahkan calon penghuni mengecek sisa kamar kosong, memesan dari jarak jauh, dan menerima tagihan bulanan secara rapi `[9]`.

### 2.3 Web Application (2 Paragraf)
* **Paragraf 1 (Arsitektur *Client-Server* & *Thin Client*):** Menjabarkan cara kerja aplikasi berbasis web menggunakan arsitektur *client-server* dengan protokol HTTP/HTTPS, di mana *web browser* pada komputer/ponsel bertindak sebagai *thin client* penampil antarmuka, sedangkan *web server* mengeksekusi logika bisnis dan kueri basis data tanpa perlu instalasi aplikasi tambahan di perangkat pengguna `[10]`.
* **Paragraf 2 (Keunggulan Pembaruan *Real-Time* & Struktur Modular):** Membahas keunggulan pembaruan informasi *real-time* dan kepraktisan perawatan sistem di *server* pusat, serta pemisahan bagian tampilan, logika bisnis, dan penyimpanan data secara modular agar respons transaksi cepat dan sistem mudah dikembangkan `[11]`.

### 2.4 UI & UX (2 Paragraf)
* **Paragraf 1 (Konsep UI & Gaya *Neo-Brutalism*):** Menjelaskan peran *User Interface* (UI) sebagai jembatan visual interaksi manusia-perangkat lunak, khususnya penerapan gaya *Neo-Brutalism* yang bercirikan garis tepi tebal, bayangan tegas berkontras tinggi, dan tata letak geometris lugas sehingga tombol aksi, formulir, dan status transaksi mudah dikenali pengguna `[12]`.
* **Paragraf 2 (Konsep UX & Kemudahan Alur Kerja):** Menitikberatkan *User Experience* (UX) pada kenyamanan dan keringkasan alur interaksi, susunan menu yang terarah untuk menuntaskan transaksi dalam beberapa langkah sederhana, serta penyajian data bertingkat yang menghemat waktu kerja operasional harian `[13]`.

### 2.5 Laravel (2 Paragraf)
* **Paragraf 1 (Arsitektur MVC, Eloquent ORM, Blade & Keamanan Bawaan):** Menguraikan Laravel sebagai *framework* PHP berarsitektur *Model-View-Controller* (MVC) yang dilengkapi *Eloquent ORM* untuk pemodelan objek basis data, mesin templat *Blade*, serta perlindungan keamanan bawaan terhadap serangan CSRF (*Cross-Site Request Forgery*) dan injeksi SQL `[14]`.
* **Paragraf 2 (Modularitas Kode & *Service Layer*):** Menjelaskan pentingnya pemisahan logika bisnis yang rumit dari *controller* ke dalam lapisan khusus (*Service Layer*) pada aplikasi berskala menengah-besar agar kode tidak menumpuk pada satu berkas, mudah diuji, dan optimal untuk perawatan jangka panjang `[15]`.

### 2.6 RDBMS (2 Paragraf)
* **Paragraf 1 (Konsep Relasional & Transaksi ACID):** Membahas pengelolaan data terstruktur pada RDBMS dalam tabel dua dimensi yang saling terhubung melalui *primary key* dan *foreign key*, menjaga integritas referensial, serta mendukung kepatuhan transaksi ACID (*Atomicity, Consistency, Isolation, Durability*) agar bebas dari korupsi data `[16]`.
* **Paragraf 2 (Normalisasi hingga 3NF):** Menjelaskan perancangan skema tabel melalui proses normalisasi hingga Bentuk Normal Ketiga (3NF) untuk memecah tabel kompleks, mencegah duplikasi data, dan menghindari anomali pada operasi tambah, ubah, maupun hapus data `[17]`.

### 2.7 MySQL Database (2 Paragraf)
* **Paragraf 1 (Arsitektur *Multithread* & Operasi CRUD):** Menguraikan MySQL sebagai RDBMS *open-source* berbasis SQL dengan arsitektur *client-server multithread* yang hemat memori, mampu menangani baca-tulis bersamaan, dan didukung penuh oleh Laravel untuk operasi CRUD yang cepat dan stabil `[18]`.
* **Paragraf 2 (Mesin Penyimpanan InnoDB & *Row-Level Locking*):** Membahas keunggulan mesin penyimpanan InnoDB pada MySQL yang mendukung transaksi ACID, penguncian tingkat baris (*row-level locking*) untuk mencegah bentrokan data saat akses bersamaan, serta penggunaan *indexing* dan *foreign key constraints* `[19]`.

### 2.8 Payment Gateway (3 Paragraf)
* **Paragraf 1 (Peran Perantara Pembayaran & Midtrans):** Menjelaskan *payment gateway* (khususnya Midtrans) sebagai penghubung aplikasi web dengan jaringan perbankan dan dompet digital (*Virtual Account*, QRIS, *e-wallet*, kartu kredit) dalam satu pintu, sehingga pengelola tidak perlu membuka rekening di tiap bank dan penyewa tidak perlu mengunggah foto bukti transfer manual `[20]`.
* **Paragraf 2 (Lingkungan *Sandbox* vs *Production*):** Menguraikan pemisahan ruang kerja *Sandbox* (uji coba simulasi transaksi, respons bank, dan tagihan kedaluwarsa) dan *Production* (transaksi uang asli) yang dapat dialihkan dengan mudah melalui *environment variable* tanpa mengubah kode program.
* **Paragraf 3 (Mekanisme API, *Webhook Callback*, & Validasi SHA-512):** Menjelaskan cara kerja teknis integrasi melalui API dan *webhook callback* yang diamankan dengan validasi *signature key* berstandar kriptografi SHA-512 untuk memperbarui status tagihan secara otomatis di basis data begitu pembayaran diverifikasi bank `[21]`.

### 2.9 WhatsApp Gateway API (2 Paragraf)
* **Paragraf 1 (Otomasi Notifikasi & Keunggulan *Open Rate*):** Membahas pemanfaatan API *gateway* WhatsApp untuk mengirim pesan notifikasi otomatis ke nomor pengguna tanpa mengoperasikan ponsel fisik, yang dipilih karena tingkat keterbacaannya (*open rate*) jauh lebih tinggi dan cepat direspons dibanding SMS atau *email* `[22]`.
* **Paragraf 2 (Protokol *HTTP POST* & Pengiriman Terjadwal):** Menjelaskan pertukaran data melalui *HTTP POST request* berautentikasi token khusus untuk mengirim pesan pengingat terjadwal secara terprogram, menggantikan pengiriman manual satu per satu `[23]`.

### 2.10 Email Gateway API (2 Paragraf)
* **Paragraf 1 (Protokol SMTP & Enkripsi TLS):** Menguraikan pengiriman surat elektronik menggunakan standar *Simple Mail Transfer Protocol* (SMTP) dengan enkripsi TLS (*Transport Layer Security*) sebagai media pengiriman dokumen administrasi resmi dan permanen yang mudah diarsipkan pengguna `[24]`.
* **Paragraf 2 (Layanan *Mail Relay* & Dokumen Bukti Resmi):** Membahas pemanfaatan *mail relay* terenkripsi pada kerangka kerja web untuk mengirimkan bukti pembayaran, *invoice* digital, dan tautan keamanan akun secara otomatis tanpa masuk ke folder *spam*, melengkapi notifikasi instan WhatsApp `[25]`.

### 2.11 Cronjob (3 Paragraf)
* **Paragraf 1 (Definisi *Time-Based Job Scheduler*):** Menjelaskan *cron job* sebagai penjadwal tugas berbasis waktu pada sistem operasi keluarga Unix yang menjalankan perintah program secara mandiri di latar belakang *server* untuk pemeliharaan data dan perhitungan denda berkala `[26]`.
* **Paragraf 2 (Otomasi Pengingat Jatuh Tempo Tagihan):** Menguraikan peran *cron job* dalam mengeksekusi perintah konsol berkala untuk memeriksa tanggal jatuh tempo *invoice* di basis data dan memicu pengiriman pengingat tagihan via WhatsApp serta *email* `[27]`.
* **Paragraf 3 (Penjadwalan Tingkat *Framework* & Prinsip Idempoten):** Menjelaskan bahwa pada tingkat *framework* (*task scheduling*), cukup satu *daemon cron* sistem operasi yang diaktifkan untuk menjalankan seluruh jadwal di latar belakang, disertai penerapan prinsip idempoten agar eksekusi berulang tidak menerbitkan tagihan ganda.

---

## BAB 3: METODE PENELITIAN

Bab ini menjelaskan lokasi/objek kajian, pendekatan metodologi penelitian, sumber dan teknik pengumpulan data, tahapan rekayasa perangkat lunak, serta kerangka pemikiran.

### 3.1 Objek Penelitian (1 Paragraf)
* **Paragraf 1:** Menetapkan objek penelitian pada Asri Boarding House di Jalan Maera Sari Nomor 1, Tembalang, Kota Semarang, yang menyediakan 32 unit kamar (tipe VIP, Deluxe, dan Standar) bagi mahasiswa dan pekerja. Fokus kajian meliputi alur reservasi kamar, penagihan sewa bulanan, dan pembukuan kas operasional yang sebelumnya masih dicatat manual pada buku besar fisik.

### 3.2 Jenis Penelitian (2 Paragraf)
* **Paragraf 1 (Metode R&D Rekayasa Perangkat Lunak):** Menjelaskan penerapan metode Penelitian dan Pengembangan (*Research and Development* / R&D) melalui pendekatan rekayasa perangkat lunak karena tujuan penelitian adalah merancang, membangun, dan menguji perangkat lunak fungsional untuk menyelesaikan masalah operasional nyata di lapangan `[28]`.
* **Paragraf 2 (Perpaduan Studi Kasus Tunggal & Analisis Kualitatif):** Menguraikan perpaduan R&D dengan studi kasus tunggal di Asri Boarding House, dimulai dari analisis deskriptif kualitatif pada tahap pengumpulan data awal (memetakan kendala kas manual, jatuh tempo, dan pemesanan kamar) hingga pengujian fungsi dan evaluasi penggunaan sistem bersama pengguna di tahap akhir.

### 3.3 Sumber Data (1 Paragraf Pengantar + 2 Poin Utama)
* **Paragraf Pengantar:** Membagi sumber data menjadi dua kategori utama (data primer dan data sekunder) untuk memetakan proses bisnis dan mendasari perancangan modul sistem.
* **Poin 1: Data Primer** (Dihimpun langsung dari narasumber melalui wawancara mendalam):
  * *Sub-poin Penjaga kost:* Sumber informasi prosedur operasional harian, alur pencatatan sewa kamar, dan kendala pembukuan buku kas fisik.
  * *Sub-poin Penghuni kost:* Sumber informasi alur pemesanan kamar, kendala pembayaran sewa bulanan, dan kebutuhan bukti transaksi yang rapi.
* **Poin 2: Data Sekunder** (Dihimpun dari dokumen internal dan literatur pendukung):
  * *Sub-poin 1:* Arsip pembukuan internal (buku besar manual, bukti transfer sewa, nota pengeluaran operasional, dan daftar tarif 32 unit kamar).
  * *Sub-poin 2:* Dokumentasi teknis API Midtrans, *gateway* WhatsApp, dan dokumentasi resmi Laravel.
  * *Sub-poin 3:* Artikel jurnal ilmiah, buku referensi, dan publikasi akademik terkait sistem informasi indekos dan *payment gateway*.

### 3.4 Teknik Pengumpulan Data (2 Paragraf + 2 Poin Narasumber)
* **Paragraf 1 (Pendekatan Wawancara Mendalam):** Menjelaskan penggunaan pendekatan kualitatif melalui teknik wawancara mendalam di Asri Boarding House `[29]` untuk menggali kebutuhan operasional, kendala pencatatan manual, dan tanggapan pengguna pasca-implementasi.
* **Paragraf 2 & Rincian Narasumber (Tatap Muka, Rekaman Suara & Transkripsi):** Menjelaskan pelaksanaan wawancara tatap muka dengan alat perekam suara yang ditranskripsi menjadi teks tertulis pada tahap analisis kebutuhan maupun evaluasi sistem bersama dua kelompok narasumber:
  * *Sub-poin Penjaga kost:* Fokus pada evaluasi pembukuan kas, pemantauan sisa kamar dari 32 unit, kepraktisan penagihan otomatis, dan efisiensi kerja setelah beralih dari buku besar manual.
  * *Sub-poin Penghuni kost:* Fokus pada pengalaman memesan kamar, kejelasan rincian tagihan bulanan, kemudahan membayar via *payment gateway*, dan kejelasan bukti pembayaran digital.

### 3.5 Metode Pengembangan Sistem (3 Paragraf + Gambar 3.1)
* **Paragraf 1 (Konsep SDLC *Waterfall*):** Menjelaskan penerapan metode *Waterfall* dalam *Software Development Life Cycle* (SDLC) `[30]` yang berjalan bertahap dari analisis kebutuhan, perancangan antarmuka, penulisan kode, pengujian, hingga pemeliharaan berkala, di mana setiap tahap harus tuntas sebelum melangkah ke tahap berikutnya.
* **Paragraf 2 (Alasan Pemilihan *Waterfall*):** Menguraikan alasan pemilihan *Waterfall*, yakni karena kebutuhan pengelolaan indekos (alur pemesanan kamar dan penagihan sewa bulanan) sudah terpetakan dengan jelas sejak awal melalui studi lapangan sehingga pembangunan sistem dan integrasi *payment gateway* berjalan terarah tanpa perubahan spesifikasi di tengah jalan.
* **Paragraf 3 (Penjelasan Gambar 3.1 Tahapan Metode SDLC *Waterfall*):** Membedah lima tahapan pada Gambar 3.1 secara berurutan:
  1. *Requirement gathering and analysis:* Menghimpun kebutuhan operasional melalui wawancara mendalam.
  2. *Design:* Menyusun rancangan antarmuka, alur kerja sistem, dan skema basis data.
  3. *Implementation:* Menulis kode program menggunakan *framework* Laravel dan mengintegrasikan *payment gateway* Midtrans.
  4. *Integration & testing / Verification:* Menguji sistem dengan metode *black box testing* untuk memastikan setiap fitur dan transaksi pembayaran berjalan bebas kendala.
  5. *Operation & maintenance:* Mengunggah aplikasi ke *server hosting*, memantau operasional harian, dan melakukan perbaikan berkala.

### 3.6 Kerangka Pemikiran (2 Paragraf + Gambar 3.2)
* **Paragraf 1 (Konsep Kerangka Pemikiran R&D):** Menjelaskan penyusunan kerangka pemikiran mengikuti alur pendekatan R&D yang menghubungkan kendala operasional di lapangan secara terukur dengan solusi perancangan aplikasi berbasis web dari identifikasi masalah hingga evaluasi akhir.
* **Paragraf 2 (Penjelasan Gambar 3.2 Kerangka Pemikiran):** Membedah empat tahapan pada bagan Gambar 3.2:
  1. *Input (Masukan):* Kondisi operasional indekos, masalah pencatatan manual, dan rincian kebutuhan sistem.
  2. *Process (Proses):* Tahapan analisis dan perancangan, penulisan kode program, hingga pengujian fitur aplikasi.
  3. *Output (Keluaran):* Produk perangkat lunak manajemen kost yang telah teruji dan siap dioperasikan.
  4. *Outcome (Dampak):* Tertatanya pembukuan kas serta makin praktisnya proses pembayaran sewa bagi penjaga maupun penghuni kost.

---

## BAB 4: HASIL DAN PEMBAHASAN

Bab inti ini menguraikan hasil perancangan teknis, implementasi antarmuka visual (36 antarmuka), implementasi kode program (9 modul kunci), pengujian fungsionalitas (60 skenario *black box*), serta evaluasi wawancara pengguna (6 narasumber).

### 4.1 Perancangan Aplikasi
* **Paragraf Pengantar Sub-bab 4.1:** Menjelaskan bahwa perancangan aplikasi menerjemahkan kebutuhan operasional Asri Boarding House ke dalam cetak biru teknis sebelum penulisan kode program, meliputi *use case diagram*, *Entity Relationship Diagram* (ERD), *activity diagram*, dan *flowchart* sistem.

#### 4.1.1 Use Case Diagram (2 Paragraf Pengantar + Gambar 4.1 + 3 Poin Aktor)
* **Paragraf 1:** Menjelaskan pemetaan batasan hak akses dan fungsi layanan menggunakan *use case diagram* pada Gambar 4.1.
* **Paragraf 2:** Menyebutkan keterlibatan tiga aktor utama (calon penyewa, admin/penjaga kost, dan penyewa aktif) dengan hak akses terpisah agar alur pemesanan hingga tagihan bulanan terkontrol.
* **Poin 1: Calon Penyewa (1 Paragraf):** Pengunjung publik yang mencari informasi kamar kosong, fasilitas, dan tarif sewa; wajib mendaftar akun untuk mengisi formulir reservasi daring tanpa datang ke lokasi; pemesanan sah setelah melunasi uang muka via Midtrans yang langsung mengunci status unit kamar agar terhindar dari *double booking*.
* **Poin 2: Admin / Penjaga Kost (1 Paragraf):** Pemegang kendali operasional harian melalui dasbor pengelola untuk mengatur ketersediaan 32 unit kamar, memperbarui harga sewa, memeriksa identitas penghuni baru, memantau pelunasan otomatis tanpa cek mutasi rekening manual, serta menindaklanjuti laporan kerusakan fasilitas.
* **Poin 3: Penyewa Kost / Penyewa Aktif (1 Paragraf):** Penghuni resmi yang mengakses portal penyewa untuk melihat rincian *invoice* bulanan dan tanggal jatuh tempo, melunasi sewa/denda secara digital yang otomatis menerbitkan kuitansi elektronik, serta mengirimkan tiket keluhan kerusakan sarana kamar.

#### 4.1.2 Entity Relationship Diagram / ERD (2 Paragraf Pengantar + Gambar 4.2 (a) & (b) + 2 Poin Modul)
* **Paragraf 1:** Menjelaskan perancangan struktur penyimpanan data relasional yang dibagi menjadi dua tampilan pada Gambar 4.2 karena kompleksitas relasi dan atribut: bagian (a) modul transaksi & hunian inti, serta bagian (b) modul operasional & layanan informasi.
* **Paragraf 2:** Pengantar pembagian skema basis data ke dalam dua modul fungsional.
* **Poin 1: Modul Pengelolaan Kamar, Pengguna, Reservasi, dan Pembayaran – Gambar 4.2 (a) (2 Paragraf):**
  * *Paragraf 1 (Tabel Master & Hunian):* Menjelaskan keterkaitan tabel `users` (akun login pengelola & penyewa), `kamar` (rincian fisik 32 unit kamar, tarif, status), dan tabel penghubung `penyewa` (relasi 1:N yang mengunci *snapshot* harga sewa riil, tanggal masuk, batas akhir sewa, dan nomor darurat wali).
  * *Paragraf 2 (Tabel Transaksi Keuangan):* Menjelaskan keterhubungan tabel `reservasi` (pengajuan sewa daring dengan kode unik), `tagihan` (rincian uang muka atau sewa berkala), dan `pembayaran` (pencatatan nomor transaksi perbankan dan waktu penyelesaian otomatis dari sinyal *webhook* Midtrans).
* **Poin 2: Modul Layanan Keluhan, Operasional Kost, Komunikasi, dan Informasi – Gambar 4.2 (b) (2 Paragraf):**
  * *Paragraf 1 (Pemeliharaan, Kas Keluar & Log Notifikasi):* Menjelaskan tabel `keluhan` (laporan kerusakan fasilitas beserta kategori dan foto bukti), `pengeluaran` (biaya operasional rutin seperti listrik, air, teknisi untuk perhitungan kas bersih), dan `log_notifikasi` (rekam jejak pengingat otomatis via WhatsApp Fonnte dan *email* SMTP).
  * *Paragraf 2 (Komunikasi Tamu & Informasi Publik):* Menjelaskan tabel `guest_chat_threads` yang berelasi 1:N dengan `chat_messages` (konsultasi pengunjung tanpa akun), `customer_reviews` (ulasan penghuni), serta `peraturan` dan `faqs` (tata tertib dan tanya-jawab umum di beranda).

#### 4.1.3 Activity Diagram (1 Paragraf Pengantar + 3 Poin Aktor)
* **Paragraf Pengantar:** Menjelaskan penjabaran urutan proses bisnis dan interaksi antara pengguna, aplikasi web, serta layanan pihak ketiga ke dalam tiga *activity diagram* (Gambar 4.3, 4.4, dan 4.5) sesuai peran aktor.
* **Poin 1: Calon Penyewa – Gambar 4.3 (2 Paragraf):**
  * *Paragraf 1:* Pengantar alur pemesanan kamar mandiri dari jarak jauh mulai dari pilih kamar di katalog, isi biodata, hingga bayar uang muka.
  * *Paragraf 2:* Bedah alur Gambar 4.3: calon penyewa memilih 1 dari 32 kamar kosong -> aplikasi memeriksa kelengkapan data -> meminta token transaksi ke *server* Midtrans Snap -> pemesan melunasi DP via *Virtual Account* / dompet digital -> sistem otomatis mengunci kamar di basis data dan mengirim bukti reservasi ke WhatsApp via Fonnte.
* **Poin 2: Admin (Penjaga Kost) – Gambar 4.4 (2 Paragraf):**
  * *Paragraf 1:* Pengantar fokus aktivitas pengelola pada pengelolaan inventaris kamar, validasi penghuni, dan pembukuan kas bulanan.
  * *Paragraf 2:* Bedah alur Gambar 4.4: autentikasi akun ke dasbor admin -> memantau 32 unit kamar, memperbarui tarif, dan memverifikasi berkas identitas penghuni baru -> sistem memperbarui data hunian dan menerbitkan tagihan bulanan otomatis ke WhatsApp penyewa -> admin memeriksa rekapitulasi pembayaran masuk dan mencatat pengeluaran rutin untuk menutup laporan keuangan bulanan.
* **Poin 3: Penyewa Aktif – Gambar 4.5 (2 Paragraf):**
  * *Paragraf 1:* Pengantar aktivitas penyewa aktif pada portal layanan mandiri untuk memeriksa tagihan rutin, melunasi sewa non-tunai, dan melaporkan kerusakan fasilitas.
  * *Paragraf 2:* Bedah alur Gambar 4.5: masuk ke portal penyewa dan meninjau tagihan mendekati jatuh tempo -> menekan tombol bayar untuk memunculkan *pop-up* Midtrans Snap tanpa pindah halaman -> pelunasan via transfer bank/dompet digital otomatis memperpanjang masa aktif sewa -> mengunduh kuitansi PDF atau membuka menu keluhan fasilitas.

#### 4.1.4 Flowchart Sistem (2 Paragraf + Gambar 4.6)
* **Paragraf 1:** Pengantar pemetaan urutan logika kerja sistem dan titik-titik pengambilan keputusan (*decision*) dari tahap pemesanan awal hingga pengaktifan status penghuni pada Gambar 4.6.
* **Paragraf 2:** Bedah alur Gambar 4.6: calon penyewa membuka situs web dan mengecek 32 unit kamar -> jika kosong, mengisi formulir reservasi, mengunggah foto KTP, dan memilih durasi sewa -> diteruskan ke Midtrans -> titik *decision* pengecekan transaksi (jika gagal/kedaluwarsa kembali ke pemilihan metode bayar; jika lunas, unit kamar dikunci di basis data dan bukti reservasi dikirim via WhatsApp Fonnte) -> berakhir saat calon penghuni tiba di lokasi kost, verifikasi berkas fisik oleh penjaga kost, serah terima kunci, dan aktivasi status sebagai penyewa resmi.

---

### 4.2 Implementasi Antarmuka Sistem
* **Paragraf Pengantar Sub-bab 4.2:** Menjelaskan penerjemahan rancangan visual menjadi halaman web fungsional bergaya *Neo-Brutalism* (garis tepi tegas, kontras tinggi, tata letak responsif) yang dibagi ke dalam empat bagian sesuai hak akses: umum, calon penyewa, admin, dan penyewa aktif.

#### 4.2.1 Implementasi Umum (1 Paragraf Pengantar + 11 Poin Antarmuka Publik)
* **Paragraf Pengantar:** Mencakup 11 antarmuka publik yang dapat diakses langsung tanpa login (profil indekos, ketersediaan kamar, fasilitas, panduan pemesanan, autentikasi, dan obrolan tamu).
* **Poin 1: Implementasi Antarmuka *Home Page* (Gambar 4.7 – 2 Paragraf):**
  * *Paragraf 1:* Fungsi beranda utama sebagai titik temu pertama pencari hunian di Tembalang dengan tata letak responsif di komputer maupun ponsel.
  * *Paragraf 2:* Bedah visual Gambar 4.7 (bilah navigasi utama di atas, spanduk pengenalan & pintasan cari kamar di kiri, foto bangunan di kanan, serta tombol obrolan interaktif di kanan bawah).
* **Poin 2: Implementasi Antarmuka Fasilitas Kost (Gambar 4.8 – 3 Paragraf):**
  * *Paragraf 1:* Fungsi halaman fasilitas kost untuk menyajikan sarana penunjang bersama secara terbuka bagi calon penyewa.
  * *Paragraf 2:* Keterangan singkat fungsi dan aturan penggunaan tiap item fasilitas.
  * *Paragraf 3:* Bedah visual Gambar 4.8 (tata letak kartu *grid* untuk dapur bersama, parkir, Wi-Fi, CCTV beserta foto asli, judul tebal, dan deskripsi singkat).
* **Poin 3: Implementasi Antarmuka Tipe Kamar Kost (Gambar 4.9 – 2 Paragraf):**
  * *Paragraf 1:* Fungsi katalog tipe kamar yang menampilkan spesifikasi ruang, luas, kamar mandi dalam, dan harga sewa bulanan beserta tombol pemesanan.
  * *Paragraf 2:* Bedah visual Gambar 4.9 (kartu tipe kamar Deluxe dengan foto kamar tidur & kamar mandi dalam, nominal harga, lencana sisa unit kosong, daftar inventaris kasur/meja/lemari/AC/*water heater*, dan tombol biru "Pemesanan Kamar").
* **Poin 4: Implementasi Antarmuka Galeri Kost (Gambar 4.10 – 3 Paragraf):**
  * *Paragraf 1:* Fungsi galeri kost menghimpun dokumentasi foto fisik bangunan dan lingkungan harian bagi calon penyewa luar kota.
  * *Paragraf 2:* Penataan dokumentasi visual dalam susunan kisi proporsional yang dapat diperbesar saat diklik.
  * *Paragraf 3:* Bedah visual Gambar 4.10 (deretan foto fasad bangunan, lahan parkir, koridor lantai 1 dan 2, dapur bersama dengan efek transisi halus saat kursor diarahkan).
* **Poin 5: Implementasi Antarmuka Alur Cara Reservasi (Gambar 4.11 – 2 Paragraf):**
  * *Paragraf 1:* Fungsi halaman panduan alur reservasi dari pembuatan akun hingga pelunasan uang muka agar pengunjung baru menyiapkan dokumen identitas sebelum memesan.
  * *Paragraf 2:* Bedah visual Gambar 4.11 (visualisasi 5 langkah berurutan: registrasi akun, pemilihan unit, pelengkapan profil, pembayaran DP Midtrans, dan penerimaan bukti reservasi dengan nomor urut berbingkai tebal).
* **Poin 6: Implementasi Antarmuka Testimoni Penyewa Kost (Gambar 4.12 – 2 Paragraf):**
  * *Paragraf 1:* Fungsi bagian testimoni untuk memberikan gambaran objektif mengenai kebersihan, keamanan, dan kesigapan pengelola dari penghuni terdahulu/aktif.
  * *Paragraf 2:* Bedah visual Gambar 4.12 (deretan kartu geser *carousel* berisi kutipan pengalaman, foto profil, nama samaran, tipe kamar, dan 5 ikon bintang kepuasan).
* **Poin 7: Implementasi Antarmuka FAQ (Gambar 4.13 – 3 Paragraf):**
  * *Paragraf 1:* Fungsi halaman *Frequently Asked Questions* (FAQ) merangkum pertanyaan umum terkait jam malam, aturan tamu menginap, uang muka, dan perpanjangan sewa.
  * *Paragraf 2:* Peran tanya jawab mandiri dalam meringankan beban penjaga kost menjawab pertanyaan berulang.
  * *Paragraf 3:* Bedah visual Gambar 4.13 (komponen panel lipat *accordion* dengan ikon panah di ujung kanan untuk membuka/menutup jawaban).
* **Poin 8: Implementasi Antarmuka Tentang Kami (Gambar 4.14 – 2 Paragraf):**
  * *Paragraf 1:* Fungsi halaman Tentang Kami memperkenalkan profil usaha, komitmen kenyamanan, koordinat lokasi di Tembalang, dan saluran komunikasi resmi.
  * *Paragraf 2:* Bedah visual Gambar 4.14 (kartu profil latar belakang Asri Boarding House, sematan peta interaktif *Google Maps*, serta nomor telepon, *email* resmi, dan jam layanan di bagian bawah).
* **Poin 9: Implementasi Antarmuka *Login User* (Gambar 4.15 – 3 Paragraf):**
  * *Paragraf 1:* Fungsi halaman *login user* sebagai pintu autentikasi calon penyewa maupun penghuni dengan pencocokan kredensial terenkripsi di MySQL.
  * *Paragraf 2:* Penyediaan tautan pemulihan akses bagi pengguna yang lupa kata sandi via *email*.
  * *Paragraf 3:* Bedah visual Gambar 4.15 (kartu *Neo-Brutalism* bergaris tepi hitam tebal berlatar kuning cerah, input *email* & kata sandi dengan ikon penampil teks, centang "Ingat Saya", tombol biru "Masuk ke Akun", dan tautan registrasi).
* **Poin 10: Implementasi Antarmuka *Register User* (Gambar 4.16 – 2 Paragraf):**
  * *Paragraf 1:* Fungsi halaman *register user* untuk menghimpun data awal pemohon baru (nama lengkap, nomor WhatsApp aktif, *email*, kata sandi) disertai validasi keunikan *email* dan kecocokan sandi.
  * *Paragraf 2:* Bedah visual Gambar 4.16 (kartu formulir "Buat Akun Baru" dengan indikator peringatan format masukan dan tombol biru "Daftar Akun" yang mengarahkan ke pelengkapan profil).
* **Poin 11: Implementasi Antarmuka *Chat Real Time Guest* (Gambar 4.17 – 3 Paragraf):**
  * *Paragraf 1:* Fungsi fitur *chat real-time guest* bagi pengunjung untuk bertanya langsung ke penjaga kost tanpa akun (cukup nama panggilan dan kontak singkat).
  * *Paragraf 2:* Penerusan seluruh pesan langsung ke dasbor pengelola agar segera dibalas.
  * *Paragraf 3:* Bedah visual Gambar 4.17 (*widget* mengambang di pojok kanan bawah dengan kepala jendela kuning cerah, tombol *minimize*, riwayat balasan beserta waktu kirim, serta kolom ketik pesan di bagian bawah).

#### 4.2.2 Implementasi Calon Penyewa (1 Paragraf Pengantar + 7 Poin Antarmuka & Integrasi)
* **Paragraf Pengantar:** Menguraikan 7 antarmuka dan layanan integrasi bagi calon penyewa pasca-registrasi (identitas kependudukan, dasbor reservasi, simulasi biaya, pembayaran 5 tahap, Midtrans, WhatsApp Fonnte, dan *email* SMTP).
* **Poin 1: Implementasi Antarmuka Lengkapi Profil (Gambar 4.18 – 3 Paragraf):**
  * *Paragraf 1:* Kewajiban melengkapi biodata resmi (NIK, nama & telepon kerabat darurat, asal kampus/instansi, dan foto KTP asli) demi keamanan lingkungan indekos.
  * *Paragraf 2:* Penyimpanan dokumen identitas di direktori terbatas yang hanya dapat diakses administrator saat verifikasi.
  * *Paragraf 3:* Bedah visual Gambar 4.18 (kolom isian nomor KTP, kontak darurat, nomor penjamin, area unggah foto KTP dengan pratinjau gambar & batas ukuran, serta tombol hijau "Simpan Profil").
* **Poin 2: Implementasi Antarmuka *Dashboard* Reservasi (Gambar 4.19 – 2 Paragraf):**
  * *Paragraf 1:* Fungsi dasbor reservasi sebagai halaman pantau utama status pengajuan sewa, nomor tagihan uang muka, dan riwayat reservasi.
  * *Paragraf 2:* Bedah visual Gambar 4.19 (sapaan pengguna, kartu ringkasan kode reservasi & unit kamar, bilah indikator tahapan proses reservasi di tengah, serta tombol "Bayar Uang Muka" dan unduh tanda terima di panel kanan).
* **Poin 3: Implementasi Antarmuka Simulasi Biaya Kamar (Gambar 4.20 – 3 Paragraf):**
  * *Paragraf 1:* Fungsi kalkulator simulasi biaya kamar untuk menghitung tarif sewa pokok, uang jaminan (*deposit*), dan potongan harga sewa jangka panjang secara terbuka.
  * *Paragraf 2:* Kebebasan pengguna mengganti opsi durasi sewa (bulanan, tiga bulanan, tahunan) untuk membandingkan total tagihan.
  * *Paragraf 3:* Bedah visual Gambar 4.20 (kartu kalkulator dengan *dropdown* durasi & fasilitas tambahan di sisi kiri, tabel rincian biaya dinamis tanpa muat ulang halaman di sisi kanan, serta tombol biru "Lanjutkan Pemesanan").
* **Poin 4: Implementasi Antarmuka Pembayaran 5 Tahap Reservasi (Gambar 4.21 – 2 Paragraf):**
  * *Paragraf 1:* Perancangan penyelesaian reservasi dalam 5 tahap (konfirmasi rincian sewa, pemilihan metode bayar, pemeriksaan tagihan, pelunasan via *payment gateway*, dan penerbitan nota tanda jadi) yang menjaga kamar tidak diambil pengguna lain selama batas waktu pembayaran aktif.
  * *Paragraf 2:* Bedah visual Gambar 4.21 (bilah langkah dengan lingkaran nomor hijau di atas, spesifikasi kamar, nominal DP, hitung mundur batas waktu pembayaran, tombol pemicu transaksi digital, dan catatan pengingat di bagian bawah).
* **Poin 5: Implementasi *Payment Gateway* Midtrans (Gambar 4.22 – 3 Paragraf):**
  * *Paragraf 1:* Penanganan pelunasan DP maupun sewa kamar melalui integrasi Midtrans Snap (*Virtual Account*, *e-wallet*, QRIS, gerai ritel).
  * *Paragraf 2:* Pembaruan status pesanan otomatis di basis data melalui *webhook callback* begitu dana ditransfer tanpa menunggu cek mutasi manual penjaga kost.
  * *Paragraf 3:* Bedah visual Gambar 4.22 (jendela *pop-up* Midtrans Snap di tengah layar memuat total tagihan, batas waktu pelunasan, pilihan metode GoPay/QRIS/VA BCA/BNI/Mandiri beserta panduan langkah pembayaran dan keamanan kriptografi).
* **Poin 6: Implementasi *WhatsApp Gateway* Fonnte (Gambar 4.23 – 2 Paragraf):**
  * *Paragraf 1:* Pengiriman pesan otomatis ke nomor ponsel pemohon via API Fonnte sesaat setelah pembayaran DP terkonfirmasi lunas oleh Midtrans (memuat nomor referensi, tipe & nomor kamar, nominal, dan tanggal *check-in*).
  * *Paragraf 2:* Bedah visual Gambar 4.23 (tangkapan layar pesan WhatsApp resmi Asri Boarding House berisi ucapan konfirmasi, kode pemesanan, rincian unit kamar, status lunas, dan tautan unduh nota digital sebagai pegangan saat mengambil kunci).
* **Poin 7: Implementasi *Email Gateway* SMTP (Gambar 4.24 – 3 Paragraf):**
  * *Paragraf 1:* Pengiriman bukti pembayaran resmi dan ketentuan sewa ke alamat *email* calon penyewa via protokol SMTP sebagai arsip administrasi formal yang mudah disimpan/dicetak.
  * *Paragraf 2:* Format pengiriman menggunakan templat HTML beridentitas resmi Asri Boarding House beserta lampiran kuitansi digital.
  * *Paragraf 3:* Bedah visual Gambar 4.24 (kop resmi "Bukti Pembayaran Reservasi Kamar Kost", tabel rincian transaksi, jadwal *check-in*, ringkasan tata tertib, dan lampiran nota PDF).

#### 4.2.3 Implementasi Admin (1 Paragraf Pengantar + 11 Poin Antarmuka Pengelola)
* **Paragraf Pengantar:** Merangkum 11 antarmuka kendali operasional khusus penjaga kost (pengawasan 32 unit kamar, verifikasi reservasi masuk, pengelolaan tagihan bulanan, pencatatan pengeluaran kas, ekspor laporan keuangan, serta penanganan keluhan fasilitas dan obrolan pengunjung).
* **Poin 1: Implementasi Antarmuka *Admin Login* (Gambar 4.25 – 2 Paragraf):**
  * *Paragraf 1:* Fungsi jalur masuk terbatas bagi penjaga kost untuk melindungi data master kamar, kas, dan KTP penghuni, dilengkapi pembatasan percobaan masuk gagal beruntun.
  * *Paragraf 2:* Bedah visual Gambar 4.25 (kartu *Neo-Brutalism* bergaris tepi hitam pekat bertajuk "Masuk Portal Admin", input *email* & sandi administrator, serta tombol hitam "Masuk Dasbor").
* **Poin 2: Implementasi Antarmuka *Dashboard Admin* (Gambar 4.26 – 3 Paragraf):**
  * *Paragraf 1:* Fungsi *dashboard admin* merangkum metrik operasional dan keuangan dalam satu layar (tingkat keterisian 32 kamar, total kas masuk bulan berjalan, jumlah penghuni aktif, dan daftar keluhan tertunda).
  * *Paragraf 2:* Manfaat penyajian data ringkas dan grafik tren bulanan untuk pemantauan harian usaha indekos.
  * *Paragraf 3:* Bedah visual Gambar 4.26 (kartu indikator kuning & biru di bagian atas, grafik batang pemasukan bulanan berdampingan dengan tabel reservasi terbaru di tengah, serta bilah navigasi vertikal di sisi kiri).
* **Poin 3: Implementasi Antarmuka Manajemen Kamar (Gambar 4.27 – 2 Paragraf):**
  * *Paragraf 1:* Wewenang penjaga kost mengelola inventaris 32 unit kamar (tambah unit, perbarui status keterisian, sesuaikan tarif dan fasilitas per tipe kamar) secara terpusat untuk menutup celah *double booking*.
  * *Paragraf 2:* Bedah visual Gambar 4.27 (tabel daftar unit kamar memuat nomor unit, kategori VIP/Deluxe/Standar, lantai, tarif bulanan, lencana ketersediaan, tombol kuning "Tambah Kamar Baru", kolom pencarian & *filter* kamar kosong, serta tombol "Edit" dan "Hapus").
* **Poin 4: Implementasi Antarmuka Manajemen Fasilitas (Gambar 4.28 – 3 Paragraf):**
  * *Paragraf 1:* Fungsi modul manajemen fasilitas untuk mengatur daftar sarana umum di katalog publik beserta pembaruan foto dan aturan pemakaian ruang bersama.
  * *Paragraf 2:* Pengaturan urutan tampil fasilitas agar sarana unggulan ditempatkan di posisi teratas.
  * *Paragraf 3:* Bedah visual Gambar 4.28 (tabel sarana umum berisi nama fasilitas, uraian fungsi, ikon kategori, pratinjau foto, tombol "Tambah Fasilitas", kolom *filter*, serta tombol aksi pengubah status sarana saat perbaikan fisik).
* **Poin 5: Implementasi Antarmuka Manajemen Penyewa (Gambar 4.29 – 2 Paragraf):**
  * *Paragraf 1:* Fungsi penghimpunan data penghuni aktif (identitas lengkap, telepon darurat kerabat, nomor kamar, masa berlaku kontrak sewa) serta penonaktifan status hunian saat *check-out*.
  * *Paragraf 2:* Bedah visual Gambar 4.29 (tabel daftar penghuni memuat nama lengkap, nomor WhatsApp, unit kamar, tanggal mulai menghuni, lencana "Aktif", kolom pencarian nama/lantai, dan tombol "Detail" untuk melihat foto KTP serta rekam jejak pembayaran).
* **Poin 6: Implementasi Antarmuka Manajemen Reservasi (Gambar 4.30 – 3 Paragraf):**
  * *Paragraf 1:* Fungsi pemrosesan pengajuan sewa kamar dari calon penyewa baru (periksa berkas KTP, pastikan uang muka lunas di Midtrans, lalu setujui penempatan kamar).
  * *Paragraf 2:* Mekanisme pembatalan pesanan yang melewati batas waktu pembayaran agar unit kamar kembali terbuka bagi calon penyewa lain.
  * *Paragraf 3:* Bedah visual Gambar 4.30 (tabel pesanan kamar memuat kode reservasi, nama pemohon, tipe kamar, rencana tanggal masuk, status DP, tombol *filter* "Menunggu Pembayaran" / "Terverifikasi" / "Dibatalkan", dan tombol aksi "Konfirmasi").
* **Poin 7: Implementasi Antarmuka Manajemen Tagihan (Gambar 4.31 – 2 Paragraf):**
  * *Paragraf 1:* Pengendalian siklus penagihan sewa bulanan dan perhitungan denda keterlambatan yang terhubung dengan *cron job* untuk menerbitkan faktur sewa dan memantau jatuh tempo setiap tanggal 10.
  * *Paragraf 2:* Bedah visual Gambar 4.31 (kartu "Daftar Tagihan Bulanan Penyewa" berisi tabel nomor *invoice*, nama penghuni, nomor kamar, nominal tagihan + denda, tanggal jatuh tempo, lencana status pembayaran, tombol sinkronisasi tagihan, dan tombol "Detail" untuk rincian biaya / kirim ulang pengingat).
* **Poin 8: Implementasi Antarmuka Manajemen Pengeluaran (Gambar 4.32 – 3 Paragraf):**
  * *Paragraf 1:* Pencatatan arus kas keluar operasional harian (listrik, air PDAM, alat kebersihan, perbaikan fasilitas) agar perhitungan laba bersih akurat.
  * *Paragraf 2:* Kelengkapan keterangan peruntukan dana dan nama petugas penanggung jawab pada setiap transaksi pengeluaran.
  * *Paragraf 3:* Bedah visual Gambar 4.32 (kartu ringkasan total pengeluaran bulan berjalan di atas, tabel riwayat kas keluar berisi tanggal, kategori, rincian penggunaan, nominal, staf pencatat di bawah, serta tombol "Tambah Biaya Operasional").
* **Poin 9: Implementasi Antarmuka Manajemen Laporan Pemasukan (Gambar 4.33 – 2 Paragraf):**
  * *Paragraf 1:* Pengolahan seluruh pembayaran sewa Midtrans dan catatan pengeluaran operasional menjadi laporan keuangan terpadu tanpa perlu mencocokkan mutasi bank dan buku kas fisik satu per satu, lengkap dengan fitur unduh Excel dan PDF.
  * *Paragraf 2:* Bedah visual Gambar 4.33 (3 kartu metrik utama: pendapatan kotor, beban operasional, dan laba bersih di atas; grafik garis tren pendapatan bulanan & tabel rincian kas masuk dengan *filter* rentang tanggal di bawah; serta tombol hijau "Ekspor Excel" dan merah "Unduh PDF").
* **Poin 10: Implementasi Antarmuka Manajemen Keluhan Penyewa (Gambar 4.34 – 3 Paragraf):**
  * *Paragraf 1:* Pengelolaan terpusat laporan kerusakan fasilitas kamar maupun gangguan kenyamanan lingkungan untuk memetakan tingkat urgensi aduan (kebocoran air, kelistrikan, perabot).
  * *Paragraf 2:* Pembaruan status tindak lanjut perbaikan oleh admin agar penghuni pelapor mengetahui progres penanganannya.
  * *Paragraf 3:* Bedah visual Gambar 4.34 (tabel tiket aduan memuat nama pelapor, nomor kamar, kategori kerusakan, tanggal pengajuan, lencana status penanganan, panel *filter* prioritas laporan baru, serta tombol aksi pembuka foto bukti dan pengubah status menjadi "Diproses" atau "Selesai").
* **Poin 11: Implementasi Antarmuka Manajemen Fitur *Chat Guest* (Gambar 4.35 – 2 Paragraf):**
  * *Paragraf 1:* Ruang pesan dua arah di sisi admin untuk menjawab pertanyaan pengunjung publik dari *widget* halaman depan seputar ketersediaan kamar dan jadwal survei lokasi.
  * *Paragraf 2:* Bedah visual Gambar 4.35 (pembagian layar menjadi 2 panel: daftar antrean pesan pengunjung di kiri dan ruang percakapan aktif beserta penanda waktu kirim di kanan, dilengkapi pilihan templat balasan cepat / *quick reply*).

#### 4.2.4 Implementasi Penyewa Aktif (1 Paragraf Pengantar + 7 Poin Antarmuka Portal Mandiri)
* **Paragraf Pengantar:** Membahas 7 antarmuka khusus penyewa aktif pada portal layanan mandiri (pantau masa sewa, bayar tagihan bulanan via Midtrans, perbarui profil, cek riwayat transaksi, ajukan keluhan kamar, dan unduh kuitansi PDF).
* **Poin 1: Implementasi Antarmuka *Login* Penyewa Aktif (Gambar 4.36 – 3 Paragraf):**
  * *Paragraf 1:* Pemisahan jalur masuk penghuni resmi agar langsung diarahkan ke portal pengelolaan sewa bulanan tanpa melewati alur pemesanan kamar lagi.
  * *Paragraf 2:* Perlindungan sesi masuk dengan enkripsi kredensial demi kerahasiaan riwayat tagihan dan data pribadi penghuni.
  * *Paragraf 3:* Bedah visual Gambar 4.36 (kartu *Neo-Brutalism* berlatar kuning cerah bertajuk "Masuk Portal Penghuni Aktif", kolom *email* & kata sandi, opsi pengingat sesi, dan tombol biru "Masuk ke Portal").
* **Poin 2: Implementasi Antarmuka *Dashboard* Penyewa (Gambar 4.37 – 2 Paragraf):**
  * *Paragraf 1:* Fungsi dasbor penyewa menyajikan ringkasan nomor unit kamar, sisa masa berlaku sewa, rincian tagihan bulan berjalan, dan papan pengumuman resmi penjaga kost.
  * *Paragraf 2:* Bedah visual Gambar 4.37 (sapaan nama penghuni & kartu informasi hunian di atas, kartu pemberitahuan tagihan berwarna kuning cerah dengan tombol pintasan "Bayar Tagihan Sekarang" di tengah, serta deretan menu cepat pengajuan keluhan, arsip kuitansi, dan pengumuman di bawah).
* **Poin 3: Implementasi Antarmuka Tagihan Penyewa (Gambar 4.38 – 3 Paragraf):**
  * *Paragraf 1:* Transparansi rincian kewajiban pembayaran sewa bulanan (nominal sewa pokok, tambahan biaya denda bila melewati tanggal 10, dan batas waktu pelunasan).
  * *Paragraf 2:* Integrasi langsung tombol pembayaran pada halaman ini dengan modul Midtrans Snap.
  * *Paragraf 3:* Bedah visual Gambar 4.38 (kartu tagihan memuat nomor *invoice*, periode bulan, total biaya, tanggal jatuh tempo, lencana kuning "Belum Dibayar", dan tombol hijau "Bayar Tagihan" pemicu *pop-up* Midtrans Snap).
* **Poin 4: Implementasi Antarmuka Profil (Gambar 4.39 – 2 Paragraf):**
  * *Paragraf 1:* Keleluasaan penyewa aktif memperbarui nomor WhatsApp, kontak darurat kerabat, dan kata sandi akun secara mandiri demi kesiapan komunikasi saat keadaan mendesak.
  * *Paragraf 2:* Bedah visual Gambar 4.39 (dua bagian utama: kartu biodata penghuni di sisi kiri dengan penyamaran sebagian digit kontak darurat untuk privasi, serta formulir pengaturan kata sandi lama/baru dengan tombol kuning "Simpan Perubahan" di sisi kanan).
* **Poin 5: Implementasi Antarmuka Riwayat Pembayaran (Gambar 4.40 – 3 Paragraf):**
  * *Paragraf 1:* Pencatatan kronologis seluruh transaksi pelunasan sewa penghuni (kode referensi, tanggal pelunasan, kanal pembayaran, dan nominal dana).
  * *Paragraf 2:* Kemudahan melacak pengeluaran sewa bulanan dan mengunduh ulang bukti kuitansi resmi kapan pun dibutuhkan.
  * *Paragraf 3:* Bedah visual Gambar 4.40 (tabel rekapitulasi memuat tanggal bayar, nomor pesanan, metode pembayaran VA/dompet digital, jumlah uang, lencana hijau "Berhasil", serta tombol biru "Cetak Nota" di ujung kanan baris).
* **Poin 6: Implementasi Antarmuka Keluhan (Gambar 4.41 – 2 Paragraf):**
  * *Paragraf 1:* Pengajuan laporan kerusakan fasilitas kamar/area umum (pilih kategori masalah, tulis deskripsi, lampirkan foto bukti fisik) tanpa harus mencari penjaga kost secara tatap muka, sekaligus memantau progres perbaikan.
  * *Paragraf 2:* Bedah visual Gambar 4.41 (formulir "Tambah Laporan Keluhan" di bagian atas dan tabel riwayat aduan beserta lencana status tindak lanjut pengelola di bagian bawah).
* **Poin 7: Implementasi Nota PDF (Gambar 4.42 – 3 Paragraf):**
  * *Paragraf 1:* Penerbitan dokumen tanda terima resmi berupa nota PDF untuk setiap pembayaran DP maupun sewa bulanan yang lunas (mencantumkan nomor faktur, identitas penyewa, nomor kamar, rincian nominal, dan cap lunas digital).
  * *Paragraf 2:* Pengaturan ukuran dan tata letak nota secara proporsional agar siap dicetak kapan saja tanpa teks terpotong.
  * *Paragraf 3:* Bedah visual Gambar 4.42 (bingkai garis tegas dengan kepala surat resmi Asri Boarding House Tembalang, rincian nomor referensi, tanggal pelunasan, nomor kamar, nama penyewa tersamar sebagian, nilai transaksi Rp1.700.000 dengan stempel "LUNAS", serta keterangan verifikasi otomatis di kaki dokumen).

---

### 4.3 Implementasi Kode Program
* **Paragraf Pengantar Sub-bab 4.3:** Menjelaskan penerjemahan rancangan arsitektur dan proses bisnis ke dalam kode PHP pada *framework* Laravel 11 dengan pemisahan logika layanan (*Service Layer*), yang dikelompokkan ke dalam empat sub-bab tematik: autentikasi dan hak akses, pemesanan kamar, *payment gateway*, serta penjadwalan dan notifikasi.

#### 4.3.1 Implementasi Autentikasi dan Hak Akses (1 Paragraf Pengantar + 2 Poin)
* **Paragraf Pengantar:** Mengatur keamanan hak masuk dan mengarahkan pengguna ke portal kerja sesuai perannya (calon penyewa, penjaga kost, penyewa aktif) agar tidak terjadi pelanggaran hak akses pada rute privat.
* **Poin 1: Implementasi Pembatasan Hak Akses Peran Pengguna / *Role Middleware* (Gambar 4.43 – 2 Paragraf):**
  * *Paragraf 1:* Fungsi *middleware* mencegat setiap permintaan halaman web sebelum diteruskan ke pengontrol utama untuk memeriksa sesi login dan label peran yang diizinkan.
  * *Paragraf 2:* Bedah kode Gambar 4.43 (baris awal memeriksa sesi *login* aktif dan mengalihkan pengguna yang belum terautentikasi ke formulir *login*; baris bawah memvalidasi kecocokan peran pengguna dan menghentikan eksekusi bila hak akses tidak sesuai).
* **Poin 2: Implementasi Pengalihan Portal Pasca-autentikasi / *Auth Redirect* (Gambar 4.44 – 2 Paragraf):**
  * *Paragraf 1:* Logika pemandu rute halaman tujuan masing-masing pengguna setelah kredensial tervalidasi (memeriksa kelengkapan nomor kontak, status aktif penyewa, hingga riwayat reservasi berjalan).
  * *Paragraf 2:* Bedah kode Gambar 4.44 (akun admin dialihkan ke dasbor operasional indekos; penghuni aktif diarahkan ke portal penyewa; calon penyewa dengan transaksi berjalan dipandu langsung ke halaman detail reservasinya).

#### 4.3.2 Implementasi Pemesanan Kamar (1 Paragraf Pengantar + 2 Poin)
* **Paragraf Pengantar:** Menangani alur penguncian inventaris 32 unit kamar secara akurat sebagai benteng utama pencegah benturan pesanan ganda (*double booking*) saat akses bersamaan.
* **Poin 1: Implementasi Transaksi ACID dan Penguncian Baris Unit Kamar (Gambar 4.45 – 2 Paragraf):**
  * *Paragraf 1:* Penanganan persaingan akses data ketika dua calon penghuni memilih unit kamar yang sama pada detik bersamaan menggunakan prinsip transaksi ACID dan penguncian baris eksklusif.
  * *Paragraf 2:* Bedah kode Gambar 4.45 (pembungkusan pembuatan reservasi dalam blok transaksi basis data dan pemanggilan metode `lockForUpdate()` untuk mengunci baris data kamar terpilih hingga validasi tanggal tuntas; jika kamar sudah terisi pemesan terdahulu, transaksi dibatalkan dan pesan peringatan ditampilkan).
* **Poin 2: Implementasi Algoritma Pengecekan Benturan (*Double Booking*) Kamar (Gambar 4.46 – 2 Paragraf):**
  * *Paragraf 1:* Algoritma pendeteksi benturan jadwal yang memeriksa apakah rentang tanggal mulai hingga selesai sewa bertabrakan dengan jadwal reservasi aktif lain maupun masa berlaku kontrak penghuni aktif.
  * *Paragraf 2:* Bedah kode Gambar 4.46 (kueri pertama menyaring irisan tanggal pada reservasi aktif di unit kamar terkait; kueri kedua memeriksa kontrak penghuni aktif; jika terdeteksi benturan, sistem mengembalikan nilai `true` untuk menolak pengajuan sewa baru).

#### 4.3.3 Implementasi *Payment Gateway* (1 Paragraf Pengantar + 2 Poin)
* **Paragraf Pengantar:** Memfasilitasi transaksi non-tunai (*Virtual Account*, QRIS, dompet digital) melalui Midtrans yang menghubungkan aplikasi web dengan *server* perbankan dan memperbarui status pelunasan otomatis via *webhook*.
* **Poin 1: Implementasi Pembuatan Transaksi dan *Snap Token* Midtrans (Gambar 4.47 – 2 Paragraf):**
  * *Paragraf 1:* Pembuatan token transaksi melalui layanan Midtrans Snap yang merangkum rincian sewa pokok dan akumulasi denda keterlambatan dengan masa berlaku pelunasan 24 jam.
  * *Paragraf 2:* Bedah kode Gambar 4.47 (pemetaan nominal pokok dan denda ke dalam larik *item* transaksi, penetapan identitas pesanan unik berbasis *timestamp* beserta masa kadaluwarsa 24 jam, dan eksekusi *HTTP POST* terenkripsi dengan *server key* untuk memperoleh *Snap token* resmi).
* **Poin 2: Implementasi Validasi *Signature Key* dan *Webhook Callback* Midtrans (Gambar 4.48 – 2 Paragraf):**
  * *Paragraf 1:* Pemrosesan otomatis pembaruan status transaksi sewa bulanan dan DP reservasi melalui *webhook callback* Midtrans yang wajib melewati verifikasi tanda tangan digital berstandar kriptografi SHA-512.
  * *Paragraf 2:* Bedah kode Gambar 4.48 (verifikasi keaslian tanda tangan digital menggunakan fungsi `hash_equals()` untuk mencegah *timing attack*, pemeriksaan status transaksi terdahulu demi menjaga sifat idempoten agar tidak terjadi pencatatan kas ganda, lalu pembaruan status tagihan menjadi lunas secara otomatis).

#### 4.3.4 Implementasi Penjadwalan dan Notifikasi (1 Paragraf Pengantar + 3 Poin)
* **Paragraf Pengantar:** Otomasi operasional indekos melalui perpaduan penjadwal tugas berkala di *server* dengan pengiriman pesan instan WhatsApp dan *email* resmi sehingga penagihan tanggal 10 dan bukti kuitansi selalu terkirim tepat waktu tanpa pengingat manual.
* **Poin 1: Implementasi Penjadwalan Tugas Otomatis / *Task Scheduler Cron Job* (Gambar 4.49 – 2 Paragraf):**
  * *Paragraf 1:* Otomatisasi pengelolaan tagihan rutin dan pembersihan transaksi kedaluwarsa melalui penjadwal tugas Laravel dengan cukup mengaktifkan satu layanan penjadwal di tingkat *server*.
  * *Paragraf 2:* Bedah kode Gambar 4.49 (penerbitan tagihan sewa bulanan dijadwalkan setiap tanggal 1 pukul 00:05 WIB; kalkulasi denda keterlambatan dieksekusi setiap dini hari pukul 01:00 WIB untuk memeriksa tagihan yang melewati jatuh tempo tanggal 10; dilengkapi metode `withoutOverlapping()` agar proses tidak tumpang-tindih).
* **Poin 2: Implementasi Integrasi Layanan Pesan *WhatsApp Gateway* Fonnte (Gambar 4.50 – 2 Paragraf):**
  * *Paragraf 1:* Pengiriman notifikasi tagihan bulanan dan konfirmasi pelunasan reservasi ke ponsel penyewa melalui API Fonnte yang diawali dengan standardisasi format nomor telepon internasional.
  * *Paragraf 2:* Bedah kode Gambar 4.50 (pemformatan nomor kontak pengguna ke awalan kode negara Indonesia, pengiriman *HTTP POST* dengan *header* token otorisasi rahasia ke *server* Fonnte, serta pemeriksaan kode status koneksi dan respons JSON).
* **Poin 3: Implementasi Integrasi Pengiriman Dokumen Tagihan via *Email Gateway* SMTP (Gambar 4.51 – 2 Paragraf):**
  * *Paragraf 1:* Pengiriman otomatis dokumen penagihan resmi dan kuitansi sewa melalui protokol SMTP berenkripsi TLS menggunakan antrean pesan (*message queue*) di latar belakang agar tidak memperlambat interaksi antarmuka web.
  * *Paragraf 2:* Bedah kode Gambar 4.51 (penerapan antarmuka `ShouldQueue` untuk eksekusi asinkron melalui antrean *server*, metode `envelope()` untuk menetapkan subjek surat resmi bermuatan nomor tagihan, dan metode `content()` untuk memadukan rincian sewa pokok, denda, serta tautan portal ke dalam templat *email*).

---

### 4.4 Pengujian Sistem (1 Paragraf Pengantar + Tabel 4.1 [60 Skenario] + 1 Paragraf Penutup)
* **Paragraf Pengantar (Metode *Black Box Testing*):** Menjelaskan pelaksanaan pengujian fungsionalitas menggunakan metode kotak hitam (*black box testing*) yang berfokus pada kesesuaian masukan data dan respons antarmuka aplikasi pada 6 domain operasional utama.
* **Tabel 4.1: Hasil Pengujian Fungsionalitas Sistem (*Black Box Testing* – 60 Skenario Uji):**
  1. **Domain I: Autentikasi, Hak Akses & Profil Pengguna (Skenario 1–12):**
     - Menguji *login* Admin (kredensial valid, *password* salah, *email* tidak terdaftar, kewajiban ganti *password* saat *login* pertama), *login* Penyewa Aktif (valid & salah sandi), lupa kata sandi (*email* terdaftar, tidak terdaftar, & pembaruan token valid), registrasi Calon Penyewa baru, serta pelengkapan profil (nomor WA valid Indonesia vs karakter tidak valid). Seluruh 12 skenario **Berhasil**.
  2. **Domain II: Portal Publik, Katalog & *Guest Chat* (Skenario 13–22):**
     - Menguji akses *home page* publik 32 kamar, penyaringan katalog berdasarkan lantai (Lantai 1) dan tipe (VIP), akses detail kamar berstatus "Tersedia" vs "Terisi", pintasan WhatsApp pengelola, pembukaan & pengiriman pesan *widget Guest Chat* tanpa *login*, ketahanan sesi obrolan saat *refresh*, serta tampilan galeri foto dan *accordion* FAQ. Seluruh 10 skenario **Berhasil**.
  3. **Domain III: Calon Penyewa & Alur Pemesanan 5 Tahap (Skenario 23–32):**
     - Menguji pemilihan kamar kosong di Tahap 1, pengisian biodata & validasi NIK 16 digit (valid vs kurang dari 16 digit) di Tahap 2, simulasi durasi sewa 12 bulan & potongan harga tahunan otomatis, pemilihan skema uang muka (DP) vs lunas penuh (100%), pemicuan *pop-up* Midtrans Snap, penutupan jendela Snap sebelum transfer (status *pending* & kunci sementara), hingga pelunasan DP yang mengunci kamar dan mengirim bukti ke WA & *email*. Seluruh 10 skenario **Berhasil**.
  4. **Domain IV: Portal Penyewa Aktif, Tagihan & Keluhan (Skenario 33–41):**
     - Menguji tampilan dasbor ringkasan hunian, daftar riwayat tagihan sewa, rincian komponen *invoice* (pokok, denda, jatuh tempo), pelunasan tagihan via *Virtual Account* Midtrans Snap (*webhook* otomatis mengubah status lunas), pengunduhan kuitansi PDF resmi, pengajuan keluhan fasilitas kamar beserta unggahan foto `.jpg` 1 MB, validasi penolakan berkas bukan gambar (`.pdf`/`.docx`), dan pemantauan status keluhan "Selesai". Seluruh 9 skenario **Berhasil**.
  5. **Domain V: Administrator & Manajemen Operasional (Skenario 42–54):**
     - Menguji kartu ringkasan operasional di dasbor admin, penambahan unit kamar baru, pembaruan tarif & spesifikasi kamar, pencegahan hapus kamar yang sedang dihuni, pengelolaan master fasilitas kost, konfirmasi aktivasi reservasi masuk, pencatatan pelunasan tagihan secara tunai, penonaktifan status hunian (*check-out*), pembaruan status kamar pasca-inspeksi kebersihan menjadi "Tersedia", tindak lanjut tiket keluhan ("Diproses"), membalas pesan *Guest Chat*, pencatatan pengeluaran operasional kost, serta ekspor laporan keuangan ke PDF dan Excel. Seluruh 13 skenario **Berhasil**.
  6. **Domain VI: Otomasi *Task Scheduler*, Denda & API Eksternal (Skenario 55–60):**
     - Menguji penerbitan tagihan sewa otomatis setiap tanggal 1 (`tagihan:generate-bulanan`), pencegahan penerbitan tagihan ganda pada bulan yang sama, perhitungan denda keterlambatan otomatis setelah melewati tanggal 10, penanganan sifat idempoten perhitungan denda saat dijalankan berulang di hari yang sama, pengiriman pengingat tagihan via API WhatsApp Fonnte, serta pengiriman surat tagihan via *Email* SMTP melalui *message queue*. Seluruh 6 skenario **Berhasil**.
* **Paragraf Penutup Sub-bab 4.4:** Menyimpulkan bahwa seluruh 60 skenario uji fungsional pada Tabel 4.1 berhasil dijalankan 100% sesuai spesifikasi kebutuhan tanpa kendala teknis, baik pada validasi formulir, penguncian transaksi kamar berprinsip ACID, maupun integrasi Midtrans, Fonnte, dan SMTP.

---

### 4.5 Hasil Wawancara (1 Paragraf Pengantar + 6 Poin Narasumber [Tabel 4.2–4.7] + 1 Paragraf Sintesis Akhir)
* **Paragraf Pengantar (*User Acceptance Testing* / UAT):** Menjelaskan pelaksanaan evaluasi kelayakan operasional sistem bersama pengelola operasional senior (Bapak Asep) dan lima mahasiswa penghuni kost aktif melalui uji coba langsung berdampingan (*side-by-side usability testing*) dan wawancara semiterstruktur terekam audio yang meninjau aspek katalog daring, reservasi bertahap, pembayaran Midtrans, kuitansi instan, hingga pembukuan kas harian.

* **Poin 1: Wawancara dengan Penjaga Kost – Bapak Asep (Paragraf Pengantar + Gambar 4.52 + Tabel 4.2 + Paragraf Analisis):**
  * *Paragraf Pengantar:* Fokus evaluasi fungsionalitas panel pengelola dan kemudahan operasional harian setelah 20 tahun menggunakan buku besar manual, dilengkapi dokumentasi foto Gambar 4.52.
  * *Tabel 4.2 (10 Aspek Pengujian Bapak Asep):*
    1. **Halaman Depan Publik:** Tipografi besar, kontras tegas ramah bagi mata pengelola senior, foto 32 kamar dan harga langsung terlihat jelas sehingga calon penyewa luar kota tidak perlu bolak-balik menelepon.
    2. **Tombol WhatsApp Melayang:** Posisi di kanan bawah mudah dijangkau jempol di HP dan langsung terhubung ke WA admin untuk respons cepat.
    3. **Dasbor Admin & Kas Otomatis (OLED Dark Mode):** Warna gelap nyaman di mata, angka kamar isi/kosong serta saldo kas masuk/keluar dan laba bersih langsung dihitung otomatis tanpa coret-coretan buku besar.
    4. **Pendaftaran Penyewa Datang Langsung (*Form Walk-In*):** Kolom ringkas dan menyimpan nomor HP orang tua/wali untuk kondisi darurat atau tunggakan.
    5. **Konfirmasi Bayar Tunai & Kuitansi Instan:** Satu klik langsung lunas dan terbit kuitansi tanpa perlu menulis tangan di buku blok kuitansi kertas yang rawan hilang.
    6. **Aturan Denda Flat 5% Kalender:** Dinilai sangat tepat dan bijaksana (lewat tanggal 10 di bulan berjalan masih bebas denda karena kiriman uang orang tua kadang mundur beberapa hari, baru jika menyeberang bulan dikenakan denda flat 5% satu kali untuk mendidik disiplin tanpa memberatkan).
    7. **Penguncian Kamar Pasca-*Checkout* (Karantina Fisik):** Dinilai wajib karena kamar yang baru ditinggal keluar harus dibersihkan dan dicek fasilitasnya terlebih dahulu sebelum diubah manual menjadi tersedia di web agar tidak mengecewakan pemesan baru.
    8. **Pencatatan Biaya & Rekap Laporan Keuangan:** Memangkas waktu rekapitulasi akhir bulan dari 3–4 hari mengumpulkan bon manual menjadi hitungan detik dengan fitur ekspor PDF/Excel untuk pemilik kost.
    9. **Portal Mandiri Anak Kost (BCA VA & Keluhan Berfoto):** Memudahkan mahasiswa membayar dari HP dan sangat menolong penjaga kost karena laporan kerusakan disertai foto bukti fisik yang bisa langsung diteruskan ke teknisi/tukang tanpa takut lupa seperti laporan lisan di lorong.
    10. **Refleksi 20 Tahun Manual vs Web & Penilaian Akhir:** Perbedaan seperti bumi dan langit, pekerjaan jauh lebih ringan dan bebas selisih kas; memberikan **skor kepuasan 9,5 hingga 10 dari 10**.
  * *Paragraf Analisis Tabel 4.2:* Merangkum skor kepuasan **9,5/10** dari Bapak Asep beserta poin keunggulan utama (tema OLED Dark Mode, rekap kas instan tanpa selisih, aturan denda flat 5% yang manusiawi, dan karantina kamar pasca-*checkout*).

* **Poin 2: Hasil Wawancara dengan Arjun – Mahasiswa UNDIP (Paragraf Pengantar + Tabel 4.3 + Paragraf Analisis):**
  * *Paragraf Pengantar:* Menggali pengalaman mahasiswa UNDIP (penghuni 3 tahun di Tembalang) dalam menelusuri katalog kamar dan bertransaksi lewat *m-Banking*.
  * *Tabel 4.3 (10 Aspek Pengujian Arjun):* Menilai tampilan katalog *Neo-Brutalism*, foto fasilitas, video *room tour*, dan lencana hijau "Tersedia" sangat membantu tanpa perlu keliling Tembalang; formulir *stepper* 5 tahap runtut dengan validasi NIK & kontak orang tua; rincian biaya transparan tanpa biaya siluman; *pop-up* BCA VA Midtrans praktis; status langsung berubah hijau "Lunas" dalam hitungan detik tanpa perlu kirim *screenshot* resi ke WA bapak kost; kuitansi PDF A5 berstempel resmi mudah diteruskan ke WA orang tua; dasbor portal simpel; notifikasi WA tanggal 1 sopan; bayar bulanan cukup dari kamar; memberikan **skor 8,5/10** dengan saran penambahan pengingat otomatis WA pada **H-3 sebelum jatuh tempo tanggal 10**.
  * *Paragraf Analisis Tabel 4.3:* Merangkum skor **8,5/10** dari Arjun, manfaat katalog *Neo-Brutalism*, kepraktisan BCA VA & kuitansi A5 untuk laporan orang tua, serta saran pengingat WA H-3.

* **Poin 3: Hasil Wawancara dengan Farrel – Mahasiswa USM (Paragraf Pengantar + Tabel 4.4 + Paragraf Analisis):**
  * *Paragraf Pengantar:* Menyoroti perspektif mahasiswa perantau luar kota yang berkuliah di USM saat mengakses sistem reservasi melalui ponsel Android dan kendala pembayaran non-BCA.
  * *Tabel 4.4 (10 Aspek Pengujian Farrel):* Mengapresiasi katalog online yang membantu anak rantau mengecek kamar tanpa harus datang jauh-jauh ke Semarang; formulir bertahap responsif di layar HP; nominal sewa jelas; verifikasi lunas otomatis menghemat waktu tanpa drama menunggu balasan penjaga kost; kuitansi PDF A5 langsung tersimpan di memori HP; tidak perlu lagi mengantre tarik uang tunai di ATM malam-malam; namun mencatat perlunya digeser sedikit saat mencari tombol riwayat bayar di layar HP dan kendala transfer antar-bank karena sehari-hari menggunakan *e-wallet*/non-BCA; memberikan **skor 7,0/10** dengan saran mengaktifkan opsi **QRIS atau dompet digital (GoPay)**.
  * *Paragraf Analisis Tabel 4.4:* Merangkum skor **7,0/10** dari Farrel, kemudahan akses Android bagi perantau luar kota, penghapusan antrean ATM malam hari, serta harapan penambahan saluran QRIS dan dompet digital.

* **Poin 4: Hasil Wawancara dengan Iman – Mahasiswa UDINUS (Paragraf Pengantar + Tabel 4.5 + Paragraf Analisis):**
  * *Paragraf Pengantar:* Membedah keandalan pencatatan pembayaran digital pada portal penghuni dan transparansi riwayat mutasi bulanan pada layar laptop bersama mahasiswa UDINUS.
  * *Tabel 4.5 (10 Aspek Pengujian Iman):* Menilai info ketersediaan kamar benar-benar *real-time*; tampilan form di laptop rapi (meski 5 tahapan terasa agak panjang saat klik Lanjut); rincian biaya sangat transparan; proses keluarnya nomor BCA VA cepat; sangat menyukai konfirmasi otomatis tanpa resi karena pernah mengalami pengalaman buruk dituduh belum bayar akibat bapak kost lama lupa mencatat di buku; kuitansi PDF A5 berstempel resmi mudah diarsipkan ke Google Drive; tabel riwayat pembayaran tersusun urut per bulan; pengingat WA awal bulan tepat waktu dan tidak canggung seperti ditagih lisan; memberikan **skor 9,0/10** dengan saran teknis menjaga **stabilitas performa *server hosting*** pada tanggal 1 awal bulan saat diakses serentak.
  * *Paragraf Analisis Tabel 4.5:* Merangkum skor **9,0/10** dari Iman atas akuntabilitas pencatatan mutasi otomatis yang menghapus risiko sengketa kuitansi hilang, kerapian arsip PDF A5, serta saran stabilitas *server hosting* di awal bulan.

* **Poin 5: Hasil Wawancara dengan Ammar – Mahasiswa PKN STAN (Paragraf Pengantar + Tabel 4.6 + Paragraf Analisis):**
  * *Paragraf Pengantar:* Meninjau respons mahasiswa baru PKN STAN saat menavigasi alur pemesanan dan pembayaran melalui peramban Safari di iPhone.
  * *Tabel 4.6 (10 Aspek Pengujian Ammar):* Menilai tampilan di Safari iPhone *clean* dan mudah dibandingkan oleh mahasiswa baru; kolom kontak darurat wali membuat orang tua di kampung tenang; rincian biaya pas dengan katalog; *pop-up* BCA VA lancar di Safari dan mudah disalin satu sentuhan; status lunas seketika tanpa *screenshot* bukti transfer; kuitansi PDF A5 tersimpan rapi di dokumen HP; *layout* dasbor nyaman dijangkau satu tangan; sangat terbantu bisa membayar sewa dari luar kota saat libur semester tanpa menitip uang tunai ke teman; memberikan **skor 7,5/10** dengan saran menyematkan **tautan langsung (*direct link*) pada pesan WhatsApp** pengingat tagihan agar langsung mengarah ke halaman pembayaran di portal.
  * *Paragraf Analisis Tabel 4.6:* Merangkum skor **7,5/10** dari Ammar, kenyamanan akses di Safari iPhone, manfaat pembayaran jarak jauh saat libur semester, serta usulan penyematan *direct link* pembayaran pada pesan WhatsApp.

* **Poin 6: Hasil Wawancara dengan Sindhu – Mahasiswa UI (Paragraf Pengantar + Tabel 4.7 + Paragraf Analisis):**
  * *Paragraf Pengantar:* Fokus pada pengujian kenyamanan navigasi dasbor portal hunian dan kecepatan verifikasi pembayaran instan bersama mahasiswa UI.
  * *Tabel 4.7 (10 Aspek Pengujian Sindhu):* Menilai foto katalog kamar sesuai kondisi asli di lapangan; deteksi otomatis nomor HP dan NIK mencegah salah ketik; rincian harga terbuka untuk mengatur bujet bulanan; pembayaran BCA VA praktis (disertai harapan penambahan QRIS/*e-wallet*); verifikasi lunas *real-time* tanpa jeda menghilangkan rasa waswas; kuitansi digital A5 berstempel jauh lebih rapi dibanding nota kertas; pelunasan mandiri via web menghilangkan rasa canggung bertemu langsung pemilik kost; namun sempat membutuhkan beberapa menit saat pertama kali *login* untuk mencari menu kuitansi dan pelaporan keluhan; memberikan **skor 8,0/10** dengan saran penambahan **panduan visual singkat atau tur panduan (*onboarding tour*)** saat pertama kali *login*.
  * *Paragraf Analisis Tabel 4.7:* Merangkum skor **8,0/10** dari Sindhu, kesesuaian visual kamar dengan kondisi fisik, transparansi bujet, penghapusan rasa canggung bayar sewa, serta saran penambahan tur panduan visual singkat bagi pengguna baru.

* **Paragraf Penutup / Sintesis Sub-bab 4.5:**  
  Merangkum hasil keseluruhan evaluasi wawancara pada skala 10, yakni **skor kepuasan 9,5 dari penjaga kost** dan **rata-rata 8,0 dari lima mahasiswa penghuni** (`(8,5 + 7,0 + 9,0 + 7,5 + 8,0) / 5 = 8,0`). Menegaskan bahwa tutup buku bulanan kini selesai dalam hitungan detik tanpa selisih kas fisik, penghuni dapat memesan kamar jarak jauh dan melunasi sewa via *Virtual Account* Midtrans berverifikasi otomatis lengkap dengan kuitansi PDF resmi, serta menghimpun saran pengembangan (kanal QRIS & *e-wallet*, tautan bayar langsung di WhatsApp, dan panduan singkat pengguna baru).

---

## BAB 5: KESIMPULAN DAN SARAN

Bab penutup ini menyajikan sintesis akhir dari seluruh hasil penelitian dan rekayasa sistem, diikuti rekomendasi pengembangan di masa mendatang.

### 5.1 Kesimpulan (1 Paragraf Komprehensif – 5 Pokok Simpulan)
Paragraf kesimpulan merangkum lima capaian utama penelitian secara berkesinambungan:
1. **Keberhasilan Perancangan & Digitalisasi Terpusat:** Perancangan Sistem Informasi Manajemen Kost pada Asri Boarding House berbasis Laravel dan MySQL berhasil mendigitalisasi tata kelola hunian secara terpusat untuk mengatasi kelemahan promosi konvensional, risiko benturan pemesanan kamar ganda (*double booking*), dan kerumitan pembukuan manual.
2. **Keberhasilan Integrasi Pembayaran, Penjadwalan & Notifikasi:** Sistem memadukan gerbang pembayaran digital Midtrans dengan penjadwal tugas otomatis (*cron job*) yang mengeksekusi penerbitan tagihan bulanan setiap awal bulan, perhitungan denda keterlambatan secara konsisten, serta pengiriman notifikasi pengingat melalui WhatsApp dan surat elektronik (*email*).
3. **Keberhasilan Pengujian Fungsionalitas (*Black Box*):** Pengujian penerimaan sistem melalui 60 skenario fungsional *black box* membuktikan seluruh modul bekerja stabil dan akurat sesuai spesifikasi kebutuhan tanpa ditemukannya galat teknis.
4. **Dampak Efektivitas bagi Pengelola Indekos:** Hasil wawancara mendalam bersama penjaga kost mengonfirmasi bahwa aplikasi memangkas waktu rekapitulasi tutup buku bulanan, meniadakan selisih kas fisik melalui verifikasi pembayaran non-tunai instan, dan mempermudah pemantauan okupansi 32 unit kamar.
5. **Dampak Kepuasan bagi Mahasiswa Penghuni Kost:** Evaluasi wawancara bersama lima mahasiswa penghuni kost menunjukkan tingkat kepuasan yang tinggi terhadap kepraktisan pelunasan sewa jarak jauh lewat *virtual account*, keterbukaan rincian tarif tanpa biaya tersembunyi, serta kemudahan akses kuitansi digital resmi.

### 5.2 Saran (1 Paragraf Komprehensif – 5 Pokok Rekomendasi Pengembangan)
Paragraf saran menguraikan lima rekomendasi konkret untuk pengembangan sistem selanjutnya:
1. **Penyematan *Direct Link* Pembayaran di WhatsApp:** Pesan pengingat tagihan WhatsApp disarankan memuat tautan pembayaran langsung agar penghuni dapat segera menyelesaikan kewajiban sewa dalam satu kali klik tanpa harus mencari menu tagihan secara mandiri.
2. **Tur Panduan Visual Interaktif (*Onboarding Tour*):** Pengelola bersama pengembang sebaiknya melengkapi dasbor portal dengan tur panduan visual interaktif guna mempercepat adaptasi pengguna baru dalam mengenali tata letak menu dan fitur layanan.
3. **Perluasan Kanal Pembayaran Digital:** Kanal transaksi pada modul gerbang pembayaran digital dapat diperluas ke berbagai jaringan perbankan nasional tambahan (serta dompet digital/QRIS) untuk memudahkan penghuni yang tidak memiliki rekening pada mitra utama saat ini.
4. **Pemantauan Utilitas Mandiri (Meteran Listrik Digital):** Fitur pemantauan utilitas mandiri berbasis pencatatan meteran listrik digital perlu dipertimbangkan agar kalkulasi pengeluaran energi tiap kamar tercatat lebih transparan bagi penghuni.
5. **Integrasi Kunci Pintu Digital Berbasis IoT:** Pengembangan sistem di masa mendatang dapat mengintegrasikan mekanisme kunci pintu digital berbasis *Internet of Things* (IoT) yang terhubung langsung dengan status keaktifan kontrak sewa di basis data.

---

## DAFTAR PUSTAKA (`[1]` s.d. `[30]`)
Naskah ditutup dengan 30 referensi ilmiah berformat IEEE yang mendukung seluruh argumen di Bab 1 hingga Bab 3:
* **Referensi `[1]`–`[5]`:** Mendukung latar belakang masalah di Bab 1 (sistem pemesanan kost web Laravel, e-kost, monitoring rumah kos & pembayaran, integrasi Midtrans *payment gateway*, serta aplikasi kas berbasis web & WhatsApp Gateway).
* **Referensi `[6]`–`[27]`:** Mendukung landasan teori di Bab 2 (SIM `[6, 7]`, E-Commerce B2C & PMS `[8, 9]`, Web Application `[10, 11]`, UI/UX & Design System `[12, 13]`, Laravel MVC `[14, 15]`, RDBMS & Normalisasi `[16, 17]`, MySQL & Indexing `[18, 19]`, Midtrans Payment Gateway `[20, 21]`, WhatsApp Gateway Fonnte `[22, 23]`, SMTP Mail Gateway & Email Notification `[24, 25]`, serta Cron Job & Notifikasi Terjadwal `[26, 27]`).
* **Referensi `[28]`–`[30]`:** Mendukung metodologi penelitian di Bab 3 (Metode R&D `[28]`, Metode Penelitian Kualitatif/Kuantitatif/R&D Sugiyono `[29]`, dan SDLC *Waterfall* `[30]`).

