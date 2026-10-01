SISTEM INFORMASI MANAJEMEN KOST TERINTEGRASI PAYMENT GATEWAY PADA ASRI BOARDING HOUSE

SKRIPSI
Diajukan sebagai salah satu syarat untuk menyusun Tugas Akhir guna mencapai gelar Sarjana Komputer pada Program Studi Sistem Informasi Universitas Katolik Soegijapranata Semarang
 



Disusun oleh:
RAFIF ARSYA PRADIVA
NIM: 22.N4.0014



Kepada
PROGRAM STUDI SISTEM INFORMASI
FAKULTAS ILMU KOMPUTER
UNIVERSITAS KATOLIK SOEGIJAPRANATA
SEMARANG
2026 
SISTEM INFORMASI MANAJEMEN KOST TERINTEGRASI PAYMENT GATEWAY PADA ASRI BOARDING HOUSE


Skripsi
Diajukan sebagai salah satu syarat untuk menyusun Tugas Akhir guna mencapai gelar Sarjana Komputer pada Program Studi Sistem Informasi Universitas Katolik Soegijapranata Semarang


Disusun oleh:
RAFIF ARSYA PRADIVA
22.N4.0014

Disetujui oleh:
Dosen Pembimbing Skripsi


Ir. Andre Kurniawan Pamudji, S.Kom, M.Ling.
NPP: 058.1.1994.161

Kepada
PROGRAM STUDI SISTEM INFORMASI
FAKULTAS ILMU KOMPUTER
UNIVERSITAS KATOLIK SOEGIJAPRANATA
SEMARANG
2026 
## HALAMAN PERNYATAAN ORISINALITAS
Saya yang bertanda tangan di bawah ini:

Nama			: Rafif Arsya Pradiva
NIM			: 22.N4.0014
Program Studi		: Sistem Informasi
Fakultas		: Ilmu Komputer

menyatakan dengan sesungguhnya bahwa Skripsi dengan judul “Sistem Informasi Manajemen Kost Terintegrasi Payment Gateway pada Asri Boarding House” adalah benar-benar hasil karya saya sendiri, bukan merupakan jiplakan dari karya tulis orang lain, baik sebagian maupun keseluruhan. Pendapat, gagasan, atau temuan orang lain yang terdapat di dalam Skripsi ini telah dikutip dan dirujuk berdasarkan kaidah penulisan ilmiah yang berlaku.
Apabila di kemudian hari terbukti bahwa pernyataan ini tidak benar, saya bersedia menerima sanksi akademik sesuai dengan ketentuan yang berlaku di Universitas Katolik Soegijapranata Semarang.

Semarang, ........................ 2026
Yang menyatakan,

Materai
Rp10.000

Rafif Arsya Pradiva
NIM. 22.N4.0014
## HALAMAN PERNYATAAN BEBAS PLAGIASI
Saya yang bertanda tangan di bawah ini:

Nama			: Rafif Arsya Pradiva
NIM			: 22.N4.0014
Program Studi		: Sistem Informasi
Fakultas		: Ilmu Komputer

menyatakan dengan sesungguhnya bahwa Skripsi yang saya susun telah bebas dari unsur plagiasi. Seluruh sumber, baik berupa kutipan langsung maupun tidak langsung, yang berasal dari karya orang lain telah disebutkan sumbernya secara jujur dan dicantumkan dalam Daftar Pustaka sesuai dengan format penulisan ilmiah yang berlaku.
Apabila di kemudian hari ditemukan adanya pelanggaran terhadap etika keilmuan dalam karya ini, atau terdapat klaim dari pihak lain terhadap keaslian karya ini, saya bersedia mempertanggungjawabkannya dan menerima sanksi sesuai dengan peraturan yang berlaku.

Semarang, ........................ 2026
Yang menyatakan,

Rafif Arsya Pradiva
NIM. 22.N4.0014
## HALAMAN PENGESAHAN
Skripsi dengan judul:

SISTEM INFORMASI MANAJEMEN KOST TERINTEGRASI PAYMENT GATEWAY PADA ASRI BOARDING HOUSE

yang dipersiapkan dan disusun oleh:
Rafif Arsya Pradiva — NIM. 22.N4.0014

