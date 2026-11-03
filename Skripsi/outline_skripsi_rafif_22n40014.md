# OUTLINE DAN BEDAH LENGKAP NASKAH SKRIPSI
**Judul Skripsi:** SISTEM INFORMASI MANAJEMEN KOST TERINTEGRASI PAYMENT GATEWAY PADA ASRI BOARDING HOUSE  
**Penulis:** Rafif Arsya Pradiva  
**NIM:** 22.N4.0014  
**Program Studi / Konsentrasi:** Sistem Informasi / E-Commerce  
**Fakultas / Universitas:** Fakultas Ilmu Komputer / Universitas Katolik Soegijapranata Semarang (2026)  
**Dosen Pembimbing 1:** Ir. Andre Kurniawan Pamudji, S.Kom., M.Ling.  
**Dosen Pembimbing 2:** Erdhi Widyarto Nugroho, S.T., M.T.  

---

## RINGKASAN BAGIAN AWAL (PRA-BAB)
Bagian pembuka sebelum Bab 1 memuat kelengkapan administratif dan intisari penelitian:
1. **Halaman Sampul dan Judul:** Menampilkan judul penelitian, identitas penulis (`Rafif Arsya Pradiva, NIM 22.N4.0014`), serta nama institusi.
2. **Halaman Pengesahan (10 April 2026):** Memuat tanda tangan persetujuan Dosen Pembimbing 1, Dosen Pembimbing 2, dan Ketua Program Studi Sistem Informasi (`Dr. Albertus Dwiyoga Widiantoro, S.Kom., M.Kom.`).
3. **Halaman Pernyataan Keaslian dan Persetujuan Publikasi:** Berisi pernyataan bebas plagiasi di atas meterai serta penyerahan Hak Bebas Royalti Non-eksklusif kepada Unika Soegijapranata untuk keperluan repositori kampus.
4. **Kata Pengantar:**
   - **Paragraf 1:** Penulis menyampaikan rasa syukur atas tuntasnya laporan skripsi sebagai syarat memperoleh gelar Sarjana Komputer.
   - **Paragraf 2 dan Daftar Poin (1 sampai 8):** Rangkaian ucapan terima kasih ditujukan kepada Dekan Fakultas Ilmu Komputer, Kaprodi Sistem Informasi, kedua dosen pembimbing, jajaran dosen dan staf, orang tua beserta keluarga, rekan mahasiswa, serta seluruh pihak yang telah membantu jalannya penelitian.
   - **Paragraf 3:** Penulis menutup pengantar dengan sikap terbuka terhadap kritik maupun saran demi penyempurnaan karya ilmiah ini.
5. **Abstrak / Abstract:**
   - **Paragraf 1 (Masalah dan Metode Solusi):** Pengelolaan 32 unit kamar di Asri Boarding House selama ini masih mengandalkan buku besar fisik dan pesan singkat manual. Cara lama tersebut kerap memicu benturan reservasi, keterlambatan penagihan sewa, hingga selisih kas akibat tutup buku yang memakan waktu berhari-hari. Sebagai jalan keluar, penelitian ini membangun aplikasi web dengan metode *Waterfall* berbasis Laravel dan MySQL (*pessimistic row-level locking*) yang dipadukan dengan gerbang pembayaran Midtrans Snap serta pengingat otomatis melalui WhatsApp dan surel.
   - **Paragraf 2 (Hasil Pengujian dan Evaluasi):** Pengujian 60 skenario *black box* membuktikan seluruh modul berjalan stabil tanpa kegagalan transaksi maupun validasi data. Evaluasi bersama pengelola indekos menunjukkan waktu tutup buku bulanan terpangkas dari beberapa hari menjadi instan dan bebas selisih kas. Sementara itu, wawancara bersama mahasiswa penghuni mengonfirmasi kepuasan tinggi atas kemudahan pemesanan kamar jarak jauh, pembayaran *Virtual Account*, serta kuitansi digital yang transparan.
   - **Kata Kunci:** Asri Boarding House, gerbang pembayaran Midtrans, Laravel, penguncian pesimis, sistem informasi manajemen kost.

---

## BAB 1: PENDAHULUAN

Bab pertama menguraikan latar belakang masalah di lapangan, rumusan masalah, serta tujuan yang ingin dicapai dari pembangunan sistem.

### 1.1 Latar Belakang (5 Paragraf)
* **Paragraf 1 (Kebutuhan Hunian di Tembalang dan Reservasi Web):**  
  Kawasan pendidikan Tembalang di Kota Semarang selalu dipadati mahasiswa perantau setiap tahun. Calon penghuni dari luar kota membutuhkan informasi kamar sewa yang praktis dan transparan, sedangkan promosi konvensional menyulitkan mereka mengecek sisa kamar kosong tanpa datang langsung ke lokasi. Kehadiran aplikasi pemesanan berbasis web menjadi solusi untuk menjembatani kendala jarak sekaligus menertibkan pencatatan reservasi sejak awal `[1]`.
* **Paragraf 2 (Pencatatan Manual dan Risiko *Double Booking* di Asri Boarding House):**  
  Kondisi serupa dialami Asri Boarding House Tembalang yang mengelola 32 unit kamar sewa dalam tipe VIP, Deluxe, dan Standar. Karena pendataan kamar masih ditulis di buku besar fisik, penjaga kost kesulitan memperbarui status keterisian unit secara cepat. Akibatnya, benturan pesanan ganda (*double booking*) sering terjadi saat beberapa calon penghuni memesan kamar yang sama dalam waktu berdekatan `[2]`.
* **Paragraf 3 (Kendala Penagihan Tanggal 10 dan Perhitungan Denda Manual):**  
  Hambatan berikutnya muncul pada siklus penagihan sewa bulanan. Pengingat jatuh tempo setiap tanggal 10 beserta perhitungan dendanya masih dikirim satu per satu lewat pesan pribadi kepada penghuni. Cara manual ini membuat jadwal penagihan kerap terlewat dan memicu penumpukan tunggakan, padahal fitur penjadwalan otomatis pada sistem informasi dapat menertibkan siklus tagihan bulanan secara konsisten `[3]`.
* **Paragraf 4 (Kerumitan Verifikasi Bukti Transfer dan Tutup Buku Akhir Bulan):**  
  Pencatatan keuangan pun berjalan lambat karena pemeriksaan pembayaran masih bergantung pada foto tangkapan layar transfer bank dan uang tunai. Saat tutup buku di akhir bulan, penjaga kost harus menghabiskan waktu berhari-hari untuk mencocokkan nota belanja operasional dengan bukti kas masuk. Integrasi gerbang pembayaran digital (*payment gateway*) mampu mengotomatisasi verifikasi transaksi non-tunai sehingga menutup celah selisih kas fisik `[4]`.
* **Paragraf 5 (Sintesis Solusi Sistem Informasi Manajemen Kost):**  
  Berangkat dari masalah tersebut, Asri Boarding House memerlukan sistem manajemen terpusat yang menyatukan reservasi daring, penjadwalan tagihan otomatis, gerbang pembayaran Midtrans, serta pengingat tagihan via WhatsApp dan *email* secara *real-time* `[5]`. Penelitian ini hadir untuk membangun "Sistem Informasi Manajemen Kost Terintegrasi *Payment Gateway* pada Asri Boarding House" guna mewujudkan tata kelola hunian yang rapi, efisien, dan bebas selisih kas.

### 1.2 Rumusan Masalah (1 Paragraf Pengantar dan 3 Poin)
* **Paragraf Pengantar:** Tiga pertanyaan penelitian dirumuskan langsung dari permasalahan operasional yang telah dipetakan pada latar belakang.
* **Poin 1 (Perancangan Arsitektur):** Bagaimana merancang arsitektur Sistem Informasi Manajemen Kost Terintegrasi *Payment Gateway* pada Asri Boarding House sebagai solusi atas kendala pemesanan unit, pencatatan pembukuan manual, dan keterlambatan penagihan sewa?
* **Poin 2 (Implementasi Teknis):** Bagaimana mengimplementasikan sistem tersebut menggunakan kerangka kerja Laravel yang dipadukan dengan gerbang pembayaran Midtrans, penjadwalan tagihan otomatis berkala, serta pengiriman notifikasi melalui WhatsApp dan *email*?
* **Poin 3 (Evaluasi Efektivitas):** Bagaimana hasil penerapan dan efektivitas sistem berdasarkan evaluasi wawancara bersama penjaga kost serta penghuni kost pada operasional harian Asri Boarding House?

### 1.3 Tujuan Penelitian (1 Paragraf Pengantar dan 3 Poin)
* **Paragraf Pengantar:** Tujuan penelitian disusun selaras untuk menjawab ketiga butir rumusan masalah di atas.
* **Poin 1 (Tujuan Perancangan):** Merancang arsitektur Sistem Informasi Manajemen Kost Terintegrasi *Payment Gateway* pada Asri Boarding House sebagai solusi terpadu atas kendala pemesanan unit, pencatatan pembukuan manual, dan keterlambatan penagihan sewa.
* **Poin 2 (Tujuan Implementasi):** Mengimplementasikan sistem tersebut menggunakan kerangka kerja Laravel yang dipadukan dengan gerbang pembayaran Midtrans, penjadwalan tagihan otomatis berkala, serta pengiriman notifikasi melalui WhatsApp dan *email*.
* **Poin 3 (Tujuan Evaluasi):** Menganalisis hasil penerapan dan efektivitas sistem berdasarkan evaluasi wawancara bersama penjaga kost serta penghuni kost pada operasional harian Asri Boarding House.

---

## BAB 2: TINJAUAN PUSTAKA

Bab kedua mengulas 11 landasan teori dan komponen teknologi yang menopang perancangan serta pengembangan sistem informasi manajemen kost.

### 2.1 Sistem Informasi Manajemen (2 Paragraf)
* **Paragraf 1 (Konsep Dasar dan Fungsi Manajerial SIM):** Sistem Informasi Manajemen (SIM) menyatukan perangkat keras, perangkat lunak, prosedur kerja, dan sumber daya manusia untuk mengolah data transaksi mentah menjadi informasi yang siap pakai. Pengolahan data yang terstruktur membantu pengelola memantau kinerja organisasi secara menyeluruh dan mengambil keputusan secara tepat `[6]`.
* **Paragraf 2 (Peran SIM pada Pengelolaan Indekos):** Dalam operasional indekos, SIM menjadi pusat data tunggal yang menghimpun inventaris kamar, biodata penghuni, riwayat pembayaran sewa, hingga laporan kas operasional. Penyimpanan terpusat ini mencegah salah catat di buku besar, menghindarkan *double booking*, mempermudah pemantauan masa sewa, dan menyajikan laporan keuangan yang transparan `[7]`.

### 2.2 E-Commerce B2C (2 Paragraf)
* **Paragraf 1 (Model Transaksi B2C):** Perdagangan elektronik *Business-to-Consumer* (B2C) mempertemukan penyedia jasa langsung dengan konsumen akhir melalui internet tanpa perantara distribusi konvensional. Pelanggan dapat melihat katalog, memilih layanan, dan menyelesaikan pembayaran secara mandiri kapan saja berkat rincian harga yang terbuka `[8]`.
* **Paragraf 2 (Penerapan B2C pada Penyewaan Indekos):** Situs web B2C menghubungkan pengelola indekos langsung dengan calon penghuni sehingga pemilik usaha menerima pendapatan sewa secara utuh tanpa potongan komisi agen. Di sisi lain, calon penghuni dapat mengecek sisa kamar kosong, memesan unit dari luar kota, dan menerima tagihan bulanan secara teratur `[9]`.

### 2.3 Web Application (2 Paragraf)
* **Paragraf 1 (Arsitektur *Client-Server* dan *Thin Client*):** Aplikasi web bekerja di atas arsitektur *client-server* melalui protokol HTTP atau HTTPS. Peramban web pada komputer maupun ponsel pintar bertindak sebagai *thin client* penampil antarmuka, sedangkan *web server* mengeksekusi logika bisnis dan kueri basis data tanpa mewajibkan pengguna memasang aplikasi tambahan di perangkat masing-masing `[10]`.
* **Paragraf 2 (Pembaruan *Real-Time* dan Struktur Modular):** Pemeliharaan aplikasi web sangat praktis karena pembaruan fitur cukup dilakukan satu kali di *server* pusat. Pemisahan komponen tampilan, logika bisnis, dan penyimpanan data secara modular turut mempercepat waktu respons transaksi sekaligus memudahkan pengembangan fitur baru di masa depan `[11]`.

