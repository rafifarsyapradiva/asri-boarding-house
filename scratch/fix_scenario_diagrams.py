# -*- coding: utf-8 -*-
import subprocess, os
from PIL import Image

WORKSPACE_ROOT = r"c:\xampp\htdocs\asri-boarding-house"
SKRIPSI_IMAGES = os.path.join(WORKSPACE_ROOT, "Skripsi", "images")
SCRATCH_DIR = r"C:\Users\RAFIF\.gemini\antigravity-ide\brain\496e6b32-1575-47ed-812b-0dce7ce448fa\scratch"

def render_mmd(mmd_text, out_png, scale=2):
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

# 1. skenario_admin_flow_aktivasi_dan_monitoring
# Quote all subgraph titles and use flowchart TB with multi-columns
mmd_flow_admin = """%%{init: {'theme': 'base', 'themeVariables': {'fontSize': '16px', 'fontFamily': 'Inter, system-ui, sans-serif'}}}%%
flowchart TB
    A["Bapak Asep (Admin) Buka Menu Reservasi: /admin/reservasi"] --> B{"Identifikasi Status Transaksi Reservasi"}

    subgraph Track_Lunas ["Jalur 1: Nur Haliza (Kamar 101 VIP - Skema Lunas 100%)"]
        B -->|"Status: LUNAS"| C1["Audit Dokumen: NIK 3374115212030001 & Kontak Darurat Wali"]
        C1 --> D1["Verifikasi Pembayaran Penuh Rp 15.400.560 (Mandiri VA)"]
        D1 --> E1["Admin Klik Tombol 'Konfirmasi & Aktifkan Penyewa'"]
        E1 --> F1["TransisiPenyewaService Eksekusi injectLunasPenuh"]
        F1 --> G1["Kamar 101 Terkunci 'terisi'<br/>Tarif Personal: Rp 1.283.380/bln<br/>Tagihan Bulan 1: LUNAS"]
    end

    subgraph Track_DP ["Jalur 2: Tyas (Kamar 104 Deluxe - Skema DP 30%)"]
        B -->|"Status: DP"| C2["Audit Dokumen: NIK 3374114112030111 & Kontak Wali"]
        C2 --> D2["Verifikasi Pembayaran DP 30% Rp 1.710.000 (QRIS)"]
        D2 --> E2["Admin Klik Tombol 'Konfirmasi & Aktifkan Penyewa'"]
        E2 --> F2["TransisiPenyewaService Eksekusi injectSisaDp"]
        F2 --> G2["Kamar 104 Terkunci 'terisi'<br/>Terbit Tagihan Sisa 70%: Rp 3.990.000"]
        G2 --> H2["Monitoring Portal: Tyas Lunasi Sisa Rp 3.990.000 via Snap"]
        H2 --> I2["Status Tagihan Sisa Berubah Menjadi LUNAS"]
    end

    G1 --> J["Orkestrasi Otomasi Sistem Terpadu"]
    I2 --> J

    subgraph Otomasi_Operasional ["Siklus Hunian Aktif & Tagihan Rutin Bulanan"]
        J --> K["Fonnte WhatsApp API Kirim Pesan Selamat Datang & Kredensial"]
        K --> L["Kuitansi Pembayaran Format A5 Siap Diunduh via html2pdf.js"]
        L --> M["Status Kedua Penghuni: Resmi Aktif Menempati Kamar"]
        M --> N["Hostinger Cron Tgl 1 Pukul 00:05 WIB: Terbitkan Invoice Baru"]
    end
"""
render_mmd(mmd_flow_admin, os.path.join(SKRIPSI_IMAGES, "skenario_admin_flow_aktivasi_dan_monitoring.png"), scale=2)

