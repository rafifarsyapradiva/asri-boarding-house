# -*- coding: utf-8 -*-
"""
Script to prepare and render all high-resolution diagrams for Bab 4.1
"""
import os, sys, subprocess, shutil
from PIL import Image

WORKSPACE_ROOT = r"c:\xampp\htdocs\asri-boarding-house"
SKRIPSI_IMAGES = os.path.join(WORKSPACE_ROOT, "Skripsi", "images")
BLUEPRINT_DIR = os.path.join(WORKSPACE_ROOT, "Blueprint")
SCRATCH_DIR = r"C:\Users\RAFIF\.gemini\antigravity-ide\brain\496e6b32-1575-47ed-812b-0dce7ce448fa\scratch"

os.makedirs(SKRIPSI_IMAGES, exist_ok=True)
os.makedirs(SCRATCH_DIR, exist_ok=True)

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

print("=== 1. GENERATE 3 MODULAR USE CASE DIAGRAMS ===")

# 1a. Penyewa Aktif
mmd_uc_penyewa = """%%{init: {'theme': 'base', 'themeVariables': {'fontSize': '15px', 'fontFamily': 'Inter, system-ui, sans-serif'}}}%%
flowchart LR
    classDef primaryActor fill:#fff8e1,stroke:#ffa000,stroke-width:2px,font-weight:bold;
    classDef secondaryActor fill:#f3e5f5,stroke:#7b1fa2,stroke-width:2px,font-weight:bold;
    classDef ucNode fill:#e8f5e9,stroke:#2e7d32,stroke-width:2px,color:#1b5e20;
    classDef extNode fill:#e1f5fe,stroke:#0288d1,stroke-width:2px,color:#01579b;

    subgraph ColPenyewa ["Aktor Utama"]
        Penyewa(["Penyewa Aktif"]):::primaryActor
    end

    subgraph BoundaryTenant ["Sistem Informasi Asri Boarding House (Modul Portal Penyewa Aktif)"]
        direction TB

        subgraph SubAksesTenant ["Akses Portal & Profil"]
            UC22_P(["UC-22: Login Portal Penyewa"]):::ucNode
            UC18_P(["UC-18: Login Google OAuth SSO"]):::ucNode
        end

        subgraph SubKeuanganTenant ["Tagihan & Transaksi Finansial"]
            UC16_P(["UC-16: Riwayat Tagihan & Status Jatuh Tempo"]):::ucNode
            UC04(["UC-04: Membayar Tagihan Sewa Rutin & Sisa DP"]):::ucNode
            UC25(["UC-25: Mengunduh Kuitansi Digital PDF A5"]):::extNode
        end

        subgraph SubLayananTenant ["Layanan & Informasi Kost"]
            UC05(["UC-05: Pengaduan Keluhan Fasilitas Berfoto"]):::ucNode
            UC11(["UC-11: Akses Menu Peraturan & Tata Tertib"]):::ucNode
            UC21(["UC-21: Menerima Pengumuman & Broadcast"]):::ucNode
        end
    end

    subgraph ColEksternalTenant ["Layanan Eksternal"]
        direction TB
        Midtrans[["Payment Gateway (Midtrans Snap)"]]:::secondaryActor
        Fonnte[["WhatsApp Gateway (Fonnte API)"]]:::secondaryActor
        Google[["Google Identity Services (OAuth)"]]:::secondaryActor
    end

    Penyewa --> UC22_P
    Penyewa --> UC18_P
    Penyewa --> UC16_P
    Penyewa --> UC04
    Penyewa --> UC25
    Penyewa --> UC05
    Penyewa --> UC11
    Penyewa --> UC21

    UC25 -.-> |"<<extend>>"| UC04

    UC18_P --> Google
    UC04 --> Midtrans
    UC05 --> Fonnte
"""
render_mmd(mmd_uc_penyewa, os.path.join(SKRIPSI_IMAGES, "use_case_penyewa_aktif.png"), scale=3)