### 2.4 UI & UX (2 Paragraf)
* **Paragraf 1 (Antarmuka Visual dan Gaya *Neo-Brutalism*):** *User Interface* (UI) merupakan media visual tempat pengguna berinteraksi dengan perangkat lunak. Penerapan gaya *Neo-Brutalism* yang bercirikan garis tepi tebal, bayangan berkontras tegas, dan tata letak geometris lugas membuat tombol aksi, formulir input, serta status transaksi langsung terlihat menonjol dan mudah dikenali `[12]`.
* **Paragraf 2 (Kenyamanan Alur *User Experience*):** *User Experience* (UX) berfokus pada keringkasan alur kerja agar pengguna dapat menyelesaikan keperluannya tanpa kebingungan. Penataan menu yang terarah serta penyajian data bertingkat membantu pengguna menuntaskan transaksi dalam beberapa langkah sederhana dan menghemat waktu operasional harian `[13]`.

### 2.5 Laravel (2 Paragraf)
* **Paragraf 1 (Pola MVC, Eloquent ORM, Blade, dan Keamanan):** Laravel merupakan *framework* PHP berarsitektur *Model-View-Controller* (MVC) yang memisahkan pengolahan data, tampilan antarmuka, dan logika bisnis. Kerangka kerja ini menyediakan *Eloquent ORM* untuk pemodelan tabel basis data, mesin templat *Blade*, serta perlindungan bawaan terhadap serangan CSRF (*Cross-Site Request Forgery*) dan injeksi SQL `[14]`.
* **Paragraf 2 (Modularitas Kode dengan *Service Layer*):** Pada aplikasi web berskala menengah hingga besar, logika bisnis yang kompleks dipisahkan dari *controller* ke dalam lapisan khusus (*Service Layer*). Pemisahan tanggung jawab kode ini mencegah penumpukan instruksi pada satu berkas, mempermudah pengujian fitur, dan menjaga kemudahan perawatan jangka panjang `[15]`.

### 2.6 RDBMS (2 Paragraf)
* **Paragraf 1 (Relasi Tabel dan Kepatuhan ACID):** *Relational Database Management System* (RDBMS) menyimpan data terstruktur ke dalam tabel dua dimensi yang saling terhubung melalui *primary key* dan *foreign key*. Arsitektur relasional ini menjaga integritas referensial antartabel dan menerapkan prinsip transaksi ACID (*Atomicity, Consistency, Isolation, Durability*) agar data operasional bebas dari korupsi `[16]`.
* **Paragraf 2 (Normalisasi hingga 3NF):** Skema tabel dirancang melalui tahapan normalisasi hingga Bentuk Normal Ketiga (3NF) untuk memecah tabel kompleks menjadi struktur yang ramping. Langkah ini mencegah duplikasi data serta menghindarkan anomali saat proses tambah, ubah, maupun hapus data `[17]`.

### 2.7 MySQL Database (2 Paragraf)
* **Paragraf 1 (Arsitektur *Multithread* dan Operasi CRUD):** MySQL adalah RDBMS sumber terbuka berbasis SQL dengan arsitektur *client-server multithread* yang mampu menangani proses baca dan tulis data secara bersamaan. Penggunaan memori yang efisien serta integrasi bawaannya dengan Laravel membuat eksekusi operasi CRUD berjalan cepat dan stabil `[18]`.
* **Paragraf 2 (Mesin Penyimpanan InnoDB dan *Row-Level Locking*):** Melalui mesin penyimpanan InnoDB, MySQL mendukung transaksi ACID dan penguncian tingkat baris (*row-level locking*). Fitur ini mencegah bentrokan data ketika beberapa pengguna memesan kamar yang sama di waktu bersamaan, didukung penggunaan *indexing* dan *foreign key constraints* untuk menjaga kecepatan pencarian data `[19]`.

### 2.8 Payment Gateway (3 Paragraf)
* **Paragraf 1 (Peran Perantara Pembayaran Digital dan Midtrans):** *Payment gateway* menghubungkan aplikasi web langsung dengan jaringan perbankan dan dompet digital. Layanan Midtrans memfasilitasi pembayaran via *Virtual Account*, QRIS, *e-wallet*, hingga kartu kredit dalam satu pintu, sehingga pengelola tidak perlu membuka rekening di tiap bank dan penyewa bebas dari keharusan mengunggah foto bukti transfer manual `[20]`.
* **Paragraf 2 (Lingkungan *Sandbox* dan *Production*):** Dalam pengembangannya, *payment gateway* menyediakan mode *Sandbox* untuk simulasi pengujian transaksi dan mode *Production* untuk transaksi uang asli. Peralihan antarmode tersebut cukup diatur melalui berkas konfigurasi (*environment variable*) tanpa perlu mengubah kode program utama.
* **Paragraf 3 (Integrasi API, *Webhook Callback*, dan Validasi SHA-512):** Pertukaran data transaksi berjalan melalui API dan mekanisme *webhook callback* yang diamankan dengan *signature key* berstandar kriptografi SHA-512. Begitu bank memverifikasi pembayaran, *webhook* langsung mengubah status tagihan di basis data menjadi lunas secara otomatis tanpa pengecekan mutasi manual `[21]`.

### 2.9 WhatsApp Gateway API (2 Paragraf)
* **Paragraf 1 (Otomasi Pesan Instan dan *Open Rate* Tinggi):** API *gateway* WhatsApp memungkinkan aplikasi web mengirim pesan notifikasi otomatis ke nomor ponsel pengguna tanpa mengoperasikan perangkat fisik. Saluran ini dipilih karena tingkat keterbacaan pesannya (*open rate*) jauh lebih tinggi dan lebih cepat direspons dibandingkan SMS maupun surel `[22]`.
* **Paragraf 2 (Pertukaran Data *HTTP POST* Terjadwal):** Pengiriman pesan berjalan melalui permintaan *HTTP POST* berautentikasi token khusus. Otomatisasi ini membebaskan pengelola dari rutinitas mengetik pesan pengingat satu per satu, sehingga informasi tagihan dan konfirmasi reservasi selalu diterima penghuni tepat waktu `[23]`.

### 2.10 Email Gateway API (2 Paragraf)
* **Paragraf 1 (Protokol SMTP dan Keamanan TLS):** Pengiriman surat elektronik menggunakan standar *Simple Mail Transfer Protocol* (SMTP) yang dilindungi enkripsi TLS (*Transport Layer Security*). Di dalam sistem informasi, surel berperan penting sebagai saluran pengiriman dokumen administrasi resmi yang mudah disimpan dan diarsipkan pengguna `[24]`.
* **Paragraf 2 (Layanan *Mail Relay* untuk Bukti Resmi):** Kerangka kerja web memanfaatkan layanan *mail relay* terenkripsi untuk meneruskan bukti pembayaran, *invoice* digital, dan tautan pemulihan akun secara otomatis. Reputasi pengirim yang terjaga mencegah dokumen masuk ke folder *spam* sekaligus melengkapi notifikasi instan WhatsApp `[25]`.

### 2.11 Cronjob (3 Paragraf)
* **Paragraf 1 (Penjadwal Tugas Berbasis Waktu di Server):** *Cron job* adalah fitur penjadwal tugas berbasis waktu pada sistem operasi keluarga Unix yang mengeksekusi perintah program secara mandiri di latar belakang *server*. Penjadwal ini sangat dibutuhkan untuk menjalankan pemeliharaan data, penerbitan tagihan, dan pemeriksaan denda berkala tepat sesuai waktu yang ditentukan `[26]`.
* **Paragraf 2 (Otomasi Pemeriksaan Jatuh Tempo Tagihan):** Konfigurasi *cron job* mengeksekusi perintah konsol secara rutin untuk memeriksa tanggal jatuh tempo *invoice* di basis data, lalu memicu pengiriman pengingat tagihan melalui gerbang WhatsApp dan *email* `[27]`.
* **Paragraf 3 (*Task Scheduling* Tingkat *Framework* dan Prinsip Idempoten):** Pada tingkat *framework*, cukup satu *daemon cron* sistem operasi yang diaktifkan untuk menggerakkan seluruh jadwal tugas di latar belakang. Penerapan prinsip idempoten pada skrip penjadwal menjamin eksekusi berulang tidak akan menerbitkan tagihan ganda.

---

## BAB 3: METODE PENELITIAN

Bab ketiga memaparkan lokasi penelitian, pendekatan metodologi, sumber dan cara pengumpulan data, tahapan pengembangan perangkat lunak, serta kerangka pemikiran.

### 3.1 Objek Penelitian (1 Paragraf)
* **Paragraf 1:** Penelitian ini dilaksanakan di Asri Boarding House yang beralamat di Jalan Maera Sari Nomor 1, Tembalang, Kota Semarang. Indekos ini mengelola 32 unit kamar dalam tipe VIP, Deluxe, dan Standar bagi mahasiswa maupun pekerja. Kajian difokuskan pada alur reservasi kamar, penagihan sewa bulanan, serta pembukuan kas operasional yang sebelumnya masih dicatat manual di buku besar fisik.

### 3.2 Jenis Penelitian (2 Paragraf)
* **Paragraf 1 (Pendekatan *Research and Development*):** Penelitian ini menggunakan metode Penelitian dan Pengembangan (*Research and Development* / R&D) berbasis rekayasa perangkat lunak. Pendekatan R&D dipilih karena tujuan utama penelitian adalah merancang, membangun, dan menguji aplikasi fungsional guna menyelesaikan permasalahan operasional nyata di lapangan `[28]`.
* **Paragraf 2 (Studi Kasus Tunggal dan Evaluasi Kualitatif):** Metode R&D tersebut dipadukan dengan studi kasus tunggal di Asri Boarding House. Tahap awal menggunakan analisis deskriptif kualitatif untuk memetakan kendala kas manual, jatuh tempo tagihan, dan pemesanan kamar, sedangkan tahap akhir melakukan pengujian fungsi serta evaluasi penggunaan sistem bersama pengguna.

### 3.3 Sumber Data (1 Paragraf Pengantar dan 2 Poin Utama)
* **Paragraf Pengantar:** Sumber data dibagi menjadi data primer dan data sekunder untuk memetakan proses bisnis sekaligus mendasari perancangan modul aplikasi.
* **Poin 1: Data Primer** (Diperoleh langsung dari wawancara mendalam di lokasi penelitian):
  * *Sub-poin Penjaga kost:* Memberikan keterangan mengenai prosedur operasional harian, alur pencatatan sewa kamar, dan kendala pembukuan pada buku kas fisik.
  * *Sub-poin Penghuni kost:* Memberikan informasi seputar pengalaman memesan kamar, kendala pembayaran sewa bulanan, dan kebutuhan bukti transaksi yang rapi.
* **Poin 2: Data Sekunder** (Diperoleh dari dokumen internal dan literatur pendukung):
  * *Sub-poin 1:* Arsip pembukuan internal meliputi buku besar manual, bukti transfer sewa, nota pengeluaran operasional, dan daftar tarif 32 unit kamar.
  * *Sub-poin 2:* Dokumentasi teknis API Midtrans, *gateway* WhatsApp Fonnte, dan dokumentasi resmi Laravel.
  * *Sub-poin 3:* Artikel jurnal ilmiah, buku referensi, serta publikasi akademik terkait sistem informasi indekos dan *payment gateway*.

### 3.4 Teknik Pengumpulan Data (2 Paragraf dan 2 Poin Narasumber)
* **Paragraf 1 (Wawancara Mendalam Kualitatif):** Pengumpulan data menerapkan teknik wawancara mendalam di Asri Boarding House `[29]` untuk menggali kebutuhan operasional, kendala pencatatan manual, hingga tanggapan pengguna setelah mencoba aplikasi web yang dibangun.
* **Paragraf 2 dan Rincian Narasumber (Tatap Muka, Rekaman Audio, dan Transkripsi):** Wawancara dilakukan secara tatap muka dengan bantuan alat perekam suara, lalu disalin menjadi transkripsi tertulis agar tidak ada informasi yang terlewat saat dianalisis. Pengambilan data ini melibatkan penjaga kost dan penghuni kost:
  * *Sub-poin Penjaga kost:* Pembahasan berfokus pada evaluasi pembukuan kas, pemantauan sisa kamar dari 32 unit yang tersedia, kepraktisan penagihan otomatis, dan efisiensi kerja dibanding buku besar manual.
  * *Sub-poin Penghuni kost:* Pembahasan berfokus pada pengalaman memesan kamar, kejelasan rincian tagihan bulanan, kemudahan membayar sewa lewat *payment gateway*, dan kejelasan bukti pembayaran digital.

