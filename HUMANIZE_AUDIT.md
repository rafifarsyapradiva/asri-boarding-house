# HUMANIZE AUDIT
## Sistem Informasi Manajemen Kost Terintegrasi Payment Gateway pada Asri Boarding House
**Penulis**: Rafif Arsya Pradiva (NIM: 22.N4.0014)  
**Dokumen yang Diaudit**: `Skripsi/Skripsi_Rafif_Arsya_Pradiva_22N40014.md`  
**Waktu Audit**: September 2026  
**Status Audit**: **SELESAI & TERVERIFIKASI PENUH (PASSED)**

---

### 1. RINGKASAN METRIK AUDIT
* **Jumlah Bab yang Diperiksa**: 5 Bab Utama + Bagian Awal (Halaman Judul, Orisinalitas, Plagiasi, Pengesahan, Abstrak/Abstract, Daftar Isi/Tabel/Gambar) + Bagian Akhir (Daftar Pustaka).
* **Total Baris Dokumen Akhir**: 1.258 baris
* **Total Ukuran Berkas Dokumen Akhir**: 159.414 byte
* **Jumlah Heading Bab dan Sub-bab Terverifikasi**: 88 heading (termasuk sub-bab baru 4.1.6 Skenario Diagram Alur Sistem)
* **Jumlah Paragraf yang Direvisi / Diparafrase**: 86 paragraf narasi
* **Jumlah Tabel Hasil Penelitian / Pengujian**: 8 tabel (Tabel 2.1, 4.1 s.d. 4.7)
* **Jumlah Skenario Pengujian Kotak Hitam**: 60 butir skenario uji (Tabel 4.3)
* **Jumlah Gambar Tersemat**: 20 berkas gambar (15 tangkapan layar sistem & diagram UML lama + 5 diagram skenario baru di folder `images/`)
* **Jumlah Diagram Mermaid**: 3 blok diagram (Gambar 2.1, Gambar 3.1, dan Gambar 4.6f)
* **Jumlah Sitasi IEEE**: 20 sitasi unik (`[1]` s.d. `[20]`), berkorespondensi 1-ke-1 dengan 20 entri pada Daftar Pustaka.

---

### 2. AUDIT KONSISTENSI DATA & TEMUAN KONFLIK
* **Konflik Target Sentuh Aksesibilitas WCAG 2.1**:
  * *Temuan*: Pada naskah awal, Bab I, II, dan IV mendefinisikan target sentuh minimum sesuai standar WCAG 2.1 adalah 44 piksel (dengan tombol aksi WhatsApp dibuat 56 piksel). Namun pada Bab V butir 5 tertulis bahwa standar WCAG 2.1 menetapkan target sentuh minimal 56 piksel.
  * *Tindakan Penyelesaian*: Disesuaikan pada Bab V butir 5 menjadi formulasi yang konsisten dan akurat: *"mematuhi standar aksesibilitas WCAG 2.1 (target sentuh minimum 44 piksel dan tombol aksi utama WhatsApp sebesar 56 piksel)"*.
* **Konsistensi Parameter Properti & Keuangan**:
  * Kapasitas total 32 unit kamar: terverifikasi konsisten di Abstrak, Bab I, Bab II, Bab IV, Bab V, dan Wawancara.
  * Distribusi kamar: 6 VIP (@ Rp1.400.000), 3 Deluxe (@ Rp950.000), dan 23 Standar (@ Rp750.000) konsisten menghasilkan total potensi pendapatan bruto Rp28.500.000 per bulan.
  * Siklus tagihan: tanggal 1 pembangkitan, jatuh tempo tanggal 10, denda flat 5% idempoten pada bulan berikutnya, eskalasi nomor wali pada bulan kedua.