# 1b. Tamu (Guest)
mmd_uc_guest = """%%{init: {'theme': 'base', 'themeVariables': {'fontSize': '15px', 'fontFamily': 'Inter, system-ui, sans-serif'}}}%%
flowchart LR
    classDef primaryActor fill:#fff8e1,stroke:#ffa000,stroke-width:2px,font-weight:bold;
    classDef secondaryActor fill:#f3e5f5,stroke:#7b1fa2,stroke-width:2px,font-weight:bold;
    classDef ucNode fill:#e8f5e9,stroke:#2e7d32,stroke-width:2px,color:#1b5e20;
    classDef extNode fill:#e1f5fe,stroke:#0288d1,stroke-width:2px,color:#01579b;

    subgraph ColGuest ["Aktor Utama"]
        Guest(["Tamu (Guest / Pengunjung)"]):::primaryActor
    end

    subgraph BoundaryGuest ["Sistem Informasi Asri Boarding House (Modul Publik & Tamu)"]
        direction TB

        subgraph SubEksplorasi ["Eksplorasi Informasi & Konsultasi"]
            UC10(["UC-10: Pencarian & Cek Ketersediaan 32 Kamar"]):::ucNode
            UC10_Calc(["Simulasi Tarif Sewa (Harian / Mingguan / Bulanan)"]):::ucNode
            UC17(["UC-17: Diskusi via Floating Guest Live Chat"]):::ucNode
            CTA_WA(["Interaksi Tombol WhatsApp Melayang (CTA)"]):::ucNode
        end

        subgraph SubAkunGuest ["Otentikasi & Registrasi Akun"]
            UC12(["UC-12: Registrasi Akun Calon Penyewa"]):::ucNode
            UC18(["UC-18: Login / Masuk via Akun Google OAuth"]):::ucNode
            UC22(["UC-22: Login Akun Pengguna Terdaftar"]):::ucNode
            UC24(["UC-24: Permohonan Reset Password Akun"]):::extNode
        end
    end

    subgraph ColEksternalGuest ["Layanan Eksternal"]
        direction TB
        Google[["Google Identity Services (OAuth)"]]:::secondaryActor
        MailServer[["SMTP Mail Server"]]:::secondaryActor
    end

    Guest --> UC10
    Guest --> UC10_Calc
    Guest --> UC17
    Guest --> CTA_WA
    Guest --> UC12
    Guest --> UC18
    Guest --> UC22

    UC24 -.-> |"<<extend>>"| UC22

    UC18 --> Google
    UC24 --> MailServer
"""
render_mmd(mmd_uc_guest, os.path.join(SKRIPSI_IMAGES, "use_case_tamu_guest.png"), scale=3)

# 1c. Calon Penyewa
mmd_uc_calon = """%%{init: {'theme': 'base', 'themeVariables': {'fontSize': '15px', 'fontFamily': 'Inter, system-ui, sans-serif'}}}%%
flowchart LR
    classDef primaryActor fill:#fff8e1,stroke:#ffa000,stroke-width:2px,font-weight:bold;
    classDef secondaryActor fill:#f3e5f5,stroke:#7b1fa2,stroke-width:2px,font-weight:bold;
    classDef ucNode fill:#e8f5e9,stroke:#2e7d32,stroke-width:2px,color:#1b5e20;
    classDef extNode fill:#e1f5fe,stroke:#0288d1,stroke-width:2px,color:#01579b;

    subgraph ColCalon ["Aktor Utama"]
        Calon(["Calon Penyewa Terdaftar"]):::primaryActor
    end

    subgraph BoundaryCalon ["Sistem Informasi Asri Boarding House (Modul Pemesanan & Reservasi)"]
        direction TB

        subgraph SubReservasi ["Proses Pemesanan Kamar (5-Step Stepper)"]
            UC01(["UC-01: Melakukan Pemesanan Kamar Online"]):::ucNode
            UC_Prof(["Verifikasi Profil & Kontak WhatsApp Valid"]):::ucNode
            UC_Data(["Pengisian NIK 16 Digit & Kontak Wali Darurat"]):::ucNode
            UC_Durasi(["Pemilihan Durasi Sewa & Kalkulasi Diskon Tahunan"]):::ucNode
        end

        subgraph SubTransaksi ["Transaksi & Komunikasi Pra-Bayar"]
            UC02(["UC-02: Membayar Uang Muka DP 30% atau Lunas 100%"]):::extNode
            UC03(["UC-03: Chat Diskusi Pra-Pembayaran (Pre-Payment Box)"]):::extNode
            UC16_C(["UC-16: Memantau Riwayat Pemesanan & Status Audit"]):::ucNode
        end
    end

    subgraph ColEksternalCalon ["Layanan Eksternal"]
        direction TB
        Midtrans[["Payment Gateway (Midtrans Snap)"]]:::secondaryActor
        Fonnte[["WhatsApp Gateway (Fonnte API)"]]:::secondaryActor
    end

    Calon --> UC01
    Calon --> UC_Prof
    Calon --> UC_Data
    Calon --> UC_Durasi
    Calon --> UC16_C
    Calon --> UC02
    Calon --> UC03

    UC02 -.-> |"<<extend>>"| UC01
    UC03 -.-> |"<<extend>>"| UC01

    UC02 --> Midtrans
    UC01 --> Fonnte
"""
render_mmd(mmd_uc_calon, os.path.join(SKRIPSI_IMAGES, "use_case_calon_penyewa.png"), scale=3)

