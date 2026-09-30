# HUMANIZE CHANGELOG
## Sistem Informasi Manajemen Kost Terintegrasi Payment Gateway pada Asri Boarding House
**Penulis**: Rafif Arsya Pradiva (NIM: 22.N4.0014)  
**Dokumen Acuan**: `Skripsi/Skripsi_Rafif_Arsya_Pradiva_22N40014.md`  
**Waktu Eksekusi**: September 2026

---

### 1. BAGIAN YANG DIRESTRUKTURISASI
* **Bab I (Pendahuluan)**:
  * Restrukturisasi alur logika Latar Belakang (Sub-bab 1.1) dari format paragraf formulaik menjadi narasi akademis yang mengalir natural:
    1. Konteks digitalisasi dan pergeseran perilaku konsumen menuju layanan *self-service* 24/7 [1].
    2. Perkembangan *proptech* dan dinamika sewa kos di kawasan kampus.
    3. Konsep dasar Sistem Informasi Manajemen (SIM) dalam mengonsolidasikan data operasional [2].
    4. Profil dan skala operasional Asri Boarding House (32 kamar, 3 tipe kamar, potensi pendapatan Rp28.500.000/bulan).
    5. Masalah aktual penagihan manual dan ketiadaan eskalasi terstruktur ke wali penyewa.
    6. Kendala pencatatan buku fisik, ketiadaan *audit trail*, dan risiko *double booking*.
    7. Hambatan survei fisik konvensional bagi calon mahasiswa luar kota dan pentingnya kanal komunikasi pra-pembayaran.
    8. Kendala pembukuan kas lepas bagi rekapitulasi laba rugi bulanan pemilik.
    9. Telaah penelitian terdahulu [3]–[5] dan identifikasi kesenjangan riset (*research gap*).
    10. Solusi sistemik terpadu berbasis Laravel 11, Midtrans Snap, Fonnte WA, dan arsitektur *3-tier* MVC.
  * Restrukturisasi kalimat pada Rumusan Masalah (1.2) dan Tujuan Penelitian (1.3) agar selaras dalam 6 butir terpadu dengan kaidah bahasa akademis yang jernih.
  * Restrukturisasi uraian Batasan Masalah (1.4), Manfaat Penelitian (1.5), dan Sistematika Penulisan (1.6) untuk mempertegas batas ruang lingkup tanpa menghilangkan rincian teknis.
* **Bab II (Tinjauan Pustaka dan Landasan Teori)**:
  * Restrukturisasi Sub-bab 2.1.1 hingga 2.1.9: mengubah gaya penulisan yang sebelumnya mirip "kamus definisi" menjadi penjelasan konseptual terapan yang menguraikan konsep, relevansi teknis, dan penerapannya secara spesifik pada Asri Boarding House.
  * Restrukturisasi paragraf sintesis pasca-Tabel 2.1 (State of the Art) untuk menonjolkan orisinalitas dan kebaruan kombinasi fitur (*novelty*) tanpa kesan promosi.
  * Restrukturisasi narasi Kerangka Pemikiran (Sub-bab 2.3) dalam alur empat tingkat (*Input \u2192 Process \u2192 Output \u2192 Outcome*).
* **Bab III (Metodologi Penelitian)**:
  * Penajaman alur deskripsi metodologi R&D [16] dan tiga teknik pengumpulan data kualitatif (observasi lapangan, wawancara mendalam bersama Bapak Asep, dan telaah dokumentasi).
  * Penjelasan tahapan model *Waterfall* [17], [18] yang sistematis dan terhubung erat dengan batasan waktu penelitian akademik enam bulan.
* **Bab IV (Hasil dan Pembahasan)**:
  * Sub-bab 4.1.2 (Activity Diagram): restrukturisasi deskripsi alur penagihan, denda, dan transisi penyewa; menghapus istilah informal/kolokial.
  * Sub-bab 4.2.4 – 4.2.6 (Implementasi Modul): penghilangan gaya bahasa brosur promosi pada modul calon penyewa dan penyewa aktif, menggantikannya dengan laporan rekayasa teknis yang baku.
  * Sub-bab 4.4.5 (Konfigurasi Variabel Lingkungan): pembersihan dan penyembunyian (*redaction*) seluruh kredensial rahasia produksi (password database, API token, server key, client key, APP key, dan email admin).
  * Sub-bab 4.5.1 – 4.5.2 (Hasil Pengujian): penghilangan frasa overclaim (*zero defect* dan *kebal*) pada kesimpulan pengujian kotak hitam dan hak akses.
  * Sub-bab 4.7 (Pembahasan): restrukturisasi 5 poin pembahasan ilmiah dari gaya pemasaran menjadi evaluasi pencapaian empiris berbasis data pengujian dan wawancara.
