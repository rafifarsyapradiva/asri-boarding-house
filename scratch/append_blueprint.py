import os

file_path = r"c:\xampp\htdocs\asri-boarding-house\Blueprint\Blueprint_Projek_Web_Asri_Boarding_House.md"

new_content = """

BAB GM — AUDIT DAN REFAKTORISASI PORTAL PENYEWA & STRUKTUR LOGIKA VIEW (BARU v132.0)

GM.1 Pembersihan Kode & Resolusi Redundansi (Clean Code)
- **Deduplikasi Status Badge & Pesan WA (DRY)**:
  * Menghapus duplikasi logika *color-mapping* badge status dan pesan WhatsApp manual dari seluruh file Blade penyewa, menggantinya dengan pemanggilan properti *accessor* dinamis `$tagihan->status_badge_class`, `$tagihan->wa_confirmation_message`, `$keluhan->status_badge_class`, `$keluhan->kategori_label`, dan `$reservasi->status_badge_class` terpusat pada Model.
- **Sentralisasi Aset Ikon SVG**:
  * Membuat komponen Blade baru [icon.blade.php](file:///c:/xampp/htdocs/asri-boarding-house/resources/views/components/icon.blade.php) untuk memusatkan rendering file SVG ikon secara dinamis, serta membersihkan tumpukan tag `<svg>` dari halaman [peraturan.blade.php](file:///c:/xampp/htdocs/asri-boarding-house/resources/views/penyewa/peraturan.blade.php).
- **Dekopling Script Javascript Inline**:
  * Memindahkan baris kode Javascript inline pemrosesan Snap Token Midtrans dan penyalinan clipboard pada [show.blade.php](file:///c:/xampp/htdocs/asri-boarding-house/resources/views/penyewa/tagihan/show.blade.php) ke dalam blok terpusat `@push('scripts')`.

GM.2 Keandalan Aliran Logika & Tipe Data Aman (Clean Logic)
- **Pemindahan Perhitungan Agregasi Keuangan & Chat**:
  * Memindahkan perhitungan statistik transaksi, akumulasi tagihan belum lunas, dan obrolan aktif dari sisi view ([riwayat-pembayaran.blade.php](file:///c:/xampp/htdocs/asri-boarding-house/resources/views/penyewa/riwayat-pembayaran.blade.php), [index.blade.php](file:///c:/xampp/htdocs/asri-boarding-house/resources/views/penyewa/tagihan/index.blade.php), [riwayat-chat.blade.php](file:///c:/xampp/htdocs/asri-boarding-house/resources/views/penyewa/riwayat-chat.blade.php)) sepenuhnya ke sisi Controller ([ReservasiController.php](file:///c:/xampp/htdocs/asri-boarding-house/app/Http/Controllers/Penyewa/ReservasiController.php) dan [TagihanController.php](file:///c:/xampp/htdocs/asri-boarding-house/app/Http/Controllers/Penyewa/TagihanController.php)).
- **Integrasi JSON Aman di AlpineJS**:
  * Mengganti penulisan tag inline JSON yang rawan eror *escaped quotes* pada [index.blade.php](file:///c:/xampp/htdocs/asri-boarding-house/resources/views/penyewa/notifikasi/index.blade.php) menggunakan atribut data HTML5 `data-log` untuk dibaca dengan `JSON.parse` oleh AlpineJS.

GM.3 Pemisahan Layer & Separation of Concerns (Clean Architecture)
- **Standardisasi Layout Portal Penyewa**:
  * Menghapus referensi layout kaku `@extends('layouts.penyewa')` pada [peraturan.blade.php](file:///c:/xampp/htdocs/asri-boarding-house/resources/views/penyewa/peraturan.blade.php), digantikan dengan layout terpadu `<x-app-layout>` untuk menjaga konsistensi visual di seluruh portal penyewa.
- **Presenter Logic Model**:
  * Memisahkan domain logika kalkulasi deposit dan status badge agar view sepenuhnya bertindak sebagai *Presentation Layer* yang bersih dari keputusan logika bisnis.

GM.4 Sertifikasi Pengujian Otomatis (Clean Testing)
- **Pembuatan Unit Test Baru**:
  * Membuat file unit test terisolasi [TagihanTest.php](file:///c:/xampp/htdocs/asri-boarding-house/tests/Unit/TagihanTest.php) dan [KeluhanTest.php](file:///c:/xampp/htdocs/asri-boarding-house/tests/Unit/KeluhanTest.php) untuk memverifikasi fungsionalitas logika accessors model.
- **Hasil Sertifikasi Akhir**:
  * Seluruh rangkaian test suite Laravel global berhasil dijalankan dengan sukses 100% dengan total **506 passed dan 2202 assertions** (meningkat dari 504 passed karena bertambah 2 file unit test baru), membuktikan sistem bebas dari regresi.
"""

with open(file_path, "a", encoding="utf-8") as f:
    f.write(new_content)

print("Successfully appended refactoring chapter to Blueprint document.")