* **Konsistensi Hasil Pengujian**:
  * Automated PHPUnit: 510 tests passed, 2.211 assertions (100% kelulusan) konsisten di Abstrak, Bab I, dan Bab IV.
  * Skor UAT: 93,5% (Kategori Sangat Layak) konsisten di Bab IV dan Bab V.
  * Skor Evaluasi Operasional: 9,5 dari 10 konsisten di Bab IV (transkrip wawancara) dan Bab V.

---

### 3. KLAIM OVERCLAIM & DIKSI NON-BAKU YANG DIPERBAIKI
Telah dilakukan perbaikan terhadap 16 titik klaim dan diksi berlebihan/informal agar memiliki bobot ilmiah yang objektif:
1. **Abstrak & Abstract**: Frasa *"eliminasi kebocoran pendapatan secara real-time"* diubah menjadi *"peningkatan efisiensi kerja administrasi dan pencegahan risiko kesalahan pencatatan transaksi kas"*.
2. **Sub-bab 2.1.9 (Concurrency Control)**: Frasa *"mengeliminasi pemesanan ganda dan kebocoran dana akibat balapan data secara mutlak"* diubah menjadi *"membantu mencegah terjadinya pemesanan ganda (double booking) dan anomali pencatatan saldo"*.
3. **Sub-bab 4.1.2 (Activity Diagram)**:
   * Frasa *"proses bisnis paling kritis yang menjadi fondasi inovasi operasional"* diubah menjadi *"proses operasional utama"*.
   * Frasa informal *"sistem tidak membebankan denda sepeser pun (Denda = Rp0)"* diubah menjadi *"sistem tidak membebankan denda keterlambatan (Denda = Rp0)"*.
   * Frasa informal *"sistem menerapkan aturan sakral"* diubah menjadi *"sistem menerapkan aturan operasional"*.
4. **Sub-bab 4.1.5 (ERD)**: Frasa *"sistem menerapkan fitur canggih"* diubah menjadi *"sistem memanfaatkan fitur Virtual Generated Columns"*.
5. **Sub-bab 4.2.3 (Virtual Generated Columns)**: Frasa *"integritas keunikan data tetap terlindungi mutlak"* diubah menjadi *"integritas keunikan data tetap terlindungi dengan baik"*.
6. **Sub-bab 4.2.4 (Autentikasi & RBAC)**: Frasa *"secara paksa mengalihkan navigasi"* diubah menjadi *"secara otomatis mengarahkan navigasi"*.
7. **Sub-bab 4.2.5 (Fitur Calon Penyewa)**:
   * Frasa promosi *"pengalaman penelusuran properti yang transparan dan bebas hambatan ... wizard 5 langkah yang transparan"* diubah menjadi *"fitur penelusuran unit kamar dan pemesanan secara mandiri ... lima tahapan pemesanan"*.
   * Frasa *"mengeliminasi masalah performa klasik N+1 Queries"* diubah menjadi *"menghindari permasalahan kueri berulang (N+1 queries problem)"*.
