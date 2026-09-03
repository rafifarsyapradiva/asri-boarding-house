# Use Case Specification - Asri Boarding House

Dokumen ini mendokumentasikan spesifikasi **Use Case** tingkat final (terintegrasi penuh) untuk **Sistem Informasi Manajemen Kost Terintegrasi Payment Gateway pada Asri Boarding House**. Dokumen ini dirancang untuk menyelaraskan kebutuhan bisnis, interaksi pengguna (aktor internal dan eksternal), serta fungsionalitas sistem yang terhubung dengan basis data relasional, API pihak ketiga, dan otomasi tugas latar belakang (*cron job*).

Semua spesifikasi dalam dokumen ini selaras dengan dokumen [Blueprint_Projek_Web_Asri_Boarding_House.md](file:///c:/xampp/htdocs/asri-boarding-house/Blueprint/Blueprint_Projek_Web_Asri_Boarding_House.md) dan naskah [Skripsi Sub-bab 4.1.2](file:///c:/xampp/htdocs/asri-boarding-house/Skripsi/Skripsi_Rafif_Arsya_Pradiva_22N40014.md#L471-L488).

---

## 1. Identifikasi Aktor (Actors)

Sistem Asri Boarding House melibatkan **4 (empat) Aktor Utama (Primary Actors)**, **2 (dua) Aktor Sekunder / Sistem Eksternal (Secondary Actors)**, dan **1 (satu) Aktor Otomasi Waktu (Time-based System Actor)**:

### 1.1. Aktor Utama (Primary Actors)

| No | Aktor | Deskripsi |
| :--- | :--- | :--- |
| 1 | **Tamu (Guest)** | Pengunjung umum website/landing page yang belum mendaftarkan akun. Dapat menjelajahi informasi kost, melakukan pencarian kamar, dan berinteraksi via Guest Chat tanpa login. |
| 2 | **Calon Penyewa** | Pengguna yang terdaftar dan masuk ke sistem, tetapi belum berstatus sebagai penyewa aktif (dalam proses pengajuan reservasi kamar, diskusi pra-pembayaran via chat reservasi, dan pembayaran DP/Lunas). |
| 3 | **Penyewa Aktif** | Pengguna kost yang kontrak sewanya telah dikonfirmasi oleh admin. Memiliki akses penuh ke portal internal penyewa (pembayaran tagihan, unduh kuitansi PDF, pengajuan keluhan, peraturan kost, dan riwayat transaksi). |
| 4 | **Administrator** | Pengelola operasional kost (Bapak Asep & tim) yang bertanggung jawab atas manajemen data master kamar, verifikasi & konfirmasi reservasi, konfirmasi pembayaran kas tunai, penanganan keluhan, checkout/perpanjang kontrak, ekspor laporan, dan pembalasan chat reservasi/tamu. |

> **Catatan Konseptual Wali / Orang Tua Penyewa**:
> Wali penyewa **bukan merupakan Aktor sistem interaktif**, karena tidak memiliki hak akses/login langsung ke aplikasi. Wali diposisikan sebagai **pihak penerima pasif (recipient contact)** yang menerima eskalasi notifikasi persuasif WhatsApp via `WhatsApp Gateway (Fonnte)` saat keterlambatan pembayaran memasuki bulan kedua.

### 1.2. Aktor Sekunder & Otomasi Sistem (Secondary & Time-based Actors)

| No | Aktor Eksternal / System | Deskripsi |
| :--- | :--- | :--- |
| 1 | **Payment Gateway (Midtrans)** | Sistem pihak ketiga yang memproses transaksi pembayaran nontunai secara otomatis via Snap API dan mengirimkan callback webhook status pembayaran (Virtual Account, E-Wallet, QRIS). |
| 2 | **WhatsApp Gateway (Fonnte)** | Layanan pihak ketiga yang mengirimkan pesan pemberitahuan WhatsApp otomatis (kredensial login, notifikasi tagihan bulanan, denda keterlambatan ke penyewa & wali, pengumuman broadcast, dan update status keluhan). |
| 3 | **System Scheduler (Timer/Cron)** | Layanan jadwal otomatis Laravel Console Engine yang mengeksekusi tugas penerbitan tagihan rutin (tgl 1 jam 00:05), denda flat 5% idempoten & eskalasi wali harian (jam 01:00), serta pelepasan kunci reservasi kedaluwarsa. |

---

## 2. Diagram Use Case & Visualisasi Alur Proses (Mermaid UML 2.5)

Berikut adalah visualisasi diagram Use Case sistem yang disusun secara terstruktur per modul, **Master Unified Boundary**, **Graph Keterkaitan Include & Extend**, **Peta Alur Keterkaitan Inter-Use Case**, serta **Visualisasi Alur Proses Interaksi End-to-End**.

### 2.1. Diagram Use Case - Modul Publik & Calon Penyewa

```mermaid
flowchart LR
    classDef primaryActor fill:#fff8e1,stroke:#ffa000,stroke-width:2px,font-weight:bold;
    classDef secondaryActor fill:#f3e5f5,stroke:#7b1fa2,stroke-width:2px,font-weight:bold;
    classDef ucNode fill:#e8f5e9,stroke:#2e7d32,stroke-width:2px,color:#1b5e20;

    %% Primary Actors
    Guest(["Tamu (Guest)"]):::primaryActor
    Calon(["Calon Penyewa"]):::primaryActor

    %% Secondary Actors
    Midtrans[["Payment Gateway (Midtrans)"]]:::secondaryActor

    %% System Boundary
    subgraph System ["Sistem Informasi Asri Boarding House (Modul Publik & Reservasi)"]
        UC10(["UC-10: Pencarian & Cek Ketersediaan Kamar"]):::ucNode
        UC12(["UC-12: Registrasi Akun Calon Penyewa"]):::ucNode
        UC17(["UC-17: Diskusi via Guest Chat"]):::ucNode
        UC18(["UC-18: Login & Registrasi Google OAuth"]):::ucNode
        UC22(["UC-22: Login Akun Manual"]):::ucNode
        UC24(["UC-24: Lupa & Reset Password"]):::ucNode
        
        UC01(["UC-01: Melakukan Reservasi Kamar"]):::ucNode
        UC03(["UC-03: Chat Reservasi Real-time"]):::ucNode
        UC02(["UC-02: Melakukan Pembayaran Reservasi (DP/Lunas)"]):::ucNode
        UC16(["UC-16: Melihat Riwayat Pemesanan & Chat"]):::ucNode
    end

    %% Relations - Guest
    Guest --> UC10
    Guest --> UC12
    Guest --> UC17
    Guest --> UC18
    Guest --> UC22

    %% Relations - Calon Penyewa
    Calon --> UC22
    Calon --> UC18
    Calon --> UC01
    Calon --> UC03
    Calon --> UC02
    Calon --> UC16

    %% Relationships (Include & Extend)
    UC03 -.-> |extend| UC01
    UC02 -.-> |extend| UC01
    UC24 -.-> |extend| UC22
    UC02 -.-> |include| Midtrans
```

![Diagram Use Case Modul Publik & Reservasi](use_case/use_case_publik_reservasi.png)

---

### 2.2. Diagram Use Case - Modul Penyewa Aktif

```mermaid
flowchart LR
    classDef primaryActor fill:#fff8e1,stroke:#ffa000,stroke-width:2px,font-weight:bold;
    classDef secondaryActor fill:#f3e5f5,stroke:#7b1fa2,stroke-width:2px,font-weight:bold;
    classDef ucNode fill:#e8f5e9,stroke:#2e7d32,stroke-width:2px,color:#1b5e20;

    %% Primary Actor
    Penyewa(["Penyewa Aktif"]):::primaryActor

    %% Secondary Actors
    Midtrans[["Payment Gateway (Midtrans)"]]:::secondaryActor
    Fonnte[["WhatsApp Gateway (Fonnte)"]]:::secondaryActor

    %% System Boundary
    subgraph System ["Sistem Informasi Asri Boarding House (Modul Penyewa Aktif)"]
        UC22_P(["UC-22: Login Portal Penyewa"]):::ucNode
        UC18_P(["UC-18: Login Google OAuth SSO"]):::ucNode
        UC16_P(["UC-16: Melihat Riwayat Tagihan & Transaksi"]):::ucNode
        UC04(["UC-04: Membayar Tagihan Bulanan Rutin"]):::ucNode
        UC25(["UC-25: Mengunduh Kuitansi Bukti Pembayaran PDF"]):::ucNode
        UC05(["UC-05: Mengajukan Keluhan & Laporan Kerusakan"]):::ucNode
        UC11(["UC-11: Mengakses Menu Peraturan & Tata Tertib"]):::ucNode
        UC21(["UC-21: Melihat Pengumuman & Notifikasi"]):::ucNode
    end

    %% Relations - Penyewa Aktif
    Penyewa --> UC22_P
    Penyewa --> UC18_P
    Penyewa --> UC16_P
    Penyewa --> UC04
    Penyewa --> UC25
    Penyewa --> UC05
    Penyewa --> UC11
    Penyewa --> UC21

    %% Relationships (Include & Extend)
    UC25 -.-> |extend| UC04
    UC04 -.-> |include| Midtrans
    UC05 -.-> |include| Fonnte
```

![Diagram Use Case Modul Penyewa Aktif](use_case/use_case_penyewa_aktif.png)

---

### 2.3. Diagram Use Case - Modul Admin Panel & Operasional

```mermaid
flowchart LR
    classDef primaryActor fill:#fff8e1,stroke:#ffa000,stroke-width:2px,font-weight:bold;
    classDef secondaryActor fill:#f3e5f5,stroke:#7b1fa2,stroke-width:2px,font-weight:bold;
    classDef ucNode fill:#e8f5e9,stroke:#2e7d32,stroke-width:2px,color:#1b5e20;

    %% Primary Actor
    Admin(["Administrator"]):::primaryActor

    %% Secondary Actor
    Fonnte[["WhatsApp Gateway (Fonnte)"]]:::secondaryActor

    %% System Boundary
    subgraph System ["Sistem Informasi Asri Boarding House (Modul Admin Panel)"]
        UC22_A(["UC-22: Login Admin Panel"]):::ucNode
        UC13(["UC-13: Manajemen Kamar & Fasilitas (CRUD)"]):::ucNode
        UC14(["UC-14: Manajemen Penyewa (CRUD)"]):::ucNode
        UC26(["UC-26: Process Checkout & Perpanjangan Kontrak"]):::ucNode
        UC06(["UC-06: Verifikasi Reservasi & Konfirmasi Kas"]):::ucNode
        UC03_A(["UC-03: Chat Diskusi Reservasi"]):::ucNode
        UC15(["UC-15: Memproses & Menanggapi Keluhan"]):::ucNode
        UC08(["UC-08: Pencatatan Pengeluaran Operasional (CRUD)"]):::ucNode
        UC07(["UC-07: Pencatatan & Evaluasi Laporan Arus Kas"]):::ucNode
        UC27(["UC-27: Mengekspor Laporan & Data (PDF/Excel/CSV)"]):::ucNode
        UC09(["UC-09: Manajemen Konten Dinamis"]):::ucNode
        UC23(["UC-23: Manajemen Pengaturan Sistem"]):::ucNode
        UC19(["UC-19: Memantau Kalender Kontrol Visual"]):::ucNode
        UC20(["UC-20: Broadcast Notifikasi & Pengumuman"]):::ucNode
        UC17_A(["UC-17: Pengelolaan Guest Chat Tamu"]):::ucNode
        UC29(["UC-29: Memantau Log Audit & Error System"]):::ucNode
    end

    %% Relations - Admin
    Admin --> UC22_A
    Admin --> UC13
    Admin --> UC14
    Admin --> UC26
    Admin --> UC06
    Admin --> UC03_A
    Admin --> UC15
    Admin --> UC08
    Admin --> UC07
    Admin --> UC27
    Admin --> UC09
    Admin --> UC23
    Admin --> UC19
    Admin --> UC20
    Admin --> UC17_A
    Admin --> UC29

    %% Relationships (Include & Extend)
    UC26 -.-> |extend| UC14
    UC27 -.-> |extend| UC07
    UC27 -.-> |extend| UC14
    UC27 -.-> |extend| UC08
    UC06 -.-> |include| Fonnte
    UC15 -.-> |include| Fonnte
    UC20 -.-> |include| Fonnte
```

![Diagram Use Case Modul Administrator](use_case/use_case_admin.png)

---

### 2.4. Diagram Use Case Master (Unified Master Boundary)

```mermaid
flowchart LR
    classDef primaryActor fill:#fff8e1,stroke:#ffa000,stroke-width:2px,font-weight:bold;
    classDef secondaryActor fill:#f3e5f5,stroke:#7b1fa2,stroke-width:2px,font-weight:bold;
    classDef timerActor fill:#e0f7fa,stroke:#00acc1,stroke-width:2px,font-weight:bold;
    classDef ucNode fill:#e8f5e9,stroke:#2e7d32,stroke-width:2px,color:#1b5e20;

    %% Actors
    Guest(["Tamu (Guest)"]):::primaryActor
    Calon(["Calon Penyewa"]):::primaryActor
    Penyewa(["Penyewa Aktif"]):::primaryActor
    Admin(["Administrator"]):::primaryActor

    %% Systems & Timers
    Midtrans[["Payment Gateway (Midtrans)"]]:::secondaryActor
    Fonnte[["WhatsApp Gateway (Fonnte)"]]:::secondaryActor
    Cron[["System Scheduler (Timer/Cron)"]]:::timerActor

    subgraph CoreSystem ["Sistem Informasi Asri Boarding House"]
        direction TB
        
        subgraph AuthMod ["Modul Otentikasi & Keamanan"]
            UC12(["UC-12: Registrasi Akun"]):::ucNode
            UC18(["UC-18: Google OAuth SSO"]):::ucNode
            UC22(["UC-22: Login Manual"]):::ucNode
            UC24(["UC-24: Lupa & Reset Password"]):::ucNode
        end

        subgraph PublicMod ["Modul Publik & Reservasi"]
            UC10(["UC-10: Cek Katalog Kamar"]):::ucNode
            UC17(["UC-17: Guest Chat"]):::ucNode
            UC01(["UC-01: Reservasi Kamar"]):::ucNode
            UC03(["UC-03: Chat Reservasi"]):::ucNode
            UC02(["UC-02: Pembayaran Reservasi"]):::ucNode
        end

        subgraph TenantMod ["Modul Penyewa Aktif"]
            UC04(["UC-04: Bayar Tagihan Bulanan"]):::ucNode
            UC25(["UC-25: Unduh Kuitansi PDF"]):::ucNode
            UC05(["UC-05: Pengaduan Keluhan"]):::ucNode
            UC11(["UC-11: Peraturan Kost"]):::ucNode
            UC21(["UC-21: Pengumuman Portal"]):::ucNode
            UC16(["UC-16: Riwayat Transaksi"]):::ucNode
        end

        subgraph AutoMod ["Modul Otomasi Latar Belakang"]
            UC28(["UC-28: Otomasi Billing, Denda & Eskalasi"]):::ucNode
        end

        subgraph AdminMod ["Modul Admin Operations"]
            UC06(["UC-06: Konfirmasi Reservasi & Kas"]):::ucNode
            UC13(["UC-13: Kelola Kamar & Fasilitas"]):::ucNode
            UC14(["UC-14: Kelola Penyewa"]):::ucNode
            UC26(["UC-26: Checkout & Perpanjang"]):::ucNode
            UC15(["UC-15: Resolusi Keluhan"]):::ucNode
            UC08(["UC-08: Kelola Pengeluaran"]):::ucNode
            UC07(["UC-07: Laporan Arus Kas"]):::ucNode
            UC27(["UC-27: Ekspor Data & Laporan"]):::ucNode
            UC19(["UC-19: Kalender Kontrol"]):::ucNode
            UC20(["UC-20: Broadcast Notifikasi"]):::ucNode
            UC09(["UC-09: Kelola Konten Dinamis"]):::ucNode
            UC23(["UC-23: Pengaturan Sistem"]):::ucNode
            UC29(["UC-29: Log Audit & Error System"]):::ucNode
        end
    end

    %% Connect Primary Actors
    Guest --> UC10
    Guest --> UC12
    Guest --> UC17
    Guest --> UC18
    Guest --> UC22

    Calon --> UC22
    Calon --> UC18
    Calon --> UC01
    Calon --> UC03
    Calon --> UC02
    Calon --> UC16

    Penyewa --> UC22
    Penyewa --> UC18
    Penyewa --> UC04
    Penyewa --> UC05
    Penyewa --> UC11
    Penyewa --> UC21
    Penyewa --> UC16

    Admin --> UC22
    Admin --> UC03
    Admin --> UC06
    Admin --> UC13
    Admin --> UC14
    Admin --> UC26
    Admin --> UC15
    Admin --> UC08
    Admin --> UC07
    Admin --> UC27
    Admin --> UC19
    Admin --> UC20
    Admin --> UC09
    Admin --> UC23
    Admin --> UC17
    Admin --> UC29

    %% Connect Timer Actor
    Cron --> UC28

    %% Core Extension Dependencies
    UC24 -.-> |extend| UC22
    UC03 -.-> |extend| UC01
    UC02 -.-> |extend| UC01
    UC25 -.-> |extend| UC04

    %% Connect Secondary Actors
    UC02 -.-> |include| Midtrans
    UC04 -.-> |include| Midtrans
    UC06 -.-> |include| Fonnte
    UC15 -.-> |include| Fonnte
    UC20 -.-> |include| Fonnte
    UC28 -.-> |include| Fonnte
```

![Master Unified Use Case Diagram](use_case/use_case_master_unified.png)

---

### 2.5. Visualisasi Keterkaitan Antar Use Case (Graph Relasi Include & Extend)

```mermaid
flowchart TD
    classDef includeStyle fill:#e1f5fe,stroke:#0288d1,stroke-width:2px,font-weight:bold;
    classDef extendStyle fill:#fff3e0,stroke:#f57c00,stroke-width:2px,font-weight:bold;
    classDef coreStyle fill:#e8f5e9,stroke:#388e3c,stroke-width:2px,font-weight:bold,color:#1b5e20;
    classDef externalStyle fill:#f3e5f5,stroke:#7b1fa2,stroke-width:2px,font-weight:bold;

    %% Base Use Cases
    UC01["UC-01: Melakukan Reservasi Kamar"]:::coreStyle
    UC04["UC-04: Membayar Tagihan Bulanan"]:::coreStyle
    UC06["UC-06: Konfirmasi Reservasi & Kas"]:::coreStyle
    UC14["UC-14: Manajemen Penyewa"]:::coreStyle
    UC07["UC-07: Laporan Arus Kas"]:::coreStyle
    UC08["UC-08: Manajemen Pengeluaran"]:::coreStyle
    UC22["UC-22: Login Akun Manual"]:::coreStyle
    UC28["UC-28: Otomasi Billing & Denda Eskalasi"]:::coreStyle
    UC15["UC-15: Resolusi Keluhan"]:::coreStyle
    UC20["UC-20: Broadcast Notifikasi"]:::coreStyle

    %% Extension Use Cases
    UC03["UC-03: Chat Reservasi Real-time"]:::extendStyle
    UC02["UC-02: Pembayaran Reservasi Online"]:::extendStyle
    UC25["UC-25: Mengunduh Kuitansi PDF"]:::extendStyle
    UC26["UC-26: Checkout & Perpanjang Kontrak"]:::extendStyle
    UC27["UC-27: Ekspor Data & Laporan (PDF/Excel/CSV)"]:::extendStyle
    UC24["UC-24: Lupa & Reset Password"]:::extendStyle

    %% External System Services
    Midtrans[["Layanan Payment Gateway (Midtrans)"]]:::externalStyle
    Fonnte[["Layanan WhatsApp Gateway (Fonnte)"]]:::externalStyle

    %% Include Relations
    UC02 -.-> |include| Midtrans
    UC04 -.-> |include| Midtrans
    UC06 -.-> |include| Fonnte
    UC15 -.-> |include| Fonnte
    UC20 -.-> |include| Fonnte
    UC28 -.-> |include| Fonnte

    %% Extend Relations
    UC03 -.-> |extend| UC01
    UC02 -.-> |extend| UC01
    UC25 -.-> |extend| UC04
    UC26 -.-> |extend| UC14
    UC27 -.-> |extend| UC07
    UC27 -.-> |extend| UC14
    UC27 -.-> |extend| UC08
    UC24 -.-> |extend| UC22
```

![Graph Relasi Include dan Extend](use_case/use_case_relations_graph.png)

---

### 2.6. Peta Alur Keterkaitan Antar Use Case (Master Inter-Use Case Process Flow Map)

Diagram alir berikut menyatukan seluruh siklus hidup sistem ke dalam satu peta proses logis, menggambarkan titik percabangan keputusan (*decision gates*), kondisi batas (*guards*), keterkaitan relasi `<<include>>` dan `<<extend>>`, serta transisi status entitas utama:

```mermaid
flowchart TB
    classDef actorNode fill:#fff8e1,stroke:#ffa000,stroke-width:2px,font-weight:bold;
    classDef externalNode fill:#f3e5f5,stroke:#7b1fa2,stroke-width:2px,font-weight:bold;
    classDef timerNode fill:#e0f7fa,stroke:#00acc1,stroke-width:2px,font-weight:bold;
    classDef baseUC fill:#e8f5e9,stroke:#2e7d32,stroke-width:2px,color:#1b5e20,font-weight:bold;
    classDef extendUC fill:#fff3e0,stroke:#f57c00,stroke-width:2px,font-weight:bold;

    %% ACTORS
    Guest(["Tamu (Guest)"]):::actorNode
    Calon(["Calon Penyewa"]):::actorNode
    Penyewa(["Penyewa Aktif"]):::actorNode
    Admin(["Administrator"]):::actorNode
    Cron[["System Scheduler (Timer/Cron)"]]:::timerNode

    %% EXTERNAL SERVICES
    Midtrans[["Payment Gateway (Midtrans Snap)"]]:::externalNode
    Fonnte[["WhatsApp Gateway (Fonnte)"]]:::externalNode

    %% FLOW PHASE 1: AUTHENTICATION & DISCOVERY
    subgraph Phase1 ["Fase 1: Penemuan & Otentikasi"]
        UC10(["UC-10: Cek Katalog & Ketersediaan Kamar"]):::baseUC
        UC12(["UC-12: Registrasi Akun Calon"]):::baseUC
        UC18(["UC-18: Login / SSO Google OAuth"]):::baseUC
        UC22(["UC-22: Login Akun Manual"]):::baseUC
        UC24(["UC-24: Lupa & Reset Password"]):::extendUC
        UC17(["UC-17: Diskusi via Guest Chat"]):::baseUC
    end

    %% FLOW PHASE 2: RESERVATION & PRE-PAYMENT
    subgraph Phase2 ["Fase 2: Reservasi & Transisi Penyewa"]
        UC01(["UC-01: Melakukan Reservasi Kamar"]):::baseUC
        UC03(["UC-03: Chat Reservasi Real-time"]):::extendUC
        UC02(["UC-02: Melakukan Pembayaran Reservasi"]):::extendUC
        UC06(["UC-06: Verifikasi Reservasi & Konfirmasi Kas"]):::baseUC
    end

    %% FLOW PHASE 3: ACTIVE TENANCY & AUTOMATED BILLING
    subgraph Phase3 ["Fase 3: Masa Sewa Aktif & Penagihan"]
        UC28(["UC-28: Otomasi Billing & Denda Eskalasi"]):::baseUC
        UC04(["UC-04: Membayar Tagihan Bulanan"]):::baseUC
        UC25(["UC-25: Mengunduh Kuitansi Bukti PDF"]):::extendUC
        UC05(["UC-05: Mengajukan Keluhan Fasilitas"]):::baseUC
        UC15(["UC-15: Memproses & Menanggapi Keluhan"]):::baseUC
        UC11(["UC-11: Mengakses Menu Peraturan"]):::baseUC
        UC21(["UC-21: Melihat Pengumuman & Notifikasi"]):::baseUC
    end

    %% FLOW PHASE 4: ADMIN CONTROLS & REPORTING
    subgraph Phase4 ["Fase 4: Operasional & Pengakhiran Sewa"]
        UC14(["UC-14: Manajemen Data Master Penyewa"]):::baseUC
        UC26(["UC-26: Process Checkout & Perpanjangan"]):::extendUC
        UC07(["UC-07: Laporan Arus Kas"]):::baseUC
        UC08(["UC-08: Pencatatan Pengeluaran (CRUD)"]):::baseUC
        UC27(["UC-27: Ekspor Data & Laporan (PDF/Excel)"]):::extendUC
        UC19(["UC-19: Memantau Kalender Kontrol Visual"]):::baseUC
        UC20(["UC-20: Broadcast Notifikasi Massal"]):::baseUC
    end

    %% CONNECTIONS & INTERACTIONS
    Guest --> UC10
    Guest --> UC17
    Guest --> UC12
    Guest --> UC18
    Guest --> UC22

    UC24 -.-> |extend| UC22
    UC12 --> Calon
    UC18 --> Calon

    Calon --> UC01
    UC03 -.-> |extend| UC01
    UC02 -.-> |extend| UC01
    UC02 -.-> |include| Midtrans
    Calon --> UC03
    Calon --> UC02

    Admin --> UC03
    Admin --> UC06
    Admin --> UC17
    UC06 -.-> |include| Fonnte
    UC06 --> |Transisi Sukses -> Akun Terbit| Penyewa

    Cron --> UC28
    UC28 -.-> |include| Fonnte
    UC28 --> |Terbit Tagihan Rutin| UC04

    Penyewa --> UC04
    UC04 -.-> |include| Midtrans
    Admin --> |Konfirmasi Kas Manual| UC06
    UC06 -.-> |Update Status Lunas| UC04

    UC25 -.-> |extend| UC04
    Penyewa --> UC25

    Penyewa --> UC05
    UC05 -.-> |include| Fonnte
    Fonnte --> |Notifikasi WA Masuk| Admin
    Admin --> UC15
    UC15 -.-> |include| Fonnte
    Fonnte --> |Notifikasi Status Selesai| Penyewa

    Admin --> UC14
    UC26 -.-> |extend| UC14
    Admin --> UC26
    Admin --> UC07
    Admin --> UC08
    Admin --> UC19
    Admin --> UC20
    UC20 -.-> |include| Fonnte

    UC27 -.-> |extend| UC07
    UC27 -.-> |extend| UC14
    UC27 -.-> |extend| UC08
    Admin --> UC27
```

![Peta Alur Keterkaitan Inter-Use Case](use_case/use_case_flow_comprehensive_map.png)

---

### 2.7. Visualisasi Alur Proses Interaksi End-to-End per Siklus Bisnis

Berikut adalah 5 (lima) diagram alur interaksi terinci (*End-to-End User Journeys*) yang menghubungkan **Aktor**, **Sistem Web Laravel**, **Database**, dan **Layanan Pihak Ketiga (Midtrans & Fonnte)**.

#### 2.7.1. Alur Siklus 1: Registrasi, Reservasi Kamar, Chat Pre-Payment, Pembayaran Snap, dan Transisi Akun Otomatis

```mermaid
flowchart TD
    classDef actorNode fill:#fff8e1,stroke:#ffa000,stroke-width:2px,font-weight:bold;
    classDef sysNode fill:#e8f5e9,stroke:#2e7d32,stroke-width:2px,color:#1b5e20;
    classDef extNode fill:#f3e5f5,stroke:#7b1fa2,stroke-width:2px,font-weight:bold;
    classDef decisionNode fill:#fff3e0,stroke:#f57c00,stroke-width:2px;
    classDef terminalNode fill:#fce4ec,stroke:#c2185b,stroke-width:2px,font-weight:bold;

    %% ACTORS & SYSTEMS
    Guest(["Tamu / Calon Penyewa"]):::actorNode
    Sys["Sistem Web Laravel (ABH)"]:::sysNode
    Midtrans[["Midtrans Snap API"]]:::extNode
    Admin(["Administrator Kost"]):::actorNode
    Fonnte[["Fonnte WhatsApp API"]]:::extNode

    %% FLOW
    Start([Mulai: Buka Landing Page]):::terminalNode --> A1[1. Cari & Filter Kamar - UC-10]:::sysNode
    A1 --> A2{Kamar Tersedia?}:::decisionNode
    A2 -- Tidak (Kamar Terisi) --> A3[Klik Tombol 'Tanya WA' - WhatsApp Direct]:::sysNode
    A2 -- Ya (Tersedia) --> B1[2. Registrasi / Google OAuth - UC-12 / UC-18]:::sysNode
    
    B1 --> B2[3. Isi Form Booking: Tanggal, Tipe Sewa, DP/Lunas - UC-01]:::sysNode
    B2 --> B3[Sistem Simpan status 'pending' & Kunci Kamar]:::sysNode
    
    B3 --> C1{Calon Butuh Tanya Kamar?}:::decisionNode
    C1 -- Ya (Extend) --> C2[Buka Chat Reservasi Real-Time - UC-03]:::sysNode
    C2 <--> |AJAX Polling 3s| C3[Admin Balas Chat via Admin Panel - UC-03]:::sysNode
    C3 --> D1
    C1 -- Tidak --> D1[4. Klik Tombol 'Bayar Sekarang' - UC-02]:::sysNode

    D1 --> D2[Sistem Request Snap Token - include Midtrans]:::sysNode
    D2 --> D3[Render Popup Midtrans Snap di Browser]:::sysNode
    D3 --> D4{Calon Selesaikan Pembayaran?}:::decisionNode
    
    D4 -- Gagal / Batal / Timeout 24 Jam --> D5[Sistem Ubah status: 'dibatalkan' & Lepas Kamar]:::sysNode
    D5 --> EndCancel([Reservasi Batal]):::terminalNode

    D4 -- Sukses Bayar --> E1[Webhook Midtrans Kirim Notifikasi Sukses]:::extNode
    E1 --> E2[Sistem Verifikasi SHA-512 & Update status 'dp' / 'lunas']:::sysNode
    
    E2 --> F1[5. Admin Verifikasi NIK & Data Wali - UC-06]:::sysNode
    F1 --> F2{Disetujui Admin?}:::decisionNode
    F2 -- Tolak --> D5

    F2 -- Setujui --> G1[Admin Klik 'Konfirmasi Reservasi' - UC-06]:::sysNode
    G1 --> G2[DB::transaction: Status 'dikonfirmasi', Kamar 'terisi', Buat Akun Penyewa]:::sysNode
    G2 --> G3[Sistem Kirim WA Kredensial via Fonnte - include Fonnte]:::sysNode
    G3 --> G4[Calon Terima WA Kredensial & Resmi Jadi Penyewa Aktif]:::actorNode
    G4 --> EndSuccess([Transisi Sukses]):::terminalNode
```

![Alur Siklus Reservasi Kamar dan Transisi Akun](use_case/use_case_flow_reservasi_transisi.png)

---

#### 2.7.2. Alur Siklus 2: Penagihan Bulanan Otomatis, Denda Flat 5% Idempoten, Eskalasi WhatsApp Wali, Pembayaran, dan Unduh Kuitansi PDF

```mermaid
flowchart TD
    classDef timerNode fill:#e0f7fa,stroke:#00acc1,stroke-width:2px,font-weight:bold;
    classDef actorNode fill:#fff8e1,stroke:#ffa000,stroke-width:2px,font-weight:bold;
    classDef sysNode fill:#e8f5e9,stroke:#2e7d32,stroke-width:2px,color:#1b5e20;
    classDef decisionNode fill:#fff3e0,stroke:#f57c00,stroke-width:2px;
    classDef terminalNode fill:#fce4ec,stroke:#c2185b,stroke-width:2px,font-weight:bold;

    %% TIMERS & ACTORS
    Cron[["Laravel Task Scheduler (Cron Engine)"]]:::timerNode
    Tenant(["Penyewa Aktif"]):::actorNode
    Admin(["Administrator"]):::actorNode
    Midtrans[["Midtrans Snap API"]]:::extNode
    Fonnte[["Fonnte WhatsApp API"]]:::extNode

    %% PHASE 1: GENERATE INVOICE
    Start([Pemicu: Tgl 1 Jam 00:05 WIB]):::terminalNode --> A1[Command 'tagihan:generate-bulanan' - UC-28]:::sysNode
    A1 --> A2[Query Penyewa Aktif Tipe Bulanan & Terbitkan Tagihan Jatuh Tempo Tgl 10]:::sysNode
    A2 --> A3[Broadcast WhatsApp Rincian Tagihan via Fonnte - include Fonnte]:::sysNode
    A3 --> A4[Penyewa Terima WA Notifikasi Tagihan]:::actorNode

    %% PHASE 2: DAILY PENALTY & GUARDIAN ESCALATION
    A4 --> B1([Pemicu: Harian Jam 01:00 WIB]):::terminalNode
    B1 --> B2[Command 'tagihan:proses-keterlambatan' - UC-28]:::sysNode
    B2 --> B3{Lewat Jatuh Tempo Tgl 10?}:::decisionNode
    
    B3 -- Belum --> B4[Masa Tenggang Normal]:::sysNode
    B3 -- Ya --> B5{Kondisi Periode Bulan?}:::decisionNode

    B5 -- Bulan Berjalan --> B6[Kirim Pengingat Rutin WA Tanpa Denda]:::sysNode
    B6 --> B7{Bulan Keterlambatan == 2?}:::decisionNode
    B7 -- Ya --> B8[Kirim Notifikasi Eskalasi ke WA Wali - include Fonnte]:::sysNode
    B7 -- Tidak --> C1

    B5 -- Menyeberang Bulan Kalender --> B9[Kenakan Denda Flat 5% tepat 1x via Idempotency Guard]:::sysNode
    B9 --> B10[Update total di DB::transaction lockForUpdate & Kirim WA Denda]:::sysNode

    %% PHASE 3: PAYMENT
    B8 --> C1[Penyewa Bayar Tagihan - UC-04]:::sysNode
    B10 --> C1
    B4 --> C1
    
    C1 --> C2{Jalur Pembayaran?}:::decisionNode
    C2 -- Online (Snap) --> D1[Bayar via Midtrans -> Webhook Update status 'lunas']:::sysNode
    C2 -- Tunai / Kasir --> D2[Admin Klik 'Konfirmasi Cash' di Panel Admin - UC-06]:::sysNode

    %% PHASE 4: RECEIPT GENERATION
    D1 --> E1[Penyewa Klik 'Unduh Nota PDF' - UC-25]:::sysNode
    D2 --> E1
    E1 --> End([File Nota PDF Terunduh via Dompdf]):::terminalNode
```

![Alur Siklus Penagihan Bulanan dan Denda Flat](use_case/use_case_flow_billing_denda.png)

---

#### 2.7.3. Alur Siklus 3: Pengaduan Keluhan Fasilitas, Penanganan Operasional, dan Notifikasi WA

```mermaid
flowchart TD
    classDef actorNode fill:#fff8e1,stroke:#ffa000,stroke-width:2px,font-weight:bold;
    classDef sysNode fill:#e8f5e9,stroke:#2e7d32,stroke-width:2px,color:#1b5e20;
    classDef terminalNode fill:#fce4ec,stroke:#c2185b,stroke-width:2px,font-weight:bold;

    %% ACTORS & SYSTEMS
    Tenant(["Penyewa Aktif"]):::actorNode
    Sys["Portal Web Laravel (ABH)"]:::sysNode
    Fonnte[["Fonnte WhatsApp API"]]:::extNode
    Admin(["Administrator"]):::actorNode

    %% FLOW
    Start([Penyewa Temukan Fasilitas Rusak]):::terminalNode --> A1[1. Buka Menu 'Keluhan' & Input Foto - UC-05]:::sysNode
    A1 --> A2[2. Simpan status: 'pending' & Kirim WA Dispatch ke Admin - include Fonnte]:::sysNode
    A2 --> A3[3. Admin Terima WA & Buka Menu 'Daftar Keluhan' - UC-15]:::sysNode
    A3 --> A4[4. Admin Ubah Status Menjadi 'diproses' & Eksekusi Perbaikan Fisik]:::sysNode
    A4 --> A5[5. Admin Input Catatan Solusi & Klik 'Selesaikan' status: 'selesai']:::sysNode
    A5 --> A6[6. Sistem Kirim WA Pemberitahuan Selesai ke Penyewa - include Fonnte]:::sysNode
    A6 --> End([Keluhan Selesai]):::terminalNode
```

![Alur Siklus Pengaduan dan Resolusi Keluhan](use_case/use_case_flow_keluhan_resolusi.png)

---

#### 2.7.4. Alur Siklus 4: Pengakhiran Kontrak (Checkout & Refund Deposit) vs Perpanjangan Sewa

```mermaid
flowchart TD
    classDef actorNode fill:#fff8e1,stroke:#ffa000,stroke-width:2px,font-weight:bold;
    classDef sysNode fill:#e8f5e9,stroke:#2e7d32,stroke-width:2px,color:#1b5e20;
    classDef decisionNode fill:#fff3e0,stroke:#f57c00,stroke-width:2px;
    classDef terminalNode fill:#fce4ec,stroke:#c2185b,stroke-width:2px,font-weight:bold;

    %% ACTORS
    Admin(["Administrator Kost"]):::actorNode
    Sys["Admin Panel Laravel (ABH)"]:::sysNode

    %% FLOW
    Start([Kontrak Mendekati Selesai]):::terminalNode --> A1[1. Buka Detail Penyewa - UC-14 / UC-26]:::sysNode
    A1 --> A2{Tindakan Kontrak?}:::decisionNode

    %% CHECKOUT
    A2 -- Checkout Sewa --> B1[Klik 'Checkout Penyewa' & Inspeksi Fisik Kamar - UC-26]:::sysNode
    B1 --> B2{Ada Kerusakan / Potongan?}:::decisionNode
    B2 -- Tidak --> C1[Catat Pengembalian Deposit 100%]:::sysNode
    B2 -- Ya --> C2[Input Potongan & Catat Pengeluaran Audit - UC-08]:::sysNode
    C1 --> D1[Ubah Status Penyewa ke 'nonaktif']:::sysNode
    C2 --> D1
    D1 --> D2[Kamar Tetap 'terisi' / Inspeksi - Safety Rule]:::sysNode
    D2 --> D3[Admin Bersihkan Kamar & Set Manual Status 'tersedia' - UC-13]:::sysNode
    D3 --> EndA([Checkout Selesai]):::terminalNode

    %% PERPANJANG
    A2 -- Perpanjang Sewa --> E1[Klik 'Perpanjang Kontrak' & Pilih Tambahan Bulan - UC-26]:::sysNode
    E1 --> E2[Update 'tanggal_selesai' & Injeksi Tagihan Baru]:::sysNode
    E2 --> EndB([Perpanjangan Selesai]):::terminalNode
```

![Alur Siklus Checkout dan Perpanjangan Sewa](use_case/use_case_flow_checkout_perpanjang.png)

---

#### 2.7.5. Alur Siklus 5: Interaksi Guest Chat Publik Tanpa Login & Pelacakan Tombol WhatsApp

```mermaid
flowchart TD
    classDef actorNode fill:#fff8e1,stroke:#ffa000,stroke-width:2px,font-weight:bold;
    classDef sysNode fill:#e8f5e9,stroke:#2e7d32,stroke-width:2px,color:#1b5e20;
    classDef terminalNode fill:#fce4ec,stroke:#c2185b,stroke-width:2px,font-weight:bold;

    %% ACTORS
    Guest(["Tamu (Pengunjung Publik)"]):::actorNode
    Sys["Sistem Web Laravel (ABH)"]:::sysNode
    Admin(["Administrator Kost"]):::actorNode

    %% FLOW
    Start([Tamu Kunjungi Landing Page]):::terminalNode --> A1[1. Klik Widget Chat Melayang - UC-17]:::sysNode
    A1 --> A2[2. Input Nama & No WA -> Sistem Buat Token UUID di Cookie & Thread]:::sysNode
    A2 --> A3[3. Tamu Kirim Pesan Pertanyaan]:::actorNode
    A3 --> A4[4. Admin Balas via Menu 'Manajemen Chat Tamu' - UC-17]:::sysNode
    A4 <--> |AJAX Polling Real-Time| A3
    A4 --> A5[5. Selesai Diskusi -> Admin Tutup Sesi Thread]:::sysNode
    A5 --> End([Thread Ditutup]):::terminalNode
```

![Alur Siklus Guest Chat Publik Tanpa Login](use_case/use_case_flow_guest_chat.png)

---

## 3. Spesifikasi Detail 29 Use Case

Berikut adalah penjabaran langkah-langkah, kondisi batas, serta alur kerja dari 29 use case utama sistem.

### 3.1. Kelompok Use Case: Otentikasi, Publik & Calon Penyewa

#### UC-01: Melakukan Reservasi Kamar
* **Aktor Utama**: Calon Penyewa
* **Deskripsi**: Calon penyewa memesan unit kamar kost tertentu secara online dengan menentukan tanggal masuk, durasi sewa, dan skema pembayaran.
* **Kondisi Awal (Pre-condition)**: Calon penyewa sudah terdaftar dan masuk log masuk (login). Kamar yang dipilih berstatus `'tersedia'`.
* **Alur Utama (Main Flow)**:
  1. Calon penyewa membuka halaman katalog kamar (`/kamar`) atau detail kamar.
  2. Sistem menampilkan spesifikasi kamar, harga sewa, foto, dan status ketersediaan.
  3. Calon penyewa mengisi form pemesanan: Tanggal Mulai, Durasi Sewa, Tipe Sewa (harian, mingguan, bulanan), Catatan Khusus, dan memilih Opsi Pembayaran (DP 30% atau Lunas 100%).
  4. Calon penyewa menekan tombol **"Pesan Unit"**.
  5. Sistem menghitung estimasi total harga secara otomatis di client-side.
  6. Calon penyewa mengonfirmasi pemesanan.
  7. Sistem membuat entri baru di tabel `reservasi` dengan status `'pending'` dan menerbitkan nomor transaksi (`order_id`).
  8. Sistem membuka akses chat room diskusi untuk reservasi tersebut.
* **Alur Alternatif (Alternative Flow - Pembatalan Reservasi)**:
  * *Alur 1A (Self-Cancel)*: Calon penyewa dapat menekan tombol **"Batalkan Pemesanan"** pada halaman detail reservasi pending (`POST /penyewa/reservasi/{id}/batal`). Sistem mengubah status reservasi menjadi `'dibatalkan'` dan melepas penguncian kamar.
  * *Alur 1B (Auto-Expire Timeout)*: Jika calon penyewa tidak menyelesaikan pembayaran dalam batas waktu (24 jam), tugas terjadwal `reservasi:cancel-expired` secara otomatis mengubah status menjadi `'dibatalkan'`.
* **Kondisi Akhir (Post-condition)**: Reservasi baru tersimpan di database dengan status `'pending'`, dan kamar dikunci agar tidak dipesan pengguna lain dalam jangka waktu tertentu.

#### UC-02: Melakukan Pembayaran Reservasi (Midtrans Snap)
* **Aktor Utama**: Calon Penyewa
* **Aktor Sekunder**: Payment Gateway (Midtrans)
* **Deskripsi**: Calon penyewa membayar uang muka (DP 30%) atau pelunasan (100%) reservasi secara online melalui Payment Gateway Midtrans.
* **Kondisi Awal (Pre-condition)**: Reservasi tersimpan dengan status `'pending'` dan `snap_token` berhasil digenerasi.
* **Alur Utama (Main Flow)**:
  1. Calon penyewa membuka halaman detail reservasi.
  2. Sistem menampilkan detail pesanan, rincian biaya, stepper alur pembayaran, dan tombol **"Bayar Sekarang"**.
  3. Calon penyewa menekan tombol **"Bayar Sekarang"**.
  4. Sistem memicu pop-up portal Midtrans Snap.
  5. Calon penyewa memilih metode pembayaran (Virtual Account, E-wallet, dll.) dan menyelesaikan transfer.
  6. Webhook Midtrans mengirim callback notifikasi status transaksi ke sistem.
  7. Sistem memverifikasi kecocokan `signature_key` dan nominal bayar.
  8. Sistem memperbarui status reservasi menjadi `'dp'` atau `'lunas'` di tabel `reservasi`.
  9. Sistem mencatat log transaksi keuangan ke tabel `pembayaran`.
* **Kondisi Akhir (Post-condition)**: Status reservasi di database berubah menjadi `'dp'` atau `'lunas'`, dan tercatat transaksi pembayaran yang sah.

#### UC-03: Diskusi Real-time Chat Reservasi
* **Aktor Utama**: Calon Penyewa, Administrator
* **Deskripsi**: Media komunikasi interaktif real-time menggunakan AJAX Polling untuk membahas kesiapan kamar, aturan, atau negosiasi sebelum reservasi disetujui.
* **Kondisi Awal (Pre-condition)**: Reservasi aktif dan tersimpan di database.
* **Alur Utama (Main Flow)**:
  1. Calon penyewa membuka detail reservasi lalu masuk ke menu chat (`/penyewa/reservasi/{id}/chat`).
  2. Administrator membuka detail reservasi di admin panel (`/admin/reservasi/{id}`).
  3. Salah satu pihak mengetik pesan lalu menekan kirim.
  4. Sistem menyimpan pesan ke tabel `chat_messages` dengan pengenal `reservasi_id` dan `sender_id`.
  5. Secara berkala (AJAX polling interval 3 detik), antarmuka Calon Penyewa dan Administrator menyinkronkan dan merender pesan baru.
* **Kondisi Akhir (Post-condition)**: Riwayat percakapan tersimpan secara urut kronologis berdasarkan waktu (`created_at`).

#### UC-10: Pencarian & Pengecekan Ketersediaan Kamar
* **Aktor Utama**: Tamu (Guest), Calon Penyewa
* **Deskripsi**: Menguji ketersediaan unit kamar kost putri secara real-time pada katalog publik menggunakan pencarian teks, tingkat lantai, dan tipe kelas kamar.
* **Kondisi Awal (Pre-condition)**: Pengunjung membuka halaman katalog `/kamar` atau landing page.
* **Alur Utama (Main Flow)**:
  1. Pengunjung memasukkan kata kunci pencarian, memfilter berdasarkan Lantai, Tipe Kamar, atau Tanggal Masuk & Durasi Sewa.
  2. Sistem menyaring kamar kost yang aktif (kamar berstatus `'maintenance'` disembunyikan).
  3. Untuk kamar berstatus `'tersedia'`, sistem memuat tombol **"Pesan Unit"**.
  4. Untuk kamar berstatus `'terisi'`, sistem memuat tombol WhatsApp **"Tanya WA"** (klik dicatat di tabel `whatsapp_clicks`).
* **Kondisi Akhir (Post-condition)**: Pengunjung melihat ketersediaan kamar yang akurat dan dialihkan ke form reservasi.

#### UC-12: Registrasi Akun Calon Penyewa
* **Aktor Utama**: Tamu (Guest)
* **Deskripsi**: Tamu mendaftarkan akun di sistem agar dapat melakukan pemesanan kamar dan melakukan diskusi pra-pembayaran.
* **Kondisi Awal (Pre-condition)**: Tamu belum memiliki akun terdaftar atau belum login ke sistem.
* **Alur Utama (Main Flow)**:
  1. Tamu membuka halaman registrasi (`/reservasi/register`).
  2. Sistem menampilkan formulir registrasi (Nama Lengkap, Email, Password, Konfirmasi Password, No HP opsional).
  3. Tamu mengisi formulir.
  4. Sistem melakukan validasi isian.
  5. Jika nomor HP kosong, sistem menghasilkan HP bayangan sementara (`temp_[timestamp]_[rand]`).
  6. Sistem membuat entri di tabel `users` dengan peran `'penyewa'`.
* **Kondisi Akhir (Post-condition)**: Akun user baru tersimpan di database dan pengguna langsung terautentikasi.

#### UC-16: Mengakses Riwayat Pemesanan, Pembayaran, dan Chat
* **Aktor Utama**: Calon Penyewa, Penyewa Aktif
* **Deskripsi**: Penyewa/calon mengakses riwayat transaksi pemesanan, pembayaran tagihan, serta log obrolan chat reservasi mereka melalui portal internal secara terpisah.
* **Kondisi Awal (Pre-condition)**: Pengguna sudah login sebagai peran `'penyewa'`.
* **Alur Utama (Main Flow)**:
  1. Pengguna membuka sidebar navigasi portal penyewa.
  2. **Riwayat Pemesanan**: Mengakses `/penyewa/reservasi/pemesanan`.
  3. **Riwayat Pembayaran**: Mengakses `/penyewa/reservasi/pembayaran`.
  4. **Riwayat Log Chat**: Mengakses `/penyewa/reservasi/{reservasi}/chat`.
* **Kondisi Akhir (Post-condition)**: Pengguna melihat daftar riwayat transaksi dan log chat secara transparan.

#### UC-17: Diskusi via Guest Chat (Tamu & Admin)
* **Aktor Utama**: Tamu (Guest), Administrator
* **Deskripsi**: Tamu memulai obrolan dengan memasukkan Nama dan nomor WhatsApp pada widget obrolan di halaman landing (tanpa login). Obrolan dilacak menggunakan token sesi berbasis cookie (`guest_chat_token`). Administrator dapat membalas dan mengelola obrolan dari panel admin.
* **Kondisi Awal (Pre-condition)**: Tamu membuka website / landing page.
* **Alur Utama (Main Flow)**:
  1. Tamu mengklik widget obrolan melayang di pojok kanan bawah.
  2. Tamu mengisi formulir awal (Nama Lengkap dan Nomor WhatsApp).
  3. Sistem memvalidasi input, membuat token sesi UUID, menyimpannya di cookie, dan mendaftarkan thread di `guest_chat_threads`.
  4. Tamu mengetik pesan obrolan. Sistem menyimpan ke `guest_chat_messages`.
  5. Admin membuka dashboard **"Manajemen Chat Tamu"** (`/admin/guest-chats`) untuk membalas atau menutup sesi.
* **Kondisi Akhir (Post-condition)**: Riwayat obrolan tamu tersimpan di database dan terkelola secara interaktif.

#### UC-18: Login & Registrasi via Google OAuth (SSO) & Kelengkapan Profil
* **Aktor Utama**: Tamu (Guest), Calon Penyewa, Penyewa Aktif
* **Deskripsi**: Membantu pengguna mendaftar atau masuk ke sistem menggunakan akun Google secara instan, lengkap dengan pengisian nomor WhatsApp dan filter keamanan email admin.
* **Kondisi Awal (Pre-condition)**: Pengguna memiliki akun Google aktif dan belum login ke sistem.
* **Alur Utama (Main Flow)**:
  1. Pengguna mengklik tombol **"Masuk dengan Google"**.
  2. Sistem mengalihkan ke halaman autentikasi Google OAuth.
  3. Google mengembalikan callback data profil pengguna (Nama, Email) ke server Laravel via Socialite.
  4. Sistem memproses registrasi/login user baru.
  5. **Middleware EnsureProfileIsComplete**: Jika nomor HP kosong, sistem mengalihkan user ke `/profil/complete`.
  6. Pengguna menginput nomor WhatsApp berformat Indonesia yang valid dan sistem menyimpan profil lengkap.
* **Kondisi Akhir (Post-condition)**: Pengguna berhasil login/registrasi, dan seluruh profil wajib tersimpan di database.

#### UC-22: Login Akun Manual (Portal Admin, Calon Penyewa, & Penyewa Aktif)
* **Aktor Utama**: Calon Penyewa, Penyewa Aktif, Administrator
* **Deskripsi**: Aktor masuk ke portal/aplikasi menggunakan kombinasi Email/Username dan Kata Sandi (Password) secara terpisah untuk menjamin isolasi portal keamanan.
* **Kondisi Awal (Pre-condition)**: Akun aktor telah terdaftar dan aktif di sistem.
* **Alur Utama (Main Flow)**:
  1. Aktor membuka halaman portal login rute masing-masing (`/login` atau `/admin/login`).
  2. Aktor memasukkan Email dan Password.
  3. Sistem memverifikasi kredensial dan memeriksa peran (`role`) aktor.
  4. Sesi login dibuat dan sistem mengalihkan pengguna ke dashboard yang sesuai.
* **Kondisi Akhir (Post-condition)**: Sesi aktor aktif dan pengguna mendapatkan akses ke fungsionalitas internal portal.

#### UC-24: Lupa & Reset Kata Sandi (Forgot & Reset Password)
* **Aktor Utama**: Tamu (Guest), Calon Penyewa, Penyewa Aktif, Administrator
* **Deskripsi**: Pengguna yang lupa kata sandi mengajukan permohonan reset password melalui tautan verifikasi email berbatas waktu (memperluas Use Case Login UC-22).
* **Kondisi Awal (Pre-condition)**: Pengguna memiliki email terdaftar di database dan mengakses form login.
* **Alur Utama (Main Flow)**:
  1. Pengguna mengklik tautan **"Lupa Password?"** di halaman login.
  2. Pengguna memasukkan alamat email yang terdaftar.
  3. Sistem memverifikasi email dan membuat token reset password di `password_reset_tokens`.
  4. Sistem mengirimkan email berisi link reset password.
  5. Pengguna mengklik tautan email dan memasukkan password baru.
  6. Sistem memperbarui password terenkripsi di tabel `users`.
* **Kondisi Akhir (Post-condition)**: Password berhasil diperbarui dan pengguna dapat login dengan password baru.

---

### 3.2. Kelompok Use Case: Penyewa Aktif

#### UC-04: Membayar Tagihan Bulanan Rutin
* **Aktor Utama**: Penyewa Aktif
* **Aktor Sekunder**: Payment Gateway (Midtrans)
* **Deskripsi**: Penyewa melakukan pelunasan tagihan bulanan yang diterbitkan sistem via Midtrans online atau transfer manual ke rekening admin.
* **Kondisi Awal (Pre-condition)**: Akun penyewa aktif dan memiliki tagihan berstatus `'pending'`.
* **Alur Utama (Main Flow)**:
  1. Penyewa login ke portal penyewa dan masuk ke halaman **"Tagihan Saya"**.
  2. Sistem menampilkan daftar tagihan bulanan beserta rincian pokok dan denda.
  3. **Opsi Online**: Penyewa memilih bayar via Midtrans Snap. Webhook memperbarui status tagihan menjadi `'lunas'`.
  4. **Opsi Offline**: Penyewa transfer manual/cash dan admin mengonfirmasi secara manual di panel admin (`UC-06`).
  5. Sistem mencatat log transaksi keuangan ke tabel `pembayaran`.
* **Kondisi Akhir (Post-condition)**: Status tagihan berubah menjadi `'lunas'` dan tercatat transaksi pembayaran yang sah.

#### UC-05: Mengajukan Keluhan & Laporan Kerusakan
* **Aktor Utama**: Penyewa Aktif
* **Aktor Sekunder**: WhatsApp Gateway (Fonnte)
* **Deskripsi**: Penyewa melaporkan kerusakan fasilitas kamar atau fasilitas bersama melalui portal untuk ditanggapi oleh admin.
* **Kondisi Awal (Pre-condition)**: Penyewa memiliki kontrak aktif di sistem.
* **Alur Utama (Main Flow)**:
  1. Penyewa membuka halaman **"Keluhan & Pengaduan"** di portal penyewa.
  2. Penyewa mengisi form: Judul, Kategori, Deskripsi Detail, dan Foto Bukti.
  3. Penyewa mengirim laporan.
  4. Sistem menyimpan pengaduan ke tabel `keluhan` dengan status `'pending'`.
  5. Sistem memicu Fonnte WA API untuk mengirimkan notifikasi otomatis ke nomor WhatsApp Admin Kost.
* **Kondisi Akhir (Post-condition)**: Keluhan tersimpan di database dan admin menerima pemberitahuan WhatsApp.

#### UC-11: Mengakses Menu Peraturan & Tata Tertib
* **Aktor Utama**: Penyewa Aktif
* **Deskripsi**: Penyewa kost putri mengakses tata tertib hunian kost yang terdata dinamis di database.
* **Kondisi Awal (Pre-condition)**: Penyewa aktif login ke portal penyewa.
* **Alur Utama (Main Flow)**:
  1. Penyewa membuka sidebar menu portal penyewa dan memilih **"Peraturan Kost"**.
  2. Sistem mengambil daftar peraturan dari tabel `peraturan` diurutkan berdasarkan kolom `urutan`.
  3. Sistem merender halaman dengan list tata tertib.
* **Kondisi Akhir (Post-condition)**: Penyewa melihat tata tertib kost terbaru secara transparan.

#### UC-21: Melihat Pengumuman & Notifikasi Pengelola
* **Aktor Utama**: Penyewa Aktif
* **Deskripsi**: Penyewa melihat riwayat log pesan notifikasi otomatis atau pengumuman manual yang diterbitkan oleh administrator melalui halaman notifikasi internal.
* **Kondisi Awal (Pre-condition)**: Penyewa aktif telah login ke portal penyewa.
* **Alur Utama (Main Flow)**:
  1. Penyewa membuka menu **"Notifikasi"** di sidebar portal penyewa (`/penyewa/notifikasi`).
  2. Sistem mengambil data log dari tabel `log_notifikasi` yang berasosiasi dengan ID penyewa.
  3. Sistem menyajikan log notifikasi secara kronologis terbalik dengan pagination.
* **Kondisi Akhir (Post-condition)**: Penyewa dapat meninjau seluruh riwayat pemberitahuan.

#### UC-25: Mengunduh Kuitansi Bukti Pembayaran PDF
* **Aktor Utama**: Penyewa Aktif
* **Deskripsi**: Penyewa mengunduh kuitansi resmi bukti pembayaran tagihan/reservasi yang telah lunas dalam format PDF (Dompdf).
* **Kondisi Awal (Pre-condition)**: Tagihan atau reservasi terkait berstatus `'lunas'`.
* **Alur Utama (Main Flow)**:
  1. Penyewa membuka detail tagihan atau riwayat pembayaran yang sudah lunas.
  2. Penyewa mengklik tombol **"Unduh Nota PDF"**.
  3. Sistem me-render template kuitansi Dompdf secara dinamis.
  4. Berkas PDF diunduh secara otomatis ke perangkat pengguna.
* **Kondisi Akhir (Post-condition)**: Berkas kuitansi PDF tersimpan di perangkat pengguna.

---

### 3.3. Kelompok Use Case: Administrator & Otomasi Sistem

#### UC-06: Verifikasi Reservasi Baru & Konfirmasi Pembayaran Tagihan Tunai (Cash)
* **Aktor Utama**: Administrator
* **Aktor Sekunder**: WhatsApp Gateway (Fonnte)
* **Deskripsi**: Admin meninjau data pengajuan reservasi, mencocokkan kelengkapan identitas KTP (NIK 16 digit), dan mengonfirmasi reservasi menjadi akun penyewa resmi, serta memverifikasi pembayaran tunai tagihan bulanan.
* **Kondisi Awal (Pre-condition)**: Terdapat data reservasi berstatus `'dp'`/`'lunas'` atau tagihan tunai yang menunggu konfirmasi.
* **Alur Utama (Main Flow)**:
  1. **Sub-Alur 1 (Konfirmasi Reservasi)**:
     - Admin membuka menu **"Daftar Reservasi"** (`/admin/reservasi`).
     - Admin memverifikasi NIK dan Data Wali, lalu menekan **"Konfirmasi Reservasi"**.
     - Sistem membungkus transaksi dalam `DB::transaction()`: status reservasi `'dikonfirmasi'`, kamar `'terisi'`, membuat profil penyewa, dan memicu pengiriman kredensial login via Fonnte WhatsApp API.
  2. **Sub-Alur 2 (Konfirmasi Pembayaran Kas Tunai)**:
     - Admin membuka menu **"Manajemen Tagihan"** (`/admin/tagihan`).
     - Admin memeriksa bukti transfer/uang tunai fisik dari penyewa, lalu menekan tombol **"Konfirmasi Cash"** (`POST /admin/tagihan/{id}/konfirmasi-cash`).
     - Sistem mengubah status tagihan menjadi `'lunas'` dan mencatat transaksi ke tabel `pembayaran`.
* **Kondisi Akhir (Post-condition)**: Akun penyewa aktif terbuat atau tagihan tunai tervalidasi lunas secara atomik.

#### UC-07: Pencatatan & Evaluasi Laporan Arus Kas
* **Aktor Utama**: Administrator
* **Deskripsi**: Admin memantau arus keuangan masuk dan keluar, mencatat pengeluaran operasional, serta mengunduh laporan bulanan.
* **Kondisi Awal (Pre-condition)**: Data transaksi sudah terisi di database.
* **Alur Utama (Main Flow)**:
  1. Admin membuka menu **"Laporan Keuangan"** di dashboard admin panel.
  2. Sistem merinci arus kas masuk dan kas keluar.
  3. Admin menyaring laporan berdasarkan rentang Bulan & Tahun.
* **Kondisi Akhir (Post-condition)**: Laporan arus kas tervisualisasikan secara real-time.

#### UC-08: Pencatatan Pengeluaran Operasional (CRUD)
* **Aktor Utama**: Administrator
* **Deskripsi**: Admin mencatat pengeluaran operasional bulanan kost (arus kas keluar) beserta unggah foto bukti nota fisik.
* **Kondisi Awal (Pre-condition)**: Admin masuk ke panel admin.
* **Alur Utama (Main Flow)**:
  1. Admin masuk ke menu **"Manajemen Pengeluaran"** di panel admin.
  2. Admin menekan tombol **"Tambah Pengeluaran"**.
  3. Admin mengisi form (Nama, Kategori, Tanggal, Nominal, Foto Bukti Nota).
  4. Sistem menyimpan catatan transaksi ke tabel `pengeluaran`.
* **Kondisi Akhir (Post-condition)**: Riwayat pengeluaran tercatat di database dan kas keluar ter-update.

#### UC-09: Manajemen Konten Dinamis (Peraturan, FAQ, Galeri, Testimoni)
* **Aktor Utama**: Administrator
* **Deskripsi**: Admin mengelola konten pendukung halaman publik (peraturan tata tertib, FAQ interaktif, foto galeri kost, ulasan pelanggan).
* **Kondisi Awal (Pre-condition)**: Admin masuk ke panel admin.
* **Alur Utama (Main Flow)**:
  1. Admin membuka sub-menu manajemen konten (Galeri, FAQ, Peraturan, Ulasan/Review).
  2. Admin melakukan operasi Tambah, Edit, atau Hapus data konten.
  3. Sistem memperbarui database dan mengelola berkas media publik.
* **Kondisi Akhir (Post-condition)**: Perubahan data master ter-update secara dinamis.

#### UC-13: Manajemen Kamar & Fasilitas (CRUD & Soft Delete/Restore)
* **Aktor Utama**: Administrator
* **Deskripsi**: Admin menambah, memperbarui, memantau ketersediaan, menghapus (soft delete), serta memulihkan data kamar dan fasilitasnya.
* **Kondisi Awal (Pre-condition)**: Admin telah login ke panel admin.
* **Alur Utama (Main Flow)**:
  1. Admin membuka menu **"Manajemen Kamar"** (`/admin/kamar`) atau **"Manajemen Fasilitas"** (`/admin/fasilitas`).
  2. Admin mengelola data kamar (nomor, lantai, tipe, harga, status, foto) dan fasilitas terkait.
  3. **Safety Constraint Hapus**: Jika kamar terikat data penyewa/reservasi aktif, penghapusan diblokir demi integritas data. Jika aman, sistem mengeksekusi *Soft Delete*.
  4. **Restore**: Admin dapat memulihkan kamar yang terhapus via rute `/admin/kamar/{id}/restore`.
* **Kondisi Akhir (Post-condition)**: Data kamar dan fasilitas ter-update di database.

#### UC-14: Manajemen Penyewa (CRUD & Soft Delete/Restore)
* **Aktor Utama**: Administrator
* **Deskripsi**: Admin mengelola profil biodata penyewa, setup kontrak (tipe sewa harian/mingguan/bulanan, durasi), deposit jaminan, dan mengelola reaktivasi/restore kontrak.
* **Kondisi Awal (Pre-condition)**: Admin telah login ke panel admin.
* **Alur Utama (Main Flow)**:
  1. Admin membuka menu **"Manajemen Penyewa"** (`/admin/penyewa`).
  2. Admin mengedit data penyewa, deposit, NIK, dan data wali.
  3. **Safety Constraint**: Jika penyewa memiliki riwayat tagihan, akun tidak dapat dihapus permanen.
* **Kondisi Akhir (Post-condition)**: Data penyewa terkelola dengan aman dan integritas basis data terjaga.

#### UC-15: Memproses & Menanggapi Keluhan (CRUD)
* **Aktor Utama**: Administrator
* **Aktor Sekunder**: WhatsApp Gateway (Fonnte)
* **Deskripsi**: Admin meninjau laporan keluhan kerusakan dari penyewa aktif, memproses perbaikan, dan memberikan tanggapan.
* **Kondisi Awal (Pre-condition)**: Ada laporan keluhan masuk dengan status `'pending'`.
* **Alur Utama (Main Flow)**:
  1. Admin membuka menu **"Daftar Keluhan"** (`/admin/keluhan`).
  2. Admin mengubah status menjadi `'diproses'` saat perbaikan berlangsung.
  3. Setelah selesai, admin menulis tanggapan dan mengubah status menjadi `'selesai'`.
  4. Sistem memicu Fonnte WA API untuk mengirim notifikasi ke WhatsApp penyewa.
* **Kondisi Akhir (Post-condition)**: Status keluhan berubah menjadi `'selesai'` dan penyewa menerima konfirmasi via WhatsApp.

#### UC-19: Memantau Kalender Kontrol Visual (Visual Control Center)
* **Aktor Utama**: Administrator
* **Deskripsi**: Admin memantau seluruh jadwal aktivitas reservasi, rencana kunjungan survei, jadwal check-in/out, serta tagihan denda keterlambatan penyewa dalam format kalender terintegrasi.
* **Kondisi Awal (Pre-condition)**: Admin telah login ke panel admin.
* **Alur Utama (Main Flow)**:
  1. Admin membuka rute `/admin/kalender`.
  2. Sistem memuat kalender berbasis Alpine.js yang mengambil event dari endpoint `/api/kalender/events`.
  3. System menyajikan visual event (Survei, Check-in, Check-out, Tagihan & Denda).
* **Kondisi Akhir (Post-condition)**: Seluruh jadwal operasional tervisualisasikan secara presisi.

#### UC-20: Broadcast Notifikasi & Pengumuman Kustom
* **Aktor Utama**: Administrator
* **Aktor Sekunder**: WhatsApp Gateway (Fonnte)
* **Deskripsi**: Admin membuat pesan pengumuman kustom dan mendistribusikannya secara langsung kepada penyewa via WhatsApp, Email, dan web portal.
* **Kondisi Awal (Pre-condition)**: Admin login ke panel admin.
* **Alur Utama (Main Flow)**:
  1. Admin masuk ke menu **"Manajemen Notifikasi"** (`/admin/notifikasi`).
  2. Admin memilih penerima, menginput judul & isi pesan, dan memilih media pengiriman.
  3. Sistem memproses pengiriman dan mencatat log di `log_notifikasi`.
* **Kondisi Akhir (Post-condition)**: Pengumuman terkirim ke penyewa dan log terdistribusi.

#### UC-23: Manajemen Pengaturan Sistem (Settings)
* **Aktor Utama**: Administrator
* **Deskripsi**: Admin mengelola konfigurasi umum sistem kost seperti rekening bank penerima transfer manual, kontak darurat WhatsApp, nominal jaminan deposit, dan iframe Google Maps.
* **Kondisi Awal (Pre-condition)**: Admin login ke panel admin.
* **Alur Utama (Main Flow)**:
  1. Admin membuka halaman **"Pengaturan Aplikasi"** (`/admin/settings`).
  2. Admin mengubah data konfigurasi dan menyimpannya.
  3. Sistem memperbarui tabel `settings` di database.
* **Kondisi Akhir (Post-condition)**: Pengaturan sistem ter-update secara global.

#### UC-26: Process Checkout & Perpanjangan Kontrak Penyewa
* **Aktor Utama**: Administrator
* **Deskripsi**: Admin memproses pengakhiran sewa (Checkout) termasuk pengembalian deposit dan pelepasan status kamar, atau memproses perpanjangan durasi sewa penyewa.
* **Kondisi Awal (Pre-condition)**: Penyewa berstatus `'aktif'`.
* **Alur Utama (Main Flow)**:
  1. Admin membuka halaman detail penyewa di panel admin.
  2. **Sub-Alur Checkout**: Admin menekan tombol **"Checkout Penyewa"**, mengonfirmasi tanggal keluar dan kondisi deposit. Sistem mengubah status penyewa menjadi `'nonaktif'` dan mengosongkan status kamar.
  3. **Sub-Alur Perpanjang**: Admin menekan tombol **"Perpanjang Kontrak"**, memilih tambahan durasi bulan. Sistem memperbarui tanggal selesai sewa dan menerbitkan tagihan perpanjangan.
* **Kondisi Akhir (Post-condition)**: Status penyewa dan kamar ter-update sesuai tindakan checkout/perpanjang.

#### UC-27: Mengekspor Laporan & Data (PDF / Excel / CSV)
* **Aktor Utama**: Administrator
* **Deskripsi**: Admin mengekspor dokumen fisik cetak atau berkas spreadsheet untuk data Laporan Arus Kas, Daftar Penyewa, dan Pengeluaran Operasional.
* **Kondisi Awal (Pre-condition)**: Data terkait tersedia pada modul laporan atau data master admin.
* **Alur Utama (Main Flow)**:
  1. Admin masuk ke halaman Laporan Keuangan, Manajemen Penyewa, atau Manajemen Pengeluaran.
  2. Admin memilih filter rentang tanggal/status data.
  3. Admin menekan tombol **"Ekspor PDF"**, **"Ekspor Excel"**, atau **"Ekspor CSV"**.
  4. Sistem memproses file stream (Dompdf / CSV Generator) dan mengunduh berkas ke perangkat admin.
* **Kondisi Akhir (Post-condition)**: Berkas ekspor terunduh secara aman di perangkat admin.

#### UC-28: Otomasi Billing Bulanan, Denda Keterlambatan Flat 5%, dan Eskalasi WhatsApp ke Wali
* **Aktor Otomasi**: System Scheduler (Timer/Cron)
* **Aktor Sekunder**: WhatsApp Gateway (Fonnte)
* **Deskripsi**: Proses latar belakang otomatis yang mengeksekusi dua siklus penting: penerbitan tagihan bulanan pada tanggal 1 awal bulan dan evaluasi denda keterlambatan flat 5% idempoten beserta eskalasi notifikasi ke wali secara harian.
* **Kondisi Awal (Pre-condition)**: Waktu server mencapai jadwal eksekusi Laravel Scheduler.
* **Alur Utama (Main Flow)**:
  1. **Siklus Bulanan (Setiap Tanggal 1 Jam 00:05 WIB)**:
     - Scheduler memicu command `tagihan:generate-bulanan`.
     - Sistem mengambil penyewa aktif bertipe bulanan (`whereNotIn('tipe_sewa', ['harian', 'mingguan'])`).
     - Sistem menerbitkan tagihan baru dengan `nominal_pokok = penyewa.harga_sewa` dan jatuh tempo tanggal 10.
     - Sistem memicu Fonnte WA API untuk mengirimkan notifikasi tagihan ke nomor WhatsApp penyewa.
  2. **Siklus Harian Denda & Eskalasi (Setiap Hari Jam 01:00 WIB)**:
     - Scheduler memicu command `tagihan:proses-keterlambatan`.
     - Sistem mengambil tagihan pending yang melewati jatuh tempo tanggal 10.
     - Jika keterlambatan masih dalam bulan berjalan, sistem mengirim pengingat reguler tanpa denda.
     - Jika keterlambatan memasuki bulan kedua, sistem mengirim notifikasi persuasif ke nomor Wali (`no_wali`).
     - Jika keterlambatan melewati batas bulan kalender, sistem mengenakan denda flat 5% tepat satu kali melalui *Idempotency Guard* di dalam `DB::transaction()` dengan `lockForUpdate()`.
* **Kondisi Akhir (Post-condition)**: Tagihan bulanan terbit atau denda keterlambatan terhitung secara idempoten dan pesan WhatsApp terkirim ke penyewa/wali.

#### UC-29: Memantau Log Audit & Activity System
* **Aktor Utama**: Administrator
* **Deskripsi**: Admin memantau log aktivitas sistem, jejak error teknis, dan catatan audit transaksi khusus untuk keperluan pemeliharaan aplikasi.
* **Kondisi Awal (Pre-condition)**: Admin login ke panel admin.
* **Alur Utama (Main Flow)**:
  1. Admin membuka rute `/admin/notifikasi-khusus`.
  2. Sistem menyajikan daftar log audit aktivitas sistem.
  3. Admin dapat meninjau rincian log atau menghapus log aktivitas lama.
* **Kondisi Akhir (Post-condition)**: Log audit tervisualisasikan dan dapat dikelola oleh admin.

---

## 4. Kepatuhan Aturan Bisnis (Business Rules Compliance)

Seluruh 29 use case wajib mematuhi aturan bisnis database berikut:

1. **Keamanan Transaksi Finansial (Atomic Transactions)**:
   Setiap pencatatan pelunasan tagihan manual cash wajib dibungkus dalam transaksi database (`DB::transaction`) untuk memastikan status tagihan menjadi `'lunas'` dan baris log pada tabel `pembayaran` dibuat secara atomik.
2. **Imutabilitas Tarif Sewa (`penyewa.harga_sewa`)**:
   Saat konfirmasi reservasi berhasil, tarif bulanan dasar disalin permanen dari `kamar.harga_bulan` ke `penyewa.harga_sewa`.
3. **Eskalasi Denda Keterlambatan Flat Kalender**:
   Tagihan bulanan diterbitkan tanggal 1 dengan jatuh tempo tanggal 10. Denda flat 5% diberlakukan secara otomatis tepat satu kali saat menyeberang bulan kalender berikutnya melalui *Idempotency Guard*. Notifikasi eskalasi dikirim ke wali saat keterlambatan memasuki bulan ke-2.
4. **Keamanan Hapus (Safety Deletion Constraints & Soft Deletes)**:
   * Unit **Kamar** dan **Penyewa** menerapkan *Soft Delete* (`deleted_at`).
   * Kamar tidak dapat dihapus jika terikat dengan data `penyewa` atau `reservasi` aktif.
   * Profil **Penyewa** tidak dapat dihapus jika memiliki riwayat transaksi/tagihan di database.
5. **Konsistensi Check-Out**:
   Saat status penyewa diubah menjadi `'nonaktif'`, kamar tetap dalam inspeksi fisik sebelum statusnya dikembalikan manual ke `'tersedia'`.

---

## 5. Pemetaan Complete Use Case ke Modul & Rute Laravel (Code Mapping Table)

Berikut adalah rujukan teknis 29 Use Case yang terhubung langsung dengan Controller, Route URL, dan View Blade di codebase Laravel:

| ID Use Case | Nama Use Case | Controller Pengendali | Rute / Endpoint URL | View Template / Blade |
| :--- | :--- | :--- | :--- | :--- |
| **UC-01** | Melakukan Reservasi | [ReservasiController](file:///c:/xampp/htdocs/asri-boarding-house/app/Http/Controllers/Penyewa/ReservasiController.php)@store | POST `/penyewa/reservasi`<br>POST `/penyewa/reservasi/{id}/batal` | [landing/show.blade.php](file:///c:/xampp/htdocs/asri-boarding-house/resources/views/landing/show.blade.php) |
| **UC-02** | Pembayaran Reservasi | [ReservasiController](file:///c:/xampp/htdocs/asri-boarding-house/app/Http/Controllers/Penyewa/ReservasiController.php)@pay <br> [MidtransReservasiCallbackController](file:///c:/xampp/htdocs/asri-boarding-house/app/Http/Controllers/Api/MidtransReservasiCallbackController.php) | GET `/penyewa/reservasi/{id}` <br> POST `/api/midtrans/callback-reservasi` | [penyewa/reservasi/show.blade.php](file:///c:/xampp/htdocs/asri-boarding-house/resources/views/penyewa/reservasi/show.blade.php) |
| **UC-03** | Chat Reservasi Real-time | [ChatController](file:///c:/xampp/htdocs/asri-boarding-house/app/Http/Controllers/Api/ChatController.php) | GET/POST `/chat-box/{reservasi}/*`<br>GET `/penyewa/reservasi/{id}/chat` | [components/chat-box.blade.php](file:///c:/xampp/htdocs/asri-boarding-house/resources/views/components/chat-box.blade.php) |
| **UC-04** | Membayar Tagihan Rutin | [TagihanController](file:///c:/xampp/htdocs/asri-boarding-house/app/Http/Controllers/Penyewa/TagihanController.php) <br> [MidtransCallbackController](file:///c:/xampp/htdocs/asri-boarding-house/app/Http/Controllers/Api/MidtransCallbackController.php) | GET `/penyewa/tagihan/{id}` <br> POST `/api/midtrans/callback` | [penyewa/tagihan/show.blade.php](file:///c:/xampp/htdocs/asri-boarding-house/resources/views/penyewa/tagihan/show.blade.php) |
| **UC-05** | Mengajukan Keluhan | [KeluhanController](file:///c:/xampp/htdocs/asri-boarding-house/app/Http/Controllers/Penyewa/KeluhanController.php) | POST `/penyewa/keluhan` | [penyewa/keluhan/index.blade.php](file:///c:/xampp/htdocs/asri-boarding-house/resources/views/penyewa/keluhan/index.blade.php) |
| **UC-06** | Konfirmasi Reservasi & Kas | [ReservasiController](file:///c:/xampp/htdocs/asri-boarding-house/app/Http/Controllers/Admin/ReservasiController.php)<br>[TagihanController](file:///c:/xampp/htdocs/asri-boarding-house/app/Http/Controllers/Admin/TagihanController.php) | POST `/admin/reservasi/{id}/konfirmasi`<br>POST `/admin/tagihan/{id}/konfirmasi-cash` | [admin/reservasi/show.blade.php](file:///c:/xampp/htdocs/asri-boarding-house/resources/views/admin/reservasi/show.blade.php)<br>[admin/tagihan/show.blade.php](file:///c:/xampp/htdocs/asri-boarding-house/resources/views/admin/tagihan/show.blade.php) |
| **UC-07** | Laporan Arus Kas | [LaporanController](file:///c:/xampp/htdocs/asri-boarding-house/app/Http/Controllers/Admin/LaporanController.php) | GET `/admin/laporan` | [admin/laporan/index.blade.php](file:///c:/xampp/htdocs/asri-boarding-house/resources/views/admin/laporan/index.blade.php) |
| **UC-08** | Manajemen Pengeluaran | [PengeluaranController](file:///c:/xampp/htdocs/asri-boarding-house/app/Http/Controllers/Admin/PengeluaranController.php) | GET/POST/DELETE `/admin/pengeluaran` | [admin/pengeluaran/index.blade.php](file:///c:/xampp/htdocs/asri-boarding-house/resources/views/admin/pengeluaran/index.blade.php) |
| **UC-09** | Manajemen Konten Dinamis | [PeraturanController](file:///c:/xampp/htdocs/asri-boarding-house/app/Http/Controllers/Admin/PeraturanController.php) <br> [FaqController](file:///c:/xampp/htdocs/asri-boarding-house/app/Http/Controllers/Admin/FaqController.php) <br> [GalleryController](file:///c:/xampp/htdocs/asri-boarding-house/app/Http/Controllers/Admin/GalleryController.php) <br> [CustomerReviewController](file:///c:/xampp/htdocs/asri-boarding-house/app/Http/Controllers/Admin/CustomerReviewController.php) | GET/POST/DELETE `/admin/peraturan` <br> `/admin/faq` <br> `/admin/gallery` <br> `/admin/reviews` | [admin/peraturan/index.blade.php](file:///c:/xampp/htdocs/asri-boarding-house/resources/views/admin/peraturan/index.blade.php) |
| **UC-10** | Pencarian & Cek Kamar | [LandingController](file:///c:/xampp/htdocs/asri-boarding-house/app/Http/Controllers/Public/LandingController.php) | GET `/kamar` <br> GET `/` | [landing/kamar-list.blade.php](file:///c:/xampp/htdocs/asri-boarding-house/resources/views/landing/kamar-list.blade.php) |
| **UC-11** | Mengakses Peraturan | [DashboardController](file:///c:/xampp/htdocs/asri-boarding-house/app/Http/Controllers/Penyewa/DashboardController.php) | GET `/penyewa/peraturan` | [penyewa/peraturan.blade.php](file:///c:/xampp/htdocs/asri-boarding-house/resources/views/penyewa/peraturan.blade.php) |
| **UC-12** | Registrasi Akun | [ReservasiAuthController](file:///c:/xampp/htdocs/asri-boarding-house/app/Http/Controllers/Auth/ReservasiAuthController.php) | GET/POST `/reservasi/register` | [auth/reservasi-register.blade.php](file:///c:/xampp/htdocs/asri-boarding-house/resources/views/auth/reservasi-register.blade.php) |
| **UC-13** | Kelola Kamar & Fasilitas | [KamarController](file:///c:/xampp/htdocs/asri-boarding-house/app/Http/Controllers/Admin/KamarController.php) <br> [FasilitasController](file:///c:/xampp/htdocs/asri-boarding-house/app/Http/Controllers/Admin/FasilitasController.php) | GET/POST/PUT/DELETE `/admin/kamar` <br> POST `/admin/kamar/{id}/restore` | [admin/kamar/index.blade.php](file:///c:/xampp/htdocs/asri-boarding-house/resources/views/admin/kamar/index.blade.php) |
| **UC-14** | Kelola Penyewa (CRUD) | [PenyewaController](file:///c:/xampp/htdocs/asri-boarding-house/app/Http/Controllers/Admin/PenyewaController.php) | GET/POST/PUT/DELETE `/admin/penyewa` <br> POST `/admin/penyewa/{id}/restore` | [admin/penyewa/index.blade.php](file:///c:/xampp/htdocs/asri-boarding-house/resources/views/admin/penyewa/index.blade.php) |
| **UC-15** | Memproses Keluhan | [KeluhanController](file:///c:/xampp/htdocs/asri-boarding-house/app/Http/Controllers/Admin/KeluhanController.php) | GET/PUT `/admin/keluhan` | [admin/keluhan/index.blade.php](file:///c:/xampp/htdocs/asri-boarding-house/resources/views/admin/keluhan/index.blade.php) |
| **UC-16** | Riwayat Transaksi & Chat | [ReservasiController](file:///c:/xampp/htdocs/asri-boarding-house/app/Http/Controllers/Penyewa/ReservasiController.php) | GET `/penyewa/reservasi/pemesanan` <br> GET `/penyewa/reservasi/pembayaran` | [penyewa/riwayat-pembayaran.blade.php](file:///c:/xampp/htdocs/asri-boarding-house/resources/views/penyewa/riwayat-pembayaran.blade.php) |
| **UC-17** | Diskusi via Guest Chat | [GuestChatApiController](file:///c:/xampp/htdocs/asri-boarding-house/app/Http/Controllers/Api/GuestChatApiController.php) <br> [GuestChatController](file:///c:/xampp/htdocs/asri-boarding-house/app/Http/Controllers/Admin/GuestChatController.php) | POST `/api/guest-chat/*` <br> GET `/admin/guest-chats` | [admin/guest-chats/index.blade.php](file:///c:/xampp/htdocs/asri-boarding-house/resources/views/admin/guest-chats/index.blade.php) |
| **UC-18** | Login Google OAuth | [SocialiteController](file:///c:/xampp/htdocs/asri-boarding-house/app/Http/Controllers/Auth/SocialiteController.php) | GET `/auth/google` <br> GET `/auth/google/callback` | [auth/complete-profile.blade.php](file:///c:/xampp/htdocs/asri-boarding-house/resources/views/auth/complete-profile.blade.php) |
| **UC-19** | Kalender Kontrol Visual | [AdminCalendarController](file:///c:/xampp/htdocs/asri-boarding-house/app/Http/Controllers/Admin/AdminCalendarController.php) | GET `/admin/kalender` <br> GET `/api/kalender/events` | [admin/calendar/index.blade.php](file:///c:/xampp/htdocs/asri-boarding-house/resources/views/admin/calendar/index.blade.php) |
| **UC-20** | Broadcast Notifikasi | [NotifikasiController](file:///c:/xampp/htdocs/asri-boarding-house/app/Http/Controllers/Admin/NotifikasiController.php) | POST `/admin/notifikasi/broadcast` | [admin/notifikasi/index.blade.php](file:///c:/xampp/htdocs/asri-boarding-house/resources/views/admin/notifikasi/index.blade.php) |
| **UC-21** | Melihat Pengumuman | [NotifikasiController](file:///c:/xampp/htdocs/asri-boarding-house/app/Http/Controllers/Penyewa/NotifikasiController.php) | GET `/penyewa/notifikasi` | [penyewa/notifikasi/index.blade.php](file:///c:/xampp/htdocs/asri-boarding-house/resources/views/penyewa/notifikasi/index.blade.php) |
| **UC-22** | Login Akun Manual | [AdminLoginController](file:///c:/xampp/htdocs/asri-boarding-house/app/Http/Controllers/Auth/AdminLoginController.php) <br> [PenyewaLoginController](file:///c:/xampp/htdocs/asri-boarding-house/app/Http/Controllers/Auth/PenyewaLoginController.php) | GET/POST `/admin/login` <br> GET/POST `/login` | [auth/admin-login.blade.php](file:///c:/xampp/htdocs/asri-boarding-house/resources/views/auth/admin-login.blade.php) |
| **UC-23** | Pengaturan Sistem | [SettingController](file:///c:/xampp/htdocs/asri-boarding-house/app/Http/Controllers/Admin/SettingController.php) | GET/PUT `/admin/settings` | [admin/settings/index.blade.php](file:///c:/xampp/htdocs/asri-boarding-house/resources/views/admin/settings/index.blade.php) |
| **UC-24** | Lupa & Reset Password | [PenyewaPasswordResetController](file:///c:/xampp/htdocs/asri-boarding-house/app/Http/Controllers/Auth/PenyewaPasswordResetController.php) <br> [AdminPasswordResetController](file:///c:/xampp/htdocs/asri-boarding-house/app/Http/Controllers/Auth/AdminPasswordResetController.php) | GET/POST `/forgot-password` <br> `/reset-password` | [auth/forgot-password.blade.php](file:///c:/xampp/htdocs/asri-boarding-house/resources/views/auth/forgot-password.blade.php) |
| **UC-25** | Unduh Kuitansi PDF | [TagihanController](file:///c:/xampp/htdocs/asri-boarding-house/app/Http/Controllers/Penyewa/TagihanController.php)@downloadNota | GET `/penyewa/nota/{pembayaran}/download` | PDF Generated via Dompdf |
| **UC-26** | Checkout & Perpanjang | [PenyewaController](file:///c:/xampp/htdocs/asri-boarding-house/app/Http/Controllers/Admin/PenyewaController.php) | POST `/admin/penyewa/{penyewa}/checkout` <br> POST `/admin/penyewa/{penyewa}/perpanjang` | [admin/penyewa/index.blade.php](file:///c:/xampp/htdocs/asri-boarding-house/resources/views/admin/penyewa/index.blade.php) |
| **UC-27** | Ekspor Data & Laporan | [LaporanController](file:///c:/xampp/htdocs/asri-boarding-house/app/Http/Controllers/Admin/LaporanController.php) <br> [PenyewaController](file:///c:/xampp/htdocs/asri-boarding-house/app/Http/Controllers/Admin/PenyewaController.php) <br> [PengeluaranController](file:///c:/xampp/htdocs/asri-boarding-house/app/Http/Controllers/Admin/PengeluaranController.php) | GET `/admin/laporan/export-pdf` <br> GET `/admin/penyewa/export-csv` <br> GET `/admin/pengeluaran/export-excel` | PDF & Excel Stream Downloads |
| **UC-28** | Otomasi Billing & Denda Eskalasi | Console (`tagihan:generate-bulanan`, `tagihan:proses-keterlambatan`) | Scheduled Task (Tgl 1 & Daily) | Background Scheduler Service |
| **UC-29** | Log Audit & Error System | [NotifikasiKhususController](file:///c:/xampp/htdocs/asri-boarding-house/app/Http/Controllers/Admin/NotifikasiKhususController.php) | GET `/admin/notifikasi-khusus` | [admin/notifikasi-khusus/index.blade.php](file:///c:/xampp/htdocs/asri-boarding-house/resources/views/admin/notifikasi-khusus/index.blade.php) |
