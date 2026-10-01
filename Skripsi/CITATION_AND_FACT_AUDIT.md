# CITATION AND FACT AUDIT REPORT
**Dokumen Target**: `Skripsi_Rafif_Arsya_Pradiva_22N40014.md`  
**Proyek**: Sistem Informasi Manajemen Kost Terintegrasi Payment Gateway pada Asri Boarding House  
**Penulis**: Rafif Arsya Pradiva (NIM: 22.N4.0014)  
**Institusi**: Program Studi Sistem Informasi, Fakultas Ilmu Komputer, Universitas Katolik Soegijapranata Semarang  
**Tanggal Audit**: 1 Oktober 2026  
**Auditor**: Senior Academic Editor & Technical Manuscript Quality Assurance Specialist  

---

## 1. Verifikasi Integritas File & Backup

| Parameter | File Sumber Asli | File Cadangan (Backup) | Status Verifikasi |
| :--- | :--- | :--- | :---: |
| **Path** | `Skripsi/Skripsi_Rafif_Arsya_Pradiva_22N40014.md` | `Skripsi/Skripsi_Rafif_Arsya_Pradiva_22N40014_BACKUP_ORIGINAL.md` | VALID |
| **Ukuran** | 160.337 bytes | 160.337 bytes | VALID (Identik) |
| **Total Baris** | 1.184 baris (1.183 baris fisik) | 1.184 baris | VALID (Identik) |
| **SHA-256** | `C14E82C63C78C4F06FB4640BE62ACB98913C06A1F35806E453706519A65B90FA` | `C14E82C63C78C4F06FB4640BE62ACB98913C06A1F35806E453706519A65B90FA` | **100% IDENTIK** |
| **Atribut Proteksi**| Read/Write | **Read-Only (True)** | **TERKUNCI** |

---

## 2. Fact Lock Register (Daftar Fakta & Data Numerik Terproteksi)

Seluruh angka dan fakta di bawah ini telah diverifikasi konsistensinya di seluruh bab dan **TIDAK BOLEH** diubah selama proses revisi/parafrase:

| Parameter Penelitian | Nilai Faktual | Lokasi Kemunculan di Dokumen | Status Audit |
| :--- | :--- | :--- | :---: |
| **Kapasitas Kamar** | 32 unit kamar | Baris 142, 146, 264, 340, 1111, 1136 | KONSISTEN |
| **Komposisi Kamar** | VIP: 6 unit (Rp1.400.000)<br/>Deluxe: 3 unit (Rp950.000)<br/>Standar: 23 unit (Rp750.000) | Baris 264 | KONSISTEN |
| **Potensi Pendapatan**| Rp28.500.000 / bulan | Baris 142, 146, 264, 1136, 1152 | KONSISTEN |
| **Alamat Objek** | Jl. Maera Sari No. 1 / No. 12, Tembalang, Kota Semarang 50275 | Baris 264 | KONSISTEN |
| **Informan Kunci** | Bapak Asep (48 tahun, pengalaman ±20 tahun) | Baris 111, 264, 502, 1101, 1109–1120 | KONSISTEN |
| **File Audio Wawancara**| `REKAMAN_UX_ADMIN_KOST_2026.m4a` (±25–35 menit) | Baris 1103 | KONSISTEN |
| **Jadwal Billing** | Eksekusi tanggal 1 pukul 00:05 WIB | Baris 276, 365, 1136, 1149 | KONSISTEN |
| **Jatuh Tempo** | Tanggal 10 setiap bulan | Baris 276, 365, 1116, 1149 | KONSISTEN |
| **Skema Denda** | Flat kalender 5%, idempoten, 1x pembebanan | Baris 142, 146, 276, 405, 1116, 1138, 1149 | KONSISTEN |
| **Skema Basis Data** | 22 tabel, MySQL 8.x InnoDB, 3NF | Baris 142, 146, 350, 500, 580, 739, 1148 | KONSISTEN |
| **Skenario Black Box**| 60 skenario uji (Kelulusan 100%) | Baris 142, 146, 502, 970–1046, 1140, 1151 | KONSISTEN |
| **PHPUnit Automated**| 510 tests passed, 2.211 assertions | Baris 142, 146, 966, 1140, 1151 | KONSISTEN |
| **Skor Kepuasan UX** | 9,5 dari 10 (Usability Testing) | Baris 146, 1120, 1140, 1152 | KONSISTEN |
| **Domain Produksi** | `https://asriboardinghouse.weatso.id/` | Baris 142, 146, 670, 1103 | KONSISTEN |

---

## 3. Citation and Reference Mapping Audit

Pemetaan 20 sitasi IEEE dari teks narasi ke Daftar Pustaka:

| ID | Penulis & Tahun | Judul Publikasi / Sumber | Kemunculan di Teks | Relevansi Konseptual | Status |
| :---: | :--- | :--- | :--- | :--- | :---: |
| **[1]** | F. Sonata (2019) | Pemanfaatan UML dalam E-Commerce C2C | Baris 258 | Pergeseran ekosistem digital & kebutuhan UML | VALID |
| **[2]** | K. C. Laudon & J. P. Laudon (2018) | Management Information Systems (15th ed.) | Baris 262, 338 | Definisi konseptual & peranan SIM | VALID |
| **[3]** | A. Cornellya & H. Afriyadi (2025) | SI Pemesanan Kost Tya Laravel | Baris 274 | Kajian sistem kos web sejenis | VALID |
| **[4]** | C. Nizar (2021) | Rancang Bangun SI Sewa Kost (E-Kost) | Baris 274 | Kajian sistem kos web sejenis | VALID |
| **[5]** | A. Jannah dkk. (2020) | SI Pemasaran Rumah Kost Web | Baris 274 | Kajian sistem pemasaran kos | VALID |
| **[6]** | Y. Anggraini dkk. (2020) | SI Penjualan Web MVC CodeIgniter | Baris 343 | Pola arsitektur MVC | VALID |
| **[7]** | R. Sutisna & F. Aziz (2025) | Sistem Sewa Web Midtrans Payment Gateway | Baris 358 | Integrasi Midtrans Snap API | VALID |
| **[8]** | Y. Fatman dkk. (2023) | Payment Gateway Midtrans UMKM | Baris 358 | Integrasi transaksi non-tunai | VALID |
| **[9]** | P. Philippaerts dkk. (2022) | OAuch: Security Compliance in OAuth 2.0 | Baris 375 | Keamanan OAuth 2.0 (Google Login) | VALID |
| **[10]**| E. J. Malaikosa & P. Mokola (2024)| Monitoring Rumah Kos & Pembayaran RAD | Baris 385 | Tinjauan State of the Art (Tabel 2.1) | VALID |
| **[11]**| D. S. Purnia dkk. (2021) | Prototyping Marketplace Kost Mobile | Baris 385 | Tinjauan State of the Art (Tabel 2.1) | VALID |
| **[12]**| M. I. Surya Pratama (2025) | Payment Gateway POS Web ReactJS | Baris 385 | Tinjauan State of the Art (Tabel 2.1) | VALID |
| **[13]**| I. P. Pramita dkk. (2024) | Midtrans Pembayaran SPP Android | Baris 385 | Tinjauan State of the Art (Tabel 2.1) | VALID |
| **[14]**| F. Wijaya dkk. (2023) | SI Laundry Web Midtrans | Baris 385 | Tinjauan State of the Art (Tabel 2.1) | VALID |
| **[15]**| L. Hakim dkk. (2021) | Kas Web dan WhatsApp Gateway | Baris 385 | Tinjauan State of the Art (Tabel 2.1) | VALID |
| **[16]**| R. S. Pressman & B. R. Maxim (2015)| Software Engineering (8th ed.) | Baris 466 | Landasan metodologi R&D rekayasa perangkat lunak | VALID |
| **[17]**| T. Pricillia & Z. Zulfachmi (2021)| Perbandingan Waterfall, Prototype, RAD | Baris 475 | Landasan pemilihan metode Waterfall | VALID |
| **[18]**| D. G. A. Candra & P. P. Pardika (2024)| Analisa SI Metode Waterfall | Baris 475 | Penerapan model sekuensial linier | VALID |
| **[19]**| W. N. Cholifah dkk. (2018) | Black Box Testing Aplikasi Android | Baris 968 | Metodologi Black Box Testing | VALID |
| **[20]**| E. Setiana dkk. (2024) | Pengujian Perangkat Lunak Black Box | Baris 968 | Metodologi Black Box Testing | VALID |

*Temuan Sitasi*: Seluruh sitasi `[1]` hingga `[20]` memiliki pasangan identik pada Daftar Pustaka (Baris 1163–1183). Tidak ditemukan sitasi yatim (*orphan citation*) maupun referensi fiktif.

---

## 4. Temuan Audit Khusus & Rekomendasi Verifikasi Penulis

### 4.1 Diskrepansi Nomor Pokok Pegawai (NPP) Dosen Pembimbing
* **Baris 37**: `Ir. Andre Kurniawan Pamudji, S.Kom, M.Ling. NPP: 058.1.1994.161`
* **Baris 102**: `Ir. Andre Kurniawan Pamudji, S.Kom, M.Ling NPP. 581.2021.403`
* **Status**: **DISKREPANSI DATA FAKTUIL**.
* **Tindakan**: Sesuai Rule 8 & 12 Master Prompt, auditor **tidak boleh** menebak nomor yang benar. Perlu konfirmasi dari penulis mengenai NPP resmi Dosen Pembimbing.