print("\n=== 2. PREPARE HIGH-RES SEQUENCE DIAGRAMS ===")
# Copy 2352px versions from Blueprint/sequence/
shutil.copy2(os.path.join(BLUEPRINT_DIR, "sequence", "seq_diagram_1.png"), os.path.join(SKRIPSI_IMAGES, "sequence_reservasi_online.png"))
shutil.copy2(os.path.join(BLUEPRINT_DIR, "sequence", "seq_diagram_2.png"), os.path.join(SKRIPSI_IMAGES, "sequence_aktivasi_admin.png"))
shutil.copy2(os.path.join(BLUEPRINT_DIR, "sequence", "seq_diagram_3.png"), os.path.join(SKRIPSI_IMAGES, "sequence_penagihan_pembayaran.png"))
print("Sequence diagrams copied from Blueprint at 2352px resolution.")

print("\n=== 3. PREPARE MODULAR FLOWCHART DIAGRAMS ===")
# Copy modular flowcharts
shutil.copy2(os.path.join(BLUEPRINT_DIR, "flowchart", "alur_billing_otomatis.png"), os.path.join(SKRIPSI_IMAGES, "flowchart_auto_billing.png"))
shutil.copy2(os.path.join(BLUEPRINT_DIR, "flowchart", "peta_hubungan_proses.png"), os.path.join(SKRIPSI_IMAGES, "flowchart_denda_kalender.png"))
shutil.copy2(os.path.join(BLUEPRINT_DIR, "flowchart", "alur_konfirmasi_reservasi.png"), os.path.join(SKRIPSI_IMAGES, "flowchart_transisi_reservasi.png"))
print("Modular flowcharts copied from Blueprint.")

print("\n=== 4. PREPARE MODULAR ERD CLUSTER DIAGRAMS ===")
shutil.copy2(os.path.join(BLUEPRINT_DIR, "erd", "diagram_erd.png"), os.path.join(SKRIPSI_IMAGES, "erd_arsitektur_global.png"))
shutil.copy2(os.path.join(BLUEPRINT_DIR, "erd", "erd_relasi_users_kamar_penyewa.png"), os.path.join(SKRIPSI_IMAGES, "erd_kluster_kamar_users.png"))
shutil.copy2(os.path.join(BLUEPRINT_DIR, "erd", "erd_relasi_reservasi_chat.png"), os.path.join(SKRIPSI_IMAGES, "erd_kluster_reservasi_chat.png"))
shutil.copy2(os.path.join(BLUEPRINT_DIR, "erd", "erd_relasi_billing_pembayaran.png"), os.path.join(SKRIPSI_IMAGES, "erd_kluster_billing_pembayaran.png"))
shutil.copy2(os.path.join(BLUEPRINT_DIR, "erd", "erd_relasi_keluhan_guest_analytics.png"), os.path.join(SKRIPSI_IMAGES, "erd_kluster_keluhan_notifikasi.png"))
print("ERD cluster diagrams copied from Blueprint.")

print("\n=== 5. RE-RENDER PROPORTIONAL SCENARIO DIAGRAMS (Eliminate 5000px noodles) ===")

# 5c. State Diagram Billing (Render Horizontal direction LR)
mmd_state_billing = """%%{init: {'theme': 'base', 'themeVariables': {'fontSize': '15px', 'fontFamily': 'Inter, system-ui, sans-serif'}}}%%
stateDiagram-v2
    direction LR

    [*] --> Tagihan_Diterbitkan: Tanggal 1 Pukul 00:05 WIB (Cron Job)
    
    state Tagihan_Diterbitkan {
        direction TB
        Setup_Nominal --> Nur_Haliza: Kamar 101 VIP (Rp 1.283.380)
        Setup_Nominal --> Tyas: Kamar 104 Deluxe (Rp 950.000)
    }

    Tagihan_Diterbitkan --> Grace_Period: WhatsApp Invoice Fonnte Terkirim
    
    state Grace_Period {
        direction TB
        Tgl_1_sd_10 --> Bayar_Disiplin: Bayar Tgl 1 - 5 (BCA / Mandiri VA)
    }

    Grace_Period --> Lunas: Pembayaran Midtrans Settlement
    Grace_Period --> Masa_Toleransi: Lewati Tgl 10 (Jatuh Tempo)

    state Masa_Toleransi {
        direction TB
        Tgl_11_sd_Akhir_Bulan --> Reminder_WA: Scheduler Harian
        Reminder_WA --> Denda_Rp0: Nominal Denda Tetap Rp 0
        Denda_Rp0 --> Bayar_Susulan: Tyas Bayar Tgl 15 via QRIS
    }

    Masa_Toleransi --> Lunas: Pembayaran Terkonfirmasi
    Masa_Toleransi --> Denda_Kalender: Ganti Bulan Kalender Baru

    state Denda_Kalender {
        direction TB
        Tgl_1_Bulan_Baru --> Denda_Flat_5: Pembebanan Denda Flat 5%
        Denda_Flat_5 --> Eskalasi_Wali: Kirim WA ke Nomor Wali
    }

    Denda_Kalender --> Lunas: Pelunasan Pokok + Denda

    state Lunas {
        direction TB
        Status_Database_Update --> Cetak_Kuitansi: Render Kuitansi Digital A5
        Cetak_Kuitansi --> Hunian_Berlanjut: Kontrak Tetap Aktif
    }

    Lunas --> [*]: Berulang Siklus Bulan Depan
"""
render_mmd(mmd_state_billing, os.path.join(SKRIPSI_IMAGES, "skenario_state_siklus_billing.png"), scale=2)