### 3.5 Metode Pengembangan Sistem (3 Paragraf dan Gambar 3.1)
* **Paragraf 1 (Tahapan Berurutan SDLC *Waterfall*):** Pengembangan perangkat lunak menerapkan metode *Waterfall* dalam *Software Development Life Cycle* (SDLC) `[30]`. Setiap tahapan mulai dari analisis kebutuhan, perancangan antarmuka, penulisan kode, pengujian, hingga pemeliharaan harus diselesaikan secara tuntas sebelum beralih ke tahap berikutnya.
* **Paragraf 2 (Alasan Pemilihan Metode *Waterfall*):** Metode ini dipilih karena aturan bisnis pengelolaan indekos, seperti alur pemesanan kamar dan jadwal penagihan sewa bulanan, sudah terpetakan dengan jelas sejak awal melalui studi lapangan. Spesifikasi yang matang membuat pembangunan sistem dan integrasi *payment gateway* berjalan terarah tanpa perubahan di tengah jalan.
* **Paragraf 3 (Uraian Gambar 3.1 Tahapan Metode SDLC *Waterfall*):** Lima tahapan rekayasa sistem pada Gambar 3.1 dijalankan secara berurutan:
  1. *Requirement gathering and analysis:* Menghimpun kebutuhan operasional indekos melalui wawancara mendalam.
  2. *Design:* Menyusun rancangan antarmuka visual, alur kerja sistem, dan skema basis data.
  3. *Implementation:* Menulis kode program menggunakan *framework* Laravel dan mengintegrasikan *payment gateway* Midtrans.
  4. *Integration & testing / Verification:* Menguji seluruh fitur dengan metode *black box testing* untuk memastikan transaksi pembayaran berjalan bebas kendala.
  5. *Operation & maintenance:* Mengunggah aplikasi ke *server hosting*, memantau operasional harian, dan melakukan perbaikan berkala.

### 3.6 Kerangka Pemikiran (2 Paragraf dan Gambar 3.2)
* **Paragraf 1 (Alur Pikir R&D):** Kerangka pemikiran disusun mengikuti pendekatan R&D untuk menghubungkan masalah operasional di lapangan dengan solusi perancangan aplikasi web secara terukur, mulai dari identifikasi masalah hingga evaluasi akhir.
* **Paragraf 2 (Uraian Gambar 3.2 Kerangka Pemikiran):** Bagan pada Gambar 3.2 membagi alur penelitian ke dalam empat komponen utama:
  1. *Input (Masukan):* Merangkum kondisi operasional indekos, masalah pencatatan manual, dan rincian kebutuhan sistem.
  2. *Process (Proses):* Mencakup tahapan analisis dan perancangan, penulisan kode program, serta pengujian fitur aplikasi.
  3. *Output (Keluaran):* Menghasilkan produk perangkat lunak manajemen kost yang telah teruji dan siap dioperasikan.
  4. *Outcome (Dampak):* Mewujudkan pembukuan kas yang tertata rapi serta proses pembayaran sewa yang praktis bagi penjaga maupun penghuni kost.

---

## BAB 4: HASIL DAN PEMBAHASAN

Bab keempat menyajikan hasil perancangan teknis, implementasi 36 antarmuka pengguna, implementasi 9 modul kode program inti, pengujian 60 skenario *black box*, serta evaluasi wawancara bersama 6 narasumber.

### 4.1 Perancangan Aplikasi
* **Paragraf Pengantar Subbab 4.1:** Tahap perancangan menerjemahkan kebutuhan operasional Asri Boarding House ke dalam cetak biru teknis sebelum penulisan kode dimulai. Pemodelan ini meliputi *use case diagram*, *Entity Relationship Diagram* (ERD), *activity diagram*, dan *flowchart* sistem.

#### 4.1.1 Use Case Diagram (2 Paragraf Pengantar, Gambar 4.1, dan 3 Poin Aktor)
* **Paragraf 1:** Batasan hak akses dan cakupan fitur bagi setiap pengguna di dalam aplikasi web dipetakan melalui *use case diagram* pada Gambar 4.1.
* **Paragraf 2:** Sistem melibatkan tiga aktor utama, yaitu calon penyewa, admin (penjaga kost), dan penyewa aktif, dengan pembagian wewenang yang terpisah agar alur pemesanan hingga penagihan bulanan tetap terkontrol.
* **Poin 1: Calon Penyewa (1 Paragraf):** Merupakan pengunjung publik yang mencari informasi kamar kosong, fasilitas, dan tarif sewa di situs web. Untuk memesan unit secara daring tanpa datang ke lokasi, calon penghuni wajib mendaftarkan akun terlebih dahulu, lalu melunasi uang muka lewat Midtrans agar status kamar langsung terkunci dan terhindar dari *double booking*.
* **Poin 2: Admin / Penjaga Kost (1 Paragraf):** Memegang kendali penuh atas operasional harian indekos melalui dasbor pengelola. Admin mengatur ketersediaan 32 unit kamar, memperbarui harga sewa, memeriksa identitas penghuni baru, memantau pelunasan otomatis tanpa perlu mengecek mutasi rekening manual, serta menindaklanjuti laporan kerusakan fasilitas.
* **Poin 3: Penyewa Kost / Penyewa Aktif (1 Paragraf):** Merupakan penghuni resmi yang mengakses portal mandiri untuk melihat rincian *invoice* bulanan dan tanggal jatuh tempo. Pelunasan sewa maupun denda keterlambatan dilakukan lewat kanal pembayaran digital yang otomatis menerbitkan kuitansi elektronik, dilengkapi fitur pengiriman tiket keluhan fasilitas kamar.

#### 4.1.2 Entity Relationship Diagram / ERD (2 Paragraf Pengantar, Gambar 4.2 (a) & (b), dan 2 Poin Modul)
* **Paragraf 1:** Struktur basis data relasional dirancang untuk menampung seluruh transaksi operasional kost. Mengingat banyaknya tabel dan relasi atribut, diagram ERD pada Gambar 4.2 dipecah menjadi bagian (a) untuk modul transaksi dan hunian inti, serta bagian (b) untuk modul operasional dan layanan informasi.
* **Paragraf 2:** Skema basis data tersebut dikelompokkan ke dalam dua modul fungsional yang saling terhubung.
* **Poin 1: Modul Pengelolaan Kamar, Pengguna, Reservasi, dan Pembayaran pada Gambar 4.2 (a) (2 Paragraf):**
  * *Paragraf 1 (Tabel Master dan Penempatan Hunian):* Pendataan hunian bertumpu pada relasi antara tabel `users` (akun pengelola dan penyewa), tabel `kamar` (data fisik 32 unit kamar, tarif, dan status), serta tabel penghubung `penyewa`. Melalui relasi satu-ke-banyak (1:N), tabel `penyewa` mengunci *snapshot* harga sewa riil, tanggal masuk, batas akhir kontrak, dan nomor darurat wali agar nilai kontrak tidak berubah saat tarif kamar diperbarui di masa depan.
  * *Paragraf 2 (Tabel Transaksi Keuangan):* Siklus keuangan dikelola oleh tabel `reservasi`, `tagihan`, dan `pembayaran`. Pengajuan sewa daring dicatat pada tabel `reservasi` dengan kode unik, diteruskan menjadi rincian uang muka atau sewa bulanan pada tabel `tagihan`, lalu sinyal *webhook* Midtrans otomatis mencatat nomor transaksi perbankan beserta waktu pelunasan ke tabel `pembayaran`.
* **Poin 2: Modul Layanan Keluhan, Operasional Kost, Komunikasi, dan Informasi pada Gambar 4.2 (b) (2 Paragraf):**
  * *Paragraf 1 (Tabel Keluhan, Pengeluaran, dan Log Notifikasi):* Laporan kerusakan fasilitas beserta foto buktinya ditampung pada tabel `keluhan`, sedangkan biaya rutin seperti token listrik bersama, air, dan jasa teknisi dicatat pada tabel `pengeluaran` untuk menghitung kas bersih. Sementara itu, tabel `log_notifikasi` menyimpan rekam jejak pengiriman pengingat otomatis melalui WhatsApp Fonnte dan *email* SMTP.
  * *Paragraf 2 (Tabel Komunikasi Tamu dan Informasi Publik):* Konsultasi pengunjung tanpa akun dilayani oleh tabel `guest_chat_threads` yang berelasi satu-ke-banyak (1:N) dengan `chat_messages`. Untuk melengkapi informasi beranda publik, tabel `customer_reviews` menyimpan ulasan penghuni, sedangkan tabel `peraturan` dan `faqs` memuat tata tertib hunian serta daftar tanya-jawab umum.

#### 4.1.3 Activity Diagram (1 Paragraf Pengantar dan 3 Poin Aktor)
* **Paragraf Pengantar:** Urutan proses bisnis dan interaksi antara pengguna, aplikasi web, serta layanan pihak ketiga dijabarkan ke dalam tiga *activity diagram* (Gambar 4.3, Gambar 4.4, dan Gambar 4.5) mengikuti peran masing-masing aktor.
* **Poin 1: Calon Penyewa pada Gambar 4.3 (2 Paragraf):**
  * *Paragraf 1:* Diagram ini memetakan alur pemesanan unit kamar secara mandiri dari jarak jauh, mulai dari pemilihan kamar di katalog, pengisian biodata sewa, hingga pelunasan uang muka.
  * *Paragraf 2:* Mengacu pada Gambar 4.3, calon penyewa memilih salah satu unit kosong dari 32 kamar yang tersedia, lalu aplikasi memeriksa kelengkapan data sebelum meminta token transaksi ke *server* Midtrans Snap. Setelah pemesan melunasi uang muka melalui transfer *Virtual Account* atau dompet digital, sistem langsung mengunci status kamar di basis data dan mengirimkan bukti reservasi ke nomor WhatsApp pemesan via Fonnte.
* **Poin 2: Admin (Penjaga Kost) pada Gambar 4.4 (2 Paragraf):**
  * *Paragraf 1:* Aktivitas pada sisi pengelola menitikberatkan pada pengaturan inventaris kamar, verifikasi penghuni baru, dan penyusunan pembukuan kas bulanan.
  * *Paragraf 2:* Berdasarkan Gambar 4.4, penjaga kost masuk ke dasbor admin untuk memantau keterisian 32 unit kamar, memperbarui tarif sewa, dan memeriksa berkas identitas penghuni baru. Sistem kemudian memperbarui data hunian serta mengirimkan tagihan bulanan otomatis ke WhatsApp penyewa, sehingga di akhir bulan admin cukup memeriksa rekapitulasi pembayaran masuk dan mencatat pengeluaran rutin untuk menutup laporan keuangan.
* **Poin 3: Penyewa Aktif pada Gambar 4.5 (2 Paragraf):**
  * *Paragraf 1:* Diagram ketiga menggambarkan aktivitas penghuni resmi saat menggunakan portal layanan mandiri untuk memeriksa tagihan rutin, melunasi sewa non-tunai, dan melaporkan kerusakan fasilitas.
  * *Paragraf 2:* Sebagaimana tampak pada Gambar 4.5, penghuni yang masuk ke portal penyewa dapat meninjau rincian tagihan mendekati jatuh tempo dan menekan tombol bayar untuk memunculkan *pop-up* Midtrans Snap tanpa berpindah halaman. Pelunasan tagihan otomatis memperpanjang masa aktif sewa di sistem, diikuti akses unduh kuitansi PDF resmi maupun menu pengajuan keluhan sarana kamar.

#### 4.1.4 Flowchart Sistem (2 Paragraf dan Gambar 4.6)
* **Paragraf 1:** Logika kerja sistem beserta titik pengambilan keputusan (*decision*) dari tahap pemesanan awal hingga pengaktifan status penghuni dipetakan secara utuh melalui *flowchart* pada Gambar 4.6.
* **Paragraf 2:** Alur pada Gambar 4.6 dimulai saat calon penyewa mengecek ketersediaan 32 unit kamar di situs web. Jika unit masih kosong, pemesan mengisi formulir reservasi, mengunggah foto identitas, menentukan durasi sewa, dan diarahkan ke gerbang pembayaran Midtrans. Pada titik pengecekan transaksi, pembayaran yang gagal atau kedaluwarsa dikembalikan ke pemilihan metode bayar, sedangkan pembayaran yang lunas langsung mengunci unit kamar di basis data dan memicu pengiriman bukti reservasi via WhatsApp Fonnte. Alur berakhir ketika calon penghuni tiba di lokasi kost untuk verifikasi berkas fisik, serah terima kunci, dan pengaktifan status sebagai penyewa resmi.

---

### 4.2 Implementasi Antarmuka Sistem
* **Paragraf Pengantar Subbab 4.2:** Rancangan visual diterjemahkan menjadi halaman web fungsional bergaya *Neo-Brutalism* yang bercirikan garis tepi tegas, kontras warna tinggi, dan tata letak responsif. Pembahasan dibagi ke dalam empat kelompok sesuai hak akses: antarmuka umum, calon penyewa, admin, serta penyewa aktif.