# 2. skenario_flow_checkout_manual_inspection_hold
# Flowchart TB with two balanced subgraphs
mmd_flow_checkout = """%%{init: {'theme': 'base', 'themeVariables': {'fontSize': '16px', 'fontFamily': 'Inter, system-ui, sans-serif'}}}%%
flowchart TB
    subgraph Fase1 ["Fase 1: Verifikasi Finansial & Prosedur Checkout Administratif"]
        A["Jadwal Akhir Kontrak: Tyas (6 Bulan) / Nur Haliza (12 Bulan)"] --> B["Admin Buka Menu Checkout (/admin/penyewa/{id}/checkout)"]
        B --> C{"Audit Kewajiban Tagihan (unpaidBillsCount)"}
        C -->|"Terdapat Tunggakan"| D["Ditolak Sistem: Selesaikan Tagihan Tertunggak"]
        C -->|"Tagihan Lunas 100%"| E["Inspeksi Fisik Kondisi Kamar bersama Penghuni"]
        E --> F["Pemeriksaan Inventaris Kamar: Seluruh Sarana Utuh & Bersih"]
        F --> G["Admin Klik 'Proses Checkout': Akun Penyewa Dinonaktifkan"]
    end

    subgraph Fase2 ["Fase 2: Protokol Karantina Fisik & Pelepasan Status Kamar Manual"]
        H["Aturan Kritis: Status Kamar di Basis Data TETAP 'terisi' (Terkunci)"] --> I["Petugas Kebersihan Melaksanakan Sanitasi & Penggantian Sprei"]
        I --> J["Bapak Asep Buka /admin/kamar -> Ubah Status MANUAL ke 'tersedia'"]
        J --> K["KamarObserver Menghapus Cache Landing Page (kamar_aktif_landing)"]
        K --> L["Unit Kamar Seketika Tayang Kembali di Katalog Publik weatso.id"]
    end

    G ==> H

    style D fill:#fee2e2,stroke:#ef4444,stroke-width:2px,color:#991b1b
    style H fill:#fef3c7,stroke:#f59e0b,stroke-width:2px,color:#92400e
    style L fill:#dcfce7,stroke:#22c55e,stroke-width:2px,color:#166534
"""
render_mmd(mmd_flow_checkout, os.path.join(SKRIPSI_IMAGES, "skenario_flow_checkout_manual_inspection_hold.png"), scale=2)

# 3. skenario_state_siklus_billing
# Flowchart state with clear width and height
mmd_state_billing = """%%{init: {'theme': 'base', 'themeVariables': {'fontSize': '16px', 'fontFamily': 'Inter, system-ui, sans-serif'}}}%%
stateDiagram-v2
    direction TB

    [*] --> Tagihan_Diterbitkan: Tanggal 1 Pukul 00:05 WIB (Cron Scheduler)
    
    state Tagihan_Diterbitkan {
        Setup_Tarif --> Nur_Haliza: Kamar 101 VIP (Rp 1.283.380/bln)
        Setup_Tarif --> Tyas: Kamar 104 Deluxe (Rp 950.000/bln)
    }

    Tagihan_Diterbitkan --> Grace_Period: Notifikasi WhatsApp Fonnte Terkirim
    
    state Grace_Period {
        Tanggal_1_sd_10 --> Bayar_Disiplin: Bayar Tgl 1 - 5 via Virtual Account
    }

    Grace_Period --> Lunas: Pembayaran Terkonfirmasi di Midtrans Webhook (Settlement)
    Grace_Period --> Masa_Toleransi: Melewati Tanggal 10 (Jatuh Tempo)

    state Masa_Toleransi {
        Tanggal_11_sd_Akhir_Bulan --> Reminder_WA: Scheduler Harian Kirim Pesan Ramah
        Reminder_WA --> Denda_Rp0: Nominal Denda Tetap Rp 0 (Bulan Kalender Sama)
        Denda_Rp0 --> Bayar_Susulan: Tyas Bayar Tanggal 15 via QRIS Snap
    }

    Masa_Toleransi --> Lunas: Pembayaran Diverifikasi Sah
    Masa_Toleransi --> Denda_Kalender: Melewati Akhir Bulan (Berganti Bulan Kalender)

    state Denda_Kalender {
        Tanggal_1_Bulan_Baru --> Denda_Flat_5_Persen: Sistem Bebankan Denda Flat 5% Tepat 1x
        Denda_Flat_5_Persen --> Eskalasi_Wali: Pengiriman Notifikasi WA ke Nomor Orang Tua/Wali
    }

    Denda_Kalender --> Lunas: Pelunasan Tagihan Pokok + Denda

    state Lunas {
        Pembaruan_Status_Database --> Cetak_Kuitansi: Render Kuitansi Digital A5 (html2pdf.js)
        Cetak_Kuitansi --> Hunian_Berlanjut: Kontrak Tetap Aktif Tanpa Interupsi
    }

    Lunas --> [*]: Berulang pada Awal Bulan Kalender Berikutnya
"""
render_mmd(mmd_state_billing, os.path.join(SKRIPSI_IMAGES, "skenario_state_siklus_billing.png"), scale=2)