### 4.2 Placeholder Data pada Lembar Pengesahan & Administratif
* **Baris 101–102**: `Pembimbing 2: NAMA | NPP. ….` (Masih berupa teks *placeholder*).
* **Baris 56, 75, 93, 117, 135**: `Semarang, ........................ 2026` (Titik-titik tanggal persetujuan).
* **Baris 96**: Terdapat tanggal definitif `Semarang, 13-07-2026`.
* **Tindakan**: Pertahankan struktur ini agar penulis dapat melengkapi saat penandatanganan resmi skripsi.

---

## 5. Diagnostik Bahasa & Pola Kalimat (Linguistic Profiling)

| Indikator Linguistik | Metrik Dokumen | Temuan Kritis & Rekomendasi Perbaikan |
| :--- | :--- | :--- |
| **Total Paragraf Naratif** | 563 paragraf | Terstruktur baik dalam hirarki heading H1–H4. |
| **Total Kalimat Naratif** | 730 kalimat | Rata-rata panjang kalimat: 20,1 kata per kalimat. |
| **Kalimat Sangat Panjang (>40 kata)** | 78 kalimat | Ditemukan beberapa kalimat majemuk bertingkat yang terlalu padat anak kalimat (*relative clause* bertumpuk). Perlu dipecah agar lebih mudah dicerna tanpa mengubah substansi. |
| **Penggunaan Kata "Guna"** | 31 kali kemunculan | Frekuensi sangat tinggi. Menyebabkan monotonitas ritme baca. Perlu diparafrase kontekstual ("untuk", "demi", "sebagai langkah", "dalam upaya", atau restrukturisasi klausa aktif). |
| **Penggunaan "Sehingga" di Awal Kalimat**| Ditemukan di beberapa bagian | Pelanggaran kaidah PUEBI/EYD (konjungsi intrakalimat tidak boleh menjadi pembuka kalimat). Perlu dihubungkan ke kalimat sebelumnya atau diganti kata transisi antarkalimat ("Oleh sebab itu", "Akibatnya"). |
| **Keseimbangan Kalimat Aktif-Pasif** | Didominasi pasif konstruksi kaku | Perlu diimbangi kalimat aktif analitis agar tulisan terasa natural dan mencerminkan pemahaman mendalam peneliti. |

---

## 6. Inventaris Artefak Visual & Blok Teknis Terproteksi

* **Diagram Mermaid (3 Blok)**:
  1. Baris 418–459: `graph TD` (Kerangka Pemikiran Penelitian: Input -> Process -> Output -> Outcome)
  2. Baris 481–493: `graph TD` (Model Waterfall: Requirements -> Design -> Coding -> Testing -> Maintenance)
  3. Baris 657–671: `flowchart TD` (Prosedur Checkout dan Protokol Penahanan Kamar)
  *Semua blok valid dan WAJIB dipertahankan sintaksnya 100%.*
* **Tabel Markdown (8 Tabel)**:
  * Tabel 2.1, Tabel 4.1, Tabel 4.2, Tabel 4.3, Tabel 4.4, Tabel 4.5, Tabel 4.6, Tabel 4.7.
  * *Seluruh struktur kolom, baris, dan penomoran tabel telah terverifikasi sinkron dengan Daftar Tabel.*
* **Path Berkas Gambar (20 Berkas)**:
  * Seluruh file pada direktori `Skripsi/images/` telah diverifikasi eksistensinya dan ukurannya di atas 48 KB.

---

## 7. Rencana Prioritas Revisi Akademik

1. **Prioritas 1 (Penting & Mendesak)**: Humanisasi Abstrak (Indonesia) & Abstract (Inggris) agar memiliki daya pikat ilmiah tinggi dan mengalir alami.
2. **Prioritas 2**: Penajaman alur latar belakang dan *Research Gap* pada BAB I, menghilangkan repetisi penjelasan kendala operasional konvensional.
3. **Prioritas 3**: Transformasi narasi BAB II dari definisi deskriptif menjadi sintesis teoretis komparatif.
4. **Prioritas 4**: Peningkatan kelancaran alur metodologis BAB III Waterfall.
5. **Prioritas 5**: Penyempurnaan bahasa teknis BAB IV (Arsitektur, Skenario, Pembahasan) dengan mempertahankan integritas data pengujian dan wawancara operasional.
6. **Prioritas 6**: Pemadatan BAB V Kesimpulan dan rekomendasi Roadmap Saran.