#### 4.2.1 Implementasi Umum (1 Paragraf Pengantar dan 11 Poin Antarmuka Publik)
* **Paragraf Pengantar:** Mencakup 11 halaman publik yang dapat diakses pengunjung tanpa perlu *login*, meliputi profil indekos, katalog kamar, fasilitas, panduan pemesanan, autentikasi akun, hingga layanan obrolan tamu.
* **Poin 1: Implementasi Antarmuka *Home Page* (Gambar 4.7, 2 Paragraf):**
  * *Paragraf 1:* Beranda utama menjadi titik temu pertama bagi pencari hunian di kawasan Tembalang. Tata letaknya dirancang responsif agar informasi tipe kamar, fasilitas unggulan, dan tarif sewa tetap terbaca jelas baik melalui komputer maupun ponsel pintar.
  * *Paragraf 2:* Tampilan Gambar 4.7 memuat bilah navigasi utama di bagian atas, spanduk pengenalan indekos beserta tombol pintasan pencarian kamar di sisi kiri, dan foto fisik gedung di sisi kanan. Pada sudut kanan bawah, tersedia tombol obrolan interaktif untuk menghubungi penjaga kost secara langsung.
* **Poin 2: Implementasi Antarmuka Fasilitas Kost (Gambar 4.8, 3 Paragraf):**
  * *Paragraf 1:* Halaman fasilitas kost menyajikan daftar sarana penunjang hunian yang dapat digunakan bersama oleh seluruh penghuni Asri Boarding House.
  * *Paragraf 2:* Setiap item fasilitas dilengkapi penjelasan singkat mengenai fungsi dan aturan pemakaiannya di lingkungan indekos.
  * *Paragraf 3:* Pada Gambar 4.8, sarana umum seperti dapur bersama, area parkir, jaringan *Wi-Fi*, dan kamera pengawas (CCTV) disusun dalam kartu *grid* yang menampilkan foto dokumentasi asli, judul berhuruf tebal, serta deskripsi peruntukannya.
* **Poin 3: Implementasi Antarmuka Tipe Kamar Kost (Gambar 4.9, 2 Paragraf):**
  * *Paragraf 1:* Katalog tipe kamar memaparkan spesifikasi ruang, luas kamar, fasilitas kamar mandi dalam, serta harga sewa bulanan agar calon penghuni dapat menyesuaikan pilihan unit dengan anggaran mereka.
  * *Paragraf 2:* Kartu katalog pada Gambar 4.9 menampilkan foto kamar, nominal tarif bulanan, lencana sisa unit kosong, daftar inventaris kamar (kasur, meja belajar, lemari, AC, dan *water heater*), serta tombol pemesanan yang mengarahkan pengunjung langsung ke pengajuan reservasi.
* **Poin 4: Implementasi Antarmuka Galeri Kost (Gambar 4.10, 3 Paragraf):**
  * *Paragraf 1:* Halaman galeri menghimpun dokumentasi foto fisik bangunan, lorong kamar, area parkir, dan ruang komunal untuk memberikan gambaran nyata bagi calon penyewa dari luar kota.
  * *Paragraf 2:* Seluruh foto ditata dalam susunan kisi proporsional yang dapat diperbesar saat diklik.
  * *Paragraf 3:* Melalui Gambar 4.10, pengunjung dapat mengamati potret fasad gedung, lahan parkir, koridor lantai satu dan dua, hingga dapur bersama yang dilengkapi efek transisi halus saat kursor diarahkan ke gambar.
* **Poin 5: Implementasi Antarmuka Alur Cara Reservasi (Gambar 4.11, 2 Paragraf):**
  * *Paragraf 1:* Halaman panduan ini menguraikan tahapan menyewa kamar secara daring dari awal hingga akhir agar pengunjung baru dapat menyiapkan dokumen identitas sebelum mengisi formulir pemesanan.
  * *Paragraf 2:* Tampilan Gambar 4.11 memvisualisasikan lima langkah berurutan, yaitu registrasi akun, pemilihan unit kamar, pengisian kelengkapan profil, pembayaran uang muka melalui Midtrans, dan penerimaan bukti reservasi dengan penanda nomor urut berbingkai tebal.
* **Poin 6: Implementasi Antarmuka Testimoni Penyewa Kost (Gambar 4.12, 2 Paragraf):**
  * *Paragraf 1:* Bagian testimoni memuat ulasan dan penilaian langsung dari penghuni mengenai kebersihan, keamanan gedung, serta kesigapan pengelola sebagai bahan pertimbangan calon penyewa baru.
  * *Paragraf 2:* Antarmuka pada Gambar 4.12 menyusun ulasan penghuni ke dalam deretan kartu geser (*carousel*) yang berisi kutipan pengalaman, foto profil, nama samaran, tipe kamar, dan lima ikon bintang kepuasan.
* **Poin 7: Implementasi Antarmuka FAQ (Gambar 4.13, 3 Paragraf):**
  * *Paragraf 1:* Halaman *Frequently Asked Questions* (FAQ) merangkum pertanyaan yang paling sering diajukan terkait batas jam malam, aturan tamu menginap, ketentuan uang muka, dan prosedur perpanjangan sewa.
  * *Paragraf 2:* Penyediaan daftar tanya-jawab mandiri ini turut meringankan pekerjaan penjaga kost dalam menanggapi pertanyaan dasar yang berulang.
  * *Paragraf 3:* Sebagaimana terlihat pada Gambar 4.13, daftar pertanyaan dikemas dalam komponen panel lipat (*accordion*) yang dapat dibuka atau ditutup cukup dengan menekan ikon panah di ujung kanan baris.
* **Poin 8: Implementasi Antarmuka Tentang Kami (Gambar 4.14, 2 Paragraf):**
  * *Paragraf 1:* Halaman Tentang Kami memperkenalkan profil usaha, komitmen layanan, koordinat lokasi di kawasan Tembalang, serta saluran komunikasi resmi pengelola.
  * *Paragraf 2:* Pada Gambar 4.14, kartu profil latar belakang Asri Boarding House disandingkan dengan peta interaktif *Google Maps*, dilengkapi informasi nomor telepon, alamat *email* resmi, dan jam layanan administrasi di bagian bawah.
* **Poin 9: Implementasi Antarmuka *Login User* (Gambar 4.15, 3 Paragraf):**
  * *Paragraf 1:* Halaman *login user* menjadi pintu autentikasi bagi calon penyewa maupun penghuni aktif dengan mencocokkan kredensial akun terhadap data terenkripsi di MySQL.
  * *Paragraf 2:* Pengguna yang lupa kata sandi dapat memanfaatkan tautan pemulihan akses yang terhubung ke surat elektronik terdaftar.
  * *Paragraf 3:* Formulir pada Gambar 4.15 dibingkai kartu *Neo-Brutalism* berlatar kuning cerah dengan kolom input *email*, kolom kata sandi berikon penampil teks, kotak centang "Ingat Saya", tombol biru "Masuk ke Akun", serta tautan menuju halaman registrasi.
* **Poin 10: Implementasi Antarmuka *Register User* (Gambar 4.16, 2 Paragraf):**
  * *Paragraf 1:* Calon penyewa baru diwajibkan mendaftarkan akun melalui halaman *register user* dengan mengisi nama lengkap, nomor WhatsApp aktif, alamat *email*, dan kata sandi yang langsung divalidasi keunikannya oleh sistem.
  * *Paragraf 2:* Kartu formulir "Buat Akun Baru" pada Gambar 4.16 menampilkan kolom isian identitas beserta indikator peringatan format masukan dan tombol biru "Daftar Akun" yang mengarahkan pengguna ke tahap pelengkapan profil.
* **Poin 11: Implementasi Antarmuka *Chat Real Time Guest* (Gambar 4.17, 3 Paragraf):**
  * *Paragraf 1:* Fitur *chat real-time guest* memungkinkan pengunjung situs bertanya langsung kepada penjaga kost seputar sisa kamar atau janji survei lokasi tanpa harus memiliki akun terlebih dahulu.
  * *Paragraf 2:* Setiap pesan yang dikirim melalui jendela obrolan ini langsung diteruskan ke dasbor pengelola agar segera mendapat balasan.
  * *Paragraf 3:* Pada Gambar 4.17, *widget* percakapan tampil mengambang di pojok kanan bawah layar dengan kepala jendela kuning cerah, tombol *minimize*, ruang riwayat balasan beserta waktu kirim, serta kolom pengetikan pesan di bagian bawah.

#### 4.2.2 Implementasi Calon Penyewa (1 Paragraf Pengantar dan 7 Poin Antarmuka & Integrasi)
* **Paragraf Pengantar:** Menguraikan 7 antarmuka dan layanan integrasi yang diakses calon penyewa setelah registrasi akun, mulai dari pengisian identitas kependudukan, pemantauan dasbor reservasi, simulasi biaya, pembayaran 5 tahap, hingga integrasi Midtrans, WhatsApp Fonnte, dan *email* SMTP.
* **Poin 1: Implementasi Antarmuka Lengkapi Profil (Gambar 4.18, 3 Paragraf):**
  * *Paragraf 1:* Sebelum memilih dan mengunci kamar, calon penyewa wajib melengkapi NIK, nama serta nomor telepon kerabat darurat, asal kampus atau instansi, dan unggahan foto KTP asli demi keamanan lingkungan indekos.
  * *Paragraf 2:* Berkas identitas tersebut disimpan di dalam direktori khusus yang hanya dapat dibuka oleh administrator saat proses verifikasi.
  * *Paragraf 3:* Halaman pada Gambar 4.18 menampilkan kolom isian nomor KTP, kontak darurat keluarga, nomor penjamin, area unggah foto KTP lengkap dengan pratinjau gambar dan batas ukuran berkas, serta tombol hijau "Simpan Profil".
* **Poin 2: Implementasi Antarmuka *Dashboard* Reservasi (Gambar 4.19, 2 Paragraf):**
  * *Paragraf 1:* Halaman *dashboard* reservasi memudahkan calon penyewa memantau status pengajuan sewa, nomor tagihan uang muka, serta riwayat reservasi yang pernah dibuat.
  * *Paragraf 2:* Tampilan Gambar 4.19 menyajikan sapaan pengguna dan kartu ringkasan pesanan di bagian atas, bilah indikator tahapan proses reservasi di bagian tengah, serta tombol "Bayar Uang Muka" dan tautan unduh tanda terima di panel kanan.
* **Poin 3: Implementasi Antarmuka Simulasi Biaya Kamar (Gambar 4.20, 3 Paragraf):**
  * *Paragraf 1:* Kalkulator simulasi biaya kamar membantu calon penghuni menghitung akumulasi tarif sewa pokok, uang jaminan (*deposit*), dan potongan harga sewa jangka panjang secara transparan.
  * *Paragraf 2:* Pengguna dapat mengganti opsi durasi sewa mulai dari bulanan, tiga bulanan, hingga tahunan untuk membandingkan total tagihan yang perlu disiapkan.
  * *Paragraf 3:* Pada Gambar 4.20, kartu kalkulator di sisi kiri memuat menu *dropdown* durasi sewa dan fasilitas tambahan, sedangkan tabel di sisi kanan langsung memperbarui rincian biaya secara dinamis tanpa memuat ulang halaman, dilengkapi tombol biru "Lanjutkan Pemesanan".
* **Poin 4: Implementasi Antarmuka Pembayaran 5 Tahap Reservasi (Gambar 4.21, 2 Paragraf):**
  * *Paragraf 1:* Penyelesaian reservasi dibagi ke dalam lima tahap terstruktur (konfirmasi rincian sewa, pemilihan metode bayar, pemeriksaan tagihan, pelunasan via *payment gateway*, dan penerbitan nota tanda jadi) yang menjaga kamar tidak diambil pengguna lain selama masa tunggu pembayaran.
  * *Paragraf 2:* Antarmuka pada Gambar 4.21 memperlihatkan bilah langkah berlingkaran hijau di bagian atas, rincian spesifikasi kamar, nominal uang muka, hitung mundur batas waktu bayar, tombol pemicu transaksi digital, dan catatan pengingat di bagian bawah.
* **Poin 5: Implementasi *Payment Gateway* Midtrans (Gambar 4.22, 3 Paragraf):**
  * *Paragraf 1:* Pelunasan uang muka reservasi maupun sewa bulanan ditangani melalui integrasi Midtrans Snap yang mendukung saluran pembayaran non-tunai seperti *Virtual Account* perbankan, dompet digital, dan QRIS.
  * *Paragraf 2:* Begitu dana ditransfer, sinyal *webhook callback* dari *server* Midtrans langsung memperbarui status pesanan di basis data tanpa menunggu pemeriksaan mutasi manual oleh penjaga kost.
  * *Paragraf 3:* Gambar 4.22 menampilkan jendela *pop-up* Midtrans Snap di tengah layar yang memuat total tagihan, batas waktu pelunasan, nomor *Virtual Account* beserta panduan langkah pembayaran, dan perlindungan validasi kriptografi.