* **Bab V (Kesimpulan dan Saran)**:
  * Sub-bab 5.1 (Kesimpulan): restrukturisasi 6 poin kesimpulan agar menjawab secara presisi 6 rumusan masalah penelitian dengan formulasi ilmiah yang objektif.

---

### 2. BAGIAN YANG DIPARAFRASE SECARA SEMANTIK (*SEMANTIC REWRITE*)
* **Abstrak & Abstract**:
  * Parafrase penutup abstrak Bahasa Indonesia dan *Abstract* Bahasa Inggris untuk menyelaraskan klaim dampak operasional dari frasa absolut menjadi formulasi terukur ("peningkatan efisiensi kerja administrasi dan pencegahan risiko kesalahan pencatatan transaksi kas").
* **Sub-bab 1.1 (Latar Belakang)**:
  * Seluruh 10 paragraf ditulis ulang berbasis makna (*semantic rewrite*), memvariasikan panjang kalimat, menghilangkan pola transisi kaku ("Berdasarkan uraian di atas...", "Hal ini menunjukkan bahwa...", "Dengan demikian..."), serta memperkuat kohesi gagasan.
* **Sub-bab 2.1 (Landasan Teori 2.1.1 – 2.1.9)**:
  * Setiap sub-bab teori diparafrasekan secara orisinal dengan mempertahankan sitasi pustaka rujukan ([2], [6], [7], [8], [9]) serta nama pustaka/framework/algoritma teknis.
* **Sub-bab 2.2 (Sintesis Penelitian Terdahulu)**:
  * Narasi pengantar dan penutup tabel diparafrasekan dengan menonjolkan analisis kritis terhadap celah penelitian literatur terkait ([10]–[15]).
* **Sub-bab 3.1 – 3.3 (Metodologi Penelitian)**:
  * Ditulis ulang dengan gaya ilmiah mahasiswa rekayasa perangkat lunak, memperjelas alasan metodologis di balik pemilihan model *Waterfall*.
* **Sub-bab 4.1, 4.2, 4.5, 4.7 (Hasil dan Pembahasan)**:
  * Paragraf pengantar dan penjelas teknis disunting untuk memastikan tone akademis konsisten, objektif, dan berbasis data.
* **Sub-bab 5.1 (Kesimpulan)**:
  * Ditulis ulang secara semantik untuk menekankan pencapaian hasil rancang bangun berdasarkan bukti pengujian 60 butir *black box*, 510 tests PHPUnit (2.211 assertions), UAT 93,5%, dan wawancara operasional Bapak Asep (nilai 9,5/10).

---

### 3. BAGIAN YANG DIKOREKSI TYPO & FORMAT
* Penyelarasan format angka dan persentase: `5%`, `93,5%`, `9,5`.
* Penyelarasan target sentuh antarmuka WCAG 2.1: standar umum minimum 44 piksel, dan tombol aksi utama mengambang WhatsApp sebesar 56 piksel (dikoreksi pada Bab V poin 5 agar konsisten dengan Bab I, II, dan IV).
* Perapian spasi tipografis em-space (`\u2003`) pada penomoran heading sub-bab sesuai konvensi naskah asli.

---

### 4. KLAIM YANG DILUNAKKAN (*TONED DOWN OVERCLAIMS*) & PENYEMPURNAAN DIKSI
1. **Klaim Abstrak**:
   * *Sebelum*: "eliminasi kebocoran pendapatan secara real-time"
   * *Sesudah*: "peningkatan efisiensi kerja administrasi dan pencegahan risiko kesalahan pencatatan transaksi kas"
2. **Klaim Bab 2.1.9 (Concurrency Control)**:
   * *Sebelum*: "mengeliminasi pemesanan ganda dan kebocoran dana akibat balapan data secara mutlak"
   * *Sesudah*: "mencegah terjadinya pemesanan ganda (*double booking*) dan anomali pencatatan saldo"
