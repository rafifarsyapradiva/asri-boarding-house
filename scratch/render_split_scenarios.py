# -*- coding: utf-8 -*-
"""
Render split scenario diagrams with perfect aspect ratios for Word
"""
import subprocess, os
from PIL import Image

WORKSPACE_ROOT = r"c:\xampp\htdocs\asri-boarding-house"
SKRIPSI_IMAGES = os.path.join(WORKSPACE_ROOT, "Skripsi", "images")
SCRATCH_DIR = r"C:\Users\RAFIF\.gemini\antigravity-ide\brain\496e6b32-1575-47ed-812b-0dce7ce448fa\scratch"

def render_mmd(mmd_text, out_png, scale=3):
    base_name = os.path.basename(out_png).replace('.png', '')
    mmd_file = os.path.join(SCRATCH_DIR, f"{base_name}.mmd")
    with open(mmd_file, "w", encoding="utf-8") as f:
        f.write(mmd_text)
    
    cmd = f'cmd /c npx.cmd @mermaid-js/mermaid-cli -i "{mmd_file}" -o "{out_png}" -s {scale} -b white'
    res = subprocess.run(cmd, shell=True, capture_output=True, text=True)
    if res.returncode != 0:
        print(f"FAILED to render {out_png}: {res.stderr}")
        return False
    else:
        im = Image.open(out_png)
        print(f"SUCCESS: {os.path.basename(out_png)} ({im.size[0]}x{im.size[1]})")
        return True

# 1. State Diagram - Bagian 1
mmd_state_p1 = """%%{init: {'theme': 'base', 'themeVariables': {'fontSize': '16px', 'fontFamily': 'Inter, system-ui, sans-serif'}}}%%
stateDiagram-v2
    direction TB
    [*] --> Tagihan_Diterbitkan: Tgl 1 Pukul 00:05 WIB (Hostinger Cron Scheduler)
    
    state Tagihan_Diterbitkan {
        Setup_Nominal --> Nur_Haliza: Kamar 101 VIP (Rp 1.283.380/bln)
        Setup_Nominal --> Tyas: Kamar 104 Deluxe (Rp 950.000/bln)
    }

    Tagihan_Diterbitkan --> Grace_Period: WhatsApp Invoice Fonnte Terkirim Otomatis
    
    state Grace_Period {
        Tanggal_1_sd_10 --> Bayar_Tepat_Waktu: Nur Haliza Bayar Tgl 1 - 5 via VA
    }

    Grace_Period --> Lunas_Disiplin: Pembayaran Midtrans Settlement (Denda Rp 0)
    Grace_Period --> Menuju_Masa_Toleransi: Melewati Tanggal 10 (Jatuh Tempo)
"""
render_mmd(mmd_state_p1, os.path.join(SKRIPSI_IMAGES, "skenario_state_billing_part1.png"), scale=3)

# 2. State Diagram - Bagian 2
mmd_state_p2 = """%%{init: {'theme': 'base', 'themeVariables': {'fontSize': '16px', 'fontFamily': 'Inter, system-ui, sans-serif'}}}%%
stateDiagram-v2
    direction TB
    [*] --> Masa_Toleransi: Transisi dari Jatuh Tempo Tanggal 10

    state Masa_Toleransi {
        Tanggal_11_sd_Akhir_Bulan --> Reminder_WA: Scheduler Harian Kirim Notifikasi Sopan
        Reminder_WA --> Denda_Nol: Kebijakan Denda Tetap Rp 0 (Bulan Kalender Berjalan)
        Denda_Nol --> Bayar_Susulan: Tyas Bayar Tanggal 15 via QRIS
    }

    Masa_Toleransi --> Lunas_Toleransi: Pembayaran Terkonfirmasi di Midtrans
    Masa_Toleransi --> Denda_Kalender: Melintasi Batas Pergantian Bulan Kalender

    state Denda_Kalender {
        Tanggal_1_Bulan_Baru --> Pembebanan_Denda: Penegakan Denda Flat 5% Tepat 1x
        Pembebanan_Denda --> Eskalasi_Wali: Pengiriman Notifikasi WA ke Orang Tua / Wali
    }

    Denda_Kalender --> Lunas_Denda: Pelunasan Tagihan Pokok + Denda

    state Status_Lunas {
        Pembaruan_Basis_Data --> Unduh_Kuitansi: Render Kuitansi Digital A5 via html2pdf.js
        Unduh_Kuitansi --> Hunian_Aktif: Masa Tinggal Berjalan Normal
    }

    Lunas_Toleransi --> Status_Lunas
    Lunas_Denda --> Status_Lunas
    Status_Lunas --> [*]: Siklus Berulang pada Bulan Kalender Berikutnya
"""
render_mmd(mmd_state_p2, os.path.join(SKRIPSI_IMAGES, "skenario_state_billing_part2.png"), scale=3)

# 3. Flowchart Checkout - Bagian 1
mmd_co_p1 = """%%{init: {'theme': 'base', 'themeVariables': {'fontSize': '16px', 'fontFamily': 'Inter, system-ui, sans-serif'}}}%%
flowchart TB
    A["Jadwal Akhir Masa Sewa: Tyas (6 Bln) / Nur Haliza (12 Bln)"] --> B["Admin Buka Menu Checkout (/admin/penyewa/{id}/checkout)"]
    B --> C{"Pemeriksaan Tagihan Tertunggak (unpaidBillsCount)"}
    C -->|"unpaidBillsCount > 0"| D["Ditolak Sistem: Selesaikan Seluruh Tagihan Sewa"]
    C -->|"unpaidBillsCount == 0"| E["Inspeksi Fisik Bersama Penghuni di Kamar Kost"]
    E --> F["Pemeriksaan Inventaris: Kondisi Kasur, AC & Sanitasi Bersih"]
    F --> G["Admin Menekan Tombol 'Proses Checkout'"]
    G --> H["Akun Penyewa Dinonaktifkan (is_active = 0, tanggal_keluar tercatat)"]
    
    style D fill:#fee2e2,stroke:#ef4444,stroke-width:2px,color:#991b1b
    style H fill:#dbeafe,stroke:#3b82f6,stroke-width:2px,color:#1e40af
"""
render_mmd(mmd_co_p1, os.path.join(SKRIPSI_IMAGES, "skenario_checkout_part1.png"), scale=3)