* **Poin 6: Implementasi *WhatsApp Gateway* Fonnte (Gambar 4.23, 2 Paragraf):**
  * *Paragraf 1:* Sesaat setelah uang muka dinyatakan lunas oleh Midtrans, sistem mengirim pesan otomatis ke nomor ponsel pemohon melalui API Fonnte yang berisi nomor referensi, tipe dan nomor kamar, nominal setoran, serta tanggal *check-in*.
  * *Paragraf 2:* Tangkapan layar pada Gambar 4.23 menunjukkan pesan WhatsApp resmi Asri Boarding House yang memuat ucapan konfirmasi, rincian kamar yang dikunci, status lunas, dan tautan unduh nota digital sebagai bukti sah saat mengambil kunci.
* **Poin 7: Implementasi *Email Gateway* SMTP (Gambar 4.24, 3 Paragraf):**
  * *Paragraf 1:* Aplikasi juga mengirimkan bukti pembayaran resmi dan ketentuan sewa ke alamat surel calon penyewa melalui protokol SMTP sebagai arsip administrasi formal yang mudah disimpan atau dicetak ulang.
  * *Paragraf 2:* Pesan dikirim menggunakan templat HTML beridentitas resmi Asri Boarding House beserta lampiran kuitansi digital.
  * *Paragraf 3:* Pada Gambar 4.24, surat elektronik bertajuk "Bukti Pembayaran Reservasi Kamar Kost" menyajikan tabel rincian transaksi, jadwal *check-in*, ringkasan tata tertib penghuni, serta lampiran berkas nota PDF.

#### 4.2.3 Implementasi Admin (1 Paragraf Pengantar dan 11 Poin Antarmuka Pengelola)
* **Paragraf Pengantar:** Merangkum 11 antarmuka kendali operasional khusus penjaga kost untuk mengawasi keterisian 32 unit kamar, memverifikasi reservasi masuk, mengelola tagihan sewa bulanan, mencatat pengeluaran kas, mengekspor laporan keuangan, hingga menangani keluhan fasilitas dan obrolan pengunjung.
* **Poin 1: Implementasi Antarmuka *Admin Login* (Gambar 4.25, 2 Paragraf):**
  * *Paragraf 1:* Halaman *admin login* memisahkan jalur masuk khusus penjaga kost guna melindungi data master kamar, pembukuan kas, dan dokumen identitas penghuni, disertai pembatasan percobaan *login* gagal beruntun.
  * *Paragraf 2:* Tampilan Gambar 4.25 menyajikan kartu *Neo-Brutalism* bergaris tepi hitam pekat bertajuk "Masuk Portal Admin" dengan kolom *email* administrator, kata sandi tersamar, dan tombol hitam "Masuk Dasbor".
* **Poin 2: Implementasi Antarmuka *Dashboard Admin* (Gambar 4.26, 3 Paragraf):**
  * *Paragraf 1:* Halaman *dashboard admin* merangkum metrik operasional dan keuangan Asri Boarding House dalam satu layar, mencakup tingkat keterisian 32 unit kamar, total kas masuk bulan berjalan, jumlah penghuni aktif, dan daftar keluhan tertunda.
  * *Paragraf 2:* Penyajian ringkasan angka dan grafik tren bulanan ini mempermudah pengelola memantau perkembangan usaha indekos setiap hari.
  * *Paragraf 3:* Sebagaimana tampak pada Gambar 4.26, bagian atas layar memuat deretan kartu indikator berwarna tegas, bagian tengah menampilkan grafik batang pemasukan bulanan beserta tabel reservasi terbaru, dan sisi kiri menyediakan bilah navigasi vertikal antarmodul.
* **Poin 3: Implementasi Antarmuka Manajemen Kamar (Gambar 4.27, 2 Paragraf):**
  * *Paragraf 1:* Modul manajemen kamar memberikan wewenang kepada penjaga kost untuk menambah unit baru, memperbarui status keterisian, dan menyesuaikan tarif serta rincian fasilitas dari 32 unit kamar secara terpusat.
  * *Paragraf 2:* Tabel pada Gambar 4.27 memuat nomor unit, kategori tipe kamar (VIP, Deluxe, atau Standar), posisi lantai, tarif bulanan, dan lencana ketersediaan, dilengkapi tombol kuning "Tambah Kamar Baru", kolom pencarian, *filter* kamar kosong, serta tombol "Edit" dan "Hapus".
* **Poin 4: Implementasi Antarmuka Manajemen Fasilitas (Gambar 4.28, 3 Paragraf):**
  * *Paragraf 1:* Antarmuka manajemen fasilitas digunakan pengelola untuk memperbarui daftar sarana umum, foto dokumentasi, dan aturan pemakaian ruang bersama yang tampil di katalog publik.
  * *Paragraf 2:* Pengelola juga dapat mengatur urutan tampil setiap fasilitas agar sarana unggulan ditempatkan di posisi teratas.
  * *Paragraf 3:* Melalui halaman pada Gambar 4.28, daftar sarana umum disajikan dalam tabel yang memuat nama fasilitas, uraian fungsi, ikon kategori, pratinjau foto, tombol "Tambah Fasilitas", kolom *filter*, serta tombol pengubah status apabila sarana sedang diperbaiki.
* **Poin 5: Implementasi Antarmuka Manajemen Penyewa (Gambar 4.29, 2 Paragraf):**
  * *Paragraf 1:* Seluruh data penghuni aktif yang meliputi identitas lengkap, telepon darurat kerabat, nomor kamar, dan masa berlaku kontrak sewa dihimpun pada halaman manajemen penyewa, termasuk fitur penonaktifan status hunian saat *check-out*.
  * *Paragraf 2:* Pada Gambar 4.29, tabel daftar penghuni menampilkan nama lengkap, nomor WhatsApp, unit kamar, tanggal mulai menghuni, dan lencana "Aktif", disertai kolom pencarian nama atau penyaring lantai serta tombol "Detail" untuk memeriksa foto KTP dan riwayat bayar.
* **Poin 6: Implementasi Antarmuka Manajemen Reservasi (Gambar 4.30, 3 Paragraf):**
  * *Paragraf 1:* Modul manajemen reservasi memfasilitasi penjaga kost memeriksa berkas KTP pemohon baru, memastikan uang muka telah terverifikasi lunas di Midtrans, lalu menyetujui penempatan unit kamar.
  * *Paragraf 2:* Pesanan yang melewati batas waktu pembayaran dapat dibatalkan agar unit kamar kembali terbuka bagi calon penyewa lain.
  * *Paragraf 3:* Tampilan Gambar 4.30 menyajikan tabel pesanan kamar berisi kode reservasi, nama pemohon, tipe kamar, rencana tanggal masuk, dan status uang muka, dilengkapi tombol *filter* ("Menunggu Pembayaran", "Terverifikasi", "Dibatalkan") serta tombol aksi "Konfirmasi".
* **Poin 7: Implementasi Antarmuka Manajemen Tagihan (Gambar 4.31, 2 Paragraf):**
  * *Paragraf 1:* Siklus penagihan sewa bulanan dan perhitungan denda keterlambatan dikendalikan melalui antarmuka manajemen tagihan yang terhubung langsung dengan *cron job* penerbit faktur dan pemantau jatuh tempo tanggal 10.
  * *Paragraf 2:* Kartu "Daftar Tagihan Bulanan Penyewa" pada Gambar 4.31 menampilkan tabel nomor *invoice*, nama penghuni, nomor kamar, nominal tagihan beserta denda, tanggal jatuh tempo, dan lencana status bayar, dilengkapi tombol sinkronisasi tagihan serta tombol "Detail" untuk rincian biaya atau kirim ulang pengingat.
* **Poin 8: Implementasi Antarmuka Manajemen Pengeluaran (Gambar 4.32, 3 Paragraf):**
  * *Paragraf 1:* Seluruh arus kas keluar untuk operasional harian seperti listrik, air PDAM, alat kebersihan, dan perbaikan fasilitas kamar dicatat melalui antarmuka manajemen pengeluaran agar perhitungan laba bersih akurat.
  * *Paragraf 2:* Setiap catatan pengeluaran dilengkapi keterangan peruntukan dana dan nama petugas penanggung jawab.
  * *Paragraf 3:* Halaman pada Gambar 4.32 memadukan kartu ringkasan total pengeluaran bulan berjalan di bagian atas dengan tabel riwayat kas keluar di bagian bawah, disertai tombol "Tambah Biaya Operasional" untuk mencatat nota belanja baru.
* **Poin 9: Implementasi Antarmuka Manajemen Laporan Pemasukan (Gambar 4.33, 2 Paragraf):**
  * *Paragraf 1:* Modul laporan pemasukan mengolah seluruh pembayaran sewa Midtrans dan catatan pengeluaran operasional menjadi laporan keuangan terpadu tanpa perlu mencocokkan mutasi bank dan buku kas fisik satu per satu, lengkap dengan fitur unduh Excel dan PDF.
  * *Paragraf 2:* Pada Gambar 4.33, tiga kartu metrik utama (pendapatan kotor, beban operasional, dan laba bersih) ditampilkan di bagian atas, diikuti grafik garis tren pendapatan bulanan, tabel rincian kas masuk berfilter rentang tanggal, serta tombol hijau "Ekspor Excel" dan merah "Unduh PDF".
* **Poin 10: Implementasi Antarmuka Manajemen Keluhan Penyewa (Gambar 4.34, 3 Paragraf):**
  * *Paragraf 1:* Laporan kerusakan fasilitas kamar maupun gangguan kenyamanan lingkungan dikelola secara terpusat melalui antarmuka manajemen keluhan penyewa untuk memetakan tingkat urgensi perbaikan.
  * *Paragraf 2:* Pembaruan status tindak lanjut oleh admin memudahkan penghuni pelapor mengetahui progres pengerjaan keluhannya.
  * *Paragraf 3:* Tabel tiket aduan pada Gambar 4.34 memuat nama pelapor, nomor kamar, kategori kerusakan, tanggal pengajuan, dan lencana status penanganan, dilengkapi panel *filter* laporan baru serta tombol pembuka foto bukti dan pengubah status menjadi "Diproses" atau "Selesai".
* **Poin 11: Implementasi Antarmuka Manajemen Fitur *Chat Guest* (Gambar 4.35, 2 Paragraf):**
  * *Paragraf 1:* Pertanyaan dari pengunjung publik melalui *widget* halaman depan masuk ke antarmuka manajemen *chat guest* di sisi admin agar penjaga kost dapat menjawab ketersediaan kamar dan jadwal survei tanpa berpindah perangkat.
  * *Paragraf 2:* Layar obrolan pada Gambar 4.35 dibagi menjadi dua panel, yakni daftar antrean pesan pengunjung di sebelah kiri dan ruang percakapan aktif berpenanda waktu kirim di sebelah kanan, lengkap dengan pilihan templat balasan cepat (*quick reply*).

#### 4.2.4 Implementasi Penyewa Aktif (1 Paragraf Pengantar dan 7 Poin Antarmuka Portal Mandiri)
* **Paragraf Pengantar:** Membahas 7 antarmuka khusus penghuni resmi pada portal layanan mandiri untuk memantau masa sewa, melunasi tagihan bulanan via Midtrans, memperbarui profil, memeriksa riwayat transaksi, mengajukan keluhan kamar, dan mengunduh kuitansi PDF.
* **Poin 1: Implementasi Antarmuka *Login* Penyewa Aktif (Gambar 4.36, 3 Paragraf):**
  * *Paragraf 1:* Jalur masuk penghuni resmi dipisahkan melalui halaman *login* penyewa aktif agar pengguna langsung diarahkan ke portal pengelolaan sewa bulanan tanpa melewati alur pemesanan kamar lagi.
  * *Paragraf 2:* Sesi autentikasi dilindungi enkripsi kredensial demi menjaga kerahasiaan riwayat tagihan dan data pribadi penghuni.
  * *Paragraf 3:* Halaman pada Gambar 4.36 tampil dengan bingkai kartu *Neo-Brutalism* berlatar kuning cerah bertajuk "Masuk Portal Penghuni Aktif" yang memuat kolom *email*, kata sandi, opsi pengingat sesi, dan tombol biru "Masuk ke Portal".
