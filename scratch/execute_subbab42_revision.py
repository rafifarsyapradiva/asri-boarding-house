# -*- coding: utf-8 -*-
import re

file_path = r'Skripsi/skripsi_Rafif_Arsya_Pradiva_22.N4.0014.md'

with open(file_path, 'r', encoding='utf-8') as f:
    full_text = f.read()

# 1. Ekstrak 36 div gambar asli yang ada di dalam sub-bab 4.2
idx_start = full_text.find("## 4.2 Implementasi Sistem")
idx_end = full_text.find('<div style="page-break-after: always;"></div>\n\n<div align="center">\n\n## DAFTAR PUSTAKA')
if idx_end == -1:
    idx_end = full_text.find('## DAFTAR PUSTAKA')
    idx_pb = full_text.rfind('<div style="page-break-after: always;"></div>', idx_start, idx_end)
    if idx_pb != -1:
        idx_end = idx_pb

subbab_42_raw = full_text[idx_start:idx_end]

div_matches = re.findall(r'(<div align="center">\s*<img [^>]+>\s*<br>\s*\*Gambar 4\.\d+ [^*]+\*\s*</div>)', subbab_42_raw)
print(f"Total div gambar asli di sub-bab 4.2 ditemukan: {len(div_matches)}")
assert len(div_matches) == 36, f"Expected 36 divs, found {len(div_matches)}"


