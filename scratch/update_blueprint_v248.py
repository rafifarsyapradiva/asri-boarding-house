import os

file_path = r"c:\xampp\htdocs\asri-boarding-house\Blueprint\Blueprint_Projek_Web_Asri_Boarding_House.md"

with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

target_str = "124\tHarmonisasi Data Riil Produksi Live (Nur Haliza & Tyas), Dual Track Skripsi & Zero Server Load html2pdf.js (v247.0)"
assert target_str in content, "target_str not found"

row_125 = "125\tTata Kelola Sinkronisasi Aset Visual Skripsi & Penjaminan 100% Validitas Daftar Gambar (v248.0)\tAset gambar di direktori Skripsi/images sebelumnya terfragmentasi, menyisakan diagram lama bertanggal 19 September 2026, dan referensi berkas WebP (gambar_4_1 s.d. gambar_4_12) menghasilkan broken links pada Markdown preview.\tSINKRONISASI SELEKTIF & AUDIT 100% VALIDITAS LINK (v248.0): Melaksanakan audit forensik dan penyelarasan selektif 1:1 terhadap 23 gambar pada DAFTAR GAMBAR dokumen Skripsi_Rafif_Arsya_Pradiva_22N40014.md langsung dari berkas master Blueprint/ (Class Diagram 22 model, ERD 22 tabel 3NF, Activity Diagram 3 proses kritis, Sequence Diagram alur utama, dan 3 modul Use Case), mengeliminasi subfolder sementara guna mempertahankan format direktori datar/flat yang bersih (34 berkas, 8.58 MB), membangkitkan aset teknis WebP Bab 4 (termasuk visual terminal otentik php artisan test lulus 510 tests/2211 assertions dan manifest Vite production), melindungi diagram metodologi Bab 2 & 3, serta memverifikasi 100% kelulusan audit link gambar (zero broken links)."

rev_248 = """Revisi v248.0 — Audit Forensik Aset Visual, Sinkronisasi Selektif Skripsi Images & Penjaminan Zero Broken Links (v248.0)
Revisi v248.0 mencakup audit forensik menyeluruh terhadap seluruh aset diagram pada direktori Skripsi/images/, eliminasi redundansi berkas tak terpakai, penyelarasan selektif 1:1 terhadap 23 gambar yang tercantum pada DAFTAR GAMBAR dokumen naskah Skripsi (Skripsi/Skripsi_Rafif_Arsya_Pradiva_22N40014.md), serta pembentukan mekanisme tata kelola sinkronisasi Blueprint-to-Thesis: (1) Penyelarasan Diagram Inti UML & ERD Termutakhir: Memperbarui diagram usang di Skripsi/images/ dengan aset resmi resolusi tinggi dari Blueprint/, mencakup Class Diagram 22 model Eloquent (class_diagram.png 1.87 MB), ERD 22 tabel 3NF (gambar_4_5_erd.png 348 KB), Activity Diagram 3 proses kritis (Auto-Billing, Denda Flat Kalender, Transisi Reservasi), Sequence Diagram alur utama (Reservasi & Billing), serta 3 sudut pandang modul Use Case. (2) Penegakan Format Bersih & Ramping (Flat Directory): Menghapus 12 subfolder sementara yang tidak dirujuk oleh naskah skripsi sehingga direktori Skripsi/images/ tetap berformat flat ramping (34 berkas, 8.58 MB) tanpa bloatware direktori. (3) Proteksi Aset Akademis Orisinal: Mempertahankan 100% keutuhan diagram metodologi skripsi Bab 2 (kerangka_pemikiran_diagram.png) dan Bab 3 (waterfall_model_diagram.png). (4) Penyediaan Aset Visual Implementasi Teknis Bab 4 (gambar_4_1.webp s.d. gambar_4_12.webp): Membangkitkan visual teknis resolusi tinggi untuk seluruh alur backend (Billing Engine, Middleware Webhook Midtrans, Interseptor Google OAuth, Fonnte WA Service, Arsitektur 3-Tier, Katalog Neo-Brutalisme, Arus Kas, Dompdf Nota, Stepper Onboarding, hingga dokumentasi wawancara UAT), termasuk visual terminal otentik untuk Gambar 4.15 (lulus 510 tests, 2211 assertions sesuai teks baris 1328) dan Gambar 4.10 (kompilasi bundel aset Vite production & manifest.json). (5) Sertifikasi Audit Tautan Gambar (Zero Broken Links): Menjalankan pemindaian otomatis terhadap 23 tag gambar di Skripsi_Rafif_Arsya_Pradiva_22N40014.md dan 11 tag di Skripsi_Rafif_Arsya_Pradiva_22N40014_recovered.md dengan hasil 100% VALID dan terverifikasi eksis secara fisik."""

# Find line containing 124
lines = content.splitlines()
new_lines = []
inserted_row = False
inserted_rev = False

for line in lines:
    new_lines.append(line)
    if not inserted_row and line.startswith("124\tHarmonisasi Data Riil"):
        new_lines.append(row_125)
        inserted_row = True
    elif not inserted_rev and line.startswith("Revisi v247.0 — Harmonisasi Data Riil"):
        # Insert before Revisi v247.0
        new_lines.pop() # remove v247
        new_lines.append(rev_248)
        new_lines.append("")
        new_lines.append(line)
        inserted_rev = True

assert inserted_row, "Failed to insert row 125"
assert inserted_rev, "Failed to insert Revisi v248.0"

with open(file_path, "w", encoding="utf-8") as f:
    f.write("\n".join(new_lines))

print("Successfully updated Blueprint_Projek_Web_Asri_Boarding_House.md with v248.0!")