8. **Sub-bab 4.2.6 (Fitur Penyewa Aktif)**: Frasa *"menghemat ruang penyimpanan hosting secara total"* diubah menjadi *"mengurangi kebutuhan ruang penyimpanan pada peladen hosting"*.
9. **Sub-bab 4.5.1 (Black Box Testing)**: Frasa *"kelulusan 100% (zero defect)"* diubah menjadi *"kelulusan 100%"*.
10. **Sub-bab 4.5.2 (Hak Akses)**: Frasa *"kebal terhadap ancaman eskalasi hak akses"* diubah menjadi *"terjaga secara teratur dan mencegah terjadinya eskalasi hak akses"*.
11. **Sub-bab 4.7 (Pembahasan Poin 1)**: Judul *"Pemberantasan Kebocoran Finansial (Zero Billing Leakage)"* diubah menjadi *"Pengendalian Risiko Kesalahan dan Kebocoran Finansial"*.
12. **Sub-bab 4.7 (Pembahasan Poin 4)**: Judul *"Pencegahan Mutlak atas Risiko Pemesanan Ganda (Zero Double-Booking)"* diubah menjadi *"Pencegahan Risiko Pemesanan Ganda (Double-Booking Mitigation)"*.
13. **Sub-bab 4.7 (Pembahasan Poin 5)**: Judul *"Kualitas Perangkat Lunak Berstandar Industri"* diubah menjadi *"Kualitas dan Keandalan Perangkat Lunak Teruji"*, dan klaim *"kebal terhadap ancaman peretasan otorisasi IDOR"* disesuaikan menjadi *"pemenuhan isolasi hak akses peran (RBAC) pada pengujian parameter IDOR"*.
14. **Sub-bab 5.1 (Kesimpulan Poin 2)**: Frasa *"menjamin keunikan mutlak"* diubah menjadi *"menjamin keunikan pada baris data aktif"*.
15. **Sub-bab 5.1 (Kesimpulan Poin 5)**: Frasa *"mengeliminasi insiden pemesanan ganda secara mutlak ... kebal terhadap pengujian"* diubah menjadi *"berhasil mencegah terjadinya pemesanan ganda pada seluruh skenario yang diuji ... kepatuhan isolasi hak akses peran"*.
16. **Sub-bab 5.1 (Kesimpulan Poin 6)**: Frasa *"mengamankan kapasitas pendapatan bruto ... dari risiko kebocoran kas (zero billing leakage)"* diubah menjadi *"membantu mengamankan pencatatan potensi pendapatan bruto ... dari risiko kesalahan hitung dan kebocoran kas"*.

---

### 4. INTEGRITAS SITASI & DAFTAR PUSTAKA
* **Pemeriksaan Nomor Sitasi di Badan Naskah**:
  * Seluruh nomor sitasi `[1]`, `[2]`, `[3]`, `[4]`, `[5]`, `[6]`, `[7]`, `[8]`, `[9]`, `[10]`, `[11]`, `[12]`, `[13]`, `[14]`, `[15]`, `[16]`, `[17]`, `[18]`, `[19]`, `[20]` hadir secara lengkap dan berurutan.
* **Pemeriksaan Daftar Pustaka**:
  * Terdapat 20 entri referensi IEEE yang terverifikasi utuh tanpa penambahan sumber fiktif atau penghapusan sumber referensi asli.
* **Pemeriksaan Mismatch Klaim-Sitasi**:
  * Tidak ditemukan ketidaksesuaian antara narasi rujukan dan sumber pustaka yang disitasi.

---

### 5. PEMBERSIHAN KREDENSIAL & KEAMANAN (SECURITY CLEANUP)
Pemeriksaan menyeluruh dilakukan pada Sub-bab 4.4.5 (`.env.production`). Seluruh parameter sensitif produksi telah dimasking menggunakan penanda `[REDACTED]` guna mencegah kebocoran informasi kredensial peladen:
* `APP_KEY` \u2192 `[REDACTED]`
* `DB_DATABASE` \u2192 `[REDACTED]`
* `DB_USERNAME` \u2192 `[REDACTED]`
* `DB_PASSWORD` \u2192 `[REDACTED]`
* `MIDTRANS_SERVER_KEY` \u2192 `[REDACTED]`
* `MIDTRANS_CLIENT_KEY` \u2192 `[REDACTED]`
* `FONNTE_TOKEN` \u2192 `[REDACTED]`
* `MAIL_USERNAME` \u2192 `[REDACTED]`

Hasil pemindaian otomatis memvalidasi bahwa **0 kredensial terbuka** tertinggal di dalam naskah akhir skripsi.

---