3. **Diksi Bab 4.1.2 (Activity Diagram)**:
   * *Sebelum*: "proses bisnis paling kritis yang menjadi fondasi inovasi operasional"
   * *Sesudah*: "proses operasional utama"
   * *Sebelum*: "sistem tidak membebankan denda sepeser pun (Denda = Rp0)"
   * *Sesudah*: "sistem tidak membebankan denda keterlambatan (Denda = Rp0)"
   * *Sebelum*: "sistem menerapkan aturan sakral: status kamar tidak dilepas secara otomatis"
   * *Sesudah*: "sistem menerapkan aturan operasional: status kamar tidak dilepas secara otomatis"
4. **Diksi Bab 4.1.5 (ERD)**:
   * *Sebelum*: "sistem menerapkan fitur canggih MySQL 8.x berupa Virtual Generated Columns"
   * *Sesudah*: "sistem memanfaatkan fitur Virtual Generated Columns pada MySQL 8.x"
5. **Klaim Bab 4.2.3 (Virtual Generated Columns)**:
   * *Sebelum*: "integritas keunikan data tetap terlindungi mutlak tanpa perlu memodifikasi string data historis"
   * *Sesudah*: "integritas keunikan data tetap terlindungi dengan baik tanpa perlu memodifikasi string data historis"
6. **Diksi Bab 4.2.4 (Autentikasi & RBAC)**:
   * *Sebelum*: "sistem secara paksa mengalihkan navigasi ke /profil/complete"
   * *Sesudah*: "sistem secara otomatis mengarahkan navigasi ke /profil/complete"
7. **Diksi Bab 4.2.5 (Fitur Calon Penyewa)**:
   * *Sebelum*: "pengalaman penelusuran properti yang transparan dan bebas hambatan ... wizard 5 langkah yang transparan"
   * *Sesudah*: "fitur penelusuran unit kamar dan pemesanan secara mandiri ... lima tahapan pemesanan"
   * *Sebelum*: "mengeliminasi masalah performa klasik N+1 Queries"
   * *Sesudah*: "menghindari permasalahan kueri berulang (*N+1 queries problem*)"
8. **Diksi Bab 4.2.6 (Fitur Penyewa Aktif)**:
   * *Sebelum*: "menghemat ruang penyimpanan hosting secara total"
   * *Sesudah*: "mengurangi kebutuhan ruang penyimpanan pada peladen hosting"
9. **Klaim Bab 4.5.1 (Black Box Testing)**:
   * *Sebelum*: "kelulusan 100% (*zero defect*)"
   * *Sesudah*: "kelulusan 100%"
10. **Klaim Bab 4.5.2 (Hak Akses)**:
    * *Sebelum*: "terjaga secara ketat dan kebal terhadap ancaman eskalasi hak akses"
    * *Sesudah*: "terjaga secara teratur dan mencegah terjadinya eskalasi hak akses"
11. **Klaim Pembahasan 4.7 Poin 1**:
    * *Sebelum*: "Pemberantasan Kebocoran Finansial (*Zero Billing Leakage*)"
    * *Sesudah*: "Pengendalian Risiko Kesalahan dan Kebocoran Finansial"
12. **Klaim Pembahasan 4.7 Poin 4**:
    * *Sebelum*: "Pencegahan Mutlak atas Risiko Pemesanan Ganda (*Zero Double-Booking*)"
    * *Sesudah*: "Pencegahan Risiko Pemesanan Ganda (*Double-Booking Mitigation*)"
13. **Klaim Pembahasan 4.7 Poin 5**:
    * *Sebelum*: "Kualitas Perangkat Lunak Berstandar Industri ... kebal terhadap ancaman peretasan otorisasi IDOR"
    * *Sesudah*: "Kualitas dan Keandalan Perangkat Lunak Teruji ... pemenuhan isolasi hak akses peran (RBAC) pada pengujian parameter IDOR"
14. **Klaim Kesimpulan 5.1 Poin 2**:
    * *Sebelum*: "menjamin keunikan mutlak pada baris data aktif"
    * *Sesudah*: "menjamin keunikan pada baris data aktif"
15. **Klaim Kesimpulan 5.1 Poin 5**:
    * *Sebelum*: "mengeliminasi insiden pemesanan ganda (*zero double-booking*) secara mutlak ... kebal terhadap pengujian hak akses RBAC dan IDOR"
    * *Sesudah*: "berhasil mencegah terjadinya pemesanan ganda (*double booking*) pada seluruh skenario yang diuji ... kepatuhan isolasi hak akses peran (RBAC)"