# 2. Definisikan narasi P1, P2, P3 untuk masing-masing 36 item
# Format: (title, p1, p2, p3)
narratives = [
    # 4.2.1 Implementasi Umum (Item 1 s.d. 11)
    (
        "1. Implementasi Antarmuka Home Page  ",
        "Halaman beranda utama berfungsi sebagai pusat informasi awal bagi masyarakat yang mencari hunian sewa di kawasan Tembalang. Antarmuka ini menampilkan profil Asri Boarding House beserta pilihan kamar dan fasilitas penunjang yang tersedia. Calon penghuni dapat meninjau kondisi umum indekos secara mandiri sebelum memutuskan untuk mendaftar.",
        "Desain tata letak dirancang responsif agar informasi tarif sewa tetap nyaman dibaca lewat layar komputer maupun ponsel. Tampilan lengkap beranda publik indekos diperlihatkan pada Gambar 4.7.",
        "Berdasarkan Gambar 4.7, bagian atas halaman memuat bilah navigasi utama yang berisi tautan menuju informasi kamar, fasilitas, cara booking, serta tombol akses akun. Sisi kiri layar menampilkan spanduk pengenalan indekos dengan tombol aksi cepat untuk mencari kamar, didampingi foto gedung fisik pada sisi kanan. Sudut kanan bawah antarmuka dilengkapi tombol obrolan tamu interaktif yang memudahkan pengunjung berkonsultasi langsung dengan pihak pengelola."
    ),
    (
        "2. Implementasi Antarmuka Fasilitas Kost  ",
        "Antarmuka fasilitas kost menyajikan sarana pendukung hunian yang dapat digunakan bersama oleh seluruh penghuni Asri Boarding House. Ketersediaan sarana penunjang dipaparkan secara terbuka guna memberikan informasi kenyamanan fisik bangunan indekos. Calon penyewa dapat menimbang kelayakan tempat tinggal berdasarkan sarana yang disediakan.",
        "Setiap fasilitas dilengkapi keterangan ringkas mengenai aturan pemakaian dan lokasi sarana di dalam area indekos. Rincian tampilan halaman fasilitas indekos dapat dilihat pada Gambar 4.8.",
        "Berdasarkan Gambar 4.8, antarmuka mengusung tata letak kartu berbasis kisi yang mengelompokkan fasilitas umum seperti dapur bersama, area parkir, internet nirkabel, dan kamera pengawas. Masing-masing kartu fasilitas dilengkapi foto dokumentasi asli, tajuk nama sarana berhuruf tebal, serta paragraf penjelasan fungsi ruangan. Tata letak responsif ini memudahkan pengguna menggulirkan layar untuk memeriksa seluruh fasilitas penunjang hunian secara efisien."
    ),
    (
        "3. Implementasi Antarmuka Tipe Kamar Kost  ",
        "Halaman tipe kamar kost menyajikan spesifikasi fisik dan perabot setiap unit kamar sewa di Asri Boarding House. Informasi ukuran ruang, fasilitas interior kamar mandi dalam, serta tarif sewa bulanan ditampilkan secara transparan. Kejelasan data ini membantu calon penyewa memilih unit kamar yang sesuai dengan kebutuhan dan anggaran pribadi.",
        "Pengguna dapat langsung menekan tombol pemesanan pada kartu kamar yang diminati untuk melanjutkan transaksi sewa. Visualisasi katalog kamar indekos disajikan pada Gambar 4.9.",
        "Berdasarkan Gambar 4.9, antarmuka memperlihatkan kartu kamar tipe Deluxe dengan ilustrasi foto interior kamar tidur dan kamar mandi dalam. Bagian bawah foto memuat informasi tarif sewa bulanan, lencana ketersediaan unit kamar, serta rincian fasilitas kasur, meja belajar, lemari, pendingin ruangan, dan pemanas air. Sudut bawah kartu dilengkapi tombol biru Pemesanan Kamar yang mengarahkan pengguna langsung ke alur pemesanan unit secara cepat."
    ),
    (
        "4. Implementasi Antarmuka Galeri Kost  ",
        "Antarmuka galeri kost mendokumentasikan foto lingkungan fisik bangunan dan suasana harian di Asri Boarding House. Tampilan visual ini menyajikan potret lingkungan sekitar, lorong kamar, area parkir, hingga kebersihan area komunal. Dokumentasi visual yang memadai membangun kepercayaan calon penyewa terhadap kondisi tempat tinggal yang sebenarnya.",
        "Seluruh foto disusun dalam format kisi rapi yang dapat diperbesar untuk memperjelas sudut pandang ruangan. Rincian galeri foto hunian indekos tercantum pada Gambar 4.10.",
        "Berdasarkan Gambar 4.10, halaman menampilkan susunan kisi foto lingkungan indekos yang mencakup tampak depan gedung, area parkir, lorong kamar lantai satu dan dua, serta dapur bersama. Setiap bingkai gambar memiliki efek transisi halus saat disentuh penunjuk tetikus untuk memberikan pengalaman penjelajahan visual yang interaktif. Penataan komposisi galeri ini memberikan representasi visual yang jelas mengenai kebersihan dan kerapian lingkungan indekos."
    ),
    (
        "5. Implementasi Antarmuka Alur Cara Reservasi  ",
        "Halaman alur cara reservasi memberikan panduan bertahap bagi calon penghuni dalam memesan kamar indekos secara daring. Petunjuk alur disusun berurutan mulai dari pendaftaran akun, pemilihan unit kamar, hingga penyelesaian transaksi uang muka. Edukasi alur ini memandu pengguna baru agar tidak mengalami kebingungan selama proses reservasi berlangsung.",
        "Diagram alur visual dilengkapi instruksi ringkas agar pemohon dapat menyiapkan kelengkapan identitas diri lebih awal. Ilustrasi tahapan alur reservasi kamar diperlihatkan pada Gambar 4.11.",
        "Berdasarkan Gambar 4.11, antarmuka menyajikan bagan urutan langkah pemesanan kamar yang terdiri dari pendaftaran akun, pemilihan unit kamar, pengisian biodata, pembayaran uang muka lewat Midtrans, dan penerimaan bukti reservasi. Setiap tahapan diwakili oleh nomor urut berdesain tegas dan kartu penjelasan yang memuat ringkasan aksi pengguna. Visualisasi bertahap ini mempermudah calon penghuni memahami prosedur sewa kamar secara runtut tanpa perlu bertanya berulang kali kepada pengelola."
    ),
    (
        "6. Implementasi Antarmuka Testimoni Penyewa Kost  ",
        "Antarmuka testimoni memuat ulasan dan penilaian pengalaman dari para penghuni yang tinggal di Asri Boarding House. Informasi testimoni ini menjadi bukti sosial atas kualitas pelayanan, kenyamanan lingkungan, dan keandalan sarana indekos. Calon penyewa dapat meninjau pandangan penghuni lain sebagai bahan pertimbangan objektif sebelum memesan kamar.",
        "Ulasan disajikan secara bergiliran dalam kartu geser yang memuat nama penghuni dan nilai bintang kepuasan. Tampilan komponen testimoni penghuni kost dapat dilihat pada Gambar 4.12.",
        "Berdasarkan Gambar 4.12, antarmuka menampilkan deretan kartu testimoni yang berisi kutipan ulasan penghuni terkait kebersihan kamar, keamanan lingkungan, dan keramahan pengelola. Setiap kartu dilengkapi foto profil penghuni, nama samaran, status kamar yang dihuni, serta ikon bintang lima sebagai indikator kepuasan fasilitas. Tata letak komidi putar interaktif memudahkan calon penyewa menggeser ulasan secara mulus untuk membaca pengalaman penghuni lain."
    ),
    (
        "7. Implementasi Antarmuka FAQ  ",
        "Halaman FAQ merangkum daftar pertanyaan yang sering diajukan calon penyewa seputar ketentuan sewa di Asri Boarding House. Informasi mencakup aturan jam malam, kebijakan tamu menginap, ketentuan uang muka, serta metode perpanjangan sewa bulanan. Rangkuman ini memberikan jawaban cepat sehingga mengurangi beban komunikasi rutin pihak pengelola.",
        "Format akordeon interaktif memungkinkan pengunjung membuka dan menutup jawaban pertanyaan secara efisien. Susunan halaman tanya jawab ini diperlihatkan pada Gambar 4.13.",
        "Berdasarkan Gambar 4.13, halaman menyajikan daftar pertanyaan umum yang dikelompokkan dalam panel akordeon bertata letak rapi dan mudah dibaca. Setiap bilah pertanyaan dilengkapi ikon panah yang dapat diklik untuk menampilkan atau menyembunyikan teks jawaban secara ringkas. Pengorganisasian topik pertanyaan yang jelas membantu calon penyewa menemukan jawaban atas aturan indekos secara cepat tanpa harus membuka banyak halaman."
    ),
    (
        "8. Implementasi Antarmuka Tentang Kami  ",
        "Antarmuka Tentang Kami memaparkan identitas operasional, visi kenyamanan hunian, dan komitmen pelayanan Asri Boarding House. Halaman ini juga menyajikan informasi titik lokasi geografis indekos di Tembalang yang terhubung dengan peta digital. Pemaparan profil usaha yang jelas menumbuhkan rasa aman bagi calon mahasiswa dan perantau yang hendak mencari tempat tinggal.",
        "Halaman ini memuat kanal kontak resmi seperti nomor telepon, surat elektronik, dan tautan media sosial pengelola indekos. Tampilan profil dan peta lokasi hunian indekos disajikan pada Gambar 4.14.",
        "Berdasarkan Gambar 4.14, antarmuka menyajikan kartu profil Asri Boarding House yang memuat sejarah singkat indekos, keunggulan tata kelola hunian, dan etika pelayanan lingkungan. Sisi samping halaman menampilkan sematan peta Google Maps interaktif yang menandai titik koordinat presisi gedung indekos dekat kampus. Bagian bawah memuat kontak pengelola, alamat surat elektronik resmi, serta jam operasional layanan kantor sekretariat indekos."
    ),
    (
        "9. Implementasi Antarmuka Login User  ",
        "Antarmuka masuk pengguna menjadi pintu gerbang autentikasi akun bagi calon penyewa dan penghuni terdaftar. Modul ini memverifikasi kecocokan alamat surat elektronik dan kata sandi yang tersimpan di basis data secara aman. Penerapan sesi terenkripsi memastikan akses data pribadi pengguna terlindungi dari risiko pembajakan akun.",
        "Halaman menyediakan tautan pemulihan kata sandi bagi pengguna yang mengalami kendala lupa kode rahasia akun. Antarmuka autentikasi masuk pengguna diperlihatkan pada Gambar 4.15.",
        "Berdasarkan Gambar 4.15, antarmuka login dirancang berbingkai kartu neo-brutalisme dengan garis tepi hitam tegas berlatar belakang kuning cerah. Formulir memuat kolom input alamat email, kata sandi berfitur intip sandi, kotak centang Ingat Saya, dan tombol biru Masuk ke Akun. Sisi bawah formulir menyediakan tautan navigasi menuju pendaftaran akun baru bagi calon pengguna yang belum memiliki kredensial resmi."
    ),
    (
        "10. Implementasi Antarmuka Register User  ",
        "Antarmuka pendaftaran akun memfasilitasi pembuatan akun pengguna baru bagi calon penyewa yang ingin memesan kamar indekos. Formulir mengumpulkan data dasar seperti nama lengkap, nomor WhatsApp aktif, alamat email, dan konfirmasi kata sandi. Pendaftaran mandiri ini mempercepat pencatatan data calon penghuni secara terstruktur sebelum memasuki alur sewa.",
        "Sistem langsung memvalidasi keunikan alamat email dan kekuatan kata sandi saat pengguna mengisi formulir pendaftaran. Tampilan antarmuka registrasi pengguna baru tercantum pada Gambar 4.16.",
        "Berdasarkan Gambar 4.16, halaman pendaftaran menampilkan kartu bertajuk Buat Akun Baru dengan latar belakang beraksen geometris modern. Formulir menyediakan kolom isian nama lengkap, nomor WhatsApp aktif, alamat email, kata sandi, serta konfirmasi kata sandi dengan validasi pesan galat interaktif. Tombol biru Daftar Akun di bagian bawah kartu memproses penyimpanan identitas akun baru secara langsung ke basis data sistem."
    ),
    (
        "11. Implementasi Antarmuka Chat Real Time Guest  ",
        "Fitur obrolan langsung tamu memungkinkan pengunjung situs berkonsultasi secara instan dengan pengelola tanpa perlu masuk ke akun terlebih dahulu. Pengunjung cukup memasukkan nama dan nomor kontak untuk memulai percakapan seputar ketersediaan kamar dan jadwal survei lokasi. Kanal komunikasi langsung ini meningkatkan minat calon penghuni dalam mengambil keputusan sewa.",
        "Pesan obrolan tersinkronisasi langsung ke dasbor pengelola sehingga pertanyaan calon penyewa dapat direspons secara tepat waktu. Bentuk widget percakapan langsung tamu diperlihatkan pada Gambar 4.17.",
        "Berdasarkan Gambar 4.17, jendela obrolan tamu muncul mengambang pada sudut kanan bawah antarmuka dengan bingkai kuning cerah dan tombol minimalkan jendela di sisi atas. Badan obrolan menampilkan riwayat gelembung percakapan antara tamu dan admin lengkap dengan label penunjuk waktu pengiriman pesan. Bilah bawah menyediakan bidang pengetikan pesan interaktif beserta tombol kirim yang langsung menyalurkan teks pertanyaan pengunjung ke pengelola."
    ),

    # 4.2.2 Implementasi Calon Penyewa (Item 12 s.d. 18)
    (
        "1. Implementasi Antarmuka Lengkapi Profil  ",
        "Antarmuka pelengkap profil mewajibkan calon penyewa mengunggah identitas resmi sebelum melakukan reservasi unit kamar. Formulir meminta data nomor induk kependudukan, kontak darurat keluarga, instansi pekerjaan atau kampus, serta foto dokumen KTP asli. Verifikasi identitas ini menjaga keamanan bersama dan ketertiban lingkungan penghuni di Asri Boarding House.",
        "Berkas identitas dienkripsi dan disimpan dalam direktori aman yang hanya dapat diverifikasi oleh admin pengelola. Format halaman pelengkapan profil calon penyewa disajikan pada Gambar 4.18.",
        "Berdasarkan Gambar 4.18, antarmuka menyajikan formulir pengisian data diri yang mencakup nomor KTP, nama darurat keluarga, nomor telepon penjamin, serta bidang unggah foto identitas. Terdapat panduan visual ukuran berkas dan pratinjau gambar untuk memastikan foto kartu identitas terbaca jelas sebelum disimpan. Tombol hijau Simpan Profil di bagian bawah mengunci data verifikasi calon penyewa ke dalam basis data sistem."
    ),
    (
        "2. Implementasi Antarmuka Dashboard Reservasi  ",
        "Dasbor reservasi berfungsi sebagai pusat kendali calon penyewa dalam memantau seluruh proses pemesanan kamar yang sedang diajukan. Halaman ini merangkum status reservasi aktif, nomor tagihan uang muka, serta riwayat interaksi pemesanan kamar sebelumnya. Pemusatan informasi ini memudahkan pemohon meninjau tahapan reservasi secara mandiri dan transparan.",
        "Terdapat indikator kemajuan proses yang memberi tahu langkah selanjutnya yang harus diselesaikan oleh pemohon sewa. Visualisasi dasbor reservasi calon penghuni disajikan pada Gambar 4.19.",
        "Berdasarkan Gambar 4.19, antarmuka menyajikan spanduk selamat datang dan kartu ringkasan reservasi yang memuat nomor pesanan, unit kamar pilihan, dan status verifikasi akun. Panel tengah menampilkan bilah penunjuk tahapan alur reservasi mulai dari pengajuan berkas hingga konfirmasi pembayaran sewa. Kartu aksi di sisi kanan menyediakan tombol Bayar Uang Muka serta tautan langsung untuk mengunduh bukti tanda terima reservasi."
    ),
    (
        "3. Implementasi Antarmuka Simulasi Biaya Kamar  ",
        "Antarmuka simulasi biaya kamar membantu calon penyewa menghitung total pengeluaran sewa sesuai durasi waktu tinggal yang diinginkan. Kalkulator ini menghitung tarif sewa pokok, biaya deposit jaminan, dan potongan diskon promo sewa jangka panjang secara otomatis. Transparansi perhitungan ini mencegah kesalahpahaman nominal pembayaran antara pemohon dan pengelola indekos.",
        "Pengguna dapat memilih durasi sewa bulanan, tiga bulanan, atau tahunan untuk melihat perbandingan total tagihan secara seketika. Tampilan modul simulasi biaya sewa kamar ditunjukkan pada Gambar 4.20.",
        "Berdasarkan Gambar 4.20, antarmuka menyediakan kartu kalkulator biaya yang dilengkapi pilihan menu tarik-turun durasi sewa dan opsi fasilitas tambahan. Tabel rincian pembiayaan di sisi kanan langsung memperbarui akumulasi biaya sewa pokok, uang jaminan deposit, dan total tagihan bersih secara seketika. Tombol biru Lanjutkan Pemesanan di bagian bawah tabel memungkinkan calon penyewa meneruskan hasil simulasi ke formulir transaksi reservasi."
    ),
    (
        "4. Implementasi Antarmuka Pembayaran 5 Tahap Reservasi  ",
        "Antarmuka alur pembayaran memandu calon penghuni menyelesaikan lima tahapan transaksi pemesanan kamar secara berurutan. Tahapan mencakup konfirmasi data sewa, pemilihan metode setor, verifikasi tagihan, pemrosesan gerbang pembayaran, dan penerbitan nota tanda jadi. Alur terstruktur ini menjamin keamanan pencatatan dana sewa dan kepastian alokasi unit kamar bagi pemohon.",
        "Indikator langkah visual memberi tahu status aktif dari setiap proses pembayaran yang sedang berjalan. Rangkaian lima tahapan pembayaran reservasi diperlihatkan pada Gambar 4.21.",
        "Berdasarkan Gambar 4.21, bagian atas antarmuka memuat indikator penunjuk langkah bertingkat yang menyorot tahapan pembayaran aktif dengan lingkaran angka berwarna hijau. Bagian tengah menampilkan rincian ringkasan kamar, rincian biaya uang muka, batas waktu penyelesaian transaksi, serta tombol pemicu gerbang bayar digital. Antarmuka juga menyajikan panduan singkat bagi calon penyewa agar tidak menutup jendela peramban sebelum transaksi diverifikasi oleh sistem."
    ),
    (
        "5. Implementasi Payment Gateway Midtrans  ",
        "Integrasi gerbang pembayaran Midtrans Snap memungkinkan calon penghuni melunasi biaya sewa kamar menggunakan berbagai kanal pembayaran digital resmi. Sistem mendukung transaksi melalui transfer rekening virtual bank, dompet digital, kartu kredit, dan gerai retail swalayan. Konfirmasi pembayaran otomatis mengeliminasi verifikasi mutasi manual yang memakan waktu lama.",
        "Webhook Midtrans langsung menyinkronkan status lunas ke basis data begitu dana berhasil disetorkan pengguna. Antarmuka popup pembayaran digital Midtrans ditampilkan pada Gambar 4.22.",
        "Berdasarkan Gambar 4.22, jendela sembulan Midtrans Snap muncul di tengah layar menampilkan total nominal tagihan sewa dan tenggat waktu penyelesaian transaksi. Antarmuka menyediakan pilihan kanal bayar seperti GoPay, QRIS, Virtual Account BCA, BNI, Mandiri, dan gerai retail dengan instruksi setor yang lengkap. Tampilan antarmuka ini menjamin transaksi berjalan aman karena diawasi langsung oleh sistem enkripsi gerbang pembayaran resmi."
    ),
    (
        "6. Implementasi WhatsApp Gateway Fonnte  ",
        "Layanan gerbang pesan WhatsApp Fonnte mengirimkan notifikasi instan ke nomor ponsel calon penyewa begitu pembayaran berhasil diverifikasi. Pesan otomatis memuat nomor faktur, rincian kamar yang dipesan, nominal uang muka, serta tanggal mulai menghuni indekos. Pengiriman pesan instan ini memberi kepastian transaksi yang cepat dan mudah dibaca lewat ponsel penghuni.",
        "Integrasi API Fonnte berjalan secara latar belakang melalui sistem pemicu kejadian di peladen web. Bentuk pesan notifikasi WhatsApp tanda jadi sewa disajikan pada Gambar 4.23.",
        "Berdasarkan Gambar 4.23, tangkapan layar aplikasi WhatsApp menampilkan pesan masuk otomatis dari akun resmi Asri Boarding House dengan centang verifikasi waktu pengiriman. Teks pesan merinci ucapan terima kasih, nomor referensi reservasi, tipe kamar, nominal pembayaran lunas, dan tautan unduh nota elektronik. Notifikasi ini memberikan konfirmasi tertulis yang sah bagi calon penyewa sebagai bukti resmi pemesanan kamar indekos."
    ),
    (
        "7. Implementasi Email Gateway SMTP  ",
        "Gerbang email SMTP mengirimkan salinan faktur resmi dan dokumen perjanjian sewa ke alamat surat elektronik calon penyewa. Salinan digital ini berfungsi sebagai arsip cadangan resmi yang dapat dicetak sewaktu-waktu oleh penyewa untuk keperluan administrasi pribadi. Pengiriman email otomatis memperkuat akuntabilitas pencatatan transaksi hunian indekos.",
        "Surat elektronik dirancang menggunakan templat HTML profesional yang memuat logo dan rincian kontak resmi indekos. Tampilan email bukti tanda jadi sewa kamar diperlihatkan pada Gambar 4.24.",
        "Berdasarkan Gambar 4.24, antarmuka surat elektronik menampilkan templat resmi berlogo Asri Boarding House dengan tajuk Bukti Pembayaran Reservasi Kamar Kost. Badan email memuat tabel rincian transaksi sewa, informasi jadwal masuk kamar, tata tertib indekos, serta lampiran dokumen nota dalam format PDF. Penyajian email ini memastikan penghuni memiliki dokumen arsip digital yang rapi dan terstruktur di kotak masuk surat elektronik pribadi."
    ),

    # 4.2.3 Implementasi Admin (Item 19 s.d. 29)
    (
        "1. Implementasi Antarmuka Admin Login  ",
        "Antarmuka masuk admin merupakan pintu akses terbatas yang khusus disediakan bagi pengelola operasional Asri Boarding House. Modul ini menerapkan proteksi otentikasi ketat guna memisahkan hak akses data manajerial dari pengguna umum. Pengamanan ini mencegah pihak yang tidak berwenang memanipulasi data kamar, keuangan, dan data pribadi penghuni.",
        "Halaman dilengkapi perlindungan pembatasan upaya login untuk mengantisipasi serangan keamanan siber. Tampilan formulir masuk pengelola indekos disajikan pada Gambar 4.25.",
        "Berdasarkan Gambar 4.25, halaman login admin tampil dengan bingkai kartu bergaya neo-brutalisme bergaris tepi hitam pekat dengan tajuk Masuk Portal Admin. Formulir menyediakan kolom isian nama pengguna atau email pengelola, bidang kata sandi bersandi rahasia, serta kotak opsi ingat sesi login. Tombol hitam tebal Masuk Dasbor di bagian bawah memvalidasi hak otorisasi akun sebelum mengarahkan admin ke ruang kendali operasional."
    ),
    (
        "2. Implementasi Antarmuka Dashboard Admin  ",
        "Dasbor utama admin menyajikan ringkasan metrik statistik operasional Asri Boarding House secara terpadu. Pengelola dapat memantau tingkat keterisian 32 unit kamar, total pemasukan kas bulanan, jumlah penyewa aktif, serta keluhan yang belum terselesaikan. Pemantauan terpusat ini membantu pengambil keputusan mengevaluasi kinerja bisnis indekos secara cepat dan akurat.",
        "Diagram visual interaktif menyajikan tren pendapatan dan pengeluaran kas rutin dari bulan ke bulan. Tampilan dasbor analitik pengelola diperlihatkan pada Gambar 4.26.",
        "Berdasarkan Gambar 4.26, layar dasbor menampilkan deretan kartu metrik berwarna kuning dan biru yang merangkum angka okupansi kamar, jumlah penghuni aktif, total pendapatan, dan keluhan tertunda. Bagian tengah memuat grafik batang statistik pendapatan bulanan serta tabel daftar reservasi terbaru yang membutuhkan verifikasi admin. Bilah navigasi vertikal di sisi kiri layar menyediakan akses instan menuju seluruh menu manajemen operasional indekos."
    ),
    (
        "3. Implementasi Antarmuka Manajemen Kamar  ",
        "Modul manajemen kamar memberikan kendali penuh bagi pengelola untuk mendata status, fasilitas, dan tarif seluruh unit di Asri Boarding House. Admin dapat menambah kamar baru, memperbarui ketersediaan unit, atau mengubah rincian harga sewa secara mandiri. Pengelolaan data fisik kamar yang tertib mencegah kesalahan penempatan hunian bagi calon penyewa.",
        "Daftar kamar dilengkapi fitur penyaring lantai dan status hunian untuk mempermudah inventarisasi ruang indekos. Tampilan antarmuka manajemen data kamar disajikan pada Gambar 4.27.",
        "Berdasarkan Gambar 4.27, antarmuka menyajikan tabel inventaris 32 unit kamar lengkap dengan nomor kamar, tipe kamar, penetapan lantai, harga sewa bulanan, dan lencana status keterisian. Sudut atas tabel menyediakan tombol Tambah Kamar Baru berwarna kuning cerah, kolom pencarian cepat, serta menu penyaring unit kamar kosong. Ujung kanan setiap baris memuat tombol aksi Edit dan Hapus yang mempermudah pembaruan data spesifikasi kamar secara seketika."
    ),
    (
        "4. Implementasi Antarmuka Manajemen Fasilitas  ",
        "Antarmuka manajemen fasilitas digunakan untuk memperbarui sarana komunal yang ditampilkan pada katalog publik indekos. Pengelola dapat menambah sarana baru, mengubah deskripsi aturan pakai, dan mengunggah foto dokumentasi fisik ruangan. Pembaruan informasi sarana yang berkala memastikan fasilitas di website selalu mencerminkan kondisi lapangan sebenarnya.",
        "Sistem menyertakan kontrol urutan tata letak fasilitas agar sarana unggulan dapat ditonjolkan pada halaman utama. Tampilan antarmuka manajemen sarana indekos ditunjukkan pada Gambar 4.28.",
        "Berdasarkan Gambar 4.28, halaman menampilkan kartu tabel fasilitas umum yang memuat nama fasilitas, keterangan fungsi ruangan, ikon kategori, dan foto pratinjau sarana. Sisi atas tabel memuat tombol Tambah Fasilitas dengan aksen kuning khas serta bidang filter pencarian nama sarana. Kolom aksi di sisi kanan menyediakan kontrol untuk mengedit narasi fasilitas atau menonaktifkan sarana yang sedang dalam masa perbaikan fisik."
    ),
    (
        "5. Implementasi Antarmuka Manajemen Penyewa  ",
        "Antarmuka manajemen penyewa memuat basis data seluruh penghuni aktif yang tinggal di Asri Boarding House. Modul ini menyimpan riwayat identitas diri, nomor kontak darurat keluarga, nomor kamar yang ditempati, serta tanggal berakhirnya masa sewa. Manajemen data penghuni yang rapi memudahkan pengelola menjalin komunikasi dan memeriksa ketertiban sewa.",
        "Admin dapat menonaktifkan akun penyewa saat masa sewa berakhir atau mencetak biodata penghuni untuk arsip lingkungan. Format antarmuka manajemen data penyewa diperlihatkan pada Gambar 4.29.",
        "Berdasarkan Gambar 4.29, antarmuka menyajikan tabel komprehensif data penghuni yang mencantumkan nama penyewa, nomor WhatsApp, unit kamar hunian, tanggal mulai sewa, dan lencana status Aktif. Panel atas tabel dilengkapi fitur pencarian nama penyewa serta tombol penyaring penghuni berdasarkan nomor lantai kamar. Tombol Detail di sisi kanan baris membuka berkas dokumen identitas KTP penghuni dan riwayat keterlambatan pembayaran sewa secara lengkap."
    ),
    (
        "6. Implementasi Antarmuka Manajemen Reservasi  ",
        "Antarmuka manajemen reservasi mengelola daftar pesanan kamar baru yang diajukan oleh calon penyewa melalui website. Pengelola dapat meninjau kelengkapan identitas pemesan, memeriksa status transfer uang muka, dan menyetujui pengalokasian unit kamar. Penanganan reservasi yang terpusat mencegah terjadinya pesanan ganda pada kamar yang sama.",
        "Admin dapat membatalkan pesanan kedaluwarsa secara otomatis agar unit kamar dapat dipesan kembali oleh calon penyewa lain. Tampilan antarmuka pemantauan reservasi disajikan pada Gambar 4.30.",
        "Berdasarkan Gambar 4.30, halaman menampilkan tabel transaksi reservasi kamar lengkap dengan nomor pesanan, nama calon penghuni, tipe kamar, tanggal mulai sewa, dan status pembayaran uang muka. Sisi atas kartu menyajikan panel filter status pesanan mulai dari Menunggu Pembayaran, Terverifikasi, hingga Dibatalkan. Kolom aksi menyediakan tombol Konfirmasi untuk menyetujui penempatan kamar serta tombol Rincian untuk memeriksa data identitas pemohon."
    ),
    (
        "7. Implementasi Antarmuka Manajemen Tagihan  ",
        "Antarmuka manajemen tagihan mengontrol siklus penerbitan faktur sewa bulanan bagi seluruh penghuni aktif Asri Boarding House. Modul ini memantau masa jatuh tempo pembayaran setiap tanggal sepuluh guna meminimalkan keterlambatan kas masuk. Rekapitulasi status tagihan secara terpusat memastikan tata kelola keuangan indekos berjalan tertib dan transparan.",
        "Pengelola dapat menyaring riwayat pembayaran dan memverifikasi dana sewa yang terhubung langsung ke Midtrans secara praktis. Tampilan antarmuka manajemen tagihan sewa bulanan disajikan pada Gambar 4.31.",
        "Berdasarkan Gambar 4.31, halaman menampilkan kartu bertajuk Daftar Tagihan Bulanan Penyewa dengan tombol sinkronisasi tagihan dan filter pencarian di sisi atas. Tabel rekapitulasi memuat rincian nomor invoice, nama penghuni, unit kamar, nominal tagihan sewa, tanggal jatuh tempo, lencana status Belum Dibayar, serta kolom aksi. Tombol Detail di ujung kanan baris memungkinkan pengelola meninjau rincian komponen tagihan dan menerbitkan ulang bukti faktur sewa."
    ),
    (
        "8. Implementasi Antarmuka Manajemen Pengeluaran  ",
        "Antarmuka manajemen pengeluaran mencatat seluruh pengeluaran kas operasional harian dan perawatan fisik gedung indekos. Pengelola mendata biaya listrik, tagihan air PDAM, pembelian perlengkapan kebersihan, dan upah perbaikan fasilitas. Pencatatan arus keluar kas yang teratur menjaga keakuratan laba bersih usaha Asri Boarding House.",
        "Setiap pengeluaran dapat dilampiri foto bukti kuitansi belanja untuk menjamin keabsahan audit keuangan bulanan. Tampilan antarmuka pencatatan biaya operasional tercantum pada Gambar 4.32.",
        "Berdasarkan Gambar 4.32, antarmuka menyediakan formulir input biaya operasional dan tabel riwayat pengeluaran kas bulanan yang tertata rapi. Tabel memuat tanggal transaksi, kategori pengeluaran, uraian peruntukan dana, nominal biaya, dan nama staf penanggung jawab pengeluaran. Sisi atas tabel menyajikan rekapitulasi total akumulasi pengeluaran bulan berjalan serta tombol Tambah Biaya Operasional untuk mencatat transaksi kas baru."
    ),
    (
        "9. Implementasi Antarmuka Manajemen Laporan Pemasukan  ",
        "Modul laporan pemasukan mengolah akumulasi pembayaran sewa kamar menjadi laporan keuangan komprehensif bagi pemilik indekos. Sistem memadukan data transaksi Midtrans dan pembayaran tunai guna menghitung total pendapatan kotor dan bersih. Laporan keuangan terstruktur ini menjadi dasar evaluasi bisnis dan perencanaan ekspansi usaha indekos ke depan.",
        "Laporan keuangan dapat diekspor ke dalam berkas Excel dan dokumen PDF untuk kebutuhan arsip pembukuan resmi. Visualisasi rekapitulasi laporan pendapatan disajikan pada Gambar 4.33.",
        "Berdasarkan Gambar 4.33, halaman menyajikan dasbor rekapitulasi keuangan yang memuat ringkasan pendapatan sewa kotor, total pengeluaran operasional, dan laba bersih usaha. Panel tengah menampilkan grafik garis tren pemasukan bulanan serta tabel rincian transaksi kas masuk dengan kolom penyaring rentang tanggal. Bagian atas kartu menyediakan tombol hijau Ekspor Excel dan tombol merah Unduh PDF untuk mencetak laporan keuangan berkala."
    ),
    (
        "10. Implementasi Antarmuka Manajemen Keluhan Penyewa  ",
        "Antarmuka manajemen keluhan mengelola laporan kendala sarana yang dikirimkan oleh penghuni Asri Boarding House. Pengelola dapat memantau tingkat kegentingan aduan kerusakan fasilitas air, listrik, atau gangguan ketertiban lingkungan. Penanganan keluhan yang cepat dan terdokumentasi meningkatkan kepuasan dan loyalitas tinggal para penyewa.",
        "Status laporan dapat diubah dari tahap diterima, sedang diproses, hingga selesai ditangani oleh tim perbaikan indekos. Tampilan modul penanganan keluhan fasilitas indekos diperlihatkan pada Gambar 4.34.",
        "Berdasarkan Gambar 4.34, antarmuka menampilkan tabel daftar keluhan yang mencakup nama pelapor, nomor kamar, kategori kendala sarana, tanggal pelaporan, dan lencana status penanganan. Tombol aksi di sisi kanan memungkinkan admin membuka foto bukti kerusakan fasilitas serta memperbarui status perbaikan menjadi Selesai. Panel filter di atas tabel memudahkan pengelola memilah laporan yang membutuhkan tindakan darurat segera."
    ),
    (
        "11. Implementasi Antarmuka Manajemen Fitur Chat Guest  ",
        "Antarmuka manajemen obrolan tamu menjadi pusat layanan admin dalam melayani pertanyaan pengunjung situs secara langsung. Pengelola dapat membalas pesan calon penyewa, menjelaskan ketersediaan unit kamar, dan mengarahkan tamu menuju proses survei lokasi. Kecepatan tanggapan melalui fitur obrolan interaktif ini secara nyata meningkatkan konversi pemesanan kamar indekos.",
        "Sistem menampilkan notifikasi audio dan visual saat pesan tamu baru masuk ke ruang obrolan pengelola. Tampilan antarmuka penanganan pesan tamu disajikan pada Gambar 4.35.",
        "Berdasarkan Gambar 4.35, antarmuka mengadopsi tata letak obrolan dua panel dengan daftar kontak tamu di sisi kiri dan jendela percakapan aktif pada panel kanan. Panel obrolan menampilkan riwayat pertukaran pesan teks lengkap dengan penanda waktu serta status pesan terkirim. Kotak pengetikan di bagian bawah dilengkapi pintasan respons templat cepat untuk mempermudah admin menjawab pertanyaan berulang secara efisien."
    ),

    # 4.2.4 Implementasi Penyewa Aktif (Item 30 s.d. 36)
    (
        "1. Implementasi Antarmuka Login Penyewa Aktif  ",
        "Antarmuka masuk penyewa aktif memberikan jalur autentikasi khusus bagi penghuni yang telah resmi menempati unit kamar indekos. Modul ini mengarahkan penghuni langsung ke portal layanan mandiri kamar tanpa tercampur dengan alur reservasi calon penyewa. Autentikasi sesi terproteksi memastikan riwayat pembayaran dan data privasi penghuni tetap aman.",
        "Halaman menyediakan validasi kredensial instan yang terintegrasi dengan basis data penyewa aktif di Asri Boarding House. Format tampilan halaman masuk penyewa aktif disajikan pada Gambar 4.36.",
        "Berdasarkan Gambar 4.36, antarmuka masuk penyewa dirancang dalam kartu neo-brutalisme beraksen kuning cerah dengan tajuk Masuk Portal Penghuni Aktif. Formulir menyediakan kolom isian alamat email, kolom kata sandi berfitur tampilkan sandi, serta tombol centang ingatkan akun. Tombol biru Masuk ke Portal di bawah formulir memproses verifikasi hak akses penghuni secara cepat menuju dasbor personal."
    ),
    (
        "2. Implementasi Antarmuka Dashboard Penyewa  ",
        "Dasbor penyewa berfungsi sebagai pusat layanan mandiri bagi penghuni Asri Boarding House selama masa sewa berlangsung. Halaman ini memuat informasi nomor kamar hunian, masa berlaku masa sewa, status tagihan bulan berjalan, dan pengumuman dari pengelola. Akses informasi yang mudah meningkatkan kemandirian penghuni dalam mengelola kebutuhan tempat tinggalnya.",
        "Kartu navigasi cepat memungkinkan penyewa mengakses menu pembayaran tagihan dan pengajuan keluhan fasilitas secara praktis. Tampilan dasbor layanan mandiri penyewa diperlihatkan pada Gambar 4.37.",
        "Berdasarkan Gambar 4.37, antarmuka menampilkan spanduk selamat datang personal dan kartu ringkasan hunian yang memuat nomor kamar aktif, tipe sewa, dan tanggal jatuh tempo tagihan berikutnya. Panel tengah menyajikan kartu status tagihan bulanan berlatar kuning cerah yang dilengkapi tombol Bayar Tagihan Sekarang. Sisi bawah antarmuka memuat bilah menu cepat untuk melaporkan keluhan sarana, mengunduh tanda terima sewa, dan meninjau pengumuman pengelola."
    ),
    (
        "3. Implementasi Antarmuka Tagihan Penyewa  ",
        "Antarmuka tagihan penyewa menyajikan rincian faktur pembayaran sewa kamar bulanan yang harus diselesaikan oleh penghuni. Modul ini menguraikan biaya pokok sewa kamar, denda keterlambatan jika ada, dan batas akhir waktu pembayaran sewa setiap bulannya. Transparansi rincian biaya ini memudahkan penghuni mengatur pembayaran tepat waktu sebelum jatuh tempo.",
        "Tombol bayar terhubung langsung ke Midtrans Snap sehingga penghuni dapat melunasi tagihan menggunakan dompet digital atau transfer bank secara instan. Visualisasi halaman tagihan sewa bulanan disajikan pada Gambar 4.38.",
        "Berdasarkan Gambar 4.38, halaman menampilkan kartu tagihan sewa bulanan yang mencantumkan nomor invoice, periode bulan sewa, nominal tagihan, dan batas waktu jatuh tempo pembayaran. Terdapat lencana status Belum Dibayar berwarna kuning yang menandakan tagihan sewa sedang menunggu pelunasan. Tombol hijau Bayar Tagihan di bagian bawah kartu langsung memunculkan sembulan Midtrans Snap untuk menyelesaikan setoran secara daring."
    ),
    (
        "4. Implementasi Antarmuka Profil  ",
        "Antarmuka profil mengelola informasi biodata diri, nomor kontak darurat keluarga, dan kredensial keamanan akun penyewa aktif. Penghuni dapat memperbarui nomor telepon WhatsApp atau mengubah kata sandi akun secara mandiri sewaktu-waktu. Pembaruan kontak yang berkala memastikan pengelola dapat selalu menghubungi penghuni saat terjadi situasi darurat di indekos.",
        "Modul ini memproteksi data sensitif dengan enkripsi dan sensor privasi pada tampilan publik guna mencegah kebocoran informasi. Tampilan antarmuka profil penghuni kost tercantum pada Gambar 4.39.",
        "Berdasarkan Gambar 4.39, antarmuka menyajikan kartu profil penghuni yang memuat foto akun, nama lengkap, nomor KTP, nomor WhatsApp aktif, dan nomor kontak darurat keluarga yang tersensor demi privasi. Sisi kanan kartu menyediakan formulir pembaruan kata sandi yang dilengkapi validasi sandi lama dan konfirmasi sandi baru. Tombol kuning Simpan Perubahan di sudut bawah memastikan pembaruan data identitas penghuni tersimpan aman di basis data sistem."
    ),
    (
        "5. Implementasi Antarmuka Riwayat Pembayaran  ",
        "Antarmuka riwayat pembayaran mendokumentasikan arsip seluruh transaksi sewa yang pernah diselesaikan penghuni selama tinggal di indekos. Halaman mencatat nomor transaksi, tanggal setor dana, metode pembayaran yang digunakan, serta nominal uang yang dibayarkan. Arsip digital yang rapi memberikan bukti transaksi yang sah bagi penghuni maupun pengelola indekos.",
        "Setiap baris riwayat transaksi dilengkapi tombol unduh kuitansi resmi dalam format PDF yang dapat diakses kapan saja. Susunan antarmuka rekapitulasi pembayaran penyewa ditampilkan pada Gambar 4.40.",
        "Berdasarkan Gambar 4.40, antarmuka menyajikan tabel rekapitulasi transaksi pembayaran yang memuat tanggal pembayaran, nomor referensi pesanan, metode bayar transfer virtual account, nominal dana lunas, dan lencana status Berhasil berwarna hijau. Ujung kanan setiap baris transaksi menyediakan tombol biru Cetak Nota untuk mengunduh bukti tanda terima sewa dalam format berkas digital. Tata letak tabel yang teratur mempermudah penghuni memeriksa jejak pelunasan sewa kamar dari bulan ke bulan."
    ),
    (
        "6. Implementasi Antarmuka Keluhan  ",
        "Antarmuka keluhan memfasilitasi penghuni dalam menyampaikan laporan gangguan sarana atau ketertiban di lingkungan Asri Boarding House. Penghuni dapat memilih kategori kendala fasilitas kamar, melampirkan foto bukti fisik kerusakan, dan menuliskan penjelasan masalah secara detail. Saluran laporan mandiri ini mempercepat penanganan perbaikan fasilitas tanpa harus mencari pengelola secara tatap muka.",
        "Halaman menampilkan linimasa status penanganan aduan mulai dari laporan diterima hingga selesai diperbaiki oleh staf indekos. Tampilan antarmuka pelaporan keluhan penghuni diperlihatkan pada Gambar 4.41.",
        "Berdasarkan Gambar 4.41, halaman menampilkan formulir Tambah Laporan Keluhan di sisi atas yang memuat pilihan kategori kendala, deskripsi kerusakan fasilitas, dan bidang unggah foto dokumentasi fisik. Bagian bawah menyajikan riwayat keluhan yang pernah dikirimkan lengkap dengan lencana status proses perbaikan fasilitas. Tabel ini membantu penghuni memantau tindak lanjut staf teknisi indekos terhadap kendala sarana yang dilaporkan."
    ),
    (
        "7. Implementasi Nota PDF  ",
        "Implementasi nota PDF menghasilkan dokumen tanda terima pembayaran sewa kamar yang sah secara otomatis dari sistem. Berkas digital ini mencantumkan nomor faktur resmi, identitas penghuni, nomor kamar hunian, rincian biaya pokok sewa, serta stempel lunas digital. Penerbitan nota digital menjamin bukti transaksi yang akuntabel bagi penyewa dan pengelola indekos.",
        "Berkas nota dirancang menggunakan ukuran cetak termal rapi dengan tata letak proporsional yang bebas dari risiko terpotong. Bukti fisik cetak nota digital dalam format dokumen PDF disajikan pada Gambar 4.42.",
        "Berdasarkan Gambar 4.42, dokumen nota pembayaran tampil dalam format faktur bersih berbingkai garis tegas dengan tajuk resmi Asri Boarding House dan alamat lengkap indekos di bagian kepala nota. Badan kuitansi memuat rincian nomor nota transaksi, tanggal bayar, nomor kamar hunian, identitas penyewa yang tersensor privasi, serta rincian nominal sewa sebesar Rp 1.700.000 dengan cap LUNAS tebal di tengah dokumen. Bagian bawah nota memuat catatan konfirmasi pembayaran digital otomatis serta kode verifikasi transaksi sistem sebagai jaminan keabsahan dokumen sewa."
    )
]