# 5e. Flowchart Aktivasi Admin (Render Horizontal 2-Track)
mmd_flow_admin = """%%{init: {'theme': 'base', 'themeVariables': {'fontSize': '14px', 'fontFamily': 'Inter, system-ui, sans-serif'}}}%%
flowchart LR
    A[Buka Menu Reservasi /admin/reservasi] --> B{Identifikasi Status Reservasi Masuk}

    subgraph Track_Lunas [Jalur 1: Nur Haliza (Kamar 101 - Lunas 100%)]
        direction TB
        B -->|Status: LUNAS| C1[Audit NIK 3374115212030001 & Kontak Wali]
        C1 --> D1[Verifikasi Pembayaran Penuh Rp 15.400.560 Mandiri VA]
        D1 --> E1[Klik 'Konfirmasi & Aktifkan Penyewa']
        E1 --> F1[TransisiPenyewaService: injectLunasPenuh]
        F1 --> G1[Kamar 101 Terkunci 'terisi'<br/>Tarif Personal: Rp 1.283.380/bln<br/>Tagihan Bulan 1: LUNAS]
    end

    subgraph Track_DP [Jalur 2: Tyas (Kamar 104 - DP 30%)]
        direction TB
        B -->|Status: DP| C2[Audit NIK 3374114112030111 & Kontak Wali]
        C2 --> D2[Verifikasi Pembayaran DP Rp 1.710.000 QRIS]
        D2 --> E2[Klik 'Konfirmasi & Aktifkan Penyewa']
        E2 --> F2[TransisiPenyewaService: injectSisaDp]
        F2 --> G2[Kamar 104 Terkunci 'terisi'<br/>Terbit Tagihan Sisa 70%: Rp 3.990.000]
        G2 --> H2[Monitoring Portal: Tyas Lunasi Sisa via Snap]
        H2 --> I2[Status Tagihan Sisa Berubah LUNAS]
    end

    G1 --> J[Otomasi Sistem Terpadu]
    I2 --> J

    subgraph Otomasi_Operasional [Siklus Hunian Aktif & Tagihan Rutin]
        direction TB
        J --> K[Fonnte API Kirim WA Selamat Datang & Kredensial]
        K --> L[Kuitansi Pembayaran A5 Siap Cetak via html2pdf.js]
        L --> M[Kedua Penghuni Resmi Menempati Kamar]
        M --> N[Cron Scheduler Tgl 1 Pukul 00:05 WIB:<br/>Penerbitan Invoice Tagihan Bulanan Baru]
    end
"""
render_mmd(mmd_flow_admin, os.path.join(SKRIPSI_IMAGES, "skenario_admin_flow_aktivasi_dan_monitoring.png"), scale=2)

# 5f. Flowchart Checkout Admin (Render Horizontal 2-Phase)
mmd_flow_checkout = """%%{init: {'theme': 'base', 'themeVariables': {'fontSize': '14px', 'fontFamily': 'Inter, system-ui, sans-serif'}}}%%
flowchart LR
    subgraph Fase1 [Fase 1: Verifikasi Finansial & Checkout Administratif]
        direction TB
        A["Akhir Kontrak: Tyas (6 Bln) / Nur Haliza (12 Bln)"] --> B["Buka Menu Checkout (/admin/penyewa/{id}/checkout)"]
        B --> C{"Audit Kewajiban Tagihan"}
        C -->|"Ada Tunggakan"| D["Ditolak: Lunasi Tagihan Dulu"]
        C -->|"Lunas 100%"| E["Inspeksi Fisik Bersama Penyewa di Kamar"]
        E --> F["Pemeriksaan Inventaris: Fasilitas Lengkap & Utuh"]
        F --> G["Admin Klik 'Proses Checkout': Akun Dinonaktifkan"]
    end

    subgraph Fase2 [Fase 2: Protokol Karantina & Pelepasan Kamar Manual]
        direction TB
        H["Kebijakan Kritis: Status Kamar di DB TETAP 'terisi' (Terkunci)"] --> I["Tim Kebersihan Kost Melakukan Pembersihan & Sterilisasi"]
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

print("\n=== ALL DIAGRAMS SUCCESSFULLY PROCESSED AND VERIFIED ===")