16. **Klaim Kesimpulan 5.1 Poin 6**:
    * *Sebelum*: "mengamankan kapasitas pendapatan bruto maksimal kost sebesar Rp28.500.000 per bulan dari risiko kebocoran kas (*zero billing leakage*)"
    * *Sesudah*: "membantu mengamankan pencatatan potensi pendapatan bruto hingga Rp28.500.000 per bulan dari risiko kesalahan hitung dan kebocoran kas"

---

### 5. ISTILAH YANG DISTANDARKAN
* Framework: `Laravel 11.x`
* DBMS: `MySQL 8.0` dengan mesin penyimpanan `InnoDB`
* Arsitektur: `3-Tier Architecture` dipadukan dengan `Service Layer Decoupling`
* Komponen Layanan: `BillingService`, `MidtransService`, `ReservasiService`, `FonnteService`, `TransisiPenyewaService`
* Mekanisme Basis Data: `lockForUpdate()`, `DB::transaction`, *Virtual Generated Columns*, `SoftDeletes`
* Gerbang Pembayaran: `Midtrans Snap API v2` (saluran Bank BCA Virtual Account, tanda tangan digital SHA-512)
* Otomasi Pesan: `Fonnte WhatsApp Gateway API v2`, `Hostinger SMTP Mailer TLS (port 587)`
* Antarmuka: `Neo-Brutalism`, kepatuhan `WCAG 2.1` (touch target minimum 44px, action button 56px)
* Aturan Bisnis: Tagihan tanggal 1, jatuh tempo tanggal 10, denda flat 5% idempoten pada tanggal 1 bulan berikutnya, eskalasi nomor wali pada bulan ke-2, isolasi kamar merah pasca-checkout hingga inspeksi fisik manual selesai.

---

### 6. BAGIAN YANG TIDAK BOLEH DIUBAH (IMMUTABLE ELEMENTS)
* Halaman Identitas, Judul Skripsi, Nama Mahasiswa (Rafif Arsya Pradiva), NIM (22.N4.0014), Program Studi, Fakultas, Universitas, Tahun Akademik (2026).
* Dosen Pembimbing: Ir. Andre Kurniawan Pamudji, S.Kom, M.Ling. (NPP: 058.1.1994.161).
* Lembar Pernyataan Orisinalitas, Pernyataan Bebas Plagiasi, Lembar Pengesahan, Persetujuan Publikasi Karya Ilmiah.
* Daftar Isi, Daftar Tabel, Daftar Gambar.
* Struktur 87 Heading Bab dan Sub-bab (termasuk karakter em-space).
* Blok Diagram Mermaid (Gambar 2.1 Kerangka Pemikiran dan Gambar 3.1 Model Waterfall).
* Seluruh 15 Tautan Berkas Gambar di folder `images/`.
* Seluruh Cuplikan Kode Program Migrasi dan Konfigurasi Deployment.
* Seluruh Tabel Hasil Penelitian dan Pengujian:
  * Tabel 2.1 (State of the Art - 12 baris penelitian)
  * Tabel 4.1 (22 Tabel Basis Data)
  * Tabel 4.2 (Matriks Pengujian Black Box 60 butir)
  * Tabel 4.3 (Matriks Pengujian Hak Akses RBAC)
  * Tabel 4.4 (Matriks Pengujian Midtrans BCA VA)
  * Tabel 4.5 (Hasil Pengujian Keamanan Live)
  * Tabel 4.6 (Rekapitulasi UAT Skala Likert)
* Transkrip Wawancara Spontan Bapak Asep pada Sub-bab 4.6.1 (ungkapan lisan autentik seperti "adem di mata", "bumi dan langit", "hukumnya wajib").
* Seluruh 20 Entri Sitasi IEEE `[1]` hingga `[20]` beserta Daftar Pustaka lengkap.
* Parameter Angka Faktual: 32 unit kamar (6 VIP @ Rp1.400.000, 3 Deluxe @ Rp950.000, 23 Standar @ Rp750.000), total potensi Rp28.500.000, 510 tests passed, 2.211 assertions, UAT 93,5%, wawancara 9,5/10.

---

### 7. BAGIAN YANG MEMBUTUHKAN PEMERIKSAAN MANUAL
* Nilai kesamaan indeks (*similarity index*) aktual wajib diverifikasi secara mandiri melalui pemindaian resmi aplikasi Turnitin di perguruan tinggi bersangkutan.
