# State Machine Diagram - Asri Boarding House

Dokumen ini mendokumentasikan spesifikasi **State Machine Diagram (Diagram Mesin State)** untuk **Sistem Informasi Manajemen Kost Terintegrasi Payment Gateway pada Asri Boarding House**. State machine diagram ini memvisualisasikan seluruh siklus hidup (*lifecycle*) entitas utama dan pendukung sistem yang memiliki transisi status kompleks: **Reservasi**, **Tagihan**, **Kamar**, **Penyewa**, **Keluhan**, **Log Notifikasi**, dan **Guest Chat Thread**.

Siklus transisi status ini disinkronkan secara ketat dengan aturan bisnis database, event listener, cron job scheduler, dan sistem webhook pihak ketiga (Midtrans Payment Gateway & Fonnte WhatsApp API).

Semua spesifikasi dalam dokumen ini selaras dengan dokumen blueprint lainnya:
* **Project Blueprint**: [Blueprint_Projek_Web_Asri_Boarding_House.md](file:///c:/xampp/htdocs/asri-boarding-house/Blueprint/Blueprint_Projek_Web_Asri_Boarding_House.md)
* **Product Requirement Document**: [product_requirement_document_kost.md](file:///c:/xampp/htdocs/asri-boarding-house/Blueprint/product_requirement_document_kost.md)
* **Use Case Specification**: [Use_Case_Diagram_Kost.md](file:///c:/xampp/htdocs/asri-boarding-house/Blueprint/Use_Case_Diagram_Kost.md)
* **Sequence Diagram Specification**: [Sequence_Diagram_Kost.md](file:///c:/xampp/htdocs/asri-boarding-house/Blueprint/Sequence_Diagram_Kost.md)
* **Class Diagram**: [Class_Diagram_Kost.md](file:///c:/xampp/htdocs/asri-boarding-house/Blueprint/Class_Diagram_Kost.md)
* **Entity Relationship Diagram**: [Entity_Relationship_Diagram_Kost.md](file:///c:/xampp/htdocs/asri-boarding-house/Blueprint/Entity_Relationship_Diagram_Kost.md)
* **Flowchart**: [Flowchart_Kost.md](file:///c:/xampp/htdocs/asri-boarding-house/Blueprint/Flowchart_Kost.md)
* **Component Diagram**: [Component_Diagram_Kost.md](file:///c:/xampp/htdocs/asri-boarding-house/Blueprint/Component_Diagram_Kost.md)

---

## 0. Diagram Terpadu Siklus Hidup Sistem (End-to-End System Lifecycle)

Diagram berikut memvisualisasikan bagaimana seluruh siklus hidup entitas utama sistem saling terhubung, berinteraksi, dan memicu perubahan status satu sama lain (mulai dari calon penyewa melakukan reservasi, alokasi unit kamar, penerbitan tagihan sewa, hingga penyewa menyelesaikan masa sewa).

![Visual State Machine Terpadu](state/state_end_to_end_lifecycle.png)

```mermaid
stateDiagram-v2
    state "Siklus Hidup Reservasi (Calon Penyewa)" as ReservasiLifecycle {
        [*] --> Res_Pending : Checkout Booking Kamar
        Res_Pending --> Res_DP : Settlement / Cash [DP 30%]
        Res_Pending --> Res_Lunas : Settlement / Cash [Lunas 100%]
        Res_Pending --> Res_Batal : Timeout 24 Jam / Double-Booking
        
        Res_DP --> Res_Dikonfirmasi : Admin Konfirmasi [NIK & Wali OK]
        Res_Lunas --> Res_Dikonfirmasi : Admin Konfirmasi [NIK & Wali OK]
        Res_DP --> Res_Batal : Batalkan Sebelum Konfirmasi
        Res_Lunas --> Res_Batal : Batalkan Sebelum Konfirmasi
        
        Res_Dikonfirmasi --> Res_Selesai : Auto-Onboarding [User & Tenant]
        Res_Batal --> [*] : Kunci Kamar Dilepas
        Res_Selesai --> [*]
    }

    state "Siklus Hidup Penyewa Aktif (Tenant)" as PenyewaLifecycle {
        [*] --> Pen_Aktif : Konfirmasi Reservasi / Registrasi Manual
        Pen_Aktif --> Pen_Aktif : Perpanjang Sewa [Harian/Mingguan]
        Pen_Aktif --> Pen_Nonaktif : Checkout [Potong Deposit jika Rusak]
        Pen_Nonaktif --> Pen_Aktif : Reaktivasi [Kamar 'tersedia']
        Pen_Nonaktif --> [*] : Selesai Menghuni
    }

    state "Siklus Hidup Tagihan Bulanan (Billing)" as TagihanLifecycle {
        [*] --> Tag_Pending : Scheduler Tgl 1 / Sisa DP 70%
        [*] --> Tag_Lunas : Full Payment / Registrasi Manual
        
        Tag_Pending --> Tag_Lunas : Settlement / Cash [Valid]
        Tag_Pending --> Tag_Terlambat : Scheduler [t > Tgl 10]
        Tag_Pending --> Tag_Gagal : Webhook Deny / Fraud
        Tag_Pending --> Tag_Kadaluarsa : Sesi Snap Expired
        
        Tag_Terlambat --> Tag_Lunas : Bayar Pokok + Denda 5%
        Tag_Terlambat --> Tag_Terlambat : Sesi Snap Expire [Anti-Regression]
        
        Tag_Gagal --> Tag_Pending : Klik Bayar / Regenerate SNAP
        Tag_Kadaluarsa --> Tag_Pending : Klik Bayar / Regenerate SNAP
        Tag_Lunas --> [*] : Kuitansi PDF & WA Sukses
    }

    state "Siklus Status Kamar Fisik (Room Allocation)" as KamarLifecycle {
        [*] --> Kam_Tersedia : Kamar Baru Ditambahkan
        Kam_Tersedia --> Kam_Terisi : Konfirmasi / Registrasi Penyewa
        Kam_Terisi --> Kam_Terisi : Checkout [Kunci Kamar Tetap 'terisi']
        Kam_Terisi --> Kam_Tersedia : Inspeksi Fisik OK / Set Tersedia
        Kam_Terisi --> Kam_Maintenance : Kerusakan Ditemukan / Set Perawatan
        Kam_Maintenance --> Kam_Tersedia : Perbaikan Selesai / Set Tersedia
    }

    ReservasiLifecycle --> PenyewaLifecycle : Auto-Onboarding
    PenyewaLifecycle --> TagihanLifecycle : Tagihan Bulanan
    ReservasiLifecycle --> KamarLifecycle : Alokasi Unit
    PenyewaLifecycle --> KamarLifecycle : Menghuni Unit
```

---

## 1. State Machine: Reservasi (Reservation Lifecycle)

Entitas Reservasi melacak pengajuan sewa kamar kost oleh calon penyewa baru. Alur ini memiliki sifat krusial karena melibatkan alokasi unit kamar (*locking mechanism*) dan integrasi pembayaran deposit (DP 30%) atau pelunasan penuh (*Full Payment* 100%) via Midtrans Payment Gateway maupun pembayaran tunai langsung (*cash/transfer*) yang diverifikasi Admin.

### 1.1. Diagram State Reservasi (Mermaid)

![Visual State Machine Reservasi](state/state_reservasi_lifecycle.png)

```mermaid
stateDiagram-v2
    [*] --> pending : Submit Booking Kamar
    
    pending --> dp : Bayar DP 30%
    pending --> lunas : Bayar Lunas 100%
    pending --> batal : Timeout 24 Jam / Double-Booking
    
    dp --> dikonfirmasi : Konfirmasi [NIK & Wali OK]
    lunas --> dikonfirmasi : Konfirmasi [NIK & Wali OK]
    
    dp --> batal : Batal Sebelum Konfirmasi
    lunas --> batal : Batal Sebelum Konfirmasi
    
    dikonfirmasi --> selesai : Auto-Onboarding [User & Tenant]
    selesai --> [*]
    batal --> [*] : Hapus Permanen
```

### 1.2. Penjelasan State Reservasi

| Nama State | Deskripsi | Status Database (`reservasi.status`) | Efek Sistem / Bisnis |
| :--- | :--- | :--- | :--- |
| **`pending`** | Awal mula siklus setelah calon penyewa mengisi data simulasi sewa & memilih kamar. Kamar terkait dikunci tanggalnya sementara (`isKamarTerbooking`) agar tidak dapat dibooking orang lain selama transaksi berjalan. | `'pending'` | Menghasilkan token Snap Midtrans. Batas waktu pembayaran diatur 24 jam. |
| **`dp`** | Calon penyewa telah membayar Down Payment (DP sebesar 30% dari total sewa) baik via Midtrans maupun transfer tunai manual ke Admin. | `'dp'` | Mengubah status transaksi Midtrans menjadi `settlement`. Menunggu verifikasi NIK dan data wali oleh admin. |
| **`lunas`** | Calon penyewa telah melunasi tagihan reservasi secara penuh (*Full Payment* 100%) di awal. | `'lunas'` | Mengubah status transaksi Midtrans menjadi `settlement`. Menunggu verifikasi NIK dan data wali oleh admin. |
| **`dikonfirmasi`** | Reservasi disetujui penuh oleh Admin setelah data identitas (NIK 16 digit & data Wali) diverifikasi dengan benar. | `'dikonfirmasi'` | Mengunci kamar menjadi `'terisi'`, auto-create user & penyewa aktif, generate WhatsApp kredensial via Fonnte, dan inject tagihan sisa DP (70%) atau tagihan lunas. |
| **`selesai`** | **Logical End State**. Reservasi secara resmi selesai setelah penyewa berhasil dibuatkan akun aktif dan kamar kost siap dihuni. | `'dikonfirmasi'` | Seluruh alur selanjutnya berpindah ke siklus hidup penyewa aktif (*Tenant Lifecycle*). |
| **`batal`** | Pembayaran gagal, kedaluwarsa, ditolak, terjadi *double-booking*, atau dibatalkan oleh pihak admin/calon penyewa. | `'batal'` | Melepas kunci kamar terkait sehingga kamar kembali tersedia. Riwayat chat dilepaskan / dihapus (`cascade`). |

### 1.3. Matriks Transisi Reservasi

| Status Awal | Status Akhir | Pemicu (*Trigger*) | Aktor | Proses Internal & Perubahan Database | Event & Listener |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `[Initial]` | `pending` | Mengisi form detail kamar & Checkout | Calon Penyewa | Membuat baris `reservasi` baru. Set `status = 'pending'`, `is_dp` (true/false) sesuai pilihan, create snap_token via `MidtransService`. | `ReservasiCreated` |
| `pending` | `dp` | Webhook status `settlement`/`capture` (DP 30%) ATAU Pembayaran Cash dikonfirmasi admin | Midtrans API / Admin | Update `reservasi.status = 'dp'`, isi `transaction_id`, set `tanggal_konfirmasi = now()`. | `ReservasiDibayar` |
| `pending` | `lunas` | Webhook status `settlement`/`capture` (Lunas 100%) ATAU Pembayaran Cash dikonfirmasi admin | Midtrans API / Admin | Update `reservasi.status = 'lunas'`, isi `transaction_id`, set `tanggal_konfirmasi = now()`. | `ReservasiDibayar` |
| `pending` | `batal` | Batas waktu 24 jam terlampaui (webhook `expire`/`cancel`/`deny` dari Midtrans), terjadi tabrakan ketersediaan kamar, ATAU Pembatalan manual admin | Midtrans API / Scheduler / Admin | Update `reservasi.status = 'batal'`. Set status kamar terkait kembali menjadi `'tersedia'`. | `ReservasiDibatalkan` |
| `dp` | `batal` | Pembatalan manual oleh admin sebelum status dikonfirmasi | Admin | Update `reservasi.status = 'batal'`. Set status kamar terkait kembali menjadi `'tersedia'`. Proses refund manual. | `ReservasiDibatalkan` |
| `lunas` | `batal` | Pembatalan manual oleh admin sebelum status dikonfirmasi | Admin | Update `reservasi.status = 'batal'`. Set status kamar terkait kembali menjadi `'tersedia'`. Proses refund manual. | `ReservasiDibatalkan` |
| `dp` | `dikonfirmasi` | Klik "Konfirmasi Reservasi" setelah NIK & Wali tervalidasi | Admin | Update `reservasi.status = 'dikonfirmasi'`. Jalankan `TransisiPenyewaService`: kamar.status menjadi `'terisi'`, buat model `User` & `Penyewa` (aktif), kirim WA via Fonnte, inject sisa DP (70%) ke tagihan bulan berjalan (`BillingService::injectSisaDp`). | `ReservasiDikonfirmasi` |
| `lunas` | `dikonfirmasi` | Klik "Konfirmasi Reservasi" setelah NIK & Wali tervalidasi | Admin | Update `reservasi.status = 'dikonfirmasi'`. Jalankan `TransisiPenyewaService`: kamar.status menjadi `'terisi'`, buat model `User` & `Penyewa` (aktif), kirim WA via Fonnte, inject tagihan bulan berjalan berstatus langsung lunas (`BillingService::injectLunasPenuh`). | `ReservasiDikonfirmasi` |

### 1.4. Guard Conditions (Syarat Batas Transisi Reservasi)

| Transisi Status | Syarat Batas (*Guard Conditions*) | Penanganan Jika Gagal |
| :--- | :--- | :--- |
| `pending` $\rightarrow$ `dp` / `lunas` | 1. Signature Key Midtrans terverifikasi valid (SHA-512 `hash_equals`).<br>2. Nilai `gross_amount` dari webhook sama persis dengan `total_harga` atau `nominal_dp` di database.<br>3. Unit kamar tidak mengalami bentrok ketersediaan pada tanggal sewa. | Jika *gross amount* atau *signature* salah: transaksi ditolak HTTP 400/403. Jika *double-booking*: status diubah ke `batal` dengan instruksi *refund* manual. |
| `dp` / `lunas` $\rightarrow$ `dikonfirmasi` | 1. Input NIK calon penyewa wajib tepat 16 digit numerik.<br>2. Data wali (nama dan nomor telepon) wajib diisi lengkap.<br>3. Status kamar tujuan wajib `'tersedia'` (tidak boleh terisi atau maintenance). | Proses konfirmasi diblokir, sistem menampilkan pesan peringatan (*toast error*) di dashboard admin. |
| `pending` $\rightarrow$ `batal` | 1. Tanggal sekarang melampaui `created_at` + 24 jam.<br>2. Kamar masih dalam keadaan terkunci (status `'tersedia'` namun reservasi aktif). | Kunci kamar dilepas (status kamar diubah kembali menjadi `'tersedia'`). |
| `dp` / `lunas` $\rightarrow$ `batal` | Pembatalan manual oleh admin sebelum dikonfirmasi (misalnya data identitas salah, unit bermasalah, atau permintaan batal). | Kamar dilepaskan dari reservasi. Pengembalian dana (refund) diproses manual di luar sistem. |

---

## 2. State Machine: Tagihan (Invoice Lifecycle)

Tagihan melacak kewajiban pembayaran sewa bulanan penyewa aktif serta sisa kewajiban pelunasan dari skema DP maupun tagihan pendaftaran manual. Transisi status ini dikelola secara dinamis melalui kombinasi database update dan *dynamic Eloquent accessors*.

### 2.1. Diagram State Tagihan (Mermaid)

![Visual State Machine Tagihan](state/state_tagihan_lifecycle.png)

```mermaid
stateDiagram-v2
    [*] --> pending : Scheduler Tgl 1 / Sisa DP 70%
    [*] --> lunas : Full Payment / Registrasi Manual
    
    pending --> lunas : Settlement / Cash [Valid]
    pending --> terlambat : Lewat Jatuh Tempo [t > Tgl 10]
    pending --> gagal : Deny / Gagal Bayar
    pending --> kadaluarsa : Sesi Snap Expired
    
    terlambat --> lunas : Bayar Pokok + Denda 5%
    terlambat --> terlambat : Snap Expired (Tetap Terlambat)
    
    gagal --> pending : Bayar Ulang (Regenerate SNAP)
    kadaluarsa --> pending : Bayar Ulang (Regenerate SNAP)
    
    lunas --> [*] : Nota PDF & WA Sukses
```

### 2.2. Penjelasan State Tagihan

| Nama State | Deskripsi | Status Database (`tagihan.status`) | Efek Sistem / Bisnis |
| :--- | :--- | :--- | :--- |
| **`pending`** | Tagihan baru diterbitkan oleh sistem scheduler (tiap tanggal 1) atau sisa DP baru diinjeksi. Menunggu pelunasan. | `'pending'` | Dapat menghasilkan token Snap Midtrans untuk pembayaran digital. |
| **`lunas`** | Pembayaran berhasil diproses dan divalidasi, baik via Midtrans Callback, input cash manual, maupun inisialisasi awal (*Full Payment* / Pendaftaran Manual). | `'lunas'` | Membuat baris `pembayaran`, trigger event cetak PDF nota pembayaran otomatis, kirim notifikasi WA terima kasih via Fonnte. |
| **`terlambat`** | **Persistent & Computed State**. Ditampilkan di UI jika status DB `'pending'`/`'terlambat'`, tanggal jatuh tempo (tanggal 10) terlewati, dan scheduler mendeteksi keterlambatan (`bulan_keterlambatan > 0`). | `'terlambat'` / `'pending'` (di DB) | Memicu eskalasi peringatan (Bulan 1: WA ke penyewa, Bulan 2: WA ke wali, Bulan 3+: Denda flat 5% dikenakan ke `nominal_denda`). |
| **`gagal`** | Transaksi pembayaran via payment gateway diblokir, ditolak oleh bank, atau terdeteksi fraud. | `'gagal'` | Kirim notifikasi WA gagal bayar, penyewa diarahkan mencoba bayar ulang dengan metode lain. |
| **`kadaluarsa`** | **Logical/Midtrans State**. Sesi token pembayaran digital kedaluwarsa tanpa penyelesaian transaksi. | `'pending'` (di DB) | Midtrans webhook memetakan status `expire` ke `'pending'` agar tagihan tetap aktif untuk dicoba bayar ulang dengan snap token baru. |

### 2.3. Dualitas Arsitektur Status `terlambat` (Database & Eloquent Accessor)

Sebagai bagian dari integritas arsitektur:
1. **Persistensi Database**: Berdasarkan migrasi `2026_07_20_102435_add_terlambat_to_tagihan_status.php`, nilai enum database `tagihan.status` mencakup `['pending', 'lunas', 'gagal', 'kadaluarsa', 'terlambat']`. Perintah harian [BillingService::prosesKeterlambatan](file:///c:/xampp/htdocs/asri-boarding-house/app/Services/BillingService.php#L97) memperbarui status fisik baris tagihan menjadi `'terlambat'`.
2. **Dynamic Runtime Accessor**: Pada Model [Tagihan.php](file:///c:/xampp/htdocs/asri-boarding-house/app/Models/Tagihan.php#L145-L151), sistem menyediakan metode accessor `getComputedStatusAttribute()`:
```php
public function getComputedStatusAttribute(): string
{
    $rawStatus = $this->attributes['status'] ?? 'pending';
    if ($rawStatus === 'pending' && $this->tanggal_jatuh_tempo && $this->tanggal_jatuh_tempo->endOfDay()->isPast()) {
        return 'terlambat';
    }
    return $rawStatus;
}
```
Pendekatan *defense-in-depth* ini memastikan dashboard penyewa dan penghitungan denda selalu akurat secara *real-time* meskipun scheduler harian belum berjalan pada hari jatuh tempo.

### 2.4. Matriks Transisi Tagihan

| Status Awal | Status Akhir | Pemicu (*Trigger*) | Aktor | Proses Internal & Perubahan Database | Event & Listener |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `[Initial]` | `pending` | Scheduler berjalan (Tanggal 1) ATAU Konfirmasi Reservasi DP | Laravel Scheduler / Admin | Menyisipkan baris `tagihan` baru. Set `status = 'pending'`, `tanggal_jatuh_tempo = tanggal 10`, `bulan_keterlambatan = 0`. | `TagihanDibuat` |
| `[Initial]` | `lunas` | Konfirmasi Reservasi Full Payment ATAU Pendaftaran Manual Baru | Admin / Sistem | Menyisipkan baris `tagihan` dengan `status = 'lunas'` via `injectLunasPenuh` atau `injectManualPenyewaLunas`, membuat record `pembayaran`, cetak PDF nota kuitansi. | `PembayaranBerhasil` / `PembayaranCashDikonfirmasi` |
| `pending` | `lunas` | Webhook status `settlement`/`capture` ATAU Admin klik konfirmasi cash | Midtrans API / Admin | Update `tagihan.status = 'lunas'`, buat baris `pembayaran`, simpan metadata bank/VA. | `PembayaranBerhasil` |
| `pending` | `terlambat` | Scheduler mendeteksi tanggal berjalan > `tanggal_jatuh_tempo` | Laravel Scheduler | Update `tagihan.status = 'terlambat'`, `bulan_keterlambatan += 1`. Jika `bulan_keterlambatan >= 3`, hitung denda otomatis `nominal_denda = nominal_pokok * 0.05` dan `nominal_total = nominal_pokok + nominal_denda`. | `ReminderPenyewa` (Bulan 1-2), `DendaDikenakan` & `NotifikasiWali` (Bulan 3+) |
| `terlambat` | `lunas` | Pembayaran lunas pokok + denda (via Midtrans / Cash) | Penyewa / Admin | Update `tagihan.status = 'lunas'`. Catat transaksi pembayaran, cetak PDF nota berisi rincian denda. | `PembayaranBerhasil` |
| `terlambat` | `terlambat` | Sesi Snap dibatalkan atau expired saat tagihan terlambat | Midtrans API | Proteksi Anti-Regression di `MidtransCallbackController`: status tagihan tetap `'terlambat'` (tidak turun ke `'pending'`). | - |
| `pending` | `gagal` | Webhook status `deny` dari Midtrans | Midtrans API | Update `tagihan.status = 'gagal'`. | - |
| `pending` | `kadaluarsa` | Webhook status `expire` / `cancel` dari Midtrans | Midtrans API | Sesi Midtrans expired. Di DB status tetap aktif untuk dicoba bayar ulang. | - |
| `gagal` / `kadaluarsa` | `pending` | Penyewa klik "Bayar Sekarang" lagi di Dashboard | Penyewa | Tagihan menghasilkan Snap Token baru dengan menambahkan timestamp pada order ID (`order_id-{timestamp}`). | - |

---

## 3. State Machine Entitas Pendukung

### 3.1. State Kamar (Room Status Lifecycle)

Mencegah terjadinya reservasi ganda (*double-booking*) dan menjamin inspeksi fisik check-out terlaksana.

![Visual State Machine Kamar](state/state_kamar_lifecycle.png)

```mermaid
stateDiagram-v2
    [*] --> tersedia : Tambah Kamar Baru
    tersedia --> terisi : Reservasi / Registrasi Penyewa
    terisi --> terisi : Checkout [Kamar Tetap 'terisi']
    terisi --> tersedia : Set Tersedia [Inspeksi Fisik OK]
    terisi --> maintenance : Set Perawatan [Kerusakan Fasilitas]
    tersedia --> maintenance : Set Perawatan [Rutin]
    maintenance --> tersedia : Set Tersedia [Perbaikan Selesai]
```

* **Aturan Bisnis Konsistensi Check-Out**: Saat penyewa dinonaktifkan (`status = 'nonaktif'`), status unit kamar terkait **tetap `'terisi'`**. Ini memaksa admin melakukan inspeksi fisik kamar terlebih dahulu sebelum mengubah status kamar secara manual di menu manajemen kamar menjadi `'tersedia'` atau `'maintenance'`.

### 3.2. State Penyewa (Tenant Lifecycle)

Mengontrol akses masuk ke portal aplikasi dan mengelola masa sewa.

![Visual State Machine Penyewa](state/state_penyewa_lifecycle.png)

```mermaid
stateDiagram-v2
    [*] --> aktif : Onboarding Online / Manual
    aktif --> nonaktif : Checkout (Potong Deposit)
    nonaktif --> aktif : Reaktivasi (Kamar OK)
    aktif --> aktif : Perpanjang Sewa (Harian/Mingguan)
```

* **Mekanisme Check-out dan Deposit**:
  * **Check-out Tanpa Kerusakan**: Status berubah menjadi `'nonaktif'`, nominal deposit jaminan (`penyewa.deposit`) dikembalikan penuh dan dicatat pada pengeluaran operasional.
  * **Check-out Dengan Kerusakan (*Damage Flow*)**: Status berubah menjadi `'nonaktif'`, nominal deposit dipotong sebesar biaya kerusakan. Sistem membuat baris pengeluaran baru pada tabel `pengeluaran` dengan kategori `maintenance` beserta foto nota pendukung.
* **Perpanjangan Sewa (*Self-Transition*)**: Penyewa harian/mingguan dapat memperpanjang kontrak sewa via `BillingService::perpanjangKontrakManual`. Status penyewa tetap `'aktif'`, durasi diakumulasikan, `tanggal_keluar_seharusnya` diperbarui, dan diterbitkan tagihan baru.
* **Proteksi Reaktivasi**: Jika admin mengaktifkan kembali penyewa lama, sistem memvalidasi kamar tujuan. Jika kamar berstatus `'terisi'` atau `'maintenance'`, reaktivasi diblokir.

### 3.3. State Keluhan (Complaint Lifecycle)

Melacak penanganan masalah fasilitas kost yang dilaporkan oleh penyewa.

![Visual State Machine Keluhan](state/state_keluhan_lifecycle.png)

```mermaid
stateDiagram-v2
    [*] --> pending : Submit Keluhan [Foto < 2MB]<br>/ WA ke Admin
    pending --> diproses : Klik Proses / Mulai Perbaikan
    pending --> selesai : Klik Selesai [Tanggapan Diisi]<br>/ Kirim WA Solusi
    diproses --> selesai : Klik Selesai [Tanggapan Diisi]<br>/ Kirim WA Solusi
    selesai --> diproses : Klik Reopen / Tiket Dibuka Kembali
    selesai --> [*] : Arsip Keluhan
```

* Saat penyewa mengirim keluhan (`pending`), notifikasi WA dikirim ke Admin via Fonnte.
* Saat status diubah menjadi `selesai`, sistem mencatat `tanggal_selesai` dan mengirimkan notifikasi WA solusi penutupan tiket ke penyewa.

### 3.4. State Log Notifikasi WhatsApp (`log_notifikasi`)

Sistem melacak status pengiriman notifikasi WhatsApp ke penyewa dan admin melalui tabel `log_notifikasi`.

![Visual State Machine Log Notifikasi](state/state_notifikasi_lifecycle.png)

```mermaid
stateDiagram-v2
    [*] --> pending_send : Event Terpicu / Queue Job Pushed
    pending_send --> sukses : Kirim Sukses [HTTP 200]<br>/ Log Sukses
    pending_send --> gagal : Kirim Gagal [Timeout/API Down]<br>/ Log Error
    gagal --> pending_send : Klik Retry / kirimUlangNotifikasi()
    gagal --> [*] : Selesai
    sukses --> [*] : Selesai
```

* **Resilience Non-Blocking**: Kegagalan pengiriman Fonnte API tidak akan me-rollback transaksi utama. Status tercatat `'gagal'` dengan pesan kesalahan (`error_msg`). Admin dapat melakukan retry manual via dashboard.

### 3.5. State Guest Chat Thread (Guest Chat Lifecycle)

Melacak status sesi obrolan antara pengunjung anonim (Tamu/Guest) dengan Administrator pada landing page.

![Visual State Machine Guest Chat](state/state_guest_chat_lifecycle.png)

```mermaid
stateDiagram-v2
    [*] --> active : Tamu Kirim Chat Pertama [Cookie UUID]<br>/ Hash & Save Token
    active --> closed : Klik Tutup Obrolan<br>/ Inject ___CHAT_CLOSED___
    closed --> active : Admin Kirim Balasan<br>/ Reopen Chat
    closed --> [*] : Scheduler [t > 90 Hari]<br>/ chat-guest:prune (Delete)
```

* Sesi obrolan diidentifikasi menggunakan `guest_chat_token` (UUID) yang disimpan di cookie browser selama 30 hari dan di-hash SHA-256 (`session_token`).
* Saat admin menutup obrolan (`closeThread`), status menjadi `closed` dan sistem menginjeksi pesan `___CHAT_CLOSED___`.
* Jika admin mengirim balasan baru ke thread `closed`, status obrolan otomatis aktif kembali (`closed --> active`).
* Laravel Scheduler menjalankan perintah `chat-guest:prune` setiap hari pukul 03:00 WIB untuk menghapus secara permanen thread `closed` yang berumur lebih dari 90 hari.

---

## 4. Keamanan Transaksi Finansial (ACID Compliance & Idempotency)

Setiap transisi status yang melibatkan perubahan data finansial atau mutasi log wajib dibungkus dalam transaksi database (`DB::transaction`) dan menggunakan penguncian baris data (`lockForUpdate()`) guna mengantisipasi *race condition* dan *double-click*:
```php
DB::transaction(function () use ($tagihan, $tagihanStatus, $request) {
    // Lock data secara eksklusif
    $lockedTagihan = Tagihan::where('id', $tagihan->id)->lockForUpdate()->first();
    
    // Validasi Idempotensi di bawah proteksi lock
    if ($lockedTagihan->status === 'lunas') {
        return ['status' => 200, 'message' => 'Already processed'];
    }
    
    $lockedTagihan->update(['status' => $tagihanStatus]);
});
```