* **Poin 2: Implementasi Antarmuka *Dashboard* Penyewa (Gambar 4.37, 2 Paragraf):**
  * *Paragraf 1:* Halaman *dashboard* penyewa merangkum nomor unit kamar yang ditempati, sisa masa berlaku sewa, rincian tagihan bulan berjalan, serta papan pengumuman resmi dari penjaga kost.
  * *Paragraf 2:* Pada Gambar 4.37, bagian atas menampilkan sapaan nama penghuni dan kartu informasi hunian, bagian tengah memuat kartu pemberitahuan tagihan berwarna kuning cerah dengan tombol pintasan "Bayar Tagihan Sekarang", dan bagian bawah menyediakan menu cepat pengajuan keluhan, arsip kuitansi, serta pengumuman.
* **Poin 3: Implementasi Antarmuka Tagihan Penyewa (Gambar 4.38, 3 Paragraf):**
  * *Paragraf 1:* Rincian kewajiban sewa bulanan yang meliputi nominal sewa pokok, biaya denda keterlambatan, dan batas waktu pelunasan disajikan secara transparan pada halaman tagihan penyewa.
  * *Paragraf 2:* Tombol pembayaran pada halaman ini terhubung langsung dengan modul Midtrans Snap untuk mempermudah pelunasan non-tunai.
  * *Paragraf 3:* Kartu tagihan pada Gambar 4.38 menampilkan nomor *invoice*, periode bulan, total biaya, tanggal jatuh tempo, lencana kuning "Belum Dibayar", serta tombol hijau "Bayar Tagihan" yang memicu jendela *pop-up* Midtrans Snap.
* **Poin 4: Implementasi Antarmuka Profil (Gambar 4.39, 2 Paragraf):**
  * *Paragraf 1:* Halaman profil memberi keleluasaan bagi penyewa aktif untuk memperbarui nomor WhatsApp, kontak darurat kerabat, dan kata sandi akun secara mandiri tanpa harus meminta bantuan penjaga kost.
  * *Paragraf 2:* Tampilan Gambar 4.39 terbagi atas kartu biodata penghuni di sisi kiri (dengan penyamaran sebagian digit kontak darurat demi privasi) dan formulir pembaruan kata sandi beserta tombol kuning "Simpan Perubahan" di sisi kanan.
* **Poin 5: Implementasi Antarmuka Riwayat Pembayaran (Gambar 4.40, 3 Paragraf):**
  * *Paragraf 1:* Seluruh transaksi pelunasan sewa selama penghuni menetap di Asri Boarding House dicatat secara kronologis pada halaman riwayat pembayaran, mencakup kode referensi, tanggal lunas, kanal pembayaran, dan nominal dana.
  * *Paragraf 2:* Arsip digital ini memudahkan penghuni melacak pengeluaran bulanan sekaligus mengunduh ulang kuitansi resmi kapan saja diperlukan.
  * *Paragraf 3:* Tabel rekapitulasi pada Gambar 4.40 menampilkan tanggal bayar, nomor pesanan, metode pembayaran *Virtual Account* atau dompet digital, jumlah uang, lencana hijau "Berhasil", serta tombol biru "Cetak Nota" di ujung kanan baris.
* **Poin 6: Implementasi Antarmuka Keluhan (Gambar 4.41, 2 Paragraf):**
  * *Paragraf 1:* Penghuni yang mendapati kerusakan fasilitas di kamar maupun area umum dapat langsung mengajukan tiket laporan beserta foto bukti fisik melalui halaman keluhan tanpa harus mencari penjaga kost secara tatap muka.
  * *Paragraf 2:* Halaman pada Gambar 4.41 memuat formulir "Tambah Laporan Keluhan" di bagian atas (pilihan kategori sarana, uraian masalah, dan bidang unggah foto) serta tabel riwayat aduan lengkap dengan lencana status tindak lanjut di bagian bawah.
* **Poin 7: Implementasi Nota PDF (Gambar 4.42, 3 Paragraf):**
  * *Paragraf 1:* Setiap pembayaran uang muka maupun tagihan sewa bulanan yang terverifikasi lunas otomatis menerbitkan tanda terima resmi berupa nota PDF yang mencantumkan nomor faktur, identitas penyewa, nomor kamar, rincian nominal, dan cap lunas digital.
  * *Paragraf 2:* Ukuran dan tata letak dokumen diatur proporsional agar siap dicetak kapan saja tanpa ada bagian teks yang terpotong.
  * *Paragraf 3:* Kuitansi digital pada Gambar 4.42 menampilkan kepala surat resmi Asri Boarding House Tembalang, nomor referensi nota, tanggal pelunasan, nomor unit kamar, nama penyewa yang disamarkan sebagian, nilai transaksi Rp1.700.000 berstempel "LUNAS", serta keterangan verifikasi otomatis di kaki dokumen.

---

### 4.3 Implementasi Kode Program
* **Paragraf Pengantar Subbab 4.3:** Rancangan arsitektur dan aturan bisnis diterjemahkan ke dalam kode PHP pada *framework* Laravel 11 dengan memisahkan logika layanan (*Service Layer*). Pembahasan dibagi menjadi empat sub-bab tematik: autentikasi dan hak akses, pemesanan kamar, *payment gateway*, serta penjadwalan dan notifikasi.

#### 4.3.1 Implementasi Autentikasi dan Hak Akses (1 Paragraf Pengantar dan 2 Poin)
* **Paragraf Pengantar:** Modul autentikasi mengatur keamanan sesi masuk dan mengarahkan setiap pengguna (calon penyewa, penjaga kost, penyewa aktif) ke portal kerjanya masing-masing guna mencegah pelanggaran hak akses pada rute privat.
* **Poin 1: Implementasi Pembatasan Hak Akses Peran Pengguna / *Role Middleware* (Gambar 4.43, 2 Paragraf):**
  * *Paragraf 1:* Komponen *middleware* mencegat setiap permintaan halaman web sebelum mencapai fungsi *controller* utama untuk memeriksa status *login* dan kesesuaian peran pengguna.
  * *Paragraf 2:* Pada potongan kode Gambar 4.43, baris awal memeriksa sesi *login* aktif dan mengalihkan pengguna yang belum masuk ke formulir autentikasi, sedangkan baris berikutnya memvalidasi label peran dan menghentikan eksekusi bila hak akses tidak cocok.
* **Poin 2: Implementasi Pengalihan Portal Pasca-autentikasi / *Auth Redirect* (Gambar 4.44, 2 Paragraf):**
  * *Paragraf 1:* Setelah kredensial tervalidasi, logika pengalihan rute memeriksa kelengkapan nomor kontak, status keaktifan penyewa, hingga riwayat reservasi yang sedang berjalan untuk menentukan halaman tujuan.
  * *Paragraf 2:* Kode pada Gambar 4.44 mengarahkan akun berstatus admin ke dasbor operasional indekos, memandu penghuni aktif ke portal penyewa, dan mengarahkan calon penyewa yang memiliki transaksi tertunda langsung ke halaman detail reservasinya.

#### 4.3.2 Implementasi Pemesanan Kamar (1 Paragraf Pengantar dan 2 Poin)
* **Paragraf Pengantar:** Modul pemesanan kamar mengendalikan penguncian inventaris 32 unit kamar secara presisi untuk mencegah benturan pesanan ganda (*double booking*) saat terjadi akses bersamaan.
* **Poin 1: Implementasi Transaksi ACID dan Penguncian Baris Unit Kamar (Gambar 4.45, 2 Paragraf):**
  * *Paragraf 1:* Persaingan akses ketika dua calon penghuni memilih unit kamar yang sama pada detik bersamaan ditangani menggunakan blok transaksi ACID dan penguncian baris eksklusif di basis data.
  * *Paragraf 2:* Sebagaimana terlihat pada Gambar 4.45, pemanggilan metode `lockForUpdate()` di dalam blok transaksi mengunci baris data kamar terpilih hingga validasi tanggal selesai dieksekusi, lalu membatalkan transaksi secara otomatis apabila kamar tersebut sudah lebih dulu terisi.
* **Poin 2: Implementasi Algoritma Pengecekan Benturan (*Double Booking*) Kamar (Gambar 4.46, 2 Paragraf):**
  * *Paragraf 1:* Algoritma pemeriksaan jadwal bertugas memastikan rentang tanggal mulai hingga selesai sewa yang diajukan pemohon tidak bertabrakan dengan jadwal reservasi aktif lain maupun masa kontrak penghuni yang sedang berjalan.
  * *Paragraf 2:* Pada kode Gambar 4.46, kueri pertama memeriksa irisan tanggal pada daftar reservasi aktif di unit kamar terkait dan kueri kedua memeriksa kontrak penghuni aktif; jika ditemukan benturan jadwal, fungsi mengembalikan nilai `true` untuk menolak pengajuan sewa baru.

#### 4.3.3 Implementasi *Payment Gateway* (1 Paragraf Pengantar dan 2 Poin)
* **Paragraf Pengantar:** Modul *payment gateway* menghubungkan aplikasi web dengan *server* perbankan melalui layanan Midtrans untuk memproses transaksi non-tunai dan memperbarui status pelunasan secara otomatis lewat *webhook*.
* **Poin 1: Implementasi Pembuatan Transaksi dan *Snap Token* Midtrans (Gambar 4.47, 2 Paragraf):**
  * *Paragraf 1:* Pembayaran sewa daring diawali dengan pembentukan *Snap token* Midtrans yang merangkum nominal sewa pokok dan akumulasi denda keterlambatan dengan batas waktu pelunasan 24 jam.
  * *Paragraf 2:* Potongan kode pada Gambar 4.47 memetakan nominal pokok dan denda ke dalam larik *item* transaksi, menetapkan kode pesanan unik berbasis *timestamp* beserta masa kedaluwarsa 24 jam, lalu mengirim permintaan *HTTP POST* terenkripsi menggunakan *server key* untuk memperoleh *Snap token* resmi.
* **Poin 2: Implementasi Validasi *Signature Key* dan *Webhook Callback* Midtrans (Gambar 4.48, 2 Paragraf):**
  * *Paragraf 1:* Setiap notifikasi pembayaran yang dikirim *server* Midtrans melalui *webhook callback* wajib melewati pemeriksaan tanda tangan digital berstandar algoritma SHA-512 agar terhindar dari manipulasi pihak luar.
  * *Paragraf 2:* Mengacu pada Gambar 4.48, keaslian *signature key* diverifikasi menggunakan fungsi `hash_equals()` guna menangkal *timing attack*, diikuti pengecekan status transaksi terdahulu untuk menjaga sifat idempoten sebelum memperbarui status tagihan menjadi lunas di basis data lokal.

#### 4.3.4 Implementasi Penjadwalan dan Notifikasi (1 Paragraf Pengantar dan 3 Poin)
* **Paragraf Pengantar:** Otomasi operasional indekos memadukan penjadwal tugas berkala di *server* dengan pengiriman pesan WhatsApp dan surel resmi sehingga penagihan tanggal 10 dan pengiriman kuitansi berjalan tepat waktu tanpa intervensi manual.
* **Poin 1: Implementasi Penjadwalan Tugas Otomatis / *Task Scheduler Cron Job* (Gambar 4.49, 2 Paragraf):**
  * *Paragraf 1:* Penerbitan tagihan rutin dan pembersihan transaksi kedaluwarsa diotomatisasi melalui fitur penjadwal tugas Laravel dengan mengaktifkan satu layanan *cron* di tingkat *server*.
  * *Paragraf 2:* Pada Gambar 4.49, perintah penerbitan tagihan bulanan (`tagihan:generate-bulanan`) dijadwalkan setiap tanggal 1 pukul 00:05 WIB, sedangkan perintah pemeriksaan keterlambatan dan denda (`tagihan:proses-keterlambatan`) dijalankan setiap hari pukul 01:00 WIB dengan perlindungan metode `withoutOverlapping()` agar eksekusi tidak tumpang-tindih.
* **Poin 2: Implementasi Integrasi Layanan Pesan *WhatsApp Gateway* Fonnte (Gambar 4.50, 2 Paragraf):**
  * *Paragraf 1:* Pengiriman pengingat tagihan bulanan dan konfirmasi reservasi ke nomor ponsel penyewa dijalankan melalui API Fonnte setelah nomor tujuan distandarkan ke format panggilan internasional.
  * *Paragraf 2:* Kode pada Gambar 4.50 mengubah awalan nomor telepon pengguna ke kode negara Indonesia, mengirimkan permintaan *HTTP POST* berotorisasi token rahasia menuju *server* Fonnte, lalu memeriksa kode status HTTP beserta respons JSON untuk memastikan pesan terkirim.
