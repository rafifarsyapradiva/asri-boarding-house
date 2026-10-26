# -*- coding: utf-8 -*-
import re

# Uji coba parser kalimat untuk memastikan logika penghitungan presisi
def count_sentences(paragraph):
    p = paragraph.strip()
    if not p:
        return 0, []
    p_safe = re.sub(r'Gambar (\d+)\.(\d+)', r'Gambar_\1_\2', p)
    p_safe = re.sub(r'No\.', r'No_', p_safe)
    p_safe = re.sub(r'dsb\.', r'dsb_', p_safe)
    p_safe = re.sub(r'dll\.', r'dll_', p_safe)
    p_safe = re.sub(r's\.d\.', r's_d_', p_safe)
    p_safe = re.sub(r'Rp\s+(\d+)', r'Rp_\1', p_safe)
    p_safe = re.sub(r'Rp\.', r'Rp_', p_safe)
    
    sentences = [s.strip() for s in re.split(r'(?<=[.!?])\s+', p_safe) if s.strip()]
    return len(sentences), sentences

test_p1 = "Halaman beranda utama berfungsi sebagai pusat informasi awal bagi masyarakat yang mencari hunian sewa di kawasan Tembalang. Antarmuka ini menampilkan profil Asri Boarding House beserta pilihan kamar dan fasilitas penunjang yang tersedia. Calon penghuni dapat meninjau gambaran umum indekos secara mandiri sebelum memutuskan untuk mendaftar."
test_p2 = "Desain tata letak dirancang responsif agar informasi tarif sewa tetap nyaman dibaca lewat layar komputer maupun ponsel. Tampilan lengkap beranda publik indekos diperlihatkan pada Gambar 4.7."
test_p3 = "Berdasarkan Gambar 4.7, bagian atas halaman memuat bilah navigasi utama yang berisi tautan menuju informasi kamar, fasilitas, cara booking, serta tombol akses akun. Sisi kiri layar menampilkan spanduk pengenalan indekos dengan tombol aksi cepat untuk mencari kamar, didampingi foto gedung fisik pada sisi kanan. Sudut kanan bawah antarmuka dilengkapi tombol obrolan tamu interaktif yang memudahkan pengunjung berkonsultasi langsung dengan pihak pengelola."

c1, s1 = count_sentences(test_p1)
c2, s2 = count_sentences(test_p2)
c3, s3 = count_sentences(test_p3)

print(f"P1: {c1} kalimat -> {[s[:30] for s in s1]}")
print(f"P2: {c2} kalimat -> {[s[:30] for s in s2]}")
print(f"P3: {c3} kalimat -> {[s[:30] for s in s3]}")