### 6. AUDIT SUB-BAB 4.1.6 SKENARIO DIAGRAM ALUR SISTEM
* **Sub-bab Baru 4.1.6**:
  * Menghubungkan pemodelan UML teknis (Use Case, Activity, Flowchart, Sequence, ERD) dengan data operasional riil *live testing* di domain `https://asriboardinghouse.weatso.id/`.
  * Merekonstruksi secara presisi 2 persona empiris: Nur Haliza (Kamar 101 VIP, Google OAuth, Full Payment Mandiri VA Rp15.400.560, bayar tepat waktu) dan Tyas (Kamar 104 Deluxe, form web, DP 30% QRIS Rp1.710.000, pelunasan sisa 70% di portal Rp3.990.000, masa toleransi bebas denda Denda = Rp0).
  * Jalur 3 (walk-in offline / Ratih / denda keterlambatan 5% menunggak lintas bulan / eskalasi wali) **100% ditiadakan** sehingga naskah murni menyajikan apa adanya hasil pengujian empiris yang telah terlaksana secara valid.
  * Memodelkan alur kerja harian pengelola (Bapak Asep, 48 tahun) dan kebijakan kritis **Protokol Penahanan Kamar (*Manual Inspection Hold*)**: kamar pasca-checkout tetap berstatus terkunci `terisi` hingga dilakukan pembersihan/sterilisasi fisik, lalu diubah secara manual ke `tersedia` (memicu `KamarObserver::updated` untuk menghapus tembolok `kamar_aktif_landing`).
* **Verifikasi Diagram Visual & Konsolidasi State**:
  * Gambar 4.6 (a) s.d. (f) disematkan dengan tautan berkas valid di direktori `images/`.
  * Tabel 4.1 menyajikan matriks 15 fase siklus hidup sistem lintas 8 dimensi operasional.
  * Seluruh penomoran Gambar 4.6 s.d. 4.11 dan Tabel 4.1 s.d. 4.7 telah tersinkronisasi 100% di seluruh batang tubuh dokumen, `DAFTAR ISI`, `DAFTAR TABEL`, dan `DAFTAR GAMBAR`.

---

### 7. BAGIAN YANG SENGAJA TIDAK DIUBAH (IMMUTABLE ITEMS)
Guna menjaga keabsahan administratif dan integritas akademik naskah skripsi:
1. Halaman identitas, judul skripsi, nama penulis (Rafif Arsya Pradiva), NIM (22.N4.0014), Program Studi, Fakultas, Universitas, dan data Dosen Pembimbing (Ir. Andre Kurniawan Pamudji, S.Kom, M.Ling.).
2. Halaman pernyataan orisinalitas, pernyataan bebas plagiasi, pengesahan, dan persetujuan publikasi.
3. Struktur Daftar Isi, Daftar Tabel, dan Daftar Gambar (disinkronkan dengan penambahan sub-bab 4.1.6).
4. Seluruh 88 heading bab dan sub-bab.
5. Seluruh diagram Mermaid dan tautan gambar.
6. Seluruh cuplikan kode program Laravel dan skrip deployment.
7. Seluruh tabel data hasil penelitian dan pengujian (Tabel 2.1, 4.1 s.d. 4.7).
8. Transkrip wawancara kualitatif Bapak Asep pada Sub-bab 4.6.1 yang merupakan tuturan lisan narasumber asli.
9. Seluruh 20 entri pada Daftar Pustaka.

---

### 8. BAGIAN YANG MEMBUTUHKAN REVIEW MANUAL & TARGET SIMILARITY
* **Pernyataan Terkait Target Similarity (<18%)**:
  * Naskah telah dioptimalkan secara komprehensif melalui penulisan ulang semantik (*semantic rewrite*), penghilangan frasa klise, rekonstruksi struktur kalimat, dan penguraian konsep berdasarkan konteks spesifik objek penelitian, tanpa menggunakan teknik manipulasi tipografis/karakter tersembunyi.
  * **Namun demikian**, angka kesamaan (*similarity index*) aktual di bawah 18% belum dapat diverifikasi secara definitif tanpa pemindaian aktual pada sistem Turnitin resmi institusi. Penulis disarankan melakukan pemindaian uji coba pada portal Turnitin universitas sebelum sidang/pengumpulan akhir.