assert len(narratives) == 36

# 3. Susun teks baru sub-bab 4.2
def format_item(idx, item_tuple, div_html):
    title, p1, p2, p3 = item_tuple
    # Format blok
    res = f"{title}\n{p1}\n\n{p2}\n\n{div_html}\n\n{p3}"
    return res

items_421 = [format_item(i, narratives[i], div_matches[i]) for i in range(0, 11)]
items_422 = [format_item(i, narratives[i], div_matches[i]) for i in range(11, 18)]
items_423 = [format_item(i, narratives[i], div_matches[i]) for i in range(18, 29)]
items_424 = [format_item(i, narratives[i], div_matches[i]) for i in range(29, 36)]

sec_421_str = "\n\n<br>\n\n".join(items_421)
sec_422_str = "\n\n<br>\n\n".join(items_422)
sec_423_str = "\n\n<br>\n\n".join(items_423)
sec_424_str = "\n\n<br>\n\n".join(items_424)

new_subbab_42 = f"""## 4.2 Implementasi Sistem

<br>

### 4.2.1 Implementasi Umum

{sec_421_str}

<br>

### 4.2.2 Implementasi Calon Penyewa

{sec_422_str}

<br>

### 4.2.3 Implementasi Admin

{sec_423_str}

<br>

### 4.2.4 Implementasi Penyewa Aktif

{sec_424_str}

<br>
"""

# 4. Potong full_text dan gantikan sub-bab 4.2
idx_start = full_text.find("## 4.2 Implementasi Sistem")
idx_end = full_text.find('<div style="page-break-after: always;"></div>\n\n<div align="center">\n\n## DAFTAR PUSTAKA')

if idx_end == -1:
    idx_end = full_text.find('## DAFTAR PUSTAKA')
    # Cari div page-break sebelumnya
    idx_pb = full_text.rfind('<div style="page-break-after: always;"></div>', idx_start, idx_end)
    if idx_pb != -1:
        idx_end = idx_pb

print(f"Index start: {idx_start}, Index end: {idx_end}")

final_text = full_text[:idx_start] + new_subbab_42.rstrip() + "\n\n" + full_text[idx_end:]

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(final_text)

print("SUCCESS: Sub-bab 4.2 telah diperbarui ke berkas skripsi!")
