import fs from 'fs';
import path from 'path';

const mdPath = path.resolve('Blueprint/Flowchart_Kost.md');
let content = fs.readFileSync(mdPath, 'utf8');

// The initialization header for styling
const initHeader = `%%{init: {
  'theme': 'base',
  'themeVariables': {
    'primaryColor': '#FFFBEB',
    'primaryTextColor': '#000000',
    'primaryBorderColor': '#000000',
    'lineColor': '#000000',
    'secondaryColor': '#FDE047',
    'tertiaryColor': '#FFFFFF',
    'edgeLabelBackground': '#FFFFFF',
    'fontSize': '12px',
    'fontFamily': 'Inter, system-ui, sans-serif'
  }
}}%%`;

// Helper to wrap mermaid diagrams with init styling and cleaner nodes
const diagrams = {
  // 2.1 Master
  "flowchart TD\n    Start([Mulai]) --> Guest[\"Tamu membuka katalog kamar (/kamar)\"]": `flowchart TD
    Start([Mulai]) --> Guest["Tamu membuka katalog kamar (/kamar)"]
    Guest --> CariKamar{"Cari & cek status kamar"}
    
    CariKamar -- "Terisi / Maintenance" --> HubungiAdmin["Hubungi WhatsApp Admin (Tanya WA)"]
    HubungiAdmin --> Selesai([Selesai])
    
    CariKamar -- "Tersedia" --> PesanUnit["Klik 'Pesan Unit' & isi form reservasi"]
    PesanUnit --> CekAuth{"Sudah login?"}
    
    CekAuth -- "Tidak" --> LoginReg["Login / Register Akun Baru"]
    LoginReg --> IsiForm["Kirim data reservasi (kunci sementara tanggal & kamar)"]
    CekAuth -- "Ya" --> IsiForm
    
    IsiForm --> HalamanReservasi["Redirect ke halaman detail reservasi"]
    HalamanReservasi --> ChatPrePembayaran["Diskusi pre-pembayaran via Chat Box (AJAX Polling)"]
    HalamanReservasi --> BayarDP["Bayar DP 30% / Lunas 100% via Midtrans Snap"]
    
    BayarDP --> VerifBayar{"Pembayaran settlement?"}
    VerifBayar -- "Tidak (Gagal/Expired)" --> BatalAuto["Reservasi batal & tanggal kamar dilepas"]
    BatalAuto --> Selesai
    
    VerifBayar -- "Ya" --> TinjauDataAdmin["Admin meninjau NIK & kontak Wali"]
    TinjauDataAdmin --> ValidData{"Data valid?"}
    
    ValidData -- "Tidak" --> MintaKoreksi["Admin minta koreksi data ke user"]
    MintaKoreksi --> TinjauDataAdmin
    
    ValidData -- "Ya" --> KonfirmasiAdmin["Admin klik 'Konfirmasi Reservasi'"]
    
    KonfirmasiAdmin --> DBTrans["DB::transaction():<br>- Reservasi dikonfirmasi<br>- Kamar status 'terisi' (PenyewaObserver)<br>- Salin harga_sewa personal<br>- Buat profil penyewa"]
    
    DBTrans --> KirimKredensial["WA Fonnte mengirim kredensial login default"]
    KirimKredensial --> LoginPenyewa["Penyewa masuk portal pertama kali dengan sandi default"]
    
    LoginPenyewa --> CekForceChange{"require_password_change == true?"}
    CekForceChange -- "Ya" --> ForceRedirect["Redirect paksa ke form ubah password"]
    ForceRedirect --> UbahPassword["Penyewa update password & set flag = false"]
    UbahPassword --> DashboardActive["Akses penuh ke Dashboard Penyewa Aktif"]
    CekForceChange -- "Tidak" --> DashboardActive
    
    DashboardActive --> SiklusAktif{"Aktivitas penyewa aktif"}
    
    SiklusAktif -- "Tagihan Bulanan" --> BayarTagihan["Bayar tagihan tiap tanggal 1-10 (Midtrans / Cash)"]
    BayarTagihan --> SiklusAktif
    
    SiklusAktif -- "Ada Masalah" --> LaporkanKeluhan["Kirim keluhan fasilitas + foto bukti (< 2MB)"]
    LaporkanKeluhan --> TindakanAdmin["Admin perbaiki fisik & isi tanggapan (notif WA)"]
    TindakanAdmin --> SiklusAktif
    
    SiklusAktif -- "Sewa Selesai" --> CheckoutPenyewa["Admin checkout Penyewa (status -> 'nonaktif')"]
    
    CheckoutPenyewa --> KamarLocked["Aturan Bisnis: Kamar tetap berstatus 'terisi'"]
    KamarLocked --> InspeksiKamar["Admin melakukan inspeksi fisik kamar"]
    
    InspeksiKamar --> CekRusak{"Ada kerusakan fisik?"}
    CekRusak -- "Ya" --> PotongJaminan["Hitung biaya perbaikan & potong uang deposit"]
    CekRusak -- "Tidak" --> BalikJaminan["Kembalikan deposit jaminan secara penuh"]
    
    PotongJaminan --> UpdateKamarManual["Admin secara manual mengupdate status kamar"]
    BalikJaminan --> UpdateKamarManual
    
    UpdateKamarManual --> KamarKondisi{"Kamar perlu perbaikan?"}
    KamarKondisi -- "Ya" --> SetMaint["Ubah status kamar menjadi 'maintenance'"]
    KamarKondisi -- "Tidak" --> SetReady["Ubah status kamar menjadi 'tersedia'"]
    
    SetMaint --> Selesai
    SetReady --> Selesai`,

  // 2.2 Sub 1
  "flowchart TD\n    Start([Mulai]) --> BukaKatalog[\"Guest membuka katalog kamar (/kamar) atau Beranda\"]": `flowchart TD
    Start([Mulai]) --> BukaKatalog["Guest membuka katalog kamar (/kamar) atau Beranda"]
    BukaKatalog --> CekUX{"Aktivitas Rendering Halaman Landing"}
    
    CekUX --> RenderHamburger["Render Navbar Hamburger responsif mobile"]
    CekUX --> RenderWAFloat["Render WhatsApp Floating Button (z-index z-50, target >= 44px)"]
    CekUX --> RenderRoomTour["Render Room Tour Video Section (YouTube aspect-video)"]
    
    RenderHamburger --> InputFilter["User memasukkan Filter (Lantai, Tipe, Tanggal, Durasi)"]
    RenderWAFloat --> InputFilter
    RenderRoomTour --> InputFilter
    
    InputFilter --> QueryKamar["Sistem melakukan query SELECT * FROM kamar"]
    
    QueryKamar --> FilterMaint{"Apakah kamar berstatus 'maintenance'?"}
    FilterMaint -- "Ya" --> Sembunyikan["Sembunyikan kamar sepenuhnya dari katalog publik"]
    Sembunyikan --> Selesai([Selesai])
    
    FilterMaint -- "Tidak" --> Tampilkan["Tampilkan kamar di grid katalog"]
    Tampilkan --> EvaluasiStatus{"Bagaimana status kamar?"}
    
    EvaluasiStatus -- "terisi" --> TampilTombolWA["Tampilkan tombol 'Tanya WA' (Warna flat Neo-Brutalisme)"]
    TampilTombolWA --> KlikWA["Arahkan ke WhatsApp Chat Admin (URL-encoded message)"]
    KlikWA --> Selesai
    
    EvaluasiStatus -- "tersedia" --> TampilTombolPesan["Tampilkan tombol 'Pesan Unit' (ikat parameter filter URL)"]
    TampilTombolPesan --> KlikPesan["Guest klik 'Pesan Unit'"]
    KlikPesan --> BukaDetail["Buka detail kamar (/kamar/{id}) beserta query string filter"]
    BukaDetail --> AutoKalkulasi["JS otomatis eksekusi hitungEstimasi() tarif total"]
    AutoKalkulasi --> TampilForm["Form reservasi siap diisi di bagian detail kamar"]
    TampilForm --> Selesai`,

  // 2.3 Sub 2
  "flowchart TD\n    Start([Mulai]) --> TampilForm[\"Form reservasi dirender di detail kamar\"]": `flowchart TD
    Start([Mulai]) --> TampilForm["Form reservasi dirender di detail kamar"]
    TampilForm --> IsiData["Calon penyewa mengisi tanggal mulai, durasi sewa, catatan, dan skema bayar (DP / Lunas)"]
    IsiData --> KlikKirim["Calon penyewa klik tombol 'Pesan Unit'"]
    
    KlikKirim --> CekLogin{"Apakah user telah terautentikasi?"}
    CekLogin -- "Tidak" --> RedirectLogin["Redirect ke halaman login/register (/reservasi/login)"]
    RedirectLogin --> RegisterForm["Registrasi akun baru (isi Nama, Email, Sandi, No HP)"]
    RegisterForm --> LoginSuccess["Berhasil login & sesi aktif"]
    LoginSuccess --> KlikKirim
    
    CekLogin -- "Ya" --> ValidasiForm{"Apakah validasi input form sukses?<br>(NIK 16 digit, No HP Wali format WA)"}
    ValidasiForm -- "Tidak" --> TampilErrorValidation["Kembalikan ke halaman form dengan pesan error detail & toast"]
    TampilErrorValidation --> IsiData
    
    ValidasiForm -- "Ya" --> DBTrans["DB::transaction() dimulai"]
    DBTrans --> LockRoom{"Kamar::lockForUpdate()"}
    
    LockRoom -- "Kamar sudah dibooking pada tanggal tersebut" --> RollbackTx["Rollback & kirim toast 'Kamar sudah ter-booking'"]
    RollbackTx --> Selesai([Selesai])
    
    LockRoom -- "Kamar tersedia pada tanggal tersebut" --> LockRoomSuccess["1. Buat data reservasi dengan status 'pending' (kunci sementara tanggal & kamar)<br>2. Generate order_id unik reservasi<br>3. Simpan data reservasi ke database"]
    LockRoomSuccess --> CommitTx["Commit DB Transaction"]
    CommitTx --> BukaChatBox["Aktifkan Chat Box pre-pembayaran secara real-time"]
    BukaChatBox --> RedirectDetail["Redirect ke portal reservasi calon penyewa (/penyewa/reservasi/{id})"]
    RedirectDetail --> Selesai`,

  // 2.4 Sub 3
  "flowchart TD\n    Start([Mulai]) --> BukaDetailReservasi[\"Calon penyewa membuka halaman detail reservasi\"]": `flowchart TD
    Start([Mulai]) --> BukaDetailReservasi["Calon penyewa membuka detail reservasi"]
    
    BukaDetailReservasi --> ChatSection["Akses komponen Chat Box Diskusi"]
    ChatSection --> KirimPesan["User mengetik & kirim pesan chat"]
    KirimPesan --> SimpanPesan["Pesan disimpan ke tabel 'chat_messages' (sender_id = user_id)"]
    SimpanPesan --> AJAXPolling["AJAX Polling memicu request periodik (3-5 detik)"]
    AJAXPolling --> RenderBubble["Render gelembung chat Neo-Brutalism (Kuning = Pengirim, Putih = Lawan)"]
    RenderBubble --> ChatSection
    
    BukaDetailReservasi --> KlikBayar["User klik tombol 'Bayar Sekarang'"]
    KlikBayar --> ReqSnapToken["ReservasiController meminta Snap Token ke Midtrans API"]
    ReqSnapToken --> ReturnSnapToken["Midtrans API mengembalikan snap_token"]
    ReturnSnapToken --> OpenSnapModal["Panggil snap.pay(snap_token) untuk membuka pop-up"]
    OpenSnapModal --> SelesaikanPembayaran["User menyelesaikan transfer pembayaran (VA / E-Wallet)"]
    
    SelesaikanPembayaran --> WebhookMidtrans["Midtrans mengirim callback post ke /api/midtrans/webhook"]
    WebhookMidtrans --> ValidasiSignature{"Apakah signature_key & nominal valid?"}
    
    ValidasiSignature -- "Tidak" --> RejectWebhook["Tolak request webhook & catat error log"]
    RejectWebhook --> Selesai([Selesai])
    
    ValidasiSignature -- "Ya" --> EvaluasiStatusMidtrans{"Status Transaksi?"}
    
    EvaluasiStatusMidtrans -- "Settlement / Success" --> CekSkemaBayar{"Skema Pembayaran?"}
    CekSkemaBayar -- "DP 30%" --> SetStatusDP["Update status reservasi = 'dp'<br>Simpan data transaksi ke tabel pembayaran"]
    CekSkemaBayar -- "Lunas 100%" --> SetStatusLunas["Update status reservasi = 'lunas'<br>Simpan data transaksi ke tabel pembayaran"]
    
    EvaluasiStatusMidtrans -- "Expired / Failed / Deny" --> SetBatal["1. Update status reservasi = 'batal'<br>2. Lepas kunci kamar (reservasi status menjadi 'batal', melepas kunci pemesanan kamar pada tanggal sewa terkait)"]
    
    SetStatusDP --> ReloadUI["Sistem memperbarui visual Stepper Reservasi 5-Step"]
    SetStatusLunas --> ReloadUI
    SetBatal --> ReloadUI
    ReloadUI --> Selesai`,

  // 2.5 Sub 4
  "flowchart TD\n    Start([Mulai]) --> AdminBukaReservasi[\"Admin membuka menu Daftar Reservasi (/admin/reservasi)\"]": `flowchart TD
    Start([Mulai]) --> AdminBukaReservasi["Admin membuka menu Daftar Reservasi (/admin/reservasi)"]
    AdminBukaReservasi --> PilihReservasi["Admin memilih reservasi berstatus 'dp' atau 'lunas'"]
    PilihReservasi --> TinjauBerkas["Admin memverifikasi NIK (KTP) & Nomor Telepon Wali"]
    TinjauBerkas --> KlikKonfirmasi["Admin klik tombol 'Konfirmasi Reservasi'"]
    
    KlikKonfirmasi --> ValidasiInput{"Apakah input data valid?<br>(NIK 16 digit, No HP Wali format valid)"}
    ValidasiInput -- "Tidak" --> TampilToastError["Kembalikan dengan Toast Error validation"]
    TampilToastError --> TinjauBerkas
    
    ValidasiInput -- "Ya" --> DBTrans["Sistem memulai DB::transaction()"]
    DBTrans --> UpdateReservasi["1. Ubah status reservasi menjadi 'dikonfirmasi'<br>2. Simpan tanggal_konfirmasi = NOW()"]
    UpdateReservasi --> LockKamarTerisi["Ubah status kamar terkait menjadi 'terisi' secara permanen"]
    LockKamarTerisi --> SalinHargaSewa["Salin harga kamar saat ini ke penyewa.harga_sewa secara immutable"]
    SalinHargaSewa --> CreateAccountUser["Buat akun User baru (role: 'penyewa', require_password_change: true)"]
    CreateAccountUser --> CreateProfilPenyewa["Buat data Profil Penyewa baru di database (tabel penyewa)"]
    
    CreateProfilPenyewa --> CekSkemaDP{"Apakah reservasi menggunakan skema DP?"}
    CekSkemaDP -- "Ya" --> InjectTagihanSisa["Sistem menginjeksi tagihan sisa pelunasan (70%) ke tabel tagihan"]
    CekSkemaDP -- "Tidak / Lunas" --> PanggilFonnteAPI["Sistem memanggil Fonnte WA API"]
    InjectTagihanSisa --> PanggilFonnteAPI
    
    PanggilFonnteAPI --> CommitTx["Commit DB Transaction secara atomik"]
    CommitTx --> KirimWA["Kirim WhatsApp kredensial login (Email & Password default dari No HP)"]
    KirimWA --> Selesai([Selesai])`,

  // 2.6 Sub 5
  "flowchart TD\n    Start([Setiap Tanggal 1 Awal Bulan, 00:00]) --> TriggerScheduler[\"Cron Job memicu perintah scheduler Laravel\"]": `flowchart TD
    Start([Setiap Tanggal 1 Awal Bulan, 00:00]) --> TriggerScheduler["Cron Job memicu perintah scheduler Laravel"]
    TriggerScheduler --> RunBillingCommand["Eksekusi command: php artisan billing:generate"]
    RunBillingCommand --> FetchPenyewaAktif["Ambil semua data penyewa berstatus 'aktif' tipe sewa 'bulanan'"]
    
    FetchPenyewaAktif --> LoopPenyewa{"Apakah ada penyewa aktif berikutnya?"}
    LoopPenyewa -- "Tidak" --> Selesai([Selesai])
    
    LoopPenyewa -- "Ya" --> GetImmutableRate["Ambil tarif personal dari penyewa.harga_sewa"]
    GetImmutableRate --> BuatTagihan["Buat data Tagihan baru (status: 'pending', nominal_pokok = harga_sewa)"]
    BuatTagihan --> SetJatuhTempo["Set tanggal_jatuh_tempo = tanggal 10 bulan berjalan"]
    SetJatuhTempo --> CreateOrderId["Generate order_id unik transaksi (misal: BILL-20260601-XX)"]
    CreateOrderId --> LogNotifPending["Simpan log_notifikasi baru status 'pending'"]
    LogNotifPending --> CallFonnteAPI["Panggil Fonnte WA API untuk mengirim detail invoice"]
    
    CallFonnteAPI --> CekResponse{"Apakah API mengirim dengan sukses?"}
    CekResponse -- "Ya" --> SetLogSukses["Update log_notifikasi status menjadi 'sukses'"]
    CekResponse -- "Tidak" --> SetLogGagal["Update log_notifikasi status menjadi 'gagal' & catat error_msg"]
    
    SetLogSukses --> LoopPenyewa
    SetLogGagal --> LoopPenyewa`,

  // 2.7 Sub 6
  "flowchart TD\n    Start([Mulai]) --> PenyewaLogin[\"Penyewa login & masuk Menu 'Tagihan Saya'\"]": `flowchart TD
    Start([Mulai]) --> PenyewaLogin["Penyewa login & masuk Menu 'Tagihan Saya'"]
    PenyewaLogin --> CekIDOR["Verifikasi ID Penyewa dengan ID Sesi (Mencegah IDOR)"]
    CekIDOR --> PilihTagihan["Penyewa memilih satu tagihan berstatus pending"]
    PilihTagihan --> LoadBankDinamis["Sistem memanggil data bank dari settings dinamis"]
    
    LoadBankDinamis --> CekTerlambat{"Tanggal hari ini melewati tanggal 10 jatuh tempo?"}
    CekTerlambat -- "Ya" --> HitungDendaBertahap["Sistem menerapkan denda keterlambatan:<br>- Bulan Berjalan: Bebas Denda (Masa Keringanan) & kirim WA Reminder Penyewa<br>- Bulan Berikutnya: Denda Flat 5% (Sekali saja) & kirim WA Alergi Penyewa + WA Eskalasi Wali"]
    HitungDendaBertahap --> TampilkanTotal["Tampilkan total tagihan (Nominal Pokok + Nominal Denda)"]
    CekTerlambat -- "Tidak" --> TampilkanTotal
    
    TampilkanTotal --> PilihMetode{"Pilih Metode Pembayaran?"}
    
    PilihMetode -- "Online (Midtrans Snap)" --> KlikBayarOnline["Klik tombol 'Bayar Online'"]
    KlikBayarOnline --> ReqSnapToken["Request Snap Token ke Midtrans API"]
    ReqSnapToken --> OpenSnap["Render Pop-Up Midtrans Snap"]
    OpenSnap --> BayarOnline["Penyewa menyelesaikan pembayaran online"]
    BayarOnline --> WebhookMidtrans["Webhook Midtrans mengirim callback settlement ke Laravel"]
    WebhookMidtrans --> DBTransOnline["DB::transaction() running:<br>1. Set status tagihan: 'lunas'<br>2. Simpan record pembayaran online"]
    
    PilihMetode -- "Offline (Cash / Transfer Bank)" --> TransferManual["Penyewa menyerahkan cash fisik / transfer bank manual ke admin"]
    TransferManual --> AdminVerifikasiKas["Admin memverifikasi uang masuk di mutasi rekening / cash fisik"]
    AdminVerifikasiKas --> AdminKonfirmasiCash["Admin masuk menu tagihan & klik tombol 'Konfirmasi Cash'"]
    AdminKonfirmasiCash --> DBTransOffline["DB::transaction() running:<br>1. Set status tagihan: 'lunas'<br>2. Simpan record pembayaran (dikonfirmasi_oleh = admin_id)"]
    
    DBTransOnline --> GenReceiptPDF["Sistem memicu Dompdf untuk men-generate nota kuitansi PDF"]
    DBTransOffline --> GenReceiptPDF
    
    GenReceiptPDF --> CallFonnteReceipt["Fonnte WA API mengirim pesan sukses bayar & tautan unduh PDF"]
    CallFonnteReceipt --> Selesai([Selesai])`,

  // 2.8 Sub 7
  "flowchart TD\n    Start([Mulai]) --> BukaKeluhan[\"Penyewa masuk Menu Keluhan & Pengaduan\"]": `flowchart TD
    Start([Mulai]) --> BukaKeluhan["Penyewa masuk Menu Keluhan & Pengaduan"]
    BukaKeluhan --> KlikBuatLaporan["Klik tombol 'Buat Keluhan Baru'"]
    KlikBuatLaporan --> IsiFormLaporan["Isi judul, kategori, deskripsi, & upload foto bukti"]
    IsiFormLaporan --> KlikKirim["Penyewa klik 'Kirim Laporan'"]
    
    KlikKirim --> ValidasiFoto{"Apakah foto bukti valid (< 2MB) & data lengkap?"}
    ValidasiFoto -- "Tidak" --> TampilErrorForm["Tampilkan error upload foto / deskripsi wajib"]
    TampilErrorForm --> IsiFormLaporan
    
    ValidasiFoto -- "Ya" --> SimpanKeluhanDB["Simpan keluhan status 'pending' ke database"]
    SimpanKeluhanDB --> NotifWAAdmin["Fonnte WA API mengirim notifikasi otomatis ke nomor Admin"]
    NotifWAAdmin --> AdminReviewKeluhan["Admin meninjau laporan kerusakan di panel admin"]
    
    AdminReviewKeluhan --> UbahStatusProses["Admin mengubah status keluhan menjadi 'diproses'"]
    UbahStatusProses --> LakukanTindakan["Admin melakukan perbaikan fisik / koordinasi teknisi"]
    LakukanTindakan --> IsiTanggapan["Admin mengisi tanggapan & klik 'Selesaikan Keluhan'"]
    
    IsiTanggapan --> UpdateSelesaiKeluhan["1. Update status keluhan: 'selesai'<br>2. Simpan tanggal_selesai = NOW()"]
    UpdateSelesaiKeluhan --> KirimWANotifPenyewa["Fonnte WA API mengirim notifikasi penyelesaian ke Penyewa"]
    KirimWANotifPenyewa --> Selesai([Selesai])`,

  // 2.9 Sub 8
  "flowchart TD\n    Start([Mulai]) --> BukaPengeluaran[\"Admin masuk Menu Manajemen Pengeluaran (/admin/pengeluaran)\"]": `flowchart TD
    Start([Mulai]) --> BukaPengeluaran["Admin masuk Menu Manajemen Pengeluaran (/admin/pengeluaran)"]
    BukaPengeluaran --> PilihAksi{"Pilih Operasi CRUD?"}
    
    PilihAksi -- "Tambah Pengeluaran" --> IsiFormTambah["Isi nama, kategori, tanggal, nominal, keterangan, & upload foto nota"]
    IsiFormTambah --> KlikSimpan["Admin klik Simpan"]
    KlikSimpan --> ValidasiForm{"Apakah nominal numeric & foto nota < 2MB?"}
    ValidasiForm -- "Tidak" --> TampilErrorVal["Tampilkan error form input pengeluaran"]
    TampilErrorVal --> IsiFormTambah
    ValidasiForm -- "Ya" --> SimpanDBPengeluaran["1. Upload berkas nota baru ke storage<br>2. Simpan entri pengeluaran ke database"]
    
    PilihAksi -- "Edit Pengeluaran" --> BukaFormEdit["Buka form edit data pengeluaran"]
    BukaFormEdit --> CekFotoBaru{"Apakah mengunggah berkas foto nota baru?"}
    CekFotoBaru -- "Tidak" --> SimpanPerubahan["Simpan perubahan data dengan mempertahankan foto nota lama"]
    CekFotoBaru -- "Ya" --> GantiFotoNota["1. Hapus berkas foto nota lama dari storage fisik<br>2. Upload berkas foto nota baru ke storage<br>3. Simpan data & path baru ke database"]
    
    PilihAksi -- "Hapus Pengeluaran" --> KlikHapus["Admin klik Hapus & konfirmasi modal"]
    KlikHapus --> HapusDBPengeluaran["Hapus data pengeluaran dari database"]
    HapusDBPengeluaran --> CleanupFileNota["Hapus berkas foto nota secara fisik dari storage"]
    
    SimpanDBPengeluaran --> AgregasiKeuangan["Sistem menghitung Neraca Arus Kas secara real-time"]
    SimpanPerubahan --> AgregasiKeuangan
    GantiFotoNota --> AgregasiKeuangan
    CleanupFileNota --> AgregasiKeuangan
    
    AgregasiKeuangan --> HitungArusKas["Agregasi: Kas Masuk (Tagihan Lunas + DP/Lunas Reservasi) - Kas Keluar"]
    HitungArusKas --> BukaLaporan["Admin membuka halaman Laporan Keuangan"]
    BukaLaporan --> FilterLaporan["Admin menyaring laporan berdasarkan Bulan & Tahun"]
    FilterLaporan --> KlikEkspor["Admin klik Ekspor PDF / Excel"]
    KlikEkspor --> DownloadFile["Sistem men-generate berkas & file terunduh otomatis"]
    DownloadFile --> Selesai([Selesai])`,

  // 2.10 Sub 9
  "flowchart TD\n    Start([Mulai]) --> AdminBukaKonten[\"Admin masuk halaman Pengaturan Konten di Admin Panel\"]": `flowchart TD
    Start([Mulai]) --> AdminBukaKonten["Admin masuk halaman Pengaturan Konten di Admin Panel"]
    AdminBukaKonten --> PilihModul{"Pilih Modul Konten?"}
    
    PilihModul -- "FAQ" --> CRUDFAQ["Tambah / Edit / Hapus FAQ"]
    CRUDFAQ --> SaveFAQ["Simpan field pertanyaaan, jawaban, urutan, & is_active ke database"]
    
    PilihModul -- "Galeri Kost" --> CRUDGaleri["Tambah / Edit / Hapus Galeri"]
    CRUDGaleri --> CekFotoGaleri{"Apakah mengganti file gambar galeri?"}
    CekFotoGaleri -- "Ya" --> HapusFotoGaleriLama["Hapus file gambar galeri lama dari storage"]
    HapusFotoGaleriLama --> SaveGaleri["Simpan judul, deskripsi, urutan, path gambar baru, & is_active"]
    CekFotoGaleri -- "Tidak" --> SaveGaleri
    
    PilihModul -- "Ulasan Umpan Balik" --> CRUDReview["Tambah / Edit / Hapus Ulasan Pelanggan"]
    CRUDReview --> CekFotoReview{"Apakah mengganti file foto pelanggan?"}
    CekFotoReview -- "Ya" --> HapusFotoReviewLama["Hapus file foto pelanggan lama dari storage"]
    HapusFotoReviewLama --> SaveReview["Simpan nama, pekerjaan, bintang, ulasan, & path foto"]
    CekFotoReview -- "Tidak" --> SaveReview
    
    SaveFAQ --> RenderPublik["Perubahan direfleksikan secara dinamis ke Landing Page"]
    SaveGaleri --> RenderPublik
    SaveReview --> RenderPublik
    RenderPublik --> Selesai([Selesai])`,

  // 2.11 Sub 10
  "flowchart TD\n    Start([Mulai]) --> TentukanAktor{\"Siapa yang mengakses?\"}": `flowchart TD
    Start([Mulai]) --> TentukanAktor{"Siapa yang mengakses?"}
    
    TentukanAktor -- "Administrator" --> BukaAdminPeraturan["Admin masuk Menu Peraturan Kost (/admin/peraturan)"]
    BukaAdminPeraturan --> FormPeraturan["Mengisi judul, deskripsi, pilihan 9 ikon Heroicons, & urutan"]
    FormPeraturan --> KlikSimpan["Admin klik Simpan"]
    KlikSimpan --> ValidasiPeraturan{"Apakah validasi input lolos?"}
    ValidasiPeraturan -- "Tidak" --> TampilErrorPeraturan["Tampilkan pesan error validation di form"]
    TampilErrorPeraturan --> FormPeraturan
    ValidasiPeraturan -- "Ya" --> SimpanPeraturanDB["Simpan data peraturan baru ke tabel peraturan"]
    SimpanPeraturanDB --> PerubahanDBPeraturan["Data tata tertib kost ter-update di database"]
    PerubahanDBPeraturan --> Selesai([Selesai])
    
    TentukanAktor -- "Penyewa Aktif" --> LoginPortal["Penyewa login ke portal penyewa"]
    LoginPortal --> BukaMenuPeraturan["Penyewa memilih Menu Peraturan Kost di sidebar"]
    BukaMenuPeraturan --> QueryPeraturan["Sistem query database tabel peraturan (ORDER BY urutan ASC)"]
    QueryPeraturan --> RenderTataTertib["Sistem merender peraturan lengkap dengan ikon Heroicons"]
    RenderTataTertib --> CekDarkMode{"Apakah penyewa mengaktifkan Dark Mode?"}
    CekDarkMode -- "Ya" --> ApplyDarkMode["Sistem menerapkan CSS dark:bg-slate-900 & dark:text-white"]
    CekDarkMode -- "Tidak" --> BacaPeraturan["Penyewa membaca tata tertib kost secara transparan"]
    ApplyDarkMode --> BacaPeraturan
    BacaPeraturan --> Selesai`,

  // 2.12 Sub 11
  "flowchart TD\n    Start([Mulai]) --> BukaPenyewaAdmin[\"Admin masuk Menu Manajemen Penyewa\"]": `flowchart TD
    Start([Mulai]) --> BukaPenyewaAdmin["Admin masuk Menu Manajemen Penyewa"]
    BukaPenyewaAdmin --> PilihPenyewaCheckout["Pilih Penyewa Aktif yang akan keluar dari kost"]
    PilihPenyewaCheckout --> KlikNonaktifkan["Admin klik 'Nonaktifkan Kontrak / Checkout'"]
    KlikNonaktifkan --> DBUpdateNonaktif["Sistem mengubah status penyewa menjadi 'nonaktif'"]
    DBUpdateNonaktif --> KamarTerkunci["Aturan Bisnis: Kamar kost terkait tetap berstatus 'terisi'"]
    
    KamarTerkunci --> LakukanInspeksi["Admin melakukan inspeksi fisik kebersihan & kelengkapan kamar"]
    LakukanInspeksi --> CekFasilitasRusak{"Apakah ada kerusakan fasilitas?"}
    
    CekFasilitasRusak -- "Ya" --> HitungBiayaPerbaikan["1. Hitung estimasi biaya perbaikan kerusakan<br>2. Deposit jaminan dikembalikan setelah dikurangi biaya perbaikan"]
    CekFasilitasRusak -- "Tidak" --> BalikDepositPenuh["Deposit jaminan dikembalikan secara penuh ke penyewa"]
    
    HitungBiayaPerbaikan --> BukaMenuKamar["Admin masuk Menu Manajemen Kamar"]
    BalikDepositPenuh --> BukaMenuKamar
    
    BukaMenuKamar --> UpdateKamarManual["Admin secara manual memperbarui status kamar"]
    UpdateKamarManual --> KamarKondisi{"Kamar membutuhkan perbaikan?"}
    
    KamarKondisi -- "Ya" --> SetStatusMaintenance["Ubah status kamar menjadi 'maintenance' (kamar disembunyikan)"]
    KamarKondisi -- "Tidak" --> SetStatusTersedia["Ubah status kamar menjadi 'tersedia' (kamar siap dipesan kembali)"]
    
    SetStatusMaintenance --> Selesai([Selesai])
    SetStatusTersedia --> Selesai`,

  // 2.13 Sub 12
  "flowchart TD\n    Start([Mulai]) --> BukaBerandaTamu[\"Tamu membuka Landing Page Asri Boarding House\"]": `flowchart TD
    Start([Mulai]) --> BukaBerandaTamu["Tamu membuka Landing Page Asri Boarding House"]
    BukaBerandaTamu --> KlikWidgetChat["Tamu klik widget Live Chat"]
    KlikWidgetChat --> IsiNamaHP["Tamu memasukkan Nama & Nomor HP WhatsApp"]
    IsiNamaHP --> KlikMulaiChat["Tamu klik 'Mulai Chat'"]
    
    KlikMulaiChat --> GenerateToken["Sistem generate session_token & simpan di Cookie Tamu"]
    GenerateToken --> CreateChatThread["Buat record baru di tabel guest_chat_threads status 'active'"]
    CreateChatThread --> KirimPesanTamu["Tamu mengetik & mengirim pesan pertanyaan awal"]
    KirimPesanTamu --> SimpanPesanTamu["Pesan disimpan ke tabel guest_chat_messages (sender_type = 'guest')"]
    
    SimpanPesanTamu --> TampilNotifAdmin["Sistem memunculkan alert pesan tamu masuk di panel Admin"]
    TampilNotifAdmin --> BukaChatAdmin["Admin membuka menu Guest Chats & memilih thread chat aktif"]
    BukaChatAdmin --> BalasPesanAdmin["Admin mengetik & mengirim balasan pesan"]
    BalasPesanAdmin --> SimpanPesanAdmin["Pesan disimpan ke guest_chat_messages (sender_type = 'admin')"]
    
    SimpanPesanAdmin --> AJAXPollingTamu["AJAX Polling di browser Tamu menarik pesan balasan admin (3 detik)"]
    AJAXPollingTamu --> RenderPesanAdmin["Browser merender balasan pesan di widget obrolan tamu"]
    RenderPesanAdmin --> CekDiskusiSelesai{"Diskusi selesai?"}
    
    CekDiskusiSelesai -- "Tidak" --> KirimPesanTamu
    CekDiskusiSelesai -- "Ya" --> TutupChatAdmin["Admin klik 'Tutup Chat' (Ubah status thread menjadi 'closed')"]
    TutupChatAdmin --> Selesai([Selesai])`,

  // 2.14 Sub 13
  "flowchart TD\n    Start([Mulai]) --> BukaFormLogin[\"User membuka halaman Login Portal\"]": `flowchart TD
    Start([Mulai]) --> BukaFormLogin["User membuka halaman Login Portal"]
    BukaFormLogin --> InputKredensial["User memasukkan Email dan Password default (No HP)"]
    InputKredensial --> KirimLogin["Kirim data login POST /penyewa/login"]
    
    KirimLogin --> CekKredensial{"Kredensial cocok?"}
    CekKredensial -- "Tidak" --> TampilErrorLogin["Kembalikan ke form login dengan pesan error"]
    TampilErrorLogin --> InputKredensial
    
    CekKredensial -- "Ya" --> SesiAktif["Sesi autentikasi aktif di Laravel"]
    SesiAktif --> AksesDashboard["Penyewa mengakses rute /penyewa/dashboard"]
    AksesDashboard --> SaringMiddleware["Middleware 'EnsurePasswordChanged' menyaring request"]
    
    SaringMiddleware --> CekFlag{"Apakah auth()->user()->require_password_change == true?"}
    
    CekFlag -- "Ya (Pertama kali login)" --> InterceptRedirect["Middleware mengintersepsi & paksa redirect ke /penyewa/change-password"]
    InterceptRedirect --> TampilFormUbahPwd["Render Form Ubah Password Wajib"]
    TampilFormUbahPwd --> UserInputPwd["User memasukkan Password Baru dan Konfirmasi"]
    UserInputPwd --> KlikSimpanPwd["Klik Simpan Password Baru"]
    
    KlikSimpanPwd --> ValidasiPwd{"Apakah validasi password baru lolos?<br>(Min 8 karakter, konfirmasi cocok)"}
    ValidasiPwd -- "Tidak" --> TampilErrorPwdForm["Tampilkan pesan kesalahan validasi di form"]
    TampilErrorPwdForm --> UserInputPwd
    
    ValidasiPwd -- "Ya" --> SimpanPwdDB["1. Simpan hash password baru ke tabel users<br>2. Set require_password_change = false"]
    SimpanPwdDB --> RedirectDashboard["Redirect ke Dashboard Penyewa Aktif dengan Toast Success"]
    
    CekFlag -- "Tidak (Login berikutnya)" --> RedirectDashboard
    RedirectDashboard --> TampilkanDashboardPenuh["Portal Dashboard Utama ditampilkan secara penuh"]
    TampilkanDashboardPenuh --> Selesai([Selesai])`,

  // 2.15 Sub 14
  "flowchart TD\n    Start([Mulai]) --> BukaReservasi[\"Admin masuk menu Daftar / Detail Reservasi (/admin/reservasi)\"]": `flowchart TD
    Start([Mulai]) --> BukaReservasi["Admin masuk menu Daftar / Detail Reservasi (/admin/reservasi)"]
    BukaReservasi --> CekStatusBatal{"Apakah status reservasi == 'batal'?"}
    
    CekStatusBatal -- "Tidak" --> SembunyikanTombol["Tombol Hapus Permanen disembunyikan"]
    SembunyikanTombol --> Selesai([Selesai])
    
    CekStatusBatal -- "Ya" --> TampilkanTombol["Tampilkan tombol aksi 'Hapus' khusus"]
    TampilkanTombol --> KlikHapus["Admin klik tombol 'Hapus'"]
    KlikHapus --> TampilModal["Sistem menampilkan modal konfirmasi hapus permanen"]
    
    TampilModal --> Konfirmasi{"Admin konfirmasi hapus?"}
    Konfirmasi -- "Tidak" --> BatalHapus["Batal menghapus data"]
    BatalHapus --> Selesai
    
    Konfirmasi -- "Ya" --> DBTrans["DB::transaction() running:<br>1. Hapus chat_messages terkait (Cascade Chat)<br>2. Hapus data reservasi secara permanen"]
    DBTrans --> CommitTx["Commit DB Transaction"]
    CommitTx --> ToastSuccess["Tampilkan Toast Success: 'Reservasi & chat berhasil dihapus permanen'"]
    ToastSuccess --> RedirectDaftar["Redirect ke halaman daftar reservasi ter-update"]
    RedirectDaftar --> Selesai`,

  // 2.16 Sub 15
  "flowchart TD\n    Start([Mulai]) --> WalkIn[\"Calon Penyewa transaksi via WA Pribadi / Walk-In\"]": `flowchart TD
    Start([Mulai]) --> WalkIn["Calon Penyewa transaksi via WA Pribadi / Walk-In"]
    BayarOffline["Penyewa melunasi pembayaran awal (sewa & deposit jaminan) secara offline"]
    WalkIn --> BayarOffline
    BayarOffline --> BukaMenuPenyewa["Admin membuka menu Manajemen Penyewa (/admin/penyewa)"]
    
    BukaMenuPenyewa --> KlikTambah["Admin klik 'Tambah Penyewa Baru'"]
    KlikTambah --> IsiFormPenyewa["Isi Nama, Email, NIK, No HP, No Wali, Nama Wali, Kamar, Tipe Sewa, Durasi, Tanggal Masuk, & Deposit"]
    
    IsiFormPenyewa --> KlikSimpan["Admin klik Simpan"]
    
    KlikSimpan --> ValidasiForm{"Apakah input valid?<br>(NIK 16 digit, Kamar 'tersedia')"}
    ValidasiForm -- "Tidak" --> TampilError["Kembalikan ke form dengan pesan error & toast"]
    TampilError --> IsiFormPenyewa
    
    ValidasiForm -- "Ya" --> DBTrans["DB::transaction() running"]
    DBTrans --> AutoCreateUser["1. Buat User baru (role: 'penyewa', sandi default = No HP, require_password_change = true)"]
    AutoCreateUser --> CreateProfil["2. Buat profil Penyewa (salin harga_sewa secara immutable, catat nominal deposit)"]
    CreateProfil --> LockKamar["3. Ubah status kamar menjadi 'terisi'"]
    
    LockKamar --> CallFonnte["Fonnte WA API: Kirim kredensial login akun ke No HP Penyewa"]
    CallFonnte --> CommitTx["Commit DB Transaction"]
    CommitTx --> ToastSuccess["Tampilkan Toast Success: 'Penyewa baru berhasil ditambahkan'"]
    ToastSuccess --> Selesai([Selesai])`,

  // 2.17 Sub 16
  "flowchart TD\n    Start([Mulai]) --> BukaKamarAdmin[\"Admin membuka menu Manajemen Kamar (/admin/kamar)\"]": `flowchart TD
    Start([Mulai]) --> BukaKamarAdmin["Admin membuka menu Manajemen Kamar (/admin/kamar)"]
    BukaKamarAdmin --> PilihAksi{"Pilih Operasi CRUD?"}
    
    PilihAksi -- "Tambah / Edit Kamar" --> IsiFormKamar["Isi Nomor Kamar, Lantai, Tipe, Luas, Harga Bulanan, Deskripsi, & Foto"]
    IsiFormKamar --> KlikSimpanKamar["Admin klik Simpan"]
    KlikSimpanKamar --> ValidasiKamar{"Apakah input valid?"}
    ValidasiKamar -- "Tidak" --> ErrorKamar["Tampilkan error validation & Toast di form"]
    ErrorKamar --> IsiFormKamar
    ValidasiKamar -- "Ya" --> SimpanKamarDB["1. Upload/Ganti foto kamar di storage (auto-cleanup)<br>2. Simpan / update data kamar di database"]
    SimpanKamarDB --> SelesaiKamar["Tampilkan Toast Success & perbarui daftar kamar"]
    SelesaiKamar --> Selesai([Selesai])
    
    PilihAksi -- "Hapus Kamar" --> KlikHapusKamar["Admin klik tombol 'Hapus' pada unit kamar"]
    KlikHapusKamar --> CekIkatan{"Apakah kamar terikat dengan Penyewa Aktif atau Reservasi?"}
    
    CekIkatan -- "Ya" --> CegahHapus["1. Cegah eksekusi query delete<br>2. Tampilkan Toast Error: 'Kamar dilarang dihapus!'"]
    CegahHapus --> Selesai
    
    CekIkatan -- "Tidak" --> HapusKamarDB["1. Hapus record kamar dari database<br>2. Hapus file foto kamar dari storage fisik"]
    HapusKamarDB --> ToastHapusSukses["Tampilkan Toast Success: 'Unit kamar berhasil dihapus'"]
    ToastHapusSukses --> Selesai`,

  // 2.18 Sub 17
  "flowchart TD\n    Start([Mulai]) --> UserLogin[\"Calon Penyewa membuka form login (/reservasi/login)\"]": `flowchart TD
    Start([Mulai]) --> UserLogin["Calon Penyewa membuka form login (/reservasi/login)"]
    UserLogin --> KlikGoogle["Klik tombol 'Masuk dengan Google'"]
    KlikGoogle --> RedirectGoogle["Socialite mengalihkan user ke Google Authentication page"]
    RedirectGoogle --> GoogleAuthSuccess["User sukses login di Google & kembali ke callback"]
    GoogleAuthSuccess --> FetchGoogleUser["Sistem mengambil data user Google (email, nama)"]
    
    FetchGoogleUser --> CekAdmin{"Apakah email terdaftar sebagai admin?"}
    CekAdmin -- "Ya" --> RejectOAuth["Tolak akses & redirect ke login dengan pesan kesalahan"]
    RejectOAuth --> Selesai([Selesai])
    
    CekAdmin -- "Tidak" --> GetOrCreateUser["User::firstOrCreate() berdasarkan email:<br>- default role: 'penyewa'<br>- default no_hp = null<br>- password = random bcrypt hash"]
    
    GetOrCreateUser --> LoginUser["Auth::login(user, true) & request()->session()->regenerate()"]
    LoginUser --> CheckProfileComplete{"Apakah no_hp kosong atau berawalan 'temp_'?"}
    
    CheckProfileComplete -- "Ya" --> RedirectCompleteProfile["Redirect paksa ke /profil/complete"]
    RedirectCompleteProfile --> ViewCompleteForm["Tampilkan form Pengisian Nomor WhatsApp"]
    ViewCompleteForm --> InputNoHP["Penyewa mengisi nomor WhatsApp baru"]
    InputNoHP --> SubmitForm["Submit POST /profil/complete"]
    SubmitForm --> ValidasiNoHP{"Apakah no_hp valid?<br>(Regex valid & Unik)"}
    ValidasiNoHP -- "Tidak" --> ErrorValidation["Kembalikan ke form lengkap profil dengan Toast Error"]
    ErrorValidation --> ViewCompleteForm
    ValidasiNoHP -- "Ya" --> UpdateUser["Update user.no_hp di database"]
    UpdateUser --> CheckPendingReservasi
    
    CheckProfileComplete -- "Tidak" --> CheckPendingReservasi{"Apakah user memiliki reservasi pending?"}
    
    CheckPendingReservasi -- "Ya" --> RedirectReservasi["Redirect ke halaman detail reservasi"]
    CheckPendingReservasi -- "Tidak" --> RedirectLanding["Redirect ke Beranda utama (/)"]
    
    RedirectReservasi --> Selesai
    RedirectLanding --> Selesai`,

  // 2.19 Sub 18
  "flowchart TD\n    Start([Webhook Diterima]) --> GetPayload[\"Midtrans mengirim payload callback POST ke /api/midtrans/...\"]": `flowchart TD
    Start([Webhook Diterima]) --> GetPayload["Midtrans mengirim payload callback POST ke /api/midtrans/..."]
    GetPayload --> ExtractFields["Ambil field: order_id, status_code, gross_amount, signature_key"]
    GetPayload --> LoadServerKey["Ambil serverKey dari config"]
    
    LoadServerKey --> CalcSignature["expectedSignature = hash('sha512', order_id + status_code + gross_amount + serverKey)"]
    CalcSignature --> VerifySig{"expectedSignature == signature_key?"}
    
    VerifySig -- "Tidak" --> RejectSig["Log warning 'Signature mismatch' & HTTP 403 (Unauthorized)"]
    RejectSig --> Selesai([Selesai])
    
    VerifySig -- "Ya" --> RouteCallback{"Tujuan rute callback?"}
    
    RouteCallback -- "/midtrans/callback-reservasi" --> GetReservasi["Cari data reservasi di database via order_id"]
    GetReservasi --> ReservasiStatus{"transaction_status?"}
    
    ReservasiStatus -- "settlement / capture" --> SetReservasiBayar["1. Cek skema: DP atau Lunas<br>2. Simpan record di tabel pembayaran<br>3. Pemicu stepper 5-Step di UI"]
    ReservasiStatus -- "expire / cancel / deny" --> SetReservasiBatal["1. Reservasi status -> 'batal'<br>2. Kembalikan status reservasi menjadi 'batal' (melepas kunci pemesanan kamar pada tanggal sewa terkait)"]
    
    RouteCallback -- "/midtrans/callback" --> GetTagihan["Cari data tagihan di database via order_id"]
    GetTagihan --> TagihanStatus{"transaction_status?"}
    
    TagihanStatus -- "settlement / capture" --> SetTagihanLunas["1. Update tagihan status -> 'lunas'<br>2. Simpan pembayaran<br>3. Generate nota PDF via Dompdf<br>4. Kirim WA receipt via Fonnte"]
    TagihanStatus -- "expire / cancel / deny" --> SetTagihanGagal["Update tagihan status -> 'gagal' / 'kadaluarsa'"]
    
    SetReservasiBayar --> ResponSuccess["Respon HTTP 200 (Success)"]
    SetReservasiBatal --> ResponSuccess
    SetTagihanLunas --> ResponSuccess
    SetTagihanGagal --> ResponSuccess
    
    ResponSuccess --> Selesai`,

  // 2.20 Sub 19
  "flowchart TD\n    Start([Mulai]) --> BukaFasilitas[\"Admin masuk Menu Manajemen Fasilitas (/admin/fasilitas)\"]": `flowchart TD
    Start([Mulai]) --> BukaFasilitas["Admin masuk Menu Manajemen Fasilitas (/admin/fasilitas)"]
    BukaFasilitas --> PilihAksi{"Pilih Operasi CRUD?"}
    
    PilihAksi -- "Tambah / Edit Fasilitas" --> IsiFormFasilitas["Isi Nama Fasilitas, Ikon, Deskripsi, & Status Aktif"]
    IsiFormFasilitas --> KlikSimpan["Admin klik Simpan"]
    KlikSimpan --> ValidasiFasilitas{"Apakah input valid?"}
    ValidasiFasilitas -- "Tidak" --> ErrorFasilitas["Tampilkan error validation & Toast di form"]
    ErrorFasilitas --> IsiFormFasilitas
    ValidasiFasilitas -- "Ya" --> SimpanDB["1. Simpan / update data fasilitas di database<br>2. Hapus Cache::forget('fasilitas_all')"]
    SimpanDB --> SuccessToast["Tampilkan Toast Success & muat ulang daftar"]
    SuccessToast --> Selesai([Selesai])
    
    PilihAksi -- "Hapus Fasilitas" --> KlikHapus["Admin klik tombol 'Hapus' pada fasilitas"]
    KlikHapus --> CekIkatan{"Apakah fasilitas terpasang pada satu atau lebih kamar?"}
    
    CekIkatan -- "Ya" --> CegahHapus["1. Cegah eksekusi query delete<br>2. Tampilkan Toast Error: 'Fasilitas dilarang dihapus!'"]
    CegahHapus --> Selesai
    
    CekIkatan -- "Tidak" --> HapusDB["1. Hapus record fasilitas dari database<br>2. Hapus Cache::forget('fasilitas_all')"]
    HapusDB --> SuccessHapusToast["Tampilkan Toast Success: 'Fasilitas berhasil dihapus'"]
    SuccessHapusToast --> Selesai`,

  // 2.21 Sub 20
  "flowchart TD\n    Start([Mulai]) --> BukaMenuPenyewa[\"Admin masuk Menu Manajemen Penyewa (/admin/penyewa)\"]": `flowchart TD
    Start([Mulai]) --> BukaMenuPenyewa["Admin masuk Menu Manajemen Penyewa (/admin/penyewa)"]
    BukaMenuPenyewa --> PilihAksi{"Pilih Tindakan CRUD?"}
    
    PilihAksi -- "Tambah / Edit Penyewa" --> IsiFormPenyewa["Isi NIK, No HP & Nama Wali, Deposit, Durasi, Tipe Sewa, & Kamar"]
    IsiFormPenyewa --> KlikSimpan["Admin klik Simpan"]
    KlikSimpan --> ValidasiForm{"Apakah validasi input form sukses?"}
    ValidasiForm -- "Tidak" --> TampilError["Tampilkan pesan error validation & Toast di form"]
    TampilError --> IsiFormPenyewa
    
    ValidasiForm -- "Ya" --> CekReaktivasi{"Apakah status diubah dari nonaktif menjadi aktif?"}
    CekReaktivasi -- "Tidak" --> SimpanDB["Sistem menyimpan data profil penyewa ke database"]
    SimpanDB --> SuccessToast["Tampilkan Toast Success & muat ulang daftar"]
    SuccessToast --> Selesai([Selesai])
    
    CekReaktivasi -- "Ya" --> CekKetersediaanKamar{"Apakah kamar terkait berstatus 'tersedia'?"}
    CekKetersediaanKamar -- "Tidak" --> BlockReaktivasi["Sistem membatalkan pembaruan & memicu Toast error"]
    BlockReaktivasi --> IsiFormPenyewa
    CekKetersediaanKamar -- "Ya" --> SimpanReaktivasi["1. Simpan data reaktivasi aktif<br>2. Ubah status kamar menjadi 'terisi' secara otomatis"]
    SimpanReaktivasi --> SuccessToast
    
    PilihAksi -- "Hapus Penyewa" --> KlikHapus["Admin klik tombol 'Hapus' penyewa & konfirmasi modal"]
    KlikHapus --> CekRiwayatTagihan{"Apakah penyewa memiliki riwayat tagihan?"}
    
    CekRiwayatTagihan -- "Ya" --> BlockDelete["Sistem menolak penghapusan demi integritas keuangan"]
    BlockDelete --> Selesai
    
    CekRiwayatTagihan -- "Tidak" --> HapusDB["Hapus data profil penyewa & hapus akun user dari tabel users"]
    HapusDB --> SuccessHapusToast["Tampilkan Toast Success: 'Data penyewa berhasil dihapus'"]
    SuccessHapusToast --> Selesai`
};

// Replace each block in Flowchart_Kost.md
for (const [oldBlockStart, newCode] of Object.entries(diagrams)) {
    // Find the code block of mermaid that contains oldBlockStart
    // We can split the file by ```mermaid and find the block
    const parts = content.split('```mermaid');
    for (let i = 1; i < parts.length; i++) {
        // This is a mermaid code block
        const blockContent = parts[i].split('```')[0].trim();
        // Check if blockContent starts with or matches oldBlockStart (ignoring minor whitespace/formatting)
        const normalize = (str) => str.replace(/\s+/g, ' ').trim();
        
        if (normalize(blockContent).includes(normalize(oldBlockStart.replace('flowchart TD', '')))) {
            // Replace this block's content with initHeader + newCode
            const originalBlock = parts[i].split('```')[0];
            const updatedBlock = `\n${initHeader}\n${newCode}\n`;
            parts[i] = parts[i].replace(originalBlock, updatedBlock);
            console.log(`Replaced mermaid block starting with: ${oldBlockStart.substring(0, 40)}...`);
            break;
        }
    }
    content = parts.join('```mermaid');
}

fs.writeFileSync(mdPath, content, 'utf8');
console.log('Successfully updated Flowchart_Kost.md with neat styling and cleaner texts!');