telah diperiksa dan disetujui oleh Dosen Pembimbing untuk diajukan dalam Seminar Skripsi pada Program Studi Sistem Informasi Fakultas Ilmu Komputer Universitas Katolik Soegijapranata Semarang.




Semarang, ........................ 2026

Telah diperiksa dan disetujui
Semarang, 13-07-2026

	Pembimbing 1	Pembimbing 2


	Ir. Andre Kurniawan Pamudji, S.Kom, M.Ling	NAMA
NPP. 581.2021.403					NPP. ….
## KATA PENGANTAR
Puji dan syukur senantiasa penulis panjatkan ke hadirat Tuhan Yang Maha Esa atas kelimpahan rahmat, bimbingan, dan karunia-Nya, sehingga naskah Skripsi dengan judul “Sistem Informasi Manajemen Kost Terintegrasi Payment Gateway pada Asri Boarding House” ini dapat terselesaikan dengan baik. Penyusunan naskah ini merupakan bagian dari pemenuhan syarat akademis guna menyelesaikan program studi Strata Satu (S-1) dan meraih gelar Sarjana Komputer pada Program Studi Sistem Informasi, Fakultas Ilmu Komputer, Universitas Katolik Soegijapranata Semarang.

Keberhasilan penulisan skripsi ini tentu tidak lepas dari bimbingan, arahan, serta dukungan moril maupun materiil dari berbagai pihak. Rasa hormat dan terima kasih yang mendalam penulis sampaikan kepada:
•	Rektor Universitas Katolik Soegijapranata Semarang beserta seluruh jajarannya atas lingkungan akademik yang kondusif selama masa perkuliahan;
•	Dekan Fakultas Ilmu Komputer Universitas Katolik Soegijapranata Semarang atas dukungan sarana pendidikan di lingkungan fakultas;
•	Ketua Program Studi Sistem Informasi atas motivasi, regulasi akademik, dan arahan kurikulum yang senantiasa menunjang kemajuan studi mahasiswa;
•	Ir. Andre Kurniawan Pamudji, S.Kom, M.Ling., selaku Dosen Pembimbing Skripsi, yang telah dengan tulus meluangkan waktu, memberikan telaah kritis, serta membagikan wawasan yang sangat berharga dalam membimbing penulis menyelesaikan naskah ini;
•	Bapak Asep selaku pengelola Asri Boarding House, atas keterbukaan, kerja sama yang hangat, serta kesediaan berbagi data operasional dan waktu dalam sesi wawancara hingga pengujian sistem;
•	Keluarga tercinta, khususnya kedua orang tua, yang senantiasa melangitkan doa tanpa putus, memberikan kasih sayang tulus, serta dorongan moral dan finansial yang menjadi kekuatan terbesar bagi penulis;
•	Rekan-rekan mahasiswa Program Studi Sistem Informasi angkatan 2022 serta seluruh sahabat yang telah saling menyemangati, bertukar gagasan, dan berjuang bersama melewati setiap tahapan studi.

Penulis menyadari bahwa tulisan ini masih memiliki ruang untuk penyempurnaan. Oleh sebab itu, berbagai masukan, telaah, dan saran yang bersifat konstruktif senantiasa penulis sambut dengan tangan terbuka demi perbaikan karya ilmiah ini di masa mendatang. Semoga hasil penelitian dan artefak perangkat lunak yang dikembangkan dapat memberikan sumbangsih nyata, baik bagi literatur sistem informasi terapan maupun kemajuan operasional bisnis hunian kos skala menengah.

Semarang, ........................ 2026
Penulis,

Rafif Arsya Pradiva
## HALAMAN PERNYATAAN PERSETUJUAN PUBLIKASI KARYA ILMIAH UNTUK KEPENTINGAN AKADEMIS
Sebagai sivitas akademika Universitas Katolik Soegijapranata Semarang, saya yang bertanda tangan di bawah ini:

Nama			: Rafif Arsya Pradiva
NIM			: 22.N4.0014
Program Studi		: Sistem Informasi
Fakultas		: Ilmu Komputer
Jenis Karya		: Skripsi

