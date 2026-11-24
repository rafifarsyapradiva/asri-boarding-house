# Panduan Gaya Penulisan Akademis-Humanis (Skripsi & Publikasi)

Setiap pembuatan atau penyuntingan dokumen akademis (naskah skripsi, jurnal, ringkasan poster, materi presentasi) dalam proyek ini WAJIB mematuhi prinsip-prinsip berikut:

### 1. Karakter & Ragam Bahasa (Hybrid Tingkat 2 & 3)
- Padukan ketepatan akademis (akurasi istilah, data kuantitatif, kaidah logika) dengan gaya bahasa humanis yang luwes dan mudah dicerna.
- Hindari nada birokratis yang berbelit-belit maupun bahasa obrolan santai yang kehilangan wibawa ilmiah.

### 2. Alami, Mengalir, & Bebas Klise AI
- Dilarang menggunakan pembuka klise buatan AI (misal: "Di era modern saat ini", "Perlu digarisbawahi bahwa", "Sebagai kesimpulan yang mendalam").
- Gunakan transisi antarkalimat yang organik dan logis, selayaknya tulisan akademisi atau praktisi profesional.

### 3. Efektivitas & Kepadatan Makna (To the Point)
- Singkirkan pleonasme dan pengisi kata (filler words). Setiap kalimat harus membawa informasi, argumen, atau data konkret.
- Sajikan fakta numerik dan parameter teknis secara eksplisit (misal: angka persentase, durasi hari, nama metode/algoritma).

### 4. Struktur Kalimat Dinamis (Burstiness)
- Terapkan variasi ritme membaca secara dinamis:
  - Gunakan kalimat tunggal pendek (5–10 kata) untuk penegasan temuan atau poin kunci.
  - Gunakan kalimat majemuk terstruktur (15–25 kata) untuk menguraikan hubungan sebab-akibat, komparasi arsitektural, atau alur metodologis.
- Hindari rentetan kalimat dengan struktur dan panjang yang monoton.

### 5. Presisi Diksi Teknis
- Gunakan terminologi teknologi informasi dan rekayasa perangkat lunak yang baku dan tepat konteks (misal: *concurrency control*, *pessimistic row locking*, *soft-delete unicity*, *idempotent*, *grace period*, *reconciliation*).
- Pertahankan istilah asing dalam format miring (*italics*) jika belum memiliki padanan baku yang lazim di ranah sistem informasi Indonesia.

### 6. Orisinalitas & Parafrase Murni (Anti-Plagiasi)
- Jangan pernah menyalin susunan kalimat utuh dari sumber rujukan.
- Lakukan parafrase murni dengan menangkap esensi fakta atau logika, kemudian merangkai ulang dengan struktur frasa, metafora fungsional, dan perspektif baru agar sepenuhnya orisinal serta aman dari deteksi Turnitin kampus.

### 7. Integrasi Skill `humanizer` & Batasan Akademis
- Saat menyunting, meninjau, atau memoles prosa naskah skripsi, jurnal, poster, maupun dokumentasi proyek, aktifkan skill lokal `.agents/skills/humanizer/SKILL.md` (gunakan *File Mode* untuk penyuntingan berkas penuh dan *Embedded Mode* saat membuat/memperbarui bagian tertentu).
- Terapkan 26 pola deteksi `humanizer` secara adaptif untuk Bahasa Inggris dan Bahasa Indonesia, termasuk padanan pola AI berbahasa Indonesia:
  - **§1 Kontras Semu**: hindari konstruksi *"bukan sekadar X, melainkan Y"*, *"tidak hanya X, tetapi juga Y"* bila separuh negatifnya tidak menyanggah asumsi nyata pembaca.
  - **§6 Triad Paksa & §8 Tanda Pisah**: hindari pengelompokan tiga kata/klausa berulang yang dipaksakan, serta dilarang menggunakan *em-dash* (`—`) atau *en-dash* (`–`) di dalam prosa.
  - **§12 Diksi Stok AI & §18 Kata Kerja Bertele-tele**: pangkas kata klise seperti *krusial, vital, komprehensif, lanskap, ekosistem (figuratif), menjembatani*, serta ganti frasa *bertindak sebagai / hadir sebagai / berfungsi sebagai* dengan kata kerja langsung (*adalah, menjadi, menghubungkan*).
- **Zona Terproteksi (Wajib Dipertahankan Utuh)**:
  - Format judul bab/subbab resmi Unika & IEEE (kecualikan §20 *sentence-case* & penghapusan garis pemisah template).
  - Format *bold* wajib pada blok *Abstract* template jurnal IEEE (kecualikan §19) dan kutipan/rentang halaman di `DAFTAR PUSTAKA` / `REFERENCES` (kecualikan §21 & §8).
  - Kutipan wawancara verbatim (`Tabel 4.2`–`Tabel 4.7`), tabel pengujian (`Tabel 4.1`), blok kode, rumus LaTeX, tautan gambar, nomor sitasi `[1]`–`[30]`, serta seluruh angka metrik empiris.
  - Kalimat pasif akademis Bahasa Indonesia (§11) tetap diperbolehkan seperlunya selama tidak menyembunyikan pelaku utama atau membuat kalimat kaku.