* **Poin 3: Implementasi Integrasi Pengiriman Dokumen Tagihan via *Email Gateway* SMTP (Gambar 4.51, 2 Paragraf):**
  * *Paragraf 1:* Surat tagihan resmi dan kuitansi sewa dikirimkan otomatis melalui protokol SMTP berenkripsi TLS dengan memanfaatkan antrean pesan (*message queue*) di latar belakang agar antarmuka web tetap responsif.
  * *Paragraf 2:* Sebagaimana tampak pada Gambar 4.51, kelas pengirim menerapkan antarmuka `ShouldQueue` untuk eksekusi asinkron, mengatur subjek surat bermuatan nomor tagihan pada metode `envelope()`, serta merakit rincian sewa pokok, denda, dan tautan portal ke dalam templat surel melalui metode `content()`.

---

### 4.4 Pengujian Sistem (1 Paragraf Pengantar, Tabel 4.1 [60 Skenario], dan 1 Paragraf Penutup)
* **Paragraf Pengantar (Metode *Black Box Testing*):** Pengujian fungsionalitas sistem dilaksanakan menggunakan metode kotak hitam (*black box testing*) untuk memeriksa kesesuaian antara masukan data dan respons antarmuka aplikasi pada 6 domain operasional utama.
* **Tabel 4.1: Hasil Pengujian Fungsionalitas Sistem (*Black Box Testing*, 60 Skenario Uji):**
  1. **Domain I: Autentikasi, Hak Akses & Profil Pengguna (Skenario 1 sampai 12):**
     - Menguji *login* Administrator (kredensial valid, sandi salah, *email* tidak terdaftar, dan kewajiban ganti sandi saat *login* pertama), *login* Penyewa Aktif (valid dan sandi salah), lupa kata sandi (*email* terdaftar, tidak terdaftar, dan pembaruan token valid), pendaftaran akun Calon Penyewa baru, serta pelengkapan profil (nomor WhatsApp valid format Indonesia dibanding masukan huruf/simbol). Seluruh 12 skenario **Berhasil**.
  2. **Domain II: Portal Publik, Katalog & *Guest Chat* (Skenario 13 sampai 22):**
     - Menguji akses beranda publik 32 kamar, penyaringan katalog berdasarkan lantai (Lantai 1) dan tipe (VIP), akses detail kamar berstatus "Tersedia" dibanding "Terisi", tombol pintasan WhatsApp pengelola, pembukaan dan pengiriman pesan *widget Guest Chat* tanpa *login*, ketahanan riwayat obrolan saat halaman dimuat ulang (*refresh*), serta tampilan galeri foto dan panel lipat FAQ. Seluruh 10 skenario **Berhasil**.
  3. **Domain III: Calon Penyewa & Alur Pemesanan 5 Tahap (Skenario 23 sampai 32):**
     - Menguji pemilihan unit kamar kosong di Tahap 1, pengisian biodata dan validasi NIK 16 digit (valid dibanding 14 digit) di Tahap 2, simulasi durasi sewa 12 bulan beserta perhitungan potongan harga tahunan otomatis, pemilihan skema uang muka (DP) dibanding pelunasan penuh (100%), pemicuan *pop-up* Midtrans Snap, penutupan jendela Snap sebelum transfer (status *pending* dan penguncian sementara), hingga pelunasan uang muka yang mengunci kamar dan mengirim bukti ke WhatsApp serta *email*. Seluruh 10 skenario **Berhasil**.
  4. **Domain IV: Portal Penyewa Aktif, Tagihan & Keluhan (Skenario 33 sampai 41):**
     - Menguji tampilan dasbor ringkasan hunian, daftar riwayat tagihan sewa, rincian komponen *invoice* (pokok, denda, jatuh tempo), pelunasan tagihan via *Virtual Account* Midtrans Snap (*webhook* otomatis mengubah status lunas), pengunduhan kuitansi PDF resmi, pengajuan keluhan fasilitas kamar beserta lampiran foto `.jpg` berukuran 1 MB, penolakan berkas bukan gambar (`.pdf` atau `.docx`), dan pemantauan status penyelesaian keluhan "Selesai". Seluruh 9 skenario **Berhasil**.
  5. **Domain V: Administrator & Manajemen Operasional (Skenario 42 sampai 54):**
     - Menguji kartu ringkasan operasional di dasbor admin, penambahan unit kamar baru, pembaruan tarif dan spesifikasi kamar, pencegahan hapus kamar yang sedang dihuni, pengelolaan master fasilitas kost, konfirmasi aktivasi reservasi masuk, pencatatan pelunasan tagihan secara tunai, penonaktifan status hunian (*check-out*), pembaruan status kamar pasca-inspeksi kebersihan menjadi "Tersedia", tindak lanjut tiket keluhan ("Diproses"), membalas pesan *Guest Chat*, pencatatan pengeluaran operasional kost, serta ekspor laporan keuangan ke PDF dan Excel. Seluruh 13 skenario **Berhasil**.
  6. **Domain VI: Otomasi *Task Scheduler*, Denda & API Eksternal (Skenario 55 sampai 60):**
     - Menguji penerbitan tagihan sewa otomatis setiap tanggal 1 (`tagihan:generate-bulanan`), pencegahan duplikasi tagihan pada periode bulan yang sama, perhitungan denda keterlambatan otomatis setelah melewati batas waktu, pengujian sifat idempoten perhitungan denda saat dijalankan berulang di hari yang sama, pengiriman pengingat tagihan via API WhatsApp Fonnte, serta pengiriman dokumen tagihan via *Email* SMTP melalui antrean *server*. Seluruh 6 skenario **Berhasil**.
* **Paragraf Penutup Subbab 4.4:** Hasil pengujian pada Tabel 4.1 membuktikan bahwa seluruh 60 skenario fungsional berjalan 100% sesuai spesifikasi kebutuhan tanpa kendala teknis, baik pada validasi formulir, penguncian transaksi kamar berprinsip ACID, maupun pertukaran data dengan Midtrans, Fonnte, dan SMTP.

---

### 4.5 Hasil Wawancara (1 Paragraf Pengantar, 6 Poin Narasumber [Tabel 4.2 sampai 4.7], dan 1 Paragraf Sintesis Akhir)
* **Paragraf Pengantar (*User Acceptance Testing* / UAT):** Evaluasi penerimaan pengguna dilaksanakan bersama pengelola operasional senior (Bapak Asep) dan lima mahasiswa penghuni kost aktif melalui uji coba langsung berdampingan (*side-by-side usability testing*) serta wawancara semiterstruktur yang direkam audio untuk menilai kejelasan katalog daring, reservasi bertahap, pembayaran Midtrans, kuitansi instan, dan pembukuan kas harian.

* **Poin 1: Wawancara dengan Penjaga Kost, Bapak Asep (Paragraf Pengantar, Gambar 4.52, Tabel 4.2, dan Paragraf Analisis):**
  * *Paragraf Pengantar:* Sesi evaluasi bersama Bapak Asep menguji fungsionalitas panel pengelola dan kemudahan administrasi harian setelah 20 tahun menggunakan buku besar manual, dilengkapi dokumentasi foto pada Gambar 4.52.
  * *Tabel 4.2 (10 Aspek Pengujian Bapak Asep):*
    1. **Halaman Depan Publik:** Tampilan tulisan berukuran besar dengan kontras tegas membuat mata pengelola senior tidak cepat lelah, sementara foto 32 kamar dan harga sewa langsung terlihat jelas sehingga calon penyewa luar kota tidak perlu bolak-balik menelepon.
    2. **Tombol WhatsApp Melayang:** Posisi tombol di sudut kanan bawah mudah dijangkau jempol saat dibuka dari ponsel dan langsung menyambung ke nomor WhatsApp admin untuk respons cepat.
    3. **Dasbor Admin & Kas Otomatis (OLED Dark Mode):** Tema warna gelap terasa teduh di mata saat menatap layar laptop, sedangkan jumlah kamar isi/kosong serta saldo kas masuk, kas keluar, dan laba bersih langsung terhitung otomatis tanpa coret-coretan buku besar.
    4. **Pendaftaran Penyewa Datang Langsung (*Form Walk-In*):** Kolom isian ringkas dan langsung menyimpan nomor telepon orang tua atau wali untuk keperluan darurat maupun penanganan tunggakan.
    5. **Konfirmasi Bayar Tunai & Kuitansi Instan:** Pelunasan tunai cukup diklik satu kali hingga kuitansi langsung terbit tanpa perlu menulis tangan di buku blok nota kertas yang rawan terselip.
    6. **Aturan Denda Flat 5% Kalender:** Dinilai adil dan bijaksana karena keterlambatan lewat tanggal 10 di bulan berjalan masih diberi masa keringanan bebas denda mengingat kiriman uang orang tua mahasiswa terkadang mundur beberapa hari, dan denda *flat* 5% satu kali baru dikenakan jika tagihan menyeberang ke bulan berikutnya.
    7. **Penguncian Kamar Pasca-*Checkout* (Karantina Fisik):** Dinilai sangat penting karena kamar yang baru ditinggal keluar wajib dibersihkan dan diperiksa keran maupun lampunya oleh petugas sebelum diubah manual menjadi tersedia di situs web agar tidak mengecewakan pemesan baru.
    8. **Pencatatan Biaya & Rekap Laporan Keuangan:** Memangkas waktu rekapitulasi akhir bulan dari 3 sampai 4 hari mengumpulkan nota bon menjadi hitungan detik lewat fitur ekspor PDF dan Excel untuk diserahkan kepada pemilik kost.
    9. **Portal Mandiri Anak Kost (BCA VA & Keluhan Berfoto):** Memudahkan mahasiswa membayar dari ponsel dan sangat menolong penjaga kost karena aduan kerusakan disertai foto bukti fisik yang bisa langsung diteruskan ke teknisi tanpa khawatir terlupa seperti laporan lisan di lorong.
    10. **Refleksi 20 Tahun Manual vs Web & Penilaian Akhir:** Perbedaan dirasakan sangat jauh dibanding cara manual selama 20 tahun karena pekerjaan menjadi ringan dan bebas selisih kas, dengan pemberian **skor kepuasan 9,5 hingga 10 dari skala 10**.
  * *Paragraf Analisis Tabel 4.2:* Merangkum skor kepuasan **9,5 dari 10** yang diberikan Bapak Asep berkat kenyamanan tema *OLED Black Dark Mode*, rekapitulasi kas instan tanpa selisih fisik, ketepatan aturan denda *flat* 5%, serta manfaat status karantina kamar pasca-*checkout*.

* **Poin 2: Hasil Wawancara dengan Arjun, Mahasiswa UNDIP (Paragraf Pengantar, Tabel 4.3, dan Paragraf Analisis):**
  * *Paragraf Pengantar:* Menggali pengalaman mahasiswa UNDIP yang telah tiga tahun mengekos di kawasan Tembalang dalam menelusuri katalog kamar dan bertransaksi lewat aplikasi *m-Banking*.
  * *Tabel 4.3 (10 Aspek Pengujian Arjun):* Responden menilai tampilan katalog *Neo-Brutalism*, foto fasilitas, dan lencana hijau "Tersedia" sangat membantu menemukan kamar kosong tanpa harus berkeliling Tembalang; alur formulir *stepper* 5 tahap terasa runtut dengan validasi otomatis NIK dan kontak orang tua; rincian biaya terbuka tanpa pungutan tersembunyi; *pop-up* BCA *Virtual Account* Midtrans praktis disalin ke *m-Banking*; status pembayaran berubah hijau "Lunas" dalam hitungan detik tanpa perlu mengirim tangkapan layar resi ke WhatsApp penjaga kost; kuitansi PDF format A5 berstempel resmi mudah diteruskan ke WhatsApp orang tua sebagai laporan bulanan; navigasi dasbor portal simpel; pesan pengingat WhatsApp tanggal 1 terasa sopan; pelunasan bulanan cukup dilakukan dari kamar; serta memberikan **skor 8,5 dari 10** disertai saran penambahan pengingat WhatsApp otomatis pada **H-3 sebelum jatuh tempo tanggal 10**.
  * *Paragraf Analisis Tabel 4.3:* Merangkum skor **8,5 dari 10** dari Arjun atas kejelasan katalog *Neo-Brutalism*, kepraktisan pembayaran BCA *Virtual Account* dan kuitansi A5 untuk pelaporan orang tua, serta usulan pengingat otomatis pada H-3 jatuh tempo.