demi pengembangan ilmu pengetahuan, menyetujui untuk memberikan kepada Program Studi Sistem Informasi Fakultas Ilmu Komputer Universitas Katolik Soegijapranata Semarang Hak Bebas Royalti Noneksklusif (Non-exclusive Royalty-Free Right) atas karya ilmiah saya yang berjudul di atas beserta perangkat yang ada (jika diperlukan).
Dengan Hak Bebas Royalti Noneksklusif ini, Program Studi Sistem Informasi berhak menyimpan, mengalih-media/format-kan, merawat, dan memublikasikan karya ilmiah tersebut untuk kepentingan akademis tanpa perlu meminta izin dari saya selama tetap mencantumkan nama saya sebagai penulis/pencipta dan sebagai pemilik Hak Cipta. Hak Cipta atas karya ilmiah ini tetap berada pada penulis.
Demikian pernyataan ini saya buat dengan sebenarnya.

Dibuat di Semarang
Pada tanggal ........................ 2026
Yang menyatakan,

Rafif Arsya Pradiva
NIM. 22.N4.0014
## ABSTRAK
Rafif Arsya Pradiva. 22.N4.0014. SISTEM INFORMASI MANAJEMEN KOST TERINTEGRASI PAYMENT GATEWAY PADA ASRI BOARDING HOUSE (Studi Kasus: Asri Boarding House, Tembalang, Kota Semarang).
Operasional Asri Boarding House di kawasan Tembalang, Kota Semarang, menaungi 32 unit kamar dengan potensi perputaran dana sewa mencapai Rp28.500.000 setiap bulannya. Ketergantungan pada pembukuan fisik dan koordinasi pesan instan selama puluhan tahun memicu sejumlah kendala operasional, seperti risiko selisih rekapitulasi kas, sulitnya memvalidasi setoran tunai, keterlambatan pengingat jatuh tempo, hingga kerentanan pemesanan ganda (double booking). Untuk mengatasi persoalan tersebut, penelitian ini mengembangkan Sistem Informasi Manajemen Kost berbasis framework Laravel 11 yang memadukan modul reservasi mandiri dan gerbang pembayaran digital. Pengembangan sistem menerapkan pendekatan Research and Development (R&D) melalui model sekuensial linier Waterfall, mencakup analisis kebutuhan, perancangan arsitektur, penulisan kode, serta validasi komprehensif. Perangkat lunak dibangun di atas arsitektur 3-tier berpola MVC dengan skema basis data MySQL 8.x InnoDB yang ternormalisasi 3NF. Transaksi pembayaran difasilitasi melalui Midtrans Snap API, didukung perenderan kuitansi digital instan format A5 di sisi klien via html2pdf.js (zero server load) dan pustaka Dompdf untuk laporan berkala pemilik, serta gateway WhatsApp Fonnte dan SMTP untuk pengiriman notifikasi otomatis. Sistem dilengkapi mesin penagihan berkala setiap awal bulan, aturan denda flat 5% yang idempoten pada pergantian bulan kalender dengan eskalasi ke nomor wali, alur kerja hibrida pendaftaran penghuni, serta kanal obrolan pra-pembayaran berbasis AJAX polling. Pengujian fungsional melalui black box testing (60 skenario) dan automated feature test PHPUnit dengan tingkat kelulusan 100% (510 tests passed, 2.211 assertions) untuk memvalidasi logika penagihan, denda, dan pencegahan kondisi balapan (race condition). Sistem telah diimplementasikan pada lingkungan produksi live (https://asriboardinghouse.weatso.id/) dengan hasil evaluasi pengguna yang menunjukkan peningkatan efisiensi kerja administrasi dan pencegahan risiko kesalahan pencatatan transaksi kas.
Kata kunci: sistem informasi manajemen kos, payment gateway, Laravel 11, reservasi online, penagihan otomatis, html2pdf.js
## ABSTRACT
Rafif Arsya Pradiva. 22.N4.0014. Design and Development of an Integrated Boarding House Management Information System with Payment Gateway in Asri Boarding House (Case Study: Asri Boarding House, Tembalang, Semarang City).
Asri Boarding House operates 32 rooms in Tembalang, Semarang City, generating potential monthly rental revenue of IDR 28,500,000. Decades of reliance on paper ledger books and informal messaging led to persistent operational hurdles, including cash reconciliation discrepancies, tedious manual receipt verification, delayed due-date reminders, and the risk of room double-booking. To address these vulnerabilities, this research engineered a Boarding House Management Information System built on the Laravel 11 framework, coupling an online self-service reservation module with an integrated payment gateway. The development followed a Research and Development (R&D) approach using the linear-sequential Waterfall model across requirement analysis, system modeling, coding, and multi-tier verification. The software architecture employs a 3-tier MVC structure powered by an InnoDB MySQL 8.x relational schema normalized to 3NF. Cashless transactions are handled via the Midtrans Snap API, complemented by instant client-side A5 digital receipt rendering using html2pdf.js (zero server load), Dompdf for managerial exports, and the Fonnte WhatsApp API alongside SMTP for automated messaging. Key operational logic incorporates scheduled monthly invoicing, an idempotent calendar-based 5% flat late fee with tiered escalation to tenant guardians, hybrid walk-in/online registration, and pre-payment chat communication driven by AJAX polling. Comprehensive blackbox testing (60 test cases) and PHPUnit automated suites (510 tests passed, 2,211 assertions) achieved a 100% pass rate, validating transactional integrity, billing logic, and concurrency locking against race conditions. Live production deployment at https://asriboardinghouse.weatso.id/ demonstrated operational efficacy during qualitative usability testing with the senior property manager, securing a 9.5 out of 10 satisfaction rating and substantially accelerating monthly administrative reconciliation.
Keywords: boarding house management information system, payment gateway, Laravel 11, online reservation, automated billing, html2pdf.js

 

## DAFTAR ISI
HALAMAN PERNYATAAN ORISINALITAS	ii
HALAMAN PERNYATAAN BEBAS PLAGIASI	iii
HALAMAN PENGESAHAN	iv
KATA PENGANTAR	v
HALAMAN PERNYATAAN PERSETUJUAN PUBLIKASI KARYA ILMIAH UNTUK KEPENTINGAN AKADEMIS	vii
ABSTRAK	viii
ABSTRACT	ix
DAFTAR ISI	x
DAFTAR TABEL	xii
DAFTAR GAMBAR	xiii
BAB I PENDAHULUAN	1
1.1 Latar Belakang	1
1.2 Rumusan Masalah	4
1.3 Tujuan Penelitian	5
1.4 Batasan Masalah	6
1.5 Manfaat Penelitian	8
1.5.1 Manfaat Teoretis	8
1.5.2 Manfaat Praktis	8
1.6 Sistematika Penulisan	9
BAB II  TINJAUAN PUSTAKA DAN LANDASAN TEORI	11
2.1 Landasan Teori	11
2.1.1 Sistem Informasi dan Sistem Informasi Manajemen	11
2.1.2 Sistem Berbasis Web dan E-Commerce Model B2C	11
2.1.3 Sistem Manajemen Basis Data Relasional dan Normalisasi 3NF	12
2.1.4 Arsitektur 3-Tier dan Model-View-Controller pada Laravel 11	12
2.1.5 Antarmuka Pemrograman Aplikasi (API) Eksternal: Midtrans dan Fonnte	13
2.1.6 Penjadwalan Tugas, Mesin Penagihan Otomatis, dan Cron Job	13
2.1.7 Teori Interaktivitas UI/UX: Neo-Brutalism dan WCAG 2.1	14
2.1.8 Keamanan Web dan Integritas Data	14
2.1.9 Concurrency Control dan Mekanisme Penguncian Basis Data	15
2.2 Penelitian Terdahulu (State of the Art)	15
2.3 Kerangka Pemikiran	17
BAB III  METODOLOGI PENELITIAN	20
3.1 Jenis Penelitian	20
3.2 Teknik Pengumpulan Data	20
3.3 Metode Pengembangan Sistem	21


BAB IV HASIL DAN PEMBAHASAN	22
4.1 Perancangan Sistem	22
4.1.1 Use Case Diagram	22
4.1.2 Activity Diagram	24
4.1.3 Flowchart Diagram	27
4.1.4 Sequence Diagram	29
4.1.5 Entity Relationship Diagram (ERD)	31
4.1.6 Skenario Diagram Alur Sistem	32
4.2 Implementasi Sistem	33
4.2.1 Lingkungan Implementasi	33
4.2.2 Perancangan Arsitektur Aplikasi	34
4.2.3 Implementasi Basis Data	35
4.2.4 Implementasi Autentikasi dan Hak Akses	38
4.2.5 Implementasi Fitur Calon Penyewa	39
4.2.6 Implementasi Fitur Penyewa Aktif	40
4.2.7 Implementasi Fitur Admin	41
4.2.8 Implementasi Integrasi Payment Gateway	42
4.2.9 Implementasi Notifikasi Email SMTP	43
4.2.10 Implementasi Notifikasi WhatsApp FONNTE	44
4.3 Tampilan Antarmuka Sistem	45
4.3.1 Antarmuka Calon Penyewa	45
4.3.2 Antarmuka Penyewa Aktif	47
4.3.3 Antarmuka Admin	48
4.4 Hasil Deployment	50
4.4.1 Skrip Kompilasi Bundel Aset Produksi	50
4.4.2 Konfigurasi Peladen Web LiteSpeed/Apache	51
4.4.3 Konfigurasi Penjadwal Tugas Peladen (Cron Job)	52
4.4.4 Perintah Optimasi Kinerja Produksi Laravel	53
4.4.5 Konfigurasi Variabel Lingkungan Produksi	54
4.5 Hasil Pengujian Sistem	56
4.5.1 Hasil Black Box Testing	56
4.5.2 Hasil Pengujian Hak Akses	62
4.5.3 Hasil Pengujian Transaksi Midtrans Sandbox (BCA VA)	64
4.5.4 Hasil Pengujian Hak Akses Live	66
4.6 Evaluasi Kualitatif dan Pengujian Operasional Langsung Bersama Penjaga Kost	67
4.6.1 Hasil Pengujian Langsung dan Wawancara Mendalam dengan Bapak Asep	67
4.7 Pembahasan	72
BAB V KESIMPULAN DAN SARAN	74
5.1 Kesimpulan	74
5.2 Saran	75
DAFTAR PUSTAKA	77

## DAFTAR TABEL
Tabel 2.1  Perbandingan Penelitian Terdahulu (State of the Art)	16
Tabel 4.1  Matriks Pemetaan Status Transaksional dan Transisi State Siklus Hidup Sistem	33
Tabel 4.2  Struktur dan Fungsi 22 Tabel Basis Data Sistem Asri Boarding House	35
Tabel 4.3  Matriks Hasil Pengujian Fungsionalitas Kotak Hitam (60 Butir Skenario Uji)	56
Tabel 4.4  Matriks Pengujian Hak Akses dan Isolasi Peran Pengguna	62
Tabel 4.5  Matriks Pengujian Transaksi Midtrans Snap Saluran Bank BCA Virtual Account	64
Tabel 4.6  Hasil Pengujian Parameter Keamanan dan Hak Akses Lingkungan Live	66
Tabel 4.7  Matriks Hasil Evaluasi Kualitatif dan Observasi Pengujian Operasional Langsung Bersama Penjaga Kost Senior	68

## DAFTAR GAMBAR
Gambar 2.1  Kerangka Pemikiran Penelitian	18
Gambar 3.1  Diagram Model Waterfall Pengembangan Sistem	20
Gambar 4.1  Use Case Diagram Terpadu Sistem Asri Boarding House	22
Gambar 4.2  Activity Diagram Tiga Proses Kritis Sistem Asri Boarding House	24
Gambar 4.3  Flowchart Mekanisme Billing Engine dan Penanganan Keterlambatan Massal	27
Gambar 4.4  Sequence Diagram Alur Transaksional Utama Sistem Asri Boarding House	29
Gambar 4.5  Entity Relationship Diagram (ERD) Skema Basis Data 22 Tabel	31
Gambar 4.6  Skenario Diagram Siklus Hidup Transaksional Penyewa dan Operasional Administrator	32
Gambar 4.7  Antarmuka Katalog Kamar Publik Neo-Brutalisme	45
Gambar 4.8  Workspace Stepper Alur Reservasi Calon Penyewa	46
Gambar 4.9  Portal Invoice dan Kuitansi Digital Penyewa	47
Gambar 4.10  Dasbor Administrasi Keuangan Administrator	48
Gambar 4.11  Dokumentasi Evaluasi Kualitatif dan Sesi Wawancara Bersama Bapak Asep	70