# 4. Flowchart Checkout - Bagian 2
mmd_co_p2 = """%%{init: {'theme': 'base', 'themeVariables': {'fontSize': '16px', 'fontFamily': 'Inter, system-ui, sans-serif'}}}%%
flowchart TB
    A["Proses Checkout Administratif Selesai Dilakukan"] --> B["ATURAN MUTLAK: Status Kamar di Database TETAP 'terisi' (Terkunci)"]
    B --> C["Petugas Kebersihan Melaksanakan Sanitasi Menyeluruh & Ganti Sprei"]
    C --> D["Bapak Asep Melakukan Pengecekan Fisik: Kamar 100% Bersih & Siap Huni"]
    D --> E["Bapak Asep Buka /admin/kamar -> Ubah Status MANUAL Menjadi 'tersedia'"]
    E --> F["Event KamarObserver Menghapus Cache Katalog (kamar_aktif_landing)"]
    F --> G["Unit Kamar Seketika Tayang Kembali di Landing Page Publik weatso.id"]

    style B fill:#fef3c7,stroke:#f59e0b,stroke-width:2px,color:#92400e
    style G fill:#dcfce7,stroke:#22c55e,stroke-width:2px,color:#166534
"""
render_mmd(mmd_co_p2, os.path.join(SKRIPSI_IMAGES, "skenario_checkout_part2.png"), scale=3)

# 5. Flowchart Admin Aktivasi - Bagian 1
mmd_act_p1 = """%%{init: {'theme': 'base', 'themeVariables': {'fontSize': '16px', 'fontFamily': 'Inter, system-ui, sans-serif'}}}%%
flowchart TB
    A["Bapak Asep Buka Menu Manajemen Reservasi: /admin/reservasi"] --> B{"Evaluasi Transaksi Pembayaran Masuk"}
    
    subgraph Jalur_Lunas ["Jalur 1: Nur Haliza (Kamar 101 VIP - Skema Lunas 100%)"]
        B -->|"Status: LUNAS"| C1["Audit NIK 3374115212030001 & Kontak Darurat Wali"]
        C1 --> D1["Verifikasi Pembayaran Penuh Rp 15.400.560 (Mandiri VA)"]
        D1 --> E1["Admin Klik 'Konfirmasi & Aktifkan Penyewa'"]
        E1 --> F1["TransisiPenyewaService Eksekusi injectLunasPenuh"]
        F1 --> G1["Kamar 101 Terkunci 'terisi'<br/>Tarif Personal: Rp 1.283.380/bln<br/>Tagihan Bulan 1: LUNAS"]
    end

    subgraph Jalur_DP ["Jalur 2: Tyas (Kamar 104 Deluxe - Skema DP 30%)"]
        B -->|"Status: DP"| C2["Audit NIK 3374114112030111 & Kontak Wali"]
        C2 --> D2["Verifikasi Pembayaran DP Rp 1.710.000 (QRIS Snap)"]
        D2 --> E2["Admin Klik 'Konfirmasi & Aktifkan Penyewa'"]
        E2 --> F2["TransisiPenyewaService Eksekusi injectSisaDp"]
        F2 --> G2["Kamar 104 Terkunci 'terisi'<br/>Terbit Tagihan Sisa 70%: Rp 3.990.000"]
    end
"""
render_mmd(mmd_act_p1, os.path.join(SKRIPSI_IMAGES, "skenario_admin_flow_part1.png"), scale=3)

# 6. Flowchart Admin Aktivasi - Bagian 2
mmd_act_p2 = """%%{init: {'theme': 'base', 'themeVariables': {'fontSize': '16px', 'fontFamily': 'Inter, system-ui, sans-serif'}}}%%
flowchart TB
    A["Aktivasi Kontrak Sewa di Database Selesai Dieksekusi"] --> B["Monitoring Pelunasan Sisa DP: Tyas Bayar Rp 3.990.000 via Snap"]
    B --> C["Status Tagihan Sisa 70% Berubah Menjadi LUNAS"]
    C --> D["Fonnte WhatsApp API Kirim Pesan Selamat Datang & Kredensial Akun"]
    D --> E["Kuitansi Pembayaran Format A5 Siap Diunduh via html2pdf.js"]
    E --> F["Kedua Penghuni Resmi Menempati Kamar dalam Status Aktif"]
    F --> G["Cron Scheduler Hostinger Tgl 1 Pukul 00:05 WIB:<br/>Penerbitan Invoice Tagihan Bulanan Baru secara Otomatis"]

    style G fill:#dcfce7,stroke:#22c55e,stroke-width:2px,color:#166534
"""
render_mmd(mmd_act_p2, os.path.join(SKRIPSI_IMAGES, "skenario_admin_flow_part2.png"), scale=3)

print("ALL SPLIT SCENARIO DIAGRAMS GENERATED WITH HIGH-RES ASPECT RATIOS!")