* **Poin 3: Hasil Wawancara dengan Farrel, Mahasiswa USM (Paragraf Pengantar, Tabel 4.4, dan Paragraf Analisis):**
  * *Paragraf Pengantar:* Menyoroti pengalaman mahasiswa perantau luar kota yang berkuliah di USM saat mengakses sistem reservasi melalui ponsel Android dan melakukan pembayaran non-BCA.
  * *Tabel 4.4 (10 Aspek Pengujian Farrel):* Responden mengapresiasi katalog daring yang memudahkan perantau mengecek sudut kamar dan tarif tanpa harus datang ke Semarang; formulir bertahap tampil responsif di layar ponsel; nominal tagihan jelas di awal; verifikasi lunas otomatis menghemat waktu tanpa menunggu balasan penjaga kost; berkas kuitansi PDF A5 langsung tersimpan aman di memori ponsel; penghuni tidak perlu lagi mengantre tarik uang tunai di ATM pada malam hari; namun responden memberi catatan bahwa layar ponsel perlu digeser sedikit saat mencari tombol riwayat bayar dan dirinya harus melakukan transfer antarbank karena sehari-hari menggunakan *e-wallet*; sehingga memberikan **skor 7,0 dari 10** dengan saran mengaktifkan opsi **QRIS atau dompet digital (GoPay)**.
  * *Paragraf Analisis Tabel 4.4:* Merangkum skor **7,0 dari 10** dari Farrel atas kemudahan akses Android bagi perantau luar kota, praktisnya pelunasan tanpa antre ATM malam hari, serta masukan pengaktifan saluran QRIS dan dompet digital.

* **Poin 4: Hasil Wawancara dengan Iman, Mahasiswa UDINUS (Paragraf Pengantar, Tabel 4.5, dan Paragraf Analisis):**
  * *Paragraf Pengantar:* Membedah keandalan pencatatan pembayaran digital pada portal penghuni dan transparansi riwayat mutasi bulanan melalui pengujian di layar laptop bersama mahasiswa tingkat akhir UDINUS.
  * *Tabel 4.5 (10 Aspek Pengujian Iman):* Responden menilai informasi ketersediaan kamar benar-benar *real-time*; tampilan formulir di laptop rapi meskipun 5 tahapan terasa agak panjang saat menekan tombol Lanjut; rincian biaya sangat terbuka untuk mengatur anggaran; pembuatan nomor BCA *Virtual Account* cepat; responden sangat menyukai konfirmasi lunas otomatis karena pernah mengalami kejadian dituduh belum membayar di kost lama akibat pemilik lupa mencatat di buku; kuitansi PDF A5 berstempel resmi praktis disimpan ke Google Drive; tabel riwayat pembayaran tersusun urut per bulan; pengingat WhatsApp awal bulan tepat waktu dan menghilangkan rasa canggung ditagih lisan; serta memberikan **skor 9,0 dari 10** dengan saran teknis menjaga **stabilitas performa *server hosting*** saat diakses serentak pada tanggal 1 awal bulan.
  * *Paragraf Analisis Tabel 4.5:* Merangkum skor **9,0 dari 10** dari Iman atas akuntabilitas pencatatan mutasi otomatis yang menutup celah sengketa kuitansi hilang, kerapian arsip PDF A5, serta saran menjaga kestabilan *server hosting* di awal bulan.

* **Poin 5: Hasil Wawancara dengan Ammar, Mahasiswa PKN STAN (Paragraf Pengantar, Tabel 4.6, dan Paragraf Analisis):**
  * *Paragraf Pengantar:* Meninjau tanggapan mahasiswa baru PKN STAN saat menavigasi alur pemesanan dan pembayaran sewa melalui peramban Safari di iPhone.
  * *Tabel 4.6 (10 Aspek Pengujian Ammar):* Responden menilai tampilan katalog di Safari iPhone bersih dan memudahkan mahasiswa baru membandingkan tipe kamar; keberadaan kolom kontak darurat wali membuat orang tua di kampung halaman lebih tenang; rincian biaya sesuai dengan katalog; *pop-up* BCA *Virtual Account* berjalan lancar di Safari dan praktis disalin satu sentuhan; status lunas keluar seketika tanpa perlu mengirim foto bukti transfer; kuitansi PDF A5 tersimpan rapi di dokumen ponsel; tata letak dasbor nyaman dijangkau satu tangan; responden sangat terbantu karena tetap bisa membayar sewa sendiri saat libur semester di luar kota tanpa menitip uang tunai ke teman; serta memberikan **skor 7,5 dari 10** dengan saran menyematkan **tautan langsung (*direct link*) pada pesan WhatsApp** pengingat tagihan agar langsung membuka halaman pembayaran di portal.
  * *Paragraf Analisis Tabel 4.6:* Merangkum skor **7,5 dari 10** dari Ammar atas kenyamanan navigasi di Safari iPhone, kemudahan membayar sewa jarak jauh saat libur semester, serta usulan penambahan *direct link* pembayaran pada pesan WhatsApp.

* **Poin 6: Hasil Wawancara dengan Sindhu, Mahasiswa UI (Paragraf Pengantar, Tabel 4.7, dan Paragraf Analisis):**
  * *Paragraf Pengantar:* Menguji kenyamanan navigasi dasbor portal hunian dan kecepatan verifikasi pembayaran instan bersama penghuni kost berstatus mahasiswa UI.
  * *Tabel 4.7 (10 Aspek Pengujian Sindhu):* Responden menilai foto katalog kamar sesuai dengan kondisi fisik di lapangan; deteksi otomatis nomor telepon dan NIK mencegah salah ketik; rincian tarif terbuka untuk mengatur bujet bulanan; pembayaran BCA *Virtual Account* praktis (disertai harapan penambahan opsi QRIS atau *e-wallet*); verifikasi lunas *real-time* tanpa jeda menghilangkan rasa waswas; kuitansi digital A5 berstempel jauh lebih rapi dibanding nota kertas; pelunasan mandiri via web menghapus rasa canggung bertemu langsung pemilik kost; namun responden sempat membutuhkan beberapa menit saat pertama kali *login* untuk mengenali letak menu kuitansi dan pelaporan keluhan; sehingga memberikan **skor 8,0 dari 10** dengan saran penambahan **panduan visual singkat atau tur panduan (*onboarding tour*)** bagi pengguna baru.
  * *Paragraf Analisis Tabel 4.7:* Merangkum skor **8,0 dari 10** dari Sindhu atas kesesuaian visual katalog dengan kondisi asli kamar, keterbukaan rincian biaya, hilangnya rasa canggung saat membayar sewa, serta saran penambahan tur panduan visual singkat saat pertama kali masuk ke portal.

* **Paragraf Penutup / Sintesis Subbab 4.5:**  
  Secara keseluruhan, evaluasi wawancara pada skala 10 mencatat **skor kepuasan 9,5 dari penjaga kost** dan **rata-rata 8,0 dari lima mahasiswa penghuni** (`(8,5 + 7,0 + 9,0 + 7,5 + 8,0) / 5 = 8,0`). Pekerjaan tutup buku bulanan pengelola kini selesai dalam hitungan detik tanpa selisih kas fisik, sementara penghuni dapat memesan kamar jarak jauh dan melunasi sewa via *Virtual Account* Midtrans yang terverifikasi otomatis lengkap dengan kuitansi PDF resmi. Masukan responden terkait penambahan kanal QRIS dan *e-wallet*, tautan bayar langsung di WhatsApp, serta panduan singkat pengguna baru turut dicatat sebagai bahan pengembangan selanjutnya.

---

## BAB 5: KESIMPULAN DAN SARAN

Bab penutup merangkum capaian akhir dari seluruh tahapan penelitian dan rekayasa sistem, diikuti rekomendasi pengembangan untuk penelitian maupun pembaruan sistem berikutnya.

### 5.1 Kesimpulan (1 Paragraf Utuh Memuat 5 Pokok Simpulan)
Paragraf kesimpulan merangkai lima temuan utama penelitian secara padat dan berkesinambungan:
1. **Keberhasilan Perancangan dan Digitalisasi Terpusat:** Perancangan Sistem Informasi Manajemen Kost pada Asri Boarding House berbasis Laravel dan MySQL berhasil mendigitalisasi tata kelola hunian secara terpusat untuk mengatasi kelemahan promosi konvensional, risiko benturan pemesanan kamar ganda (*double booking*), dan kerumitan pembukuan manual.
2. **Keberhasilan Integrasi Pembayaran, Penjadwalan, dan Notifikasi:** Aplikasi memadukan gerbang pembayaran digital Midtrans dengan penjadwal tugas otomatis (*cron job*) yang mengeksekusi penerbitan tagihan bulanan setiap awal bulan, perhitungan denda keterlambatan secara konsisten, serta pengiriman pengingat melalui WhatsApp dan surat elektronik (*email*).
3. **Keberhasilan Pengujian Fungsionalitas (*Black Box*):** Pengujian sistem melalui 60 skenario fungsional *black box* membuktikan seluruh modul bekerja stabil dan akurat sesuai spesifikasi kebutuhan tanpa ditemukannya galat teknis.
4. **Dampak Efektivitas bagi Pengelola Indekos:** Wawancara mendalam bersama penjaga kost mengonfirmasi bahwa aplikasi memangkas waktu rekapitulasi tutup buku bulanan, meniadakan selisih kas fisik berkat verifikasi pembayaran non-tunai instan, dan mempermudah pemantauan okupansi 32 unit kamar.
5. **Dampak Kepuasan bagi Mahasiswa Penghuni Kost:** Evaluasi bersama lima mahasiswa penghuni kost menunjukkan kepuasan yang tinggi terhadap kepraktisan pelunasan sewa jarak jauh lewat *Virtual Account*, keterbukaan rincian tarif tanpa biaya tersembunyi, serta kemudahan akses kuitansi digital resmi.

### 5.2 Saran (1 Paragraf Utuh Memuat 5 Pokok Rekomendasi Pengembangan)
Paragraf saran menyajikan lima usulan konkret untuk penyempurnaan sistem di masa mendatang:
1. **Penyematan *Direct Link* Pembayaran di WhatsApp:** Pesan pengingat tagihan WhatsApp disarankan memuat tautan pembayaran langsung agar penghuni dapat menyelesaikan kewajiban sewa dalam satu kali klik tanpa harus mencari menu tagihan secara mandiri.
2. **Tur Panduan Visual Interaktif (*Onboarding Tour*):** Pengelola bersama pengembang sebaiknya melengkapi dasbor portal dengan tur panduan visual interaktif guna mempercepat adaptasi pengguna baru dalam mengenali tata letak menu dan fitur layanan.
3. **Perluasan Kanal Pembayaran Digital:** Kanal transaksi pada modul gerbang pembayaran digital dapat diperluas ke berbagai jaringan perbankan nasional tambahan serta dompet digital dan QRIS untuk memudahkan penghuni yang tidak memiliki rekening pada mitra utama saat ini.
4. **Pemantauan Utilitas Mandiri (Meteran Listrik Digital):** Fitur pemantauan utilitas mandiri berbasis pencatatan meteran listrik digital perlu dipertimbangkan agar kalkulasi pengeluaran energi tiap kamar tercatat lebih transparan bagi penghuni.
5. **Integrasi Kunci Pintu Digital Berbasis IoT:** Pengembangan sistem selanjutnya dapat mengintegrasikan kunci pintu digital berbasis *Internet of Things* (IoT) yang terhubung langsung dengan status keaktifan kontrak sewa di basis data.

---

## DAFTAR PUSTAKA (`[1]` sampai `[30]`)
Naskah ditutup dengan 30 referensi ilmiah berformat IEEE yang menopang seluruh argumen pada Bab 1 hingga Bab 3:
* **Referensi `[1]` sampai `[5]`:** Mendukung pemetaan masalah dan solusi pada latar belakang Bab 1, meliputi sistem pemesanan kost berbasis web Laravel, *e-kost*, pemantauan rumah kos dan pembayarannya, integrasi *payment gateway* Midtrans, serta aplikasi kas berbasis web dan WhatsApp Gateway.
* **Referensi `[6]` sampai `[27]`:** Menopang 11 sub-bab tinjauan pustaka pada Bab 2, mencakup konsep SIM `[6, 7]`, *E-Commerce* B2C dan *Property Management System* `[8, 9]`, *Web Application* `[10, 11]`, UI/UX dan *Design System* `[12, 13]`, arsitektur MVC Laravel `[14, 15]`, RDBMS dan normalisasi basis data `[16, 17]`, MySQL dan pengindeksan `[18, 19]`, Midtrans *Payment Gateway* `[20, 21]`, WhatsApp Gateway Fonnte `[22, 23]`, SMTP *Mail Gateway* dan notifikasi surel `[24, 25]`, serta *Cron Job* dan penjadwalan notifikasi otomatis `[26, 27]`.
* **Referensi `[28]` sampai `[30]`:** Melandasi metode penelitian pada Bab 3, yaitu metode *Research and Development* (R&D) `[28]`, metode penelitian kualitatif dan R&D menurut Sugiyono `[29]`, serta pengembangan perangkat lunak menggunakan metode SDLC *Waterfall* `[30]`.
