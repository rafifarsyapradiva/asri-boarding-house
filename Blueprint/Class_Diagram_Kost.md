# Class Diagram - Asri Boarding House

Dokumen ini mendokumentasikan spesifikasi **Class Diagram (Diagram Kelas), Peta Relasi Struktural, & Alur Interaksi Objek Bisnis** untuk **Sistem Informasi Manajemen Kost Terintegrasi Payment Gateway pada Asri Boarding House**. Class diagram ini memetakan arsitektur berorientasi objek (*Object-Oriented Architecture*) sistem berbasis **Laravel 11**, yang mencakup model-model Eloquent, enumerasi status, kelas layanan bisnis (*Service Layer*), abstraksi interface (*Dependency Inversion*), Model Observers, kelas Middleware, Queue Jobs latar belakang, sistem Event/Listener (*Event-Driven Architecture*), Console Commands, Mailables/Notifications, Policies, serta seluruh relasi struktural (*Association, Aggregation, Composition, Generalization/Inheritance, Realization, dan Dependency*) secara menyeluruh, presisi, dan mudah dipahami.

Semua spesifikasi dalam dokumen ini selaras dengan dokumen blueprint lainnya:
* **Project Blueprint**: [Blueprint_Projek_Web_Asri_Boarding_House.md](file:///c:/xampp/htdocs/asri-boarding-house/Blueprint/Blueprint_Projek_Web_Asri_Boarding_House.md)
* **Use Case Specification**: [Use_Case_Diagram_Kost.md](file:///c:/xampp/htdocs/asri-boarding-house/Blueprint/Use_Case_Diagram_Kost.md)
* **Sequence Diagram Specification**: [Sequence_Diagram_Kost.md](file:///c:/xampp/htdocs/asri-boarding-house/Blueprint/Sequence_Diagram_Kost.md)
* **Component Diagram**: [Component_Diagram_Kost.md](file:///c:/xampp/htdocs/asri-boarding-house/Blueprint/Component_Diagram_Kost.md)
* **Entity Relationship Diagram**: [Entity_Relationship_Diagram_Kost.md](file:///c:/xampp/htdocs/asri-boarding-house/Blueprint/Entity_Relationship_Diagram_Kost.md)
* **Flowchart**: [Flowchart_Kost.md](file:///c:/xampp/htdocs/asri-boarding-house/Blueprint/Flowchart_Kost.md)
* **State Machine Diagram**: [State_Machine_Diagram_Kost.md](file:///c:/xampp/htdocs/asri-boarding-house/Blueprint/State_Machine_Diagram_Kost.md)
* **Deployment Diagram**: [Deployment_Diagram_Kost.md](file:///c:/xampp/htdocs/asri-boarding-house/Blueprint/Deployment_Diagram_Kost.md)

---

## 1. Diagram Kelas Global Master (Global Master Class Diagram)

Di bawah ini adalah representasi visual diagram kelas objek model, enumerasi status, kelas layanan bisnis, middleware kustom, antrean job latar belakang, sistem event/listener, serta relasi antar komponen dalam sistem yang dikelompokkan ke dalam *namespace* fungsional:

![Visual Master Class Diagram Kost](class/class_diagram_kost.png)

*Berkas skrip sumber Mermaid master tersimpan di: [`Blueprint/class/class_diagram_kost.mmd`](file:///c:/xampp/htdocs/asri-boarding-house/Blueprint/class/class_diagram_kost.mmd)*

```mermaid
classDiagram
    direction TB

    %% ==========================================
    %% 1. ENUMERASI KONSEPTUAL DOMAIN (12 ENUMS)
    %% ==========================================
    namespace Domain_Enums {
        class UserRole {
            <<enumeration>>
            ADMIN
            PENYEWA
        }
        class KamarStatus {
            <<enumeration>>
            TERSEDIA
            TERISI
            MAINTENANCE
        }
        class TipeKamar {
            <<enumeration>>
            STANDAR
            DELUXE
            VIP
        }
        class PenyewaStatus {
            <<enumeration>>
            AKTIF
            NONAKTIF
        }
        class TipeSewa {
            <<enumeration>>
            HARIAN
            MINGGUAN
            BULANAN
        }
        class TagihanStatus {
            <<enumeration>>
            PENDING
            LUNAS
            TERLAMBAT
            GAGAL
            KADALUARSA
        }
        class ReservasiStatus {
            <<enumeration>>
            PENDING
            DP
            LUNAS
            DIKONFIRMASI
            BATAL
        }
        class MetodePembayaran {
            <<enumeration>>
            MIDTRANS
            CASH
        }
        class StatusKeluhan {
            <<enumeration>>
            PENDING
            DIPROSES
            SELESAI
        }
        class StatusNotifikasi {
            <<enumeration>>
            SUKSES
            GAGAL
        }
        class ChannelNotifikasi {
            <<enumeration>>
            WHATSAPP
            EMAIL
            SYSTEM
        }
        class GuestChatStatus {
            <<enumeration>>
            ACTIVE
            CLOSED
        }
    }

    %% ==========================================
    %% 2. DOMAIN MODELS (21 MODELS)
    %% ==========================================
    namespace Domain_Models {
        class User {
            +int id
            +string nama
            +string email
            +string password
            +string no_hp
            +string nik
            +string nama_wali
            +string no_wali
            +UserRole role
            +string foto
            +boolean is_active
            +boolean require_password_change
            +penyewa() HasOne
            +reservasi() HasMany
            +chatMessages() HasMany
            +getNameAttribute() string
            +getDashboardRouteName() string
            +isProfileComplete() bool
            +isActiveTenant() bool
            +isAdmin() bool
            +anonymizeAndDelete() void
        }

        class Kamar {
            +int id
            +string nomor_kamar
            +tinyint lantai
            +TipeKamar tipe
            +float luas_m2
            +decimal harga_bulan
            +string deskripsi
            +string foto
            +KamarStatus status
            +fasilitas() BelongsToMany
            +penyewaAktif() HasOne
            +reservasi() HasMany
            +penyewa() HasMany
            +kalkulasiHargaDasar(tipe, durasi) float
            +kalkulasiHargaSewa(tipe, durasi) float
            +kalkulasiMinimalDp(total) float
        }

        class Fasilitas {
            +int id
            +string nama
            +string ikon
            +string deskripsi
            +boolean is_active
            +kamar() BelongsToMany
            +scopeAktif(query) Builder
        }

        class Penyewa {
            +int id
            +int user_id
            +int kamar_id
            +string nik
            +decimal harga_sewa
            +date tanggal_masuk
            +date tanggal_keluar
            +date tanggal_keluar_seharusnya
            +PenyewaStatus status
            +tinyint tanggal_billing
            +TipeSewa tipe_sewa
            +int durasi
            +decimal deposit
            +string nama_wali
            +string no_wali
            +tagihan() HasMany
            +keluhan() HasMany
            +getIsOverdueAttribute() bool
            +getDurasiFormattedAttribute() string
        }

        class Tagihan {
            +int id
            +int penyewa_id
            +string order_id
            +tinyint periode_bulan
            +smallint periode_tahun
            +date tanggal_tagihan
            +date tanggal_jatuh_tempo
            +decimal nominal_pokok
            +decimal nominal_denda
            +decimal nominal_total
            +tinyint bulan_keterlambatan
            +TagihanStatus status
            +MetodePembayaran metode_pembayaran
            +pembayaran() HasMany
            +penyewa() BelongsTo
            +getComputedStatusAttribute() string
            +scopeTerlambat(query) Builder
            +canBeConfirmedManually() bool
        }

        class Pembayaran {
            +int id
            +int tagihan_id
            +string transaction_id
            +string payment_type
            +string bank
            +string va_number
            +decimal nominal
            +string status_midtrans
            +int dikonfirmasi_oleh
            +datetime tanggal_bayar
            +tagihan() BelongsTo
            +dikonfirmasiOleh() BelongsTo
        }

        class Reservasi {
            +bigint id
            +int user_id
            +int kamar_id
            +int dikonfirmasi_oleh
            +int penyewa_id
            +TipeSewa tipe_sewa
            +date tanggal_mulai
            +date tanggal_selesai
            +int durasi
            +decimal total_harga
            +boolean is_dp
            +decimal nominal_dp
            +decimal nominal_sisa
            +string snap_token
            +string order_id
            +string transaction_id
            +ReservasiStatus status
            +chatMessages() HasMany
            +static isKamarTerbooking(kamarId, tglMulai, tglSelesai, excludeId) bool
        }

        class ChatMessage {
            +bigint id
            +bigint reservasi_id
            +int sender_id
            +text message
            +boolean is_read
            +reservasi() BelongsTo
            +sender() BelongsTo
        }

        class Keluhan {
            +int id
            +int penyewa_id
            +string judul
            +string kategori
            +text deskripsi
            +string foto_bukti
            +StatusKeluhan status
            +text tanggapan_admin
            +date tanggal_selesai
            +penyewa() BelongsTo
        }

        class Pengeluaran {
            +int id
            +string nama_pengeluaran
            +string kategori
            +decimal nominal
            +date tanggal_pengeluaran
            +string bukti_nota
        }

        class Setting {
            +string key
            +text value
            +static get(key, default) string
            +static getDiscountForDuration(tipe, durasi) float
        }

        class LogNotifikasi {
            +int id
            +int penyewa_id
            +int tagihan_id
            +ChannelNotifikasi channel
            +string event
            +StatusNotifikasi status
            +text pesan
            +penyewa() BelongsTo
            +tagihan() BelongsTo
        }

        class NotifikasiKhusus {
            +int id
            +string sumber
            +string tipe_aktivitas
            +int user_id
            +user() BelongsTo
            +static log(sumber, tipe, deskripsi, userId, data) void
        }

        class GuestChatThread {
            +bigint id
            +string session_token
            +string name
            +string no_hp
            +GuestChatStatus status
            +messages() HasMany
            +latestMessage() HasOne
        }

        class GuestChatMessage {
            +bigint id
            +bigint guest_chat_thread_id
            +string sender_type
            +int sender_id
            +text message
            +boolean is_read
            +thread() BelongsTo
            +sender() BelongsTo
        }

        class WhatsappClick {
            +int id
            +int kamar_id
            +string source
            +kamar() BelongsTo
        }

        class CustomerReview {
            +int id
            +string nama
            +string pekerjaan
            +tinyint bintang
            +text ulasan
            +string foto
        }

        class Faq {
            +int id
            +string pertanyaan
            +text jawaban
            +int urutan
            +boolean is_active
            +scopeAktif(query) Builder
        }

        class Peraturan {
            +int id
            +string judul
            +text deskripsi
            +string ikon
            +int urutan
        }

        class Gallery {
            +int id
            +string judul
            +string foto
            +boolean is_active
            +scopeAktif(query) Builder
        }

        class Pengumuman {
            +int id
            +string judul
            +text isi
            +boolean is_active
        }
    }

    %% ==========================================
    %% 3. SERVICE LAYER & REPORT ENGINE
    %% ==========================================
    namespace Service_Layer {
        class PdfGeneratorInterface {
            <<interface>>
            +generate(view, data, paper, orientation) string
        }

        class DompdfGenerator {
            +generate(view, data, paper, orientation) string
        }

        class BillingService {
            +generateTagihanBulanan() void
            +prosesKeterlambatan() void
            +terapkanDendaDirect(Tagihan) void
            +injectSisaDp(Penyewa, float) void
            +injectLunasPenuh(Penyewa, Reservasi, int) void
            +injectManualPenyewaLunas(Penyewa, int) void
            +perpanjangKontrakManual(Penyewa, int, int) Tagihan
        }

        class ReservasiService {
            +hitungHarga(Kamar, string, int) array
            +cekDoubleBooking(Kamar, string, string) bool
            +buatReservasi(array) Reservasi
            +batalkanReservasi(Reservasi) void
        }

        class TransisiPenyewaService {
            #BillingService billingService
            +transisi(Reservasi, int, array) Penyewa
        }

        class AdminPenyewaService {
            #BillingService billingService
            +registerPenyewa(array, int) Penyewa
        }

        class TagihanService {
            +confirmCashPayment(Tagihan, int, string) Pembayaran
            +getFilteredPaginatedTagihan(request, perPage) LengthAwarePaginator
        }

        class DashboardAnalyticsService {
            +getDashboardMetrics() array
        }

        class NotificationTemplateBuilder {
            +buildTagihanBaru(Tagihan, Penyewa) string
            +buildPembayaranBerhasil(Pembayaran) string
            +buildReminderJatuhTempo(Tagihan, Penyewa) string
            +buildNotifikasiWali(Tagihan, Penyewa) string
            +buildDenda(Tagihan, Penyewa) string
            +buildWelcomePenyewa(Penyewa) string
            +buildReservasiDibuat(Reservasi) string
            +buildReservasiDibayar(Reservasi) string
            +buildReservasiDikonfirmasi(Reservasi) string
        }

        class NotifikasiService {
            #FonnteService fonnte
            #NotificationTemplateBuilder templateBuilder
            +kirimNotifikasiTagihan(Tagihan, bool) void
            +kirimNotifikasiPembayaran(Pembayaran, bool) void
            +kirimNotifikasiWelcomePenyewa(Penyewa, bool) void
            +kirimNotifikasiTransisiPenyewa(Penyewa, bool) void
            +kirimReminderJatuhTempo(Tagihan, bool) void
            +kirimNotifikasiWali(Tagihan, bool) void
            +kirimNotifikasiKeluhanDibuat(Keluhan, bool) void
            +kirimNotifikasiKeluhanDitanggapi(Keluhan, bool) void
        }

        class FonnteService {
            +kirimPesan(nomor, pesan) bool
            +formatNomor(nomor) string
        }

        class MidtransService {
            +createSnapToken(Tagihan) array
            +createSnapTokenReservasi(Reservasi) array
        }
    }

    %% ==========================================
    %% 4. OBSERVERS, MIDDLEWARES, RULES & POLICIES
    %% ==========================================
    namespace Infra_Security {
        class PenyewaObserver {
            +creating(Penyewa) void
            +created(Penyewa) void
            +updating(Penyewa) void
            +updated(Penyewa) void
        }

        class KamarObserver {
            +created(Kamar) void
            +updated(Kamar) void
            +deleted(Kamar) void
            +restored(Kamar) void
            +forceDeleted(Kamar) void
        }

        class FasilitasObserver {
            +saved(Fasilitas) void
            +deleted(Fasilitas) void
        }

        class PengeluaranObserver {
            +created(Pengeluaran) void
            +deleted(Pengeluaran) void
        }

        class SettingObserver {
            +saved(Setting) void
            +deleted(Setting) void
        }

        class RoleMiddleware {
            +handle(request, next, role) Response
        }

        class VerifyMidtransSignature {
            +handle(request, next) Response
        }

        class EnsureTenantIsActive {
            +handle(request, next) Response
        }

        class EnsureProfileIsComplete {
            +handle(request, next) Response
        }

        class EnsurePasswordChanged {
            +handle(request, next) Response
        }

        class KamarTersediaRule {
            +validate(attribute, value, fail) void
        }

        class TanpaPenyewaAktifLainRule {
            +validate(attribute, value, fail) void
        }

        class ValidGoogleMapsEmbed {
            +passes(attribute, value) bool
        }

        class BaseResetPasswordNotification {
            <<abstract>>
            #string token
            #getRoleName()* string
            #resolveRouteName(notifiable)* string
            +toMail(notifiable) MailMessage
        }

        class AdminResetPasswordNotification {
            +ROUTE_NAME string
        }

        class PenyewaResetPasswordNotification {
            +ROUTE_NAME string
        }

        class ReservasiPolicy {
            +view(User, Reservasi) bool
            +chat(User, Reservasi) bool
            +pay(User, Reservasi) bool
            +update(User, Reservasi) bool
        }

        class TagihanPolicy {
            +view(User, Tagihan) bool
            +pay(User, Tagihan) bool
        }

        class PembayaranPolicy {
            +downloadNota(User, Pembayaran) bool
        }

        class KeluhanPolicy {
            +view(User, Keluhan) bool
        }
    }

    %% ==========================================
    %% 5. EVENTS, LISTENERS & QUEUE JOBS
    %% ==========================================
    namespace Event_Driven {
        class TagihanDibuat {
            +Tagihan tagihan
        }
        class HandleTagihanDibuat {
            +handle(TagihanDibuat) void
        }
        class KirimNotifikasiTagihanJob {
            +Tagihan tagihan
            +handle(NotifikasiService) void
        }

        class PembayaranCashDikonfirmasi {
            +Pembayaran pembayaran
        }
        class KirimEmailPembayaranCashListener {
            +handle(PembayaranCashDikonfirmasi) void
        }
        class KirimNotifikasiPembayaranJob {
            +Pembayaran pembayaran
            +handle(NotifikasiService) void
        }

        class ReservasiDibuat {
            +Reservasi reservasi
        }
        class HandleReservasiDibuat {
            +handle(ReservasiDibuat) void
        }
        class KirimNotifikasiAdminReservasiJob {
            +Reservasi reservasi
            +handle(NotifikasiService) void
        }

        class ReservasiDibayar {
            +Reservasi reservasi
        }
        class HandleReservasiDibayar {
            +handle(ReservasiDibayar) void
        }
        class KirimNotifikasiUserReservasiJob {
            +Reservasi reservasi
            +handle(NotifikasiService) void
        }

        class ReservasiDikonfirmasi {
            +Reservasi reservasi
        }
        class ProsesTransisiPenyewa {
            +handle(ReservasiDikonfirmasi) void
        }

        class NotifikasiKhususSubscriber {
            +subscribe(events) array
        }
    }

    %% Relasi Model Inti %%
    User "1" -- "0..1" Penyewa : One to One (Active Contract)
    Kamar "1" -- "*" Penyewa : One to Many (Historical)
    Kamar "*" -- "*" Fasilitas : Many to Many (Pivot kamar_fasilitas)
    Penyewa "1" -- "*" Tagihan : One to Many
    Tagihan "1" -- "*" Pembayaran : One to Many
    User "1" -- "*" Reservasi : One to Many (Customer)
    Kamar "1" -- "*" Reservasi : One to Many
    Reservasi "1" *-- "*" ChatMessage : Composition (Lifecycle Dependent)
    Penyewa "1" -- "*" Keluhan : One to Many
    GuestChatThread "1" *-- "*" GuestChatMessage : Composition (Lifecycle Dependent)
    Penyewa "1" -- "*" LogNotifikasi : One to Many
    Tagihan "1" -- "*" LogNotifikasi : One to Many
    User "1" -- "*" NotifikasiKhusus : One to Many
    Kamar "1" -- "*" WhatsappClick : One to Many
    User "1" -- "*" ChatMessage : One to Many (Sender)
    Reservasi "1" -- "0..1" Penyewa : References Activated Tenant
    User "1" -- "*" Pembayaran : Confirmed Cash By Admin

    %% Relasi Service & Realization %%
    DompdfGenerator ..|> PdfGeneratorInterface : Implements
    TransisiPenyewaService ..> BillingService : Dependency Injection
    AdminPenyewaService ..> BillingService : Dependency Injection
    NotifikasiService *-- NotificationTemplateBuilder : Composes
    NotifikasiService ..> FonnteService : Uses API Gateway

    BillingService ..> Tagihan : Creates / Updates
    BillingService ..> Penyewa : Manages
    ReservasiService ..> Reservasi : Creates
    TransisiPenyewaService ..> Penyewa : Activates
    TagihanService ..> Pembayaran : Records Cash Payment

    %% Relasi Observers & Security %%
    PenyewaObserver ..> Penyewa : Observes Lifecycle
    PenyewaObserver ..> Kamar : Locks Room 'terisi'
    KamarObserver ..> Kamar : Observes & Clears Cache
    FasilitasObserver ..> Fasilitas : Clears Cache
    PengeluaranObserver ..> Pengeluaran : Observes
    SettingObserver ..> Setting : Syncs Cache

    AdminResetPasswordNotification --|> BaseResetPasswordNotification : Extends
    PenyewaResetPasswordNotification --|> BaseResetPasswordNotification : Extends
    ReservasiPolicy ..> Reservasi : Authorizes
    TagihanPolicy ..> Tagihan : Authorizes
    PembayaranPolicy ..> Pembayaran : Authorizes
    KeluhanPolicy ..> Keluhan : Authorizes

    %% Relasi Event Pipeline Riil %%
    TagihanDibuat ..> HandleTagihanDibuat : Triggers
    HandleTagihanDibuat ..> KirimNotifikasiTagihanJob : Dispatches
    KirimNotifikasiTagihanJob ..> NotifikasiService : Executes

    PembayaranCashDikonfirmasi ..> KirimEmailPembayaranCashListener : Triggers
    KirimEmailPembayaranCashListener ..> KirimNotifikasiPembayaranJob : Dispatches
    KirimNotifikasiPembayaranJob ..> NotifikasiService : Executes

    ReservasiDibuat ..> HandleReservasiDibuat : Triggers
    HandleReservasiDibuat ..> KirimNotifikasiAdminReservasiJob : Dispatches
    KirimNotifikasiAdminReservasiJob ..> NotifikasiService : Executes

    ReservasiDibayar ..> HandleReservasiDibayar : Triggers
    HandleReservasiDibayar ..> KirimNotifikasiUserReservasiJob : Dispatches
    KirimNotifikasiUserReservasiJob ..> NotifikasiService : Executes

    ReservasiDikonfirmasi ..> ProsesTransisiPenyewa : Triggers
    ProsesTransisiPenyewa ..> NotifikasiService : Calls Welcome Notif
```

### Tabel 1.1. Taksonomi Pengelompokan Namespace Arsitektur Global

| Namespace | Jumlah Komponen | Pola Desain (*Design Pattern*) | Deskripsi Arsitektural |
| :--- | :---: | :--- | :--- |
| **`Domain_Enums`** | 12 Enums | Type-Safe Value Constants | Merepresentasikan himpunan status legal yang terikat pada integritas kolom MySQL ENUM dan business logic domain. |
| **`Domain_Models`** | 21 Models + 1 Pivot | Active Record (Eloquent ORM) | Mengelola persistensi data, enkapsulasi mutasi entitas, query scopes, dan relasi integritas antar tabel basis data. |
| **`Service_Layer`** | 12 Services & Interfaces | Service-Oriented & Dependency Inversion | Memisahkan *business logic* murni dari HTTP Controllers demi mematuhi *Single Responsibility Principle* (SRP). |
| **`Infra_Security`** | 5 Observers, 8 Middlewares/Rules, 4 Policies, 3 Notifications | Observer Pattern, Chain of Responsibility, Policy-Based Authorization, Template Method | Menjaga integritas data otomatis saat lifecycle mutasi record, memvalidasi request HTTP, otorisasi peran, dan reset kata sandi. |
| **`Event_Driven`** | 11 Events, 12 Listeners, 8 Queue Jobs | Publish-Subscribe & Asynchronous Queue Worker | Menangani komputasi berat, transmisi WhatsApp Fonnte, dan pencatatan audit log secara asinkron tanpa memblokir respon pengguna. |

---

## 2. Peta Relasi Struktural Antar Layer (Structural Relationship Map)

Untuk memberikan gambaran arsitektur sistem secara berjenjang dan seimbang, diagram berikut memetakan relasi antar layer (Tier/Subsystem) dalam sistem:

![Peta Relasi Struktural Global](class/class_relasi_struktural_global.png)

*Berkas skrip sumber Mermaid tersimpan di: [`Blueprint/class/class_relasi_struktural_global.mmd`](file:///c:/xampp/htdocs/asri-boarding-house/Blueprint/class/class_relasi_struktural_global.mmd)*

```mermaid
classDiagram
    direction TB

    %% Layer 1: Security & Middlewares %%
    namespace Security_Authorization {
        class BaseResetPasswordNotification { <<abstract>> }
        class AdminResetPasswordNotification
        class PenyewaResetPasswordNotification
        class ReservasiPolicy
        class TagihanPolicy
        class PembayaranPolicy
        class KeluhanPolicy
        class Middlewares
        class ValidationRules
    }

    %% Layer 2: Business Service Layer %%
    namespace Business_Services {
        class PdfGeneratorInterface { <<interface>> }
        class DompdfGenerator
        class BillingService
        class ReservasiService
        class TransisiPenyewaService
        class AdminPenyewaService
        class TagihanService
        class DashboardAnalyticsService
        class NotifikasiService
        class FonnteService
        class MidtransService
    }

    %% Layer 3: Event-Driven Infrastructure %%
    namespace Event_Queues {
        class TagihanDibuat
        class HandleTagihanDibuat
        class KirimNotifikasiTagihanJob
        class PembayaranCashDikonfirmasi
        class KirimEmailPembayaranCashListener
        class KirimNotifikasiPembayaranJob
        class ReservasiDibuat
        class HandleReservasiDibuat
        class KirimNotifikasiAdminReservasiJob
        class ReservasiDikonfirmasi
        class ProsesTransisiPenyewa
        class NotifikasiKhususSubscriber
    }

    %% Layer 4: Data Models & Persistence %%
    namespace Data_Persistence {
        class User
        class Kamar
        class Fasilitas
        class Penyewa
        class Tagihan
        class Pembayaran
        class Reservasi
        class ChatMessage
        class Keluhan
        class Pengeluaran
        class Setting
        class LogNotifikasi
        class NotifikasiKhusus
        class GuestChatThread
        class GuestChatMessage
        class WhatsappClick
        class PenyewaObserver
        class KamarObserver
    }

    %% Relasi Realization & Inheritance %%
    AdminResetPasswordNotification --|> BaseResetPasswordNotification : Extends
    PenyewaResetPasswordNotification --|> BaseResetPasswordNotification : Extends
    DompdfGenerator ..|> PdfGeneratorInterface : Implements

    %% Relasi Service Dependencies %%
    TransisiPenyewaService ..> BillingService : Uses
    AdminPenyewaService ..> BillingService : Uses
    TransisiPenyewaService ..> ReservasiService : Coordinates
    NotifikasiService ..> FonnteService : Uses Gateway

    %% Relasi Policy & Middlewares to Models %%
    ReservasiPolicy ..> Reservasi : Authorizes
    TagihanPolicy ..> Tagihan : Authorizes
    PembayaranPolicy ..> Pembayaran : Authorizes
    KeluhanPolicy ..> Keluhan : Authorizes
    Middlewares ..> User : Guards
    ValidationRules ..> Kamar : Validates

    %% Relasi Service to Models %%
    BillingService ..> Tagihan : Generates
    BillingService ..> Penyewa : Manages
    ReservasiService ..> Reservasi : Processes
    TagihanService ..> Pembayaran : Records Cash
    DashboardAnalyticsService ..> Pembayaran : Aggregates
    DashboardAnalyticsService ..> Pengeluaran : Aggregates

    %% Relasi Event & Queues %%
    TagihanDibuat ..> HandleTagihanDibuat : Triggers
    HandleTagihanDibuat ..> KirimNotifikasiTagihanJob : Dispatches
    KirimNotifikasiTagihanJob ..> NotifikasiService : Executes

    PembayaranCashDikonfirmasi ..> KirimEmailPembayaranCashListener : Triggers
    KirimEmailPembayaranCashListener ..> KirimNotifikasiPembayaranJob : Dispatches
    KirimNotifikasiPembayaranJob ..> NotifikasiService : Executes

    ReservasiDibuat ..> HandleReservasiDibuat : Triggers
    HandleReservasiDibuat ..> KirimNotifikasiAdminReservasiJob : Dispatches
    KirimNotifikasiAdminReservasiJob ..> NotifikasiService : Executes

    ReservasiDikonfirmasi ..> ProsesTransisiPenyewa : Triggers
    ProsesTransisiPenyewa ..> NotifikasiService : Calls Welcome Notif

    %% Relasi Domain Associations %%
    User "1" -- "0..1" Penyewa : 1 to 0..1 Active
    Kamar "1" -- "*" Penyewa : 1 to N Historical
    Kamar "*" -- "*" Fasilitas : N to M
    Penyewa "1" -- "*" Tagihan : 1 to N
    Tagihan "1" -- "*" Pembayaran : 1 to N
    User "1" -- "*" Reservasi : 1 to N
    Kamar "1" -- "*" Reservasi : 1 to N
    Reservasi "1" *-- "*" ChatMessage : Composition
    Penyewa "1" -- "*" Keluhan : 1 to N
    GuestChatThread "1" *-- "*" GuestChatMessage : Composition
    Kamar "1" -- "*" WhatsappClick : 1 to N

    %% Relasi Observers %%
    PenyewaObserver ..> Penyewa : Observes
    PenyewaObserver ..> Kamar : Locks room
    KamarObserver ..> Kamar : Observes
```

### Tabel 2.1. Taksonomi Hubungan Struktural Antar-Layer

| Layer Asal (*Caller*) | Layer Tujuan (*Callee*) | Jenis Relasi UML | Mekanisme Interaksi & Protokol | Peran & Alasan Desain |
| :--- | :--- | :---: | :--- | :--- |
| **Security & Middlewares** | **Data Persistence** | **Dependency (`..>`)** | Method Invocations via Eloquent (`User`, `Reservasi`, `Tagihan`) | Memvalidasi integritas data profil pengguna dan status hunian sebelum controller diizinkan memproses transaksi. |
| **Business Services** | **Data Persistence** | **Dependency (`..>`)** | Transactional CRUD (`DB::transaction`) | Mengeksekusi penagihan masal bulanan, mutasi hunian kamar, dan pencatatan transaksi masuk/keluar secara atomik. |
| **Business Services** | **Service Layer** | **Dependency Injection (`..>`)** | Constructor Injection | `TransisiPenyewaService` dan `AdminPenyewaService` menginjeksi `BillingService` untuk menjamin modularitas dan testability. |
| **Service Implementations** | **Service Abstractions** | **Realization (`..|>`)** | Interface Realization | `DompdfGenerator` mengimplementasikan `PdfGeneratorInterface` agar controller laporan tidak bergantung pada library konkrit DomPDF. |
| **Business Services / Controllers** | **Event-Driven Infrastructure** | **Event Dispatch (`..>`)** | `event(new EventClass($data))` | Memancarkan sinyal mutasi status sistem (seperti `TagihanDibuat`, `PembayaranBerhasil`) ke Event Bus Laravel. |
| **Event-Driven Listeners** | **Queue Background Jobs** | **Job Dispatch (`..>`)** | `JobClass::dispatch($payload)` | Meneruskan beban kerja asinkron ke tabel antrean (`jobs`) untuk diproses worker background secara non-blocking. |
| **Queue Background Jobs** | **External Gateways** | **HTTP REST API / Library** | cURL HTTP Request (Fonnte & Midtrans) | Mengirimkan pesan WhatsApp notifikasi resmi dan memverifikasi callback status payment gateway. |

---

## 3. Visualisasi Relasi Khusus & Tanggung Jawab Kelas (Dedicated Relationship Diagrams)

Untuk memahami secara mendalam hubungan antar kelas pada setiap ranah (*domain context*), berikut adalah visualisasi terfokus beserta penjelasan rinci hubungan dan tanggung jawabnya:

---

### A. Visualisasi Relasi Domain Models Inti (Association & Composition)

Diagram ini mengilustrasikan asosiasi struktural, relasi kepemilikan siklus hidup (*Composition*), dan kardinalitas antar model data:

![Relasi Domain Models](class/class_relasi_domain_models.png)

*Berkas skrip sumber Mermaid tersimpan di: [`Blueprint/class/class_relasi_domain_models.mmd`](file:///c:/xampp/htdocs/asri-boarding-house/Blueprint/class/class_relasi_domain_models.mmd)*

```mermaid
classDiagram
    direction TB

    %% Model Transaksional Inti %%
    class User {
        +int id
        +string nama
        +string email
        +string no_hp
        +string nik
        +UserRole role
        +isProfileComplete() bool
        +isActiveTenant() bool
    }

    class Kamar {
        +int id
        +string nomor_kamar
        +tinyint lantai
        +TipeKamar tipe
        +decimal harga_bulan
        +KamarStatus status
        +kalkulasiHargaSewa() float
    }

    class Fasilitas {
        +int id
        +string nama
        +string ikon
        +boolean is_active
    }

    class Penyewa {
        +int id
        +int user_id
        +int kamar_id
        +string nik
        +decimal harga_sewa
        +date tanggal_masuk
        +date tanggal_keluar_seharusnya
        +PenyewaStatus status
        +getIsOverdueAttribute() bool
    }

    class Tagihan {
        +int id
        +int penyewa_id
        +string order_id
        +date tanggal_jatuh_tempo
        +decimal nominal_pokok
        +decimal nominal_denda
        +decimal nominal_total
        +TagihanStatus status
        +scopeTerlambat() Builder
    }

    class Pembayaran {
        +int id
        +int tagihan_id
        +string transaction_id
        +string payment_type
        +decimal nominal
        +string status_midtrans
        +int dikonfirmasi_oleh
        +datetime tanggal_bayar
    }

    class Reservasi {
        +bigint id
        +int user_id
        +int kamar_id
        +date tanggal_mulai
        +date tanggal_selesai
        +decimal total_harga
        +boolean is_dp
        +ReservasiStatus status
        +static isKamarTerbooking() bool
    }

    class ChatMessage {
        +bigint id
        +bigint reservasi_id
        +int sender_id
        +text message
    }

    class WhatsappClick {
        +int id
        +int kamar_id
        +string source
    }

    class Keluhan {
        +int id
        +int penyewa_id
        +string judul
        +StatusKeluhan status
    }

    class Pengeluaran {
        +int id
        +string nama_pengeluaran
        +decimal nominal
        +date tanggal_pengeluaran
    }

    class Setting {
        +string key
        +text value
    }

    class GuestChatThread {
        +bigint id
        +string session_token
        +GuestChatStatus status
    }

    class GuestChatMessage {
        +bigint id
        +bigint guest_chat_thread_id
        +string sender_type
        +text message
    }

    class LogNotifikasi {
        +int id
        +int penyewa_id
        +int tagihan_id
        +StatusNotifikasi status
    }

    class NotifikasiKhusus {
        +int id
        +string sumber
        +string tipe_aktivitas
        +int user_id
    }

    %% Model Pendukung CMS Publik %%
    class CustomerReview {
        +int id
        +string nama
        +tinyint bintang
        +text ulasan
    }

    class Faq {
        +int id
        +string pertanyaan
        +text jawaban
        +boolean is_active
    }

    class Peraturan {
        +int id
        +string judul
        +text deskripsi
    }

    class Gallery {
        +int id
        +string judul
        +string foto
        +boolean is_active
    }

    class Pengumuman {
        +int id
        +string judul
        +text isi
        +boolean is_active
    }

    %% Relasi Asosiasi & Komposisi %%
    User "1" -- "0..1" Penyewa : One-to-One
    User "1" -- "*" Reservasi : One-to-Many
    User "1" -- "*" Pembayaran : Confirms Cash
    User "1" -- "*" ChatMessage : One-to-Many
    User "1" -- "*" GuestChatMessage : One-to-Many
    User "1" -- "*" NotifikasiKhusus : One-to-Many

    Kamar "1" -- "*" Penyewa : One-to-Many
    Kamar "*" -- "*" Fasilitas : Many-to-Many
    Kamar "1" -- "*" WhatsappClick : One-to-Many
    Kamar "1" -- "*" Reservasi : One-to-Many

    Penyewa "1" -- "*" Tagihan : One-to-Many
    Penyewa "1" -- "*" Keluhan : One-to-Many
    Penyewa "1" -- "*" LogNotifikasi : One-to-Many

    Tagihan "1" -- "*" Pembayaran : One-to-Many
    Tagihan "1" -- "*" LogNotifikasi : One-to-Many

    Reservasi "1" *-- "*" ChatMessage : Composition
    GuestChatThread "1" *-- "*" GuestChatMessage : Composition
```

#### Tabel 3.A.1. Matriks Relasi Antar-Model Domain (*Class Relationship Matrix*)

| No | Kelas Asal | Kelas Tujuan | Jenis Relasi UML | Multiplicity | Foreign Key & Integritas | Makna Semantik Bisnis |
| :---: | :--- | :--- | :---: | :---: | :--- | :--- |
| 1 | **User** | **Penyewa** | **Association** | `1` : `0..1` | `penyewa.user_id` ➔ `users.id`<br>`ON DELETE CASCADE` | Satu akun pengguna memiliki tepat 0 atau 1 kontrak hunian aktif pada satu waktu (`User::penyewa()` HasOne). |
| 2 | **Kamar** | **Penyewa** | **Association** | `1` : `*` | `penyewa.kamar_id` ➔ `kamar.id`<br>`ON DELETE RESTRICT` | Satu unit kamar fisik dapat dihuni oleh banyak penyewa sepanjang sejarah operasional kost (riwayat hunian). |
| 3 | **Kamar** | **Fasilitas** | **Association** | `*` : `*` | Pivot: `kamar_fasilitas`<br>`ON DELETE CASCADE` | Satu kamar memiliki banyak fasilitas (AC, Wi-Fi, dll.), dan satu fasilitas tersedia pada banyak unit kamar. |
| 4 | **Penyewa** | **Tagihan** | **Association** | `1` : `*` | `tagihan.penyewa_id` ➔ `penyewa.id`<br>`ON DELETE CASCADE` | Satu kontrak penyewa aktif diterbitkan banyak invoice tagihan sewa rutin bulanan serta tagihan sisa pelunasan DP. |
| 5 | **Tagihan** | **Pembayaran** | **Association** | `1` : `*` | `pembayaran.tagihan_id` ➔ `tagihan.id`<br>`ON DELETE CASCADE` | Satu invoice tagihan dapat memiliki banyak riwayat transaksi pembayaran (percobaan online Midtrans / tunai kasir). |
| 6 | **User** | **Reservasi** | **Association** | `1` : `*` | `reservasi.user_id` ➔ `users.id`<br>`ON DELETE CASCADE` | Satu akun calon penyewa dapat mengajukan banyak permohonan reservasi kamar online sepanjang waktu. |
| 7 | **Kamar** | **Reservasi** | **Association** | `1` : `*` | `reservasi.kamar_id` ➔ `kamar.id`<br>`ON DELETE RESTRICT` | Satu kamar menjadi target dari banyak pengajuan booking reservasi yang dijadwalkan tanpa bentrok jadwal sewa. |
| 8 | **Reservasi** | **ChatMessage** | **Composition** | `1` *-- `*` | `chat_messages.reservasi_id` ➔ `reservasi.id`<br>`ON DELETE CASCADE` | **Komposisi Penuh**: Pesan diskusi pra-bayar terikat mati pada reservasi. Jika reservasi dihapus, chat pesan ikut musnah. |
| 9 | **User** | **ChatMessage** | **Association** | `1` : `*` | `chat_messages.sender_id` ➔ `users.id`<br>`ON DELETE CASCADE` | Setiap butir pesan chat dikaitkan dengan akun pengguna pengirimnya (calon penyewa atau admin kost). |
| 10 | **Penyewa** | **Keluhan** | **Association** | `1` : `*` | `keluhan.penyewa_id` ➔ `penyewa.id`<br>`ON DELETE CASCADE` | Satu penyewa aktif dapat membuat banyak tiket pengaduan kerusakan fasilitas kamar kost. |
| 11 | **GuestChatThread** | **GuestChatMessage** | **Composition** | `1` *-- `*` | `guest_chat_messages.guest_chat_thread_id`<br>`ON DELETE CASCADE` | **Komposisi Penuh**: Pesan live chat pengunjung terikat mati pada sesi thread token tamu landing page. |
| 12 | **User** | **GuestChatMessage** | **Association** | `1` : `*` | `guest_chat_messages.sender_id` ➔ `users.id`<br>`ON DELETE SET NULL` | Mengaitkan balasan admin kost pada pesan tamu tanpa menghapus pesan tamu jika akun admin dimutasi. |
| 13 | **Kamar** | **WhatsappClick** | **Association** | `1` : `*` | `whatsapp_clicks.kamar_id` ➔ `kamar.id`<br>`ON DELETE CASCADE` | Mencatat akumulasi klik tombol WhatsApp CTA per unit kamar untuk analitik konversi landing page. |
| 14 | **Penyewa** | **LogNotifikasi** | **Association** | `1` : `*` | `log_notifikasi.penyewa_id` ➔ `penyewa.id`<br>`ON DELETE CASCADE` | Jejak audit riwayat pengiriman notifikasi WhatsApp tagihan, denda, dan kuitansi per penyewa. |
| 15 | **Tagihan** | **LogNotifikasi** | **Association** | `1` : `*` | `log_notifikasi.tagihan_id` ➔ `tagihan.id`<br>`ON DELETE CASCADE` | Jejak audit log pengiriman pesan WhatsApp dan surel yang secara spesifik merujuk pada nomor invoice tagihan. |
| 16 | **User** | **NotifikasiKhusus** | **Association** | `1` : `*` | `notifikasi_khusus.user_id` ➔ `users.id`<br>`ON DELETE CASCADE` | Jejak audit internal atas aktivitas mutasi kamar, persetujuan reservasi, dan keuangan yang dipicu user. |
| 17 | **Reservasi** | **Penyewa** | **Association** | `1` : `0..1` | `reservasi.penyewa_id` ➔ `penyewa.id`<br>`ON DELETE SET NULL` | Referensi penelusuran balik (*back-reference*) dari reservasi ke kontrak hunian aktif pasca-persetujuan admin. |
| 18 | **User** | **Pembayaran** | **Association** | `1` : `*` | `pembayaran.dikonfirmasi_oleh` ➔ `users.id`<br>`ON DELETE SET NULL` | Menandai admin kasir yang bertanggung jawab memvalidasi penerimaan setoran tunai (*cash manual*). |

#### Tabel 3.A.2. Rincian Tanggung Jawab Seluruh 21 Model Domain (*Class Responsibilities*)

| Nama Model | Kategori Domain | Tanggung Jawab Utama (*Single Responsibility*) | Method / Accessor Kunci |
| :--- | :--- | :--- | :--- |
| **[User](file:///c:/xampp/htdocs/asri-boarding-house/app/Models/User.php)** | Identitas & Autentikasi | Mengelola kredensial akun, otorisasi peran (`admin`/`penyewa`), kelengkapan profil wali darurat, dan soft-delete aman. | `isProfileComplete()`, `isActiveTenant()`, `isAdmin()`, `anonymizeAndDelete()`, `penyewa()` |
| **[Kamar](file:///c:/xampp/htdocs/asri-boarding-house/app/Models/Kamar.php)** | Inventaris Properti | Mengelola data fisik unit kamar, kalkulasi harga bertingkat (harian/mingguan/bulanan), dan batas minimal DP 30%. | `kalkulasiHargaDasar()`, `kalkulasiHargaSewa()`, `kalkulasiMinimalDp()`, `penyewaAktif()` |
| **[Fasilitas](file:///c:/xampp/htdocs/asri-boarding-house/app/Models/Fasilitas.php)** | Master Katalog | Menyimpan daftar fasilitas kost (AC, Wi-Fi, KM Dalam) beserta pemetaan relasi pivot ke unit kamar. | `kamar()`, `scopeAktif()`, `getEmojiAttribute()` |
| **[Penyewa](file:///c:/xampp/htdocs/asri-boarding-house/app/Models/Penyewa.php)** | Kontrak Hunian | Mengelola masa tinggal penghuni aktif, nomor billing bulanan, deposit jaminan, dan evaluasi masa sewa kedaluwarsa. | `getIsOverdueAttribute()`, `getDurasiFormattedAttribute()`, `tagihan()`, `keluhan()` |
| **[Tagihan](file:///c:/xampp/htdocs/asri-boarding-house/app/Models/Tagihan.php)** | Finansial & Penagihan | Mengelola invoice pembayaran sewa bulanan dan sisa DP, jatuh tempo tgl 10, nominal pokok, denda, dan status tagihan. | `getComputedStatusAttribute()`, `scopeTerlambat()`, `canBeConfirmedManually()`, `pembayaran()` |
| **[Pembayaran](file:///c:/xampp/htdocs/asri-boarding-house/app/Models/Pembayaran.php)** | Transaksi Kas | Mencatat riwayat transaksi masuk, jenis kanal bayar (VA/QRIS/Tunai), status webhook Midtrans, dan verifikator kasir. | `tagihan()`, `dikonfirmasiOleh()` |
| **[Reservasi](file:///c:/xampp/htdocs/asri-boarding-house/app/Models/Reservasi.php)** | Pemesanan Kamar | Mengelola pengajuan sewa online, jadwal booking bebas tabrakan, token Snap Midtrans, dan status verifikasi DP/Lunas. | `static isKamarTerbooking()`, `chatMessages()`, `scopeOverlapDengan()`, `dikonfirmasiOleh()` |
| **[ChatMessage](file:///c:/xampp/htdocs/asri-boarding-house/app/Models/ChatMessage.php)** | Komunikasi Booking | Menyimpan pesan obrolan pra-pembayaran calon penyewa dengan administrator di bawah konteks reservasi. | `reservasi()`, `sender()` |
| **[Keluhan](file:///c:/xampp/htdocs/asri-boarding-house/app/Models/Keluhan.php)** | Layanan Penghuni | Mengelola tiket pelaporan kerusakan fasilitas fisik oleh penghuni aktif, foto bukti kendala, dan tanggapan admin. | `penyewa()`, `getStatusBadgeClassAttribute()`, `getKategoriLabelAttribute()` |
| **[Pengeluaran](file:///c:/xampp/htdocs/asri-boarding-house/app/Models/Pengeluaran.php)** | Beban Operasional | Mencatat biaya pengeluaran operasional dan pemeliharaan gedung kost beserta bukti dokumen nota fisik. | Digunakan oleh `PengeluaranObserver` dan `DashboardAnalyticsService` |
| **[Setting](file:///c:/xampp/htdocs/asri-boarding-house/app/Models/Setting.php)** | Konfigurasi Sistem | Menyimpan konfigurasi dinamis berbasis key-value (kontak, rekening bank, copywriting, dan diskon durasi sewa). | `static get()`, `static getDiscountForDuration()`, `static formatWhatsapp()` |
| **[LogNotifikasi](file:///c:/xampp/htdocs/asri-boarding-house/app/Models/LogNotifikasi.php)** | Jejak Audit Notifikasi | Mencatat log pengiriman notifikasi WhatsApp Fonnte dan Surel untuk tagihan, jatuh tempo, dan kuitansi pembayaran. | `penyewa()`, `tagihan()`, `toModalPayload()` |
| **[NotifikasiKhusus](file:///c:/xampp/htdocs/asri-boarding-house/app/Models/NotifikasiKhusus.php)** | Audit Log Transaksi | Menyimpan riwayat audit terpusat atas aktivitas mutasi kamar, verifikasi booking, dan denda keterlambatan sewa. | `static log()`, `user()`, `toDetailPayload()` |
| **[GuestChatThread](file:///c:/xampp/htdocs/asri-boarding-house/app/Models/GuestChatThread.php)** | Komunikasi Tamu | Mengelola sesi obrolan *live chat* pengunjung beranda publik berdasarkan token sesi peramban web. | `messages()`, `latestMessage()` |
| **[GuestChatMessage](file:///c:/xampp/htdocs/asri-boarding-house/app/Models/GuestChatMessage.php)** | Pesan Obrolan Tamu | Menyimpan butir pesan interaksi antara pengunjung umum dan admin kost dalam sesi live chat aktif. | `thread()`, `sender()` |
| **[WhatsappClick](file:///c:/xampp/htdocs/asri-boarding-house/app/Models/WhatsappClick.php)** | Analitik Pemasaran | Mencatat metrik klik tombol WhatsApp CTA pada setiap unit kamar untuk laporan konversi marketing kost. | `kamar()` |
| **[CustomerReview](file:///c:/xampp/htdocs/asri-boarding-house/app/Models/CustomerReview.php)** | Pemasaran & Reputasi | Menyimpan ulasan rating bintang dan testimoni kepuasan pelanggan yang dipublikasikan di halaman landing page. | Digunakan pada katalog landing page publik |
| **[Faq](file:///c:/xampp/htdocs/asri-boarding-house/app/Models/Faq.php)** | Informasi Publik | Mengelola daftar tanya jawab umum seputar fasilitas, aturan, dan prosedur kost pada halaman utama. | `scopeAktif()` |
| **[Peraturan](file:///c:/xampp/htdocs/asri-boarding-house/app/Models/Peraturan.php)** | Tata Tertib Hunian | Mengelola daftar tata tertib dan tata krama kost yang ditampilkan di portal penyewa dan halaman publik. | `getBadgeColorClassAttribute()`, `getIkonLabelAttribute()` |
| **[Gallery](file:///c:/xampp/htdocs/asri-boarding-house/app/Models/Gallery.php)** | Dokumentasi Visual | Menyimpan katalog dokumentasi foto lingkungan fisik, bangunan luar, dan fasilitas bersama kost putri. | `scopeAktif()` |
| **[Pengumuman](file:///c:/xampp/htdocs/asri-boarding-house/app/Models/Pengumuman.php)** | Komunikasi Internal | Menyimpan pesan siaran pengumuman penting dari pemilik/pengelola kost kepada seluruh penghuni aktif. | Ditampilkan pada dashboard penyewa aktif |

---

### B. Visualisasi Relasi Service Layer & Dependency Inversion (DIP)

Diagram ini mengilustrasikan pemisahan logika bisnis dari controller, penggunaan *Dependency Injection*, dan penerapan *Inversion of Control*:

![Relasi Service Layer](class/class_relasi_service_layer.png)

*Berkas skrip sumber Mermaid tersimpan di: [`Blueprint/class/class_relasi_service_layer.mmd`](file:///c:/xampp/htdocs/asri-boarding-house/Blueprint/class/class_relasi_service_layer.mmd)*

```mermaid
classDiagram
    direction LR

    class PdfGeneratorInterface {
        <<interface>>
        +generate(view, data, paper, orientation) string
    }

    class DompdfGenerator {
        +generate(view, data, paper, orientation) string
    }

    class BillingService {
        +generateTagihanBulanan() void
        +prosesKeterlambatan() void
        +terapkanDendaDirect(Tagihan) void
        +injectSisaDp(Penyewa, float) void
        +injectLunasPenuh(Penyewa, Reservasi, int) void
        +injectManualPenyewaLunas(Penyewa, int) void
        +perpanjangKontrakManual(Penyewa, int, int) Tagihan
    }

    class TransisiPenyewaService {
        #BillingService billingService
        +transisi(Reservasi, int, array) Penyewa
    }

    class AdminPenyewaService {
        #BillingService billingService
        +registerPenyewa(array, int) Penyewa
    }

    class ReservasiService {
        +hitungHarga(Kamar, string, int) array
        +buatReservasi(array) Reservasi
        +cekDoubleBooking(Kamar, string, string) bool
        +batalkanReservasi(Reservasi) void
    }

    class TagihanService {
        +confirmCashPayment(Tagihan, int, string) Pembayaran
        +getFilteredPaginatedTagihan(request, perPage) LengthAwarePaginator
    }

    class DashboardAnalyticsService {
        +getDashboardMetrics() array
    }

    class NotificationTemplateBuilder {
        +buildTagihanBaru(Tagihan, Penyewa) string
        +buildPembayaranBerhasil(Pembayaran) string
        +buildReminderJatuhTempo(Tagihan, Penyewa) string
        +buildNotifikasiWali(Tagihan, Penyewa) string
        +buildDenda(Tagihan, Penyewa) string
        +buildWelcomePenyewa(Penyewa) string
    }

    class NotifikasiService {
        #FonnteService fonnte
        #NotificationTemplateBuilder templateBuilder
        +kirimNotifikasiTagihan(Tagihan, bool) void
        +kirimNotifikasiPembayaran(Pembayaran, bool) void
        +kirimNotifikasiWelcomePenyewa(Penyewa, bool) void
        +kirimNotifikasiTransisiPenyewa(Penyewa, bool) void
        +kirimReminderJatuhTempo(Tagihan, bool) void
        +kirimNotifikasiWali(Tagihan, bool) void
    }

    class FonnteService {
        +kirimPesan(string, string) bool
        +formatNomor(string) string
    }

    class MidtransService {
        +createSnapToken(Tagihan) array
        +createSnapTokenReservasi(Reservasi) array
    }

    %% Dependency & Realization %%
    DompdfGenerator ..|> PdfGeneratorInterface : Implements
    TransisiPenyewaService ..> BillingService : Dependency Injection
    AdminPenyewaService ..> BillingService : Dependency Injection
    NotifikasiService *-- NotificationTemplateBuilder : Composition
    NotifikasiService ..> FonnteService : Uses API Gateway
```

#### Tabel 3.B.1. Matriks Relasi Dependensi & Realisasi Service Layer

| No | Kelas Asal | Kelas Tujuan | Jenis Relasi UML | Alasan Arsitektural & Mekanisme Kerja |
| :---: | :--- | :--- | :---: | :--- |
| 1 | **DompdfGenerator** | **PdfGeneratorInterface** | **Realization (`..|>`)** | Penerapan prinsip **Dependency Inversion** (DIP): Controller hanya bergantung pada interface `PdfGeneratorInterface`, yang di-bind ke `DompdfGenerator` di `AppServiceProvider`. |
| 2 | **TransisiPenyewaService** | **BillingService** | **Dependency (`..>`)** | Injeksi via constructor (`#billingService`): Memanggil `injectSisaDp()` atau `injectLunasPenuh()` saat admin mengonfirmasi reservasi menjadi penyewa aktif. |
| 3 | **AdminPenyewaService** | **BillingService** | **Dependency (`..>`)** | Injeksi via constructor (`#billingService`): Memanggil `injectManualPenyewaLunas()` saat admin mendaftarkan penyewa offline walk-in langsung lunas di kasir. |
| 4 | **NotifikasiService** | **NotificationTemplateBuilder** | **Composition (`*--`)** | **Komposisi Internal**: `NotifikasiService` membuat dan memiliki siklus hidup `NotificationTemplateBuilder` langsung di dalam constructor untuk formatting pesan WhatsApp/Surel. |
| 5 | **NotifikasiService** | **FonnteService** | **Dependency (`..>`)** | Injeksi via constructor (`#fonnte`): Memanfaatkan wrapper API WhatsApp gateway untuk mengirim pesan berkas invoice, kuitansi, dan peringatan denda. |
| 6 | **BillingService** | **Tagihan & Penyewa** | **Dependency (`..>`)** | Mengorkestrasi pembuatan tagihan masal bulanan tgl 1, evaluasi denda 5% tgl 10, dan perpanjangan kontrak sewa manual. |
| 7 | **ReservasiService** | **Reservasi & Kamar** | **Dependency (`..>`)** | Mengalkulasi diskon durasi sewa, validasi bebas tabrakan jadwal (*anti-double booking*), dan menyimpan record booking baru. |
| 8 | **TagihanService** | **Pembayaran** | **Dependency (`..>`)** | Menangani pencatatan transaksi pembayaran tunai kasir dalam transaksi database atomik (`DB::transaction`). |
| 9 | **DashboardAnalyticsService**| **Pembayaran & Pengeluaran** | **Dependency (`..>`)** | Mengagregasi arus kas masuk, pengeluaran kas, tingkat okupansi kamar, dan grafik keuangan 12 bulan untuk dashboard admin. |
| 10 | **MidtransService** | **Midtrans Snap API** | **Dependency (`..>`)** | Mengirimkan parameter transaksi ke server Midtrans untuk memperoleh token Snap popup pembayaran tagihan dan reservasi online. |

#### Tabel 3.B.2. Rincian Tanggung Jawab Kelas Service Layer

| Nama Kelas / Layanan | Tipe Kelas | Tanggung Jawab Utama (*Single Responsibility*) | Method Kunci |
| :--- | :---: | :--- | :--- |
| **[PdfGeneratorInterface](file:///c:/xampp/htdocs/asri-boarding-house/app/Services/PdfGeneratorInterface.php)** | Interface | Kontrak abstraksi generator PDF berbasis view Blade, data payload, format kertas, dan orientasi halaman. | `generate(string $view, array $data, string $paper, string $orientation): string` |
| **[DompdfGenerator](file:///c:/xampp/htdocs/asri-boarding-house/app/Services/DompdfGenerator.php)** | Concrete Class | Implementasi nyata generator PDF menggunakan pustaka DomPDF untuk kebutuhan ekspor Laporan Keuangan admin. | `generate(string $view, array $data, string $paper, string $orientation): string` |
| **[BillingService](file:///c:/xampp/htdocs/asri-boarding-house/app/Services/BillingService.php)** | Core Service | Eksekusi penagihan masal otomatis tgl 1, pemantauan grace period tgl 10, denda flat 5%, alokasi DP/lunas, dan perpanjangan kontrak. | `generateTagihanBulanan()`, `prosesKeterlambatan()`, `terapkanDendaDirect()`, `injectSisaDp()`, `perpanjangKontrakManual()` |
| **[ReservasiService](file:///c:/xampp/htdocs/asri-boarding-house/app/Services/ReservasiService.php)** | Core Service | Validasi data booking, verifikasi pencegahan bentrok sewa (*double-booking guard*), kalkulasi tarif sewa, dan pembatalan sewa. | `hitungHarga()`, `cekDoubleBooking()`, `buatReservasi()`, `batalkanReservasi()` |
| **[TransisiPenyewaService](file:///c:/xampp/htdocs/asri-boarding-house/app/Services/TransisiPenyewaService.php)** | Domain Orchestrator | Mengalihkan data reservasi terkonfirmasi menjadi kontrak penyewa aktif, routing tagihan sisa, dan pemulihan soft-delete mantan penyewa. | `transisi(Reservasi $reservasi, int $adminId, array $overrideData): Penyewa` |
| **[AdminPenyewaService](file:///c:/xampp/htdocs/asri-boarding-house/app/Services/AdminPenyewaService.php)** | Domain Orchestrator | Pendaftaran manual penyewa offline/walk-in oleh admin, validasi status kamar, dan pembuatan tagihan awal lunas cash. | `registerPenyewa(array $data, int $adminId): Penyewa`, `getPenyewaQuery()` |
| **[TagihanService](file:///c:/xampp/htdocs/asri-boarding-house/app/Services/TagihanService.php)** | Core Service | Validasi dan pencatatan pembayaran tunai kasir dalam transaksi atomik database serta penyediaan kueri paginasi berfilter. | `confirmCashPayment(Tagihan $tagihan, int $adminId, string $catatan): Pembayaran`, `getFilteredPaginatedTagihan()` |
| **[DashboardAnalyticsService](file:///c:/xampp/htdocs/asri-boarding-house/app/Services/DashboardAnalyticsService.php)** | Analytics Service | Agregasi metrik analitik dashboard admin (laba bersih, total arus kas, tingkat okupansi kamar, dan grafik 12 bulan). | `getDashboardMetrics(): array`, `get12MonthsFinancialChart(): array` |
| **[NotificationTemplateBuilder](file:///c:/xampp/htdocs/asri-boarding-house/app/Services/Notifications/NotificationTemplateBuilder.php)** | Builder Service | Merakit copywriting terpusat untuk pesan tagihan, konfirmasi pembayaran, pengingat jatuh tempo, eskalasi wali, dan selamat datang. | `buildTagihanBaru()`, `buildPembayaranBerhasil()`, `buildReminderJatuhTempo()`, `buildNotifikasiWali()` |
| **[NotifikasiService](file:///c:/xampp/htdocs/asri-boarding-house/app/Services/NotifikasiService.php)** | Orchestrator Service | Mengirimkan pesan WhatsApp dan Surel terformat ke penyewa/wali serta mencatat jejak audit ke tabel `log_notifikasi`. | `kirimNotifikasiTagihan()`, `kirimNotifikasiPembayaran()`, `kirimReminderJatuhTempo()`, `kirimNotifikasiWali()` |
| **[FonnteService](file:///c:/xampp/htdocs/asri-boarding-house/app/Services/FonnteService.php)** | Gateway SDK | Klien wrapper HTTP API Fonnte WhatsApp Gateway dengan sanitasi nomor tujuan format internasional (`628xxx`). | `formatNomor(string $nomor): string`, `kirimPesan(string $target, string $pesan): bool` |
| **[MidtransService](file:///c:/xampp/htdocs/asri-boarding-house/app/Services/MidtransService.php)** | Gateway SDK | Klien integrasi Midtrans Snap API untuk penerbitan token transaksi online tagihan rutin dan reservasi kamar. | `createSnapToken(Tagihan $tagihan): array`, `createSnapTokenReservasi(Reservasi $reservasi): array` |

---

### C. Visualisasi Alur Event-Driven Architecture (EDA) & Antrean Latar Belakang

Diagram ini mengilustrasikan bagaimana peristiwa mutasi data (*Events*) memicu pendengar (*Listeners*) untuk mengeksekusi tugas latar belakang asinkron (*Queue Jobs*):

![Relasi Event Queue Pipeline](class/class_relasi_event_queue_pipeline.png)

*Berkas skrip sumber Mermaid tersimpan di: [`Blueprint/class/class_relasi_event_queue_pipeline.mmd`](file:///c:/xampp/htdocs/asri-boarding-house/Blueprint/class/class_relasi_event_queue_pipeline.mmd)*

```mermaid
classDiagram
    direction TB

    class Events {
        +ReservasiDibuat
        +ReservasiDibayar
        +ReservasiDikonfirmasi
        +TagihanDibuat
        +PembayaranBerhasil
        +PembayaranCashDikonfirmasi
        +ReminderPenyewa
        +NotifikasiWali
        +DendaDikenakan
        +KeluhanDibuat
        +KeluhanDitanggapi
    }

    class Listeners {
        +HandleReservasiDibuat
        +HandleReservasiDibayar
        +HandleReservasiDikonfirmasi
        +ProsesTransisiPenyewa
        +HandleTagihanDibuat
        +HandleReminderPenyewa
        +HandleNotifikasiWali
        +HandleDendaDikenakan
        +KirimEmailPembayaranCashListener
        +KirimNotifikasiKeluhanDibuat
        +KirimNotifikasiKeluhanDitanggapi
        +NotifikasiKhususSubscriber
    }

    class BackgroundJobs {
        +KirimNotifikasiAdminReservasiJob
        +KirimNotifikasiUserReservasiJob
        +KirimNotifikasiTagihanJob
        +KirimNotifikasiPembayaranJob
        +KirimReminderJatuhTempoJob
        +KirimNotifikasiWaliJob
        +KirimWelcomeMessageJob
        +KirimNotifikasiKustomJob
    }

    class ExternalGateways {
        +FonnteWhatsAppGateway
        +MidtransPaymentGateway
        +BrowserReceiptHtml2Pdf
    }

    Events ..> Listeners : Triggers via Event Bus
    Listeners ..> BackgroundJobs : Dispatches to Queue
    BackgroundJobs ..> ExternalGateways : Executes Async Request
    HandleTagihanDibuat ..> KirimNotifikasiTagihanJob : Dispatches
    KirimEmailPembayaranCashListener ..> KirimNotifikasiPembayaranJob : Dispatches
    HandleReservasiDibuat ..> KirimNotifikasiAdminReservasiJob : Dispatches
    HandleReservasiDibayar ..> KirimNotifikasiUserReservasiJob : Dispatches
    HandleReminderPenyewa ..> KirimReminderJatuhTempoJob : Dispatches
    HandleNotifikasiWali ..> KirimNotifikasiWaliJob : Dispatches
    HandleDendaDikenakan ..> KirimReminderJatuhTempoJob : Dispatches
```

#### Tabel 3.C.1. Matriks Alur Event, Listener, Queue Job & Gateway Eksternal

| No | Peristiwa Bisnis (*Domain Event*) | Pendengar (*Event Listener*) | Pekerjaan Antrean (*Queue Job*) | Layanan / Gateway Target | Dampak & Tanggung Jawab Operasional |
| :---: | :--- | :--- | :--- | :--- | :--- |
| 1 | **TagihanDibuat** | **HandleTagihanDibuat** | `KirimNotifikasiTagihanJob` | `NotifikasiService` ➔ Fonnte WA | Mengirim invoice tagihan bulanan baru dan tautan pembayaran ke nomor WhatsApp penyewa aktif. |
| 2 | **PembayaranCashDikonfirmasi**| **KirimEmailPembayaranCashListener**| `KirimNotifikasiPembayaranJob` | `NotifikasiService` ➔ Fonnte WA | Mengirim bukti pelunasan sewa tunai dan tautan kuitansi digital ke nomor WhatsApp penyewa. |
| 3 | **PembayaranBerhasil** | **NotifikasiKhususSubscriber** | Langsung diproses listener | Database Persistence | Mencatat riwayat audit log transaksi pembayaran lunas Midtrans ke tabel `notifikasi_khusus` untuk pengawasan admin. |
| 4 | **ReservasiDibuat** | **HandleReservasiDibuat** | `KirimNotifikasiAdminReservasiJob` | `NotifikasiService` ➔ Fonnte WA | Mengirimkan pesan alert pengajuan reservasi baru ke nomor WhatsApp pemilik/admin kost secara asinkron. |
| 5 | **ReservasiDibayar** | **HandleReservasiDibayar** | `KirimNotifikasiUserReservasiJob` | `NotifikasiService` ➔ Fonnte WA | Mengirim konfirmasi pembayaran uang muka (DP) atau lunas ke WhatsApp calon penyewa. |
| 6 | **ReservasiDikonfirmasi** | **ProsesTransisiPenyewa** & **HandleReservasiDikonfirmasi** | `KirimWelcomeMessageJob` | `NotifikasiService` ➔ Fonnte WA | Mengirim pesan selamat datang, rincian kamar, dan tata tertib kost ke penyewa yang baru diaktifkan. |
| 7 | **ReminderPenyewa** | **HandleReminderPenyewa** | `KirimReminderJatuhTempoJob` | `NotifikasiService` ➔ Fonnte WA | Mengirim pesan pengingat jatuh tempo sewa pada rentang H-3 s/d H-1 sebelum tanggal 10. |
| 8 | **NotifikasiWali** | **HandleNotifikasiWali** | `KirimNotifikasiWaliJob` | `NotifikasiService` ➔ Fonnte WA | Mengirimkan pesan eskalasi tunggakan sewa langsung ke kontak darurat orang tua/wali penyewa. |
| 9 | **DendaDikenakan** | **HandleDendaDikenakan** | `KirimReminderJatuhTempoJob` | `NotifikasiService` ➔ Fonnte WA | Mengirimkan pemberitahuan pengenaan denda keterlambatan flat 5% pasca terlewati batas grace period tgl 10. |
| 10 | **KeluhanDibuat** | **KirimNotifikasiKeluhanDibuat** | Langsung dipanggil listener | `NotifikasiService` ➔ Fonnte WA | Mengirim pesan darurat pelaporan kerusakan fasilitas kost ke nomor WhatsApp pengelola kost. |
| 11 | **KeluhanDitanggapi** | **KirimNotifikasiKeluhanDitanggapi** | Langsung dipanggil listener | `NotifikasiService` ➔ Fonnte WA | Mengirim notifikasi penyelesaian perbaikan fasilitas dan tanggapan admin ke WhatsApp pelapor. |

#### Tabel 3.C.2. Rincian Tanggung Jawab Komponen Event-Driven

| Nama Komponen | Kategori | Payload Data | Tanggung Jawab Utama (*Single Responsibility*) | Driver Eksekusi |
| :--- | :---: | :--- | :--- | :---: |
| **TagihanDibuat** | Domain Event | `Tagihan $tagihan` | Membawa data invoice baru saat digenerate cron bulanan atau diinjeksi admin. | Sinkron (Event Bus) |
| **HandleTagihanDibuat** | Listener | `TagihanDibuat $event` | Menerima event tagihan baru dan mendispatch job antrean pengiriman pesan WhatsApp. | Sinkron |
| **KirimNotifikasiTagihanJob**| Queue Job | `Tagihan $tagihan` | Mengambil data penyewa dan mengeksekusi pengiriman pesan rincian tagihan via Fonnte. | Asinkron (`jobs`) |
| **PembayaranCashDikonfirmasi**| Domain Event | `Pembayaran $pembayaran` | Membawa data pelunasan tunai kasir pasca transaksi database berhasil di-commit. | Sinkron (Event Bus) |
| **KirimEmailPembayaranCashListener**| Listener | `PembayaranCashDikonfirmasi $event` | Menerima pelunasan cash dan mendispatch job notifikasi kuitansi pelunasan. | Sinkron |
| **KirimNotifikasiPembayaranJob**| Queue Job | `Pembayaran $pembayaran` | Mengambil data transaksi dan mengirim tautan bukti nota digital ke WhatsApp penyewa. | Asinkron (`jobs`) |
| **NotifikasiKhususSubscriber**| Subscriber | Beragam Events | Mendengarkan event mutasi status (bayar, hunian, denda) dan mencatat jejak audit admin. | Sinkron |
| **ProsesTransisiPenyewa** | Listener | `ReservasiDikonfirmasi $event` | Mengorkestrasi pengiriman instruksi masuk kamar dan pesan selamat datang. | Sinkron |

---

### D. Visualisasi Pewarisan (Inheritance) & Kebijakan Otorisasi Keamanan

Diagram ini memperlihatkan pemanfaatan *Object-Oriented Inheritance* dan pemisahan otorisasi *Policy-Based Authorization*:

![Relasi Inheritance & Security](class/class_relasi_inheritance_security.png)

*Berkas skrip sumber Mermaid tersimpan di: [`Blueprint/class/class_relasi_inheritance_security.mmd`](file:///c:/xampp/htdocs/asri-boarding-house/Blueprint/class/class_relasi_inheritance_security.mmd)*

```mermaid
classDiagram
    direction TB

    class BaseResetPasswordNotification {
        <<abstract>>
        #string token
        #getRoleName()* string
        #resolveRouteName(notifiable)* string
        +resetUrl(notifiable) string
        +toMail(notifiable) MailMessage
    }

    class AdminResetPasswordNotification {
        +ROUTE_NAME string = "admin.password.reset"
        #getRoleName() string
        #resolveRouteName(notifiable) string
    }

    class PenyewaResetPasswordNotification {
        +ROUTE_NAME string = "password.reset"
        #getRoleName() string
        #resolveRouteName(notifiable) string
    }

    class Policies {
        +ReservasiPolicy
        +TagihanPolicy
        +PembayaranPolicy
        +KeluhanPolicy
    }

    class Middlewares {
        +RoleMiddleware
        +VerifyMidtransSignature
        +EnsureTenantIsActive
        +EnsureProfileIsComplete
        +EnsurePasswordChanged
    }

    class ValidationRules {
        +KamarTersediaRule
        +TanpaPenyewaAktifLainRule
        +ValidGoogleMapsEmbed
    }

    class ProtectedModels {
        +User
        +Reservasi
        +Tagihan
        +Pembayaran
        +Keluhan
        +Kamar
    }

    AdminResetPasswordNotification --|> BaseResetPasswordNotification : Extends
    PenyewaResetPasswordNotification --|> BaseResetPasswordNotification : Extends

    Middlewares ..> ProtectedModels : Guards HTTP Requests
    Policies ..> ProtectedModels : Authorizes User Actions
    ValidationRules ..> ProtectedModels : Validates Input State
```

#### Tabel 3.D.1. Matriks Pewarisan (*Inheritance*) Notifikasi Reset Kata Sandi

| Kelas Induk (*Superclass*) | Kelas Turunan (*Subclass*) | Tipe Relasi | Method Abstrak yang Di-override | Nilai Khusus Subclass |
| :--- | :--- | :---: | :--- | :--- |
| **[BaseResetPasswordNotification](file:///c:/xampp/htdocs/asri-boarding-house/app/Notifications/BaseResetPasswordNotification.php)** | **[AdminResetPasswordNotification](file:///c:/xampp/htdocs/asri-boarding-house/app/Notifications/AdminResetPasswordNotification.php)** | **Inheritance (`--|>`)** | `#getRoleName(): string`<br>`#resolveRouteName($notifiable): string` | `ROLE: 'admin'`<br>`ROUTE: 'admin.password.reset'` |
| **[BaseResetPasswordNotification](file:///c:/xampp/htdocs/asri-boarding-house/app/Notifications/BaseResetPasswordNotification.php)** | **[PenyewaResetPasswordNotification](file:///c:/xampp/htdocs/asri-boarding-house/app/Notifications/PenyewaResetPasswordNotification.php)** | **Inheritance (`--|>`)** | `#getRoleName(): string`<br>`#resolveRouteName($notifiable): string` | `ROLE: 'penyewa'`<br>`ROUTE: 'password.reset'` |

*Penerapan Pola Desain*: Menggunakan **Template Method Pattern**, di mana alur pembuatan email reset kata sandi (`toMail()`) dan konstruksi URL token (`resetUrl()`) ditentukan secara terpusat pada superclass, sementara penentuan nama rute dan konteks peran diserahkan kepada subclass masing-masing.

#### Tabel 3.D.2. Rincian Tanggung Jawab Komponen Keamanan, Middleware & Policies

| Nama Kelas | Kategori | Model / Target Pengawalan | Tanggung Jawab Utama (*Single Responsibility*) |
| :--- | :---: | :--- | :--- |
| **[RoleMiddleware](file:///c:/xampp/htdocs/asri-boarding-house/app/Http/Middleware/RoleMiddleware.php)** | Middleware | Seluruh Rute Terproteksi | Memvalidasi peran pengguna (`admin` atau `penyewa`) sebelum mengizinkan eksekusi rute controller. |
| **[VerifyMidtransSignature](file:///c:/xampp/htdocs/asri-boarding-house/app/Http/Middleware/VerifyMidtransSignature.php)** | Middleware | Endpoint Callback Webhook | Memvalidasi keaslian webhook Midtrans menggunakan hashing SHA-512 `signature_key` anti-pemalsuan. |
| **[EnsureTenantIsActive](file:///c:/xampp/htdocs/asri-boarding-house/app/Http/Middleware/EnsureTenantIsActive.php)** | Middleware | Rute Portal Penyewa (`/penyewa/*`) | Membatasi area fitur penghuni kost hanya untuk akun pengguna yang memiliki kontrak sewa aktif (`status === 'aktif'`). |
| **[EnsureProfileIsComplete](file:///c:/xampp/htdocs/asri-boarding-house/app/Http/Middleware/EnsureProfileIsComplete.php)** | Middleware | Rute Reservasi Kamar | Memastikan calon penyewa telah melengkapi NIK, nomor HP, nama wali, dan nomor wali sebelum booking. |
| **[EnsurePasswordChanged](file:///c:/xampp/htdocs/asri-boarding-house/app/Http/Middleware/EnsurePasswordChanged.php)** | Middleware | Rute Pasca-Login Pertama | Mewajibkan penggantian kata sandi default saat penyewa offline pertama kali login ke sistem. |
| **[KamarTersediaRule](file:///c:/xampp/htdocs/asri-boarding-house/app/Rules/KamarTersediaRule.php)** | Custom Rule | Unit Kamar Terpilih | Memvalidasi ketersediaan unit kamar fisik agar tidak berstatus nonaktif atau sedang masa pemeliharaan. |
| **[TanpaPenyewaAktifLainRule](file:///c:/xampp/htdocs/asri-boarding-house/app/Rules/TanpaPenyewaAktifLainRule.php)** | Custom Rule | Penetapan Kamar | Mencegah penetapan ganda kamar yang sama kepada lebih dari satu penyewa aktif pada waktu yang sama. |
| **[ValidGoogleMapsEmbed](file:///c:/xampp/htdocs/asri-boarding-house/app/Rules/ValidGoogleMapsEmbed.php)** | Custom Rule | Pengaturan Landing Page | Memvalidasi struktur tag iframe Google Maps agar aman, responsif, dan bebas injeksi script XSS berbahaya. |
| **[ReservasiPolicy](file:///c:/xampp/htdocs/asri-boarding-house/app/Policies/ReservasiPolicy.php)** | Authorization Policy | Model [Reservasi](file:///c:/xampp/htdocs/asri-boarding-house/app/Models/Reservasi.php) | Mengotorisasi izin melihat (`view`), berdiskusi chat (`chat`), membayar (`pay`), dan konfirmasi (`update`) reservasi. |
| **[TagihanPolicy](file:///c:/xampp/htdocs/asri-boarding-house/app/Policies/TagihanPolicy.php)** | Authorization Policy | Model [Tagihan](file:///c:/xampp/htdocs/asri-boarding-house/app/Models/Tagihan.php) | Mengotorisasi hak akses melihat dan membayar invoice bulanan eksklusif hanya untuk pemilik sah tagihan tersebut. |
| **[PembayaranPolicy](file:///c:/xampp/htdocs/asri-boarding-house/app/Policies/PembayaranPolicy.php)** | Authorization Policy | Model [Pembayaran](file:///c:/xampp/htdocs/asri-boarding-house/app/Models/Pembayaran.php) | Mengotorisasi pengunduhan kuitansi digital pembayaran resmi (`downloadNota`) hanya untuk pihak bersangkutan. |
| **[KeluhanPolicy](file:///c:/xampp/htdocs/asri-boarding-house/app/Policies/KeluhanPolicy.php)** | Authorization Policy | Model [Keluhan](file:///c:/xampp/htdocs/asri-boarding-house/app/Models/Keluhan.php) | Mengotorisasi hak pelaporan dan penelusuran status pengaduan kerusakan kamar oleh penghuni aktif. |

---

## 4. Visualisasi Alur Proses Bisnis & Interaksi Antar Objek Kelas (Business Workflow Diagrams)

Di bawah ini disajikan diagram alur (*flowcharts*) dan diagram sekuens (*sequences*) yang memvisualisasikan bagaimana kelas-kelas model, layanan (*service*), middleware, event listener, dan gateway eksternal berkolaborasi dalam setiap proses bisnis utama sistem:

---

### Alur 1: Alur Reservasi Kamar & Pembayaran Midtrans Snap

Diagram alur ini memvisualisasikan proses pemesanan kamar online, pemeriksaan kelengkapan profil, kalkulasi diskon & DP 30%, pencegahan bentrok jadwal (*double booking*), penerbitan token Snap Midtrans, dan verifikasi webhook SHA-512:

![Visual Alur Reservasi & Pembayaran](class/class_alur_reservasi_pembayaran.png)

*Berkas skrip sumber Mermaid tersimpan di: [`Blueprint/class/class_alur_reservasi_pembayaran.mmd`](file:///c:/xampp/htdocs/asri-boarding-house/Blueprint/class/class_alur_reservasi_pembayaran.mmd)*

```mermaid
%%{init: {
  'theme': 'base',
  'themeVariables': {
    'primaryColor': '#F0FDF4',
    'primaryTextColor': '#000000',
    'primaryBorderColor': '#16A34A',
    'lineColor': '#15803D',
    'secondaryColor': '#DCFCE7',
    'tertiaryColor': '#FFFFFF',
    'edgeLabelBackground': '#FFFFFF',
    'fontSize': '12px',
    'fontFamily': 'Inter, system-ui, sans-serif'
  }
}}%%
flowchart TD
    Start([Mulai: Calon Penyewa Pilih Kamar]) --> ValidateUser{Cek Profil User}
    ValidateUser -->|Belum Lengkap| FillProfile[Isi NIK, No HP, Kontak Wali<br>EnsureProfileIsComplete]
    FillProfile --> ValidateUser
    ValidateUser -->|Lengkap| InputBooking[Input Tipe Sewa & Durasi]
    
    InputBooking --> CalcPrice[Kamar::kalkulasiHargaSewa<br>+ kalkulasiMinimalDp 30%]
    CalcPrice --> CheckDoubleBooking{ReservasiService::cekDoubleBooking<br>isKamarTerbooking}
    
    CheckDoubleBooking -->|Kamar Sudah Terbooking| RejectBooking[Tampilkan Peringatan Kamar Terisi]
    RejectBooking --> EndFail([Selesai: Booking Gagal])
    
    CheckDoubleBooking -->|Kamar Tersedia| CreateReservation[ReservasiService::buatReservasi<br>Status: Pending, is_dp=true/false]
    CreateReservation --> DispatchEvent[Dispatch Event: ReservasiDibuat]
    DispatchEvent --> TriggerJob[Queue Job: KirimNotifikasiAdminReservasiJob]
    TriggerJob --> SendAdminWA[FonnteService: Kirim WA ke Admin Kost]
    
    CreateReservation --> CallMidtrans[MidtransService::createSnapTokenReservasi]
    CallMidtrans --> RenderSnap[Tampilkan Popup Midtrans Snap UI]
    
    RenderSnap --> PaymentAction{Aksi Pembayaran User}
    PaymentAction -->|Batal / Expired > 24 Jam| CronCancel[Artisan: reservasi:cancel-expired<br>Status -> Batal]
    CronCancel --> EndCancel([Selesai: Reservasi Dibatalkan])
    
    PaymentAction -->|Bayar Berhasil| MidtransWebhook[Midtrans Webhook Callback]
    MidtransWebhook --> VerifySig[Middleware: VerifyMidtransSignature<br>SHA-512 Hash Check]
    VerifySig --> UpdateResStatus[Update Status Reservasi -> 'dp' / 'lunas']
    UpdateResStatus --> DispatchPaidEvent[Dispatch Event: ReservasiDibayar]
    DispatchPaidEvent --> NotifyUserJob[Queue Job: KirimNotifikasiUserReservasiJob]
    NotifyUserJob --> SendUserWA[FonnteService: WA Konfirmasi Pembayaran Berhasil]
    SendUserWA --> EndSuccess([Selesai: Menunggu Verifikasi Admin])
```

---

### Alur 2: Alur Verifikasi Admin & Transisi Aktivasi Penyewa Baru

Diagram alur ini memvisualisasikan bagaimana data reservasi yang disetujui admin dialihkan menjadi profil penyewa aktif dalam transaksi database atomik, memicu `PenyewaObserver` untuk mengunci status kamar menjadi `terisi`, dan menginjeksi tagihan sisa DP:

![Visual Alur Transisi & Aktivasi Penyewa](class/class_alur_transisi_aktivasi_penyewa.png)

*Berkas skrip sumber Mermaid tersimpan di: [`Blueprint/class/class_alur_transisi_aktivasi_penyewa.mmd`](file:///c:/xampp/htdocs/asri-boarding-house/Blueprint/class/class_alur_transisi_aktivasi_penyewa.mmd)*

```mermaid
%%{init: {
  'theme': 'base',
  'themeVariables': {
    'primaryColor': '#EFF6FF',
    'primaryTextColor': '#000000',
    'primaryBorderColor': '#2563EB',
    'lineColor': '#1D4ED8',
    'secondaryColor': '#DBEAFE',
    'tertiaryColor': '#FFFFFF',
    'edgeLabelBackground': '#FFFFFF',
    'fontSize': '12px',
    'fontFamily': 'Inter, system-ui, sans-serif'
  }
}}%%
flowchart TD
    Start([Admin Membuka Permintaan Reservasi]) --> CheckAuth{ReservasiPolicy::update<br>Cek Hak Akses Admin}
    CheckAuth -->|Ditolak| Unauthorized[Kembalikan 403 Forbidden]
    Unauthorized --> EndAbort([Selesai: Akses Ditolak])
    
    CheckAuth -->|Diizinkan| SubmitConfirm[Admin Klik Setujui & Konfirmasi]
    SubmitConfirm --> CallService[TransisiPenyewaService::transisi]
    
    CallService --> BeginTx[Mulai DB::transaction]
    BeginTx --> CreatePenyewa[Penyewa::create<br>User ID, Kamar ID, Deposit, Tgl Masuk]
    
    CreatePenyewa --> TriggerObserver[PenyewaObserver::created]
    TriggerObserver --> LockRoom[Kamar::update status = 'terisi']
    TriggerObserver --> AuditLog[NotifikasiKhusus::log Transisi Hunian]
    
    LockRoom --> CheckPaymentType{Tipe Pembayaran Reservasi?}
    CheckPaymentType -->|Pembayaran DP 30%| InjectSisa[BillingService::injectSisaDp<br>Create Tagihan Sisa Pokok]
    CheckPaymentType -->|Pembayaran Lunas 100%| InjectLunas[BillingService::injectLunasPenuh<br>Create Tagihan & Pembayaran Lunas]
    
    InjectSisa --> UpdateRes[Reservasi::update status = 'dikonfirmasi', penyewa_id]
    InjectLunas --> UpdateRes
    
    UpdateRes --> CommitTx[Commit DB::transaction]
    CommitTx --> DispatchConfirmEvent[Dispatch Event: ReservasiDikonfirmasi]
    
    DispatchConfirmEvent --> TriggerListener[Listener: ProsesTransisiPenyewa]
    TriggerListener --> CallNotif[NotifikasiService::kirimNotifikasiTransisiPenyewa]
    CallNotif --> SendWelcomeWA[FonnteService: Kirim WA Selamat Datang & Kunci Kamar]
    
    SendWelcomeWA --> EndOK([Selesai: Penyewa Resmi Aktif])
```

---

### Alur 3: Alur Siklus Billing Otomatis & Penanganan Denda Keterlambatan

Diagram alur ini memvisualisasikan eksekusi cron harian dan bulanan (*Artisan Commands*) untuk penagihan masal otomatis (chunking 100 baris), pemantauan grace period (tgl 10), kalkulasi denda bertahap 5%, dan pengiriman notifikasi WhatsApp ke penyewa & nomor wali:

![Visual Alur Siklus Billing & Denda](class/class_alur_siklus_billing.png)

*Berkas skrip sumber Mermaid tersimpan di: [`Blueprint/class/class_alur_siklus_billing.mmd`](file:///c:/xampp/htdocs/asri-boarding-house/Blueprint/class/class_alur_siklus_billing.mmd)*

```mermaid
%%{init: {
  'theme': 'base',
  'themeVariables': {
    'primaryColor': '#FFFBEB',
    'primaryTextColor': '#000000',
    'primaryBorderColor': '#D97706',
    'lineColor': '#B45309',
    'secondaryColor': '#FEF3C7',
    'tertiaryColor': '#FFFFFF',
    'edgeLabelBackground': '#FFFFFF',
    'fontSize': '12px',
    'fontFamily': 'Inter, system-ui, sans-serif'
  }
}}%%
flowchart TD
    Start([Jadwal Cron Server: Tanggal 1 Tiap Bulan]) --> RunGenerateCmd[Artisan: tagihan:generate-bulanan<br>GenerateBulananTagihan]
    RunGenerateCmd --> CallBilling[BillingService::generateTagihanBulanan]
    
    CallBilling --> QueryTenants[Ambil Semua Penyewa Status 'aktif'<br>Chunking per 100 Baris]
    QueryTenants --> LoopTenant{Iterasi Setiap Penyewa}
    
    LoopTenant --> CheckExisting{Sudah Ada Tagihan Bulan Ini?}
    CheckExisting -->|Ya| SkipTenant[Lewati Penyewa]
    CheckExisting -->|Belum| CreateBill[Tagihan::create<br>Nominal Pokok = harga_sewa, Status = 'pending']
    
    CreateBill --> DispatchBillEvent[Dispatch Event: TagihanDibuat]
    DispatchBillEvent --> DispatchBillJob[Queue Job: KirimNotifikasiTagihanJob]
    DispatchBillJob --> SendBillWA[FonnteService: Kirim WA Rincian Invoice & Link Bayar]
    
    SkipTenant --> NextTenant[Lanjut ke Penyewa Berikutnya]
    SendBillWA --> NextTenant
    NextTenant --> CheckMoreTenants{Masih Ada Penyewa?}
    CheckMoreTenants -->|Ya| LoopTenant
    CheckMoreTenants -->|Selesai| EndGenerate([Tagihan Bulanan Berhasil Dibuat])
    
    EndGenerate -.-> DailyCron([Jadwal Cron Harian: Cek Jatuh Tempo])
    DailyCron --> RunLateCmd[Artisan: tagihan:proses-keterlambatan<br>ProsesKeterlambatanTagihan]
    RunLateCmd --> CallProcessLate[BillingService::prosesKeterlambatan]
    
    CallProcessLate --> CheckDueDate{Lewat Tanggal Jatuh Tempo tgl 10?}
    CheckDueDate -->|H-3 sd H-1 Jatuh Tempo| SendReminderJob[Queue Job: KirimReminderJatuhTempoJob<br>WhatsApp Pengingat Jatuh Tempo]
    CheckDueDate -->|Lewat Jatuh Tempo & Belum Denda| ApplyFine[BillingService::terapkanDendaDirect<br>Nominal Denda = 5% Flat]
    
    ApplyFine --> UpdateLateStatus[Update Status Tagihan -> 'terlambat']
    UpdateLateStatus --> DispatchFineEvent[Dispatch Event: DendaDikenakan]
    DispatchFineEvent --> NotifyLateJob[Queue Job: KirimReminderJatuhTempoJob + KirimNotifikasiWaliJob]
    NotifyLateJob --> SendLateWA[FonnteService: Kirim WA Peringatan Denda ke Penyewa & Wali]
    SendLateWA --> EndBillingCycle([Selesai Siklus Penagihan])
```

---

### Alur 4: Alur Pembayaran Tagihan & Penerbitan Kuitansi Digital (Client-Side Rendering)

Diagram alur ini memvisualisasikan bagaimana pembayaran lunas (baik online via Midtrans maupun tunai di kasir) memicu notifikasi bukti lunas WhatsApp dan perenderan kuitansi resmi langsung di sisi peramban (*Client-Side Rendering*) menggunakan pustaka `html2pdf.js` dengan prinsip *Zero Server Load*:

![Visual Alur Pembayaran & Kuitansi PDF](class/class_alur_pembayaran_kuitansi_pdf.png)

*Berkas skrip sumber Mermaid tersimpan di: [`Blueprint/class/class_alur_pembayaran_kuitansi_pdf.mmd`](file:///c:/xampp/htdocs/asri-boarding-house/Blueprint/class/class_alur_pembayaran_kuitansi_pdf.mmd)*

```mermaid
%%{init: {
  'theme': 'base',
  'themeVariables': {
    'primaryColor': '#F5F3FF',
    'primaryTextColor': '#000000',
    'primaryBorderColor': '#7C3AED',
    'lineColor': '#6D28D9',
    'secondaryColor': '#EDE9FE',
    'tertiaryColor': '#FFFFFF',
    'edgeLabelBackground': '#FFFFFF',
    'fontSize': '12px',
    'fontFamily': 'Inter, system-ui, sans-serif'
  }
}}%%
flowchart TD
    Start([Penyewa Melakukan Pembayaran]) --> PaymentMethod{Pilih Metode Bayar}
    
    PaymentMethod -->|Online: Midtrans Snap| RequestSnap[TagihanController -> MidtransService::createSnapToken]
    RequestSnap --> ShowSnap[Buka Snap Popup: VA / GoPay / ShopeePay / QRIS]
    ShowSnap --> PayOnline[Penyewa Selesaikan Pembayaran di Bank / e-Wallet]
    PayOnline --> MidtransCallback[Callback HTTP Webhook Midtrans]
    
    MidtransCallback --> VerifyHMAC[Middleware: VerifyMidtransSignature<br>Hash SHA-512 Signature Key]
    VerifyHMAC -->|Tidak Valid| Reject401[Tolak Callback: 401 Unauthorized]
    Reject401 --> EndFail([Selesai: Transaksi Ditolak])
    
    VerifyHMAC -->|Valid| RecordMidtransPay[Pembayaran::create<br>Simpan transaction_id & Response JSON]
    
    PaymentMethod -->|Offline: Tunai / Cash Kasir| AdminCash[Admin Buka Modal Bayar Cash]
    AdminCash --> CallCashService[TagihanService::confirmCashPayment<br>Penyimpanan DB::transaction]
    CallCashService --> RecordCashPay[Pembayaran::create<br>dikonfirmasi_oleh = Admin ID]
    
    RecordMidtransPay --> UpdateTagihanLunas[Tagihan::update status = 'lunas']
    RecordCashPay --> UpdateTagihanLunas
    
    UpdateTagihanLunas --> CashEventCheck{Jenis Pembayaran?}
    CashEventCheck -->|Pembayaran Cash| DispatchCashEvent[Dispatch Event: PembayaranCashDikonfirmasi]
    CashEventCheck -->|Pembayaran Midtrans| DispatchOnlineEvent[Dispatch Event: PembayaranBerhasil]
    
    DispatchCashEvent --> ListenerCash[Listener: KirimEmailPembayaranCashListener]
    ListenerCash --> QueueNotifJob[Queue Job: KirimNotifikasiPembayaranJob]
    
    DispatchOnlineEvent --> SubscriberAudit[NotifikasiKhususSubscriber: Audit Log Keuangan]
    DispatchOnlineEvent --> DirectNotif[NotifikasiService::kirimNotifikasiPembayaran]
    
    QueueNotifJob --> SendReceiptWA[FonnteService: Kirim WA Bukti Lunas + Link Kuitansi Web]
    DirectNotif --> SendReceiptWA
    
    SendReceiptWA --> UserOpenReceipt[Penyewa / Admin Akses Halaman Kuitansi di Web Browser]
    UserOpenReceipt --> ClientRender["Client-Side PDF Rendering (html2pdf.js)<br>Zero Server Load & Instant Download A5"]
    ClientRender --> EndSuccess([Selesai: Pembayaran Berhasil & Kuitansi Tersedia])
```

---

### Alur 5: Alur Siklus Hidup Pengaduan Keluhan Fasilitas (*Complaint Lifecycle*)

Diagram alur ini memvisualisasikan siklus hidup tiket pengaduan keluhan fasilitas oleh penyewa aktif, pengiriman notifikasi WhatsApp ke admin, pemrosesan perbaikan fisik, hingga pengiriman konfirmasi penyelesaian ke penyewa:

![Visual Alur Pengaduan Keluhan](class/class_alur_pengaduan_keluhan.png)

*Berkas skrip sumber Mermaid tersimpan di: [`Blueprint/class/class_alur_pengaduan_keluhan.mmd`](file:///c:/xampp/htdocs/asri-boarding-house/Blueprint/class/class_alur_pengaduan_keluhan.mmd)*

```mermaid
%%{init: {
  'theme': 'base',
  'themeVariables': {
    'primaryColor': '#FFF1F2',
    'primaryTextColor': '#000000',
    'primaryBorderColor': '#E11D48',
    'lineColor': '#BE123C',
    'secondaryColor': '#FFE4E6',
    'tertiaryColor': '#FFFFFF',
    'edgeLabelBackground': '#FFFFFF',
    'fontSize': '12px',
    'fontFamily': 'Inter, system-ui, sans-serif'
  }
}}%%
flowchart TD
    Start([Penyewa Melaporkan Keluhan]) --> CheckActiveTenant{Middleware: EnsureTenantIsActive<br>Penyewa Status 'aktif'?}
    CheckActiveTenant -->|Tidak Aktif| RejectAccess[Akses Ditolak]
    RejectAccess --> EndAbort([Selesai])
    
    CheckActiveTenant -->|Aktif| FillComplaint[Penyewa Isi Form Keluhan<br>Judul, Kategori, Deskripsi, Foto Bukti]
    FillComplaint --> SaveComplaint[Keluhan::create<br>Status: 'pending']
    
    SaveComplaint --> DispatchCreateEvent[Dispatch Event: KeluhanDibuat]
    DispatchCreateEvent --> TriggerAdminNotif[Listener: KirimNotifikasiKeluhanDibuat]
    TriggerAdminNotif --> SendAdminWA[FonnteService: Kirim WA Alert Keluhan Baru ke Admin Kost]
    
    SendAdminWA --> AdminReview[Admin Review Tiket Keluhan di Dashboard]
    AdminReview --> ProcessComplaint[Admin Update Status: 'diproses'<br>Tugaskan Teknisi / Perbaikan]
    
    ProcessComplaint --> FinishWork[Pekerjaan Perbaikan Fisik Selesai]
    FinishWork --> AdminRespond[Admin Isi Tanggapan & Solusi<br>Keluhan::update status = 'selesai', tanggapan_admin]
    
    AdminRespond --> DispatchResponseEvent[Dispatch Event: KeluhanDitanggapi]
    DispatchResponseEvent --> TriggerTenantNotif[Listener: KirimNotifikasiKeluhanDitanggapi]
    TriggerTenantNotif --> SendTenantWA[FonnteService: Kirim WA Notifikasi Keluhan Selesai ke Penyewa]
    
    SendTenantWA --> AuditLog[NotifikasiKhusus::log Penyelesaian Keluhan]
    AuditLog --> EndOK([Selesai: Tiket Keluhan Tuntas])
```

---

### Alur 6: Alur Agregasi Arus Kas & Analitik Dashboard (*Financial Flow*)

Diagram alur ini memvisualisasikan agregasi metrik keuangan oleh `DashboardAnalyticsService` dari tabel `reservasi`, `pembayaran`, dan `pengeluaran` untuk menghasilkan laba bersih, tingkat okupansi kamar, dan ekspor laporan PDF/Excel:

![Visual Alur Integrasi Arus Kas](class/class_alur_integrasi_arus_kas.png)

*Berkas skrip sumber Mermaid tersimpan di: [`Blueprint/class/class_alur_integrasi_arus_kas.mmd`](file:///c:/xampp/htdocs/asri-boarding-house/Blueprint/class/class_alur_integrasi_arus_kas.mmd)*

```mermaid
%%{init: {
  'theme': 'base',
  'themeVariables': {
    'primaryColor': '#F0FDFA',
    'primaryTextColor': '#000000',
    'primaryBorderColor': '#0D9488',
    'lineColor': '#0F766E',
    'secondaryColor': '#CCFBF1',
    'tertiaryColor': '#FFFFFF',
    'edgeLabelBackground': '#FFFFFF',
    'fontSize': '12px',
    'fontFamily': 'Inter, system-ui, sans-serif'
  }
}}%%
flowchart TD
    Start([Admin Membuka Dashboard Finansial]) --> RequestAnalytics[DashboardController -> DashboardAnalyticsService::getDashboardMetrics]
    
    RequestAnalytics --> AggregateCashIn[Agregasi Kas Masuk]
    RequestAnalytics --> AggregateCashOut[Agregasi Kas Keluar]
    RequestAnalytics --> AggregateOccupancy[Agregasi Okupansi Kamar]
    
    AggregateCashIn --> SumReservasi["Hitung Kas Reservasi:<br>Reservasi status 'dp' (nominal_dp) + 'lunas' (total_harga)"]
    AggregateCashIn --> SumTagihan["Hitung Kas Tagihan Sewa:<br>Pembayaran tagihan status 'lunas' (nominal)"]
    SumReservasi --> TotalPemasukan["Total Pemasukan = Kas Reservasi + Kas Tagihan"]
    SumTagihan --> TotalPemasukan
    
    AggregateCashOut --> SumExpenses["Hitung Kas Keluar:<br>Pengeluaran::sum('nominal') berdasarkan range tanggal & nota"]
    SumExpenses --> TotalPengeluaran["Total Pengeluaran = Kas Beban & Perawatan"]
    
    AggregateOccupancy --> CountRooms["Kamar::count() total, terisi, tersedia, maintenance"]
    CountRooms --> CalcOccupancyRate["Tingkat Okupansi (%) = (Terisi / Total) * 100%"]
    
    TotalPemasukan --> CalcProfit["LABA BERSIH = Total Pemasukan - Total Pengeluaran"]
    TotalPengeluaran --> CalcProfit
    
    CalcProfit --> RenderView[Render Data ke Dashboard Blade View & Chart.js Visual]
    CalcOccupancyRate --> RenderView
    
    RenderView --> ExportAction{Aksi Ekspor Laporan Admin}
    ExportAction -->|Ekspor PDF Laporan Keuangan| GeneratePdfLaporan[LaporanController -> DompdfGenerator<br>Download Laporan PDF]
    ExportAction -->|Ekspor Spreadsheet| GenerateExcel[Download File CSV / Excel]
    
    GeneratePdfLaporan --> End([Selesai])
    GenerateExcel --> End
```

---

### Alur 7: Alur Siklus Hidup Penyewa End-to-End (*Tenant Lifecycle Sequence*)

Diagram sekuens komprehensif ini merangkum kolaborasi objek antar kelas sepanjang masa sewa penyewa dari pendaftaran booking awal hingga checkout fisik kamar:

![Visual Alur Siklus Hidup Penyewa](class/class_alur_siklus_hidup_penyewa.png)

*Berkas skrip sumber Mermaid tersimpan di: [`Blueprint/class/class_alur_siklus_hidup_penyewa.mmd`](file:///c:/xampp/htdocs/asri-boarding-house/Blueprint/class/class_alur_siklus_hidup_penyewa.mmd)*

```mermaid
sequenceDiagram
    autonumber
    actor CalonPenyewa as Calon Penyewa
    actor Admin as Admin Kost
    participant Web as Web Interface (Blade)
    participant RS as ReservasiService
    participant MS as MidtransService
    participant TPS as TransisiPenyewaService
    participant PO as PenyewaObserver
    participant BS as BillingService
    participant NS as NotifikasiService
    participant DB as MySQL Database

    Note over CalonPenyewa, DB: FASE 1: PENDAFTARAN & PEMBAYARAN BOOKING
    CalonPenyewa->>Web: Pilih Kamar & Ajukan Reservasi (DP/Lunas)
    Web->>RS: buatReservasi(data)
    RS->>DB: Lock Kamar & Simpan Reservasi (Status: Pending)
    RS->>MS: createSnapTokenReservasi(reservasi)
    MS-->>Web: Snap Token Midtrans
    CalonPenyewa->>MS: Bayar DP / Lunas via VA / QRIS
    MS-->>Web: Callback Webhook (Settlement)
    Web->>DB: Update Reservasi (Status: DP / Lunas)

    Note over Admin, DB: FASE 2: VERIFIKASI & AKTIVASI PENYEWA
    Admin->>Web: Setujui & Konfirmasi Reservasi
    Web->>TPS: transisi(reservasi, adminId)
    TPS->>DB: Create Penyewa (Status: Aktif)
    activate PO
    PO->>DB: Update Kamar (Status: Terisi)
    deactivate PO
    alt Jika DP
        TPS->>BS: injectSisaDp(penyewa, nominalSisa)
        BS->>DB: Create Tagihan Sisa DP Bulan 1
    else Jika Lunas
        TPS->>BS: injectLunasPenuh(penyewa, reservasi)
        BS->>DB: Create Tagihan & Pembayaran Lunas
    end
    TPS->>NS: kirimNotifikasiTransisiPenyewa(penyewa)
    NS-->>CalonPenyewa: WhatsApp Welcome & Instruksi Masuk

    Note over BS, DB: FASE 3: SIKLUS PENAGIHAN BULANAN RUTIN
    BS->>DB: Cron tgl 1: Generate Tagihan Bulanan (Chunk 100)
    BS->>NS: kirimNotifikasiTagihan(tagihan)
    NS-->>CalonPenyewa: WhatsApp Rincian Tagihan + Link Bayar
    alt Terlambat Melewati Tgl 10
        BS->>DB: Cron: Terapkan Denda Flat 5%
        BS->>NS: kirimReminderJatuhTempo(tagihan)
        NS-->>CalonPenyewa: WhatsApp Pengingat Denda
    end

    Note over CalonPenyewa, DB: FASE 4: PEMBAYARAN SEWA & KUITANSI PDF
    CalonPenyewa->>Web: Bayar Tagihan (Midtrans / Tunai)
    Web->>DB: Update Tagihan (Lunas) & Simpan Pembayaran
    Web->>NS: kirimNotifikasiPembayaran(pembayaran)
    NS-->>CalonPenyewa: WhatsApp Bukti Lunas + Link PDF Kuitansi

    Note over Admin, DB: FASE 5: CHECKOUT & INSPEKSI FISIK KAMAR
    Admin->>Web: Proses Checkout Penyewa (Status: Nonaktif)
    Web->>DB: Update Penyewa (Status: Nonaktif, tgl_keluar)
    Note over PO: PenyewaObserver tidak langsung ubah kamar ke 'tersedia'
    Admin->>Web: Inspeksi Fisik Kamar Selesai -> Ubah Kamar ke 'Tersedia'
    Web->>DB: Update Kamar (Status: Tersedia)
```

---

## 5. Rincian Tanggung Jawab Seluruh Kelas (Complete Class Responsibilities)

Berikut adalah ringkasan tanggung jawab tunggal (*Single Responsibility*) seluruh kelas sistem yang dipetakan ke dalam kode sumber:

### A. Model Data Eloquent (21 Models & 1 Pivot)
1. **[User](file:///c:/xampp/htdocs/asri-boarding-house/app/Models/User.php)**: Mengelola akun otentikasi, otorisasi multi-role (`admin`/`penyewa`), kontak wali darurat (`nama_wali`, `no_wali`), pengecekan kelengkapan data diri (`isProfileComplete`), verifikasi status tenant aktif (`isActiveTenant`), status admin (`isAdmin`), dan pembersihan data aman saat soft delete (`anonymizeAndDelete`).
2. **[Kamar](file:///c:/xampp/htdocs/asri-boarding-house/app/Models/Kamar.php)**: Mengelola atribut fisik unit kamar (lantai, tipe sewa, luas m2, foto, harga bulanan), perhitungan dinamis tarif sewa harian/mingguan/bulanan (`kalkulasiHargaSewa`), minimal DP 30% (`kalkulasiMinimalDp`), filter status ketersediaan, dan paket promo landing page.
3. **[Fasilitas](file:///c:/xampp/htdocs/asri-boarding-house/app/Models/Fasilitas.php)**: Menyimpan master katalog fasilitas kost (AC, WiFi, Kamar Mandi Dalam, dll.) dengan relasi Many-to-Many (`kamar_fasilitas`) dan query scope fasilitas aktif.
4. **[Penyewa](file:///c:/xampp/htdocs/asri-boarding-house/app/Models/Penyewa.php)**: Mengelola kontrak hunian penyewa aktif, NIK penghuni, uang jaminan deposit, billing bulanan rutin, tanggal checkout aktual, kontak wali darurat, dan deteksi keterlambatan masa sewa (`getIsOverdueAttribute`).
5. **[Tagihan](file:///c:/xampp/htdocs/asri-boarding-house/app/Models/Tagihan.php)**: Mengelola invoice sewa bulanan dan sisa DP, tanggal jatuh tempo (tgl 10), breakdown nominal pokok dan nominal denda, status tagihan (`pending`, `lunas`, `terlambat`, `gagal`, `kadaluarsa`), metode pembayaran, serta konfirmasi cash manual.
6. **[Pembayaran](file:///c:/xampp/htdocs/asri-boarding-house/app/Models/Pembayaran.php)**: Mencatat riwayat transaksi masuk, jenis kanal pembayaran (`payment_type`, `bank`, `va_number`), verifikasi webhook Midtrans (SHA-512), dan konfirmasi kasir cash manual (`dikonfirmasi_oleh`).
7. **[Reservasi](file:///c:/xampp/htdocs/asri-boarding-house/app/Models/Reservasi.php)**: Mengelola booking online, validasi jadwal bebas bentrok (`static isKamarTerbooking`), rentang tanggal sewa (`tanggal_mulai`, `tanggal_selesai`), durasi & tipe sewa, token Snap Midtrans, dan status verifikasi DP/Lunas.
8. **[ChatMessage](file:///c:/xampp/htdocs/asri-boarding-house/app/Models/ChatMessage.php)**: Mengelola log pesan diskusi pra-pembayaran calon penyewa dengan admin di bawah konteks reservasi (berelasi Komposisi).
9. **[WhatsappClick](file:///c:/xampp/htdocs/asri-boarding-house/app/Models/WhatsappClick.php)**: Mencatat metrik klik tombol WhatsApp CTA per unit kamar untuk analitik pemasaran landing page.
10. **[Setting](file:///c:/xampp/htdocs/asri-boarding-house/app/Models/Setting.php)**: Menyimpan konfigurasi dinamis sistem berbasis pasangan key-value (kontak, rekening bank, copywriting landing page, persentase diskon durasi sewa).
11. **[CustomerReview](file:///c:/xampp/htdocs/asri-boarding-house/app/Models/CustomerReview.php)**: Mengelola ulasan dan testimonial kepuasan pelanggan untuk katalog publik.
12. **[Pengeluaran](file:///c:/xampp/htdocs/asri-boarding-house/app/Models/Pengeluaran.php)**: Mencatat pengeluaran kas operasional dan pemeliharaan kost beserta lampiran berkas bukti nota.
13. **[Faq](file:///c:/xampp/htdocs/asri-boarding-house/app/Models/Faq.php)**: Menyimpan daftar pertanyaan umum (FAQ) dan jawaban yang ditampilkan pada beranda landing page.
14. **[Keluhan](file:///c:/xampp/htdocs/asri-boarding-house/app/Models/Keluhan.php)**: Mengelola tiket pelaporan kerusakan fasilitas oleh penyewa aktif, kategori kendala, foto bukti kendala, status penanganan, dan riwayat tanggapan admin.
15. **[Peraturan](file:///c:/xampp/htdocs/asri-boarding-house/app/Models/Peraturan.php)**: Mengelola daftar tata tertib dan aturan kost yang ditampilkan pada portal penyewa dan halaman publik.
16. **[Gallery](file:///c:/xampp/htdocs/asri-boarding-house/app/Models/Gallery.php)**: Mengelola dokumentasi galeri foto lingkungan fisik dan fasilitas kost putri.
17. **[GuestChatThread](file:///c:/xampp/htdocs/asri-boarding-house/app/Models/GuestChatThread.php)** & **[GuestChatMessage](file:///c:/xampp/htdocs/asri-boarding-house/app/Models/GuestChatMessage.php)**: Mengelola obrolan *live chat* pengunjung landing page berbasis cookie session token (berelasi Komposisi).
18. **[LogNotifikasi](file:///c:/xampp/htdocs/asri-boarding-house/app/Models/LogNotifikasi.php)**: Mencatat log audit riwayat pengiriman notifikasi WhatsApp Fonnte dan Surel untuk tagihan, jatuh tempo, dan kuitansi pembayaran.
19. **[NotifikasiKhusus](file:///c:/xampp/htdocs/asri-boarding-house/app/Models/NotifikasiKhusus.php)**: Mencatat log audit terpusat atas aktivitas mutasi kamar, transaksi, denda, dan pengeluaran untuk pengawasan administrator.
20. **[Pengumuman](file:///c:/xampp/htdocs/asri-boarding-house/app/Models/Pengumuman.php)**: Mengelola pengumuman dan pesan broadcast administrator yang dipublikasikan di dashboard penyewa.

---

### B. Service Layer & Interface (12 Layanan & Abstraksi)
1. **[PdfGeneratorInterface](file:///c:/xampp/htdocs/asri-boarding-house/app/Services/PdfGeneratorInterface.php)** & **[DompdfGenerator](file:///c:/xampp/htdocs/asri-boarding-house/app/Services/DompdfGenerator.php)**: Abstraksi interface dan generator konkrit berbasis Dompdf untuk merender laporan rekapitulasi PDF administratif admin ([LaporanController](file:///c:/xampp/htdocs/asri-boarding-house/app/Http/Controllers/Admin/LaporanController.php) dan [PenyewaController](file:///c:/xampp/htdocs/asri-boarding-house/app/Http/Controllers/Admin/PenyewaController.php)).
2. **[BillingService](file:///c:/xampp/htdocs/asri-boarding-house/app/Services/BillingService.php)**: Mengeksekusi penagihan masal otomatis (chunk 100 baris) setiap tgl 1, penanganan keterlambatan grace period tgl 10, perhitungan denda bertahap 5%, alokasi tagihan sisa DP/lunas, dan perpanjangan kontrak manual.
3. **[ReservasiService](file:///c:/xampp/htdocs/asri-boarding-house/app/Services/ReservasiService.php)**: Memvalidasi formulir booking, pengecekan pencegahan tabrakan tanggal sewa (*double-booking guard*), kalkulasi harga sewa, pembuatan reservasi baru, dan pembatalan reservasi kedaluwarsa.
4. **[TransisiPenyewaService](file:///c:/xampp/htdocs/asri-boarding-house/app/Services/TransisiPenyewaService.php)**: Mengorkestrasi pengubahan data reservasi yang disetujui admin menjadi profil penyewa aktif, routing tagihan sisa kewajiban, pemulihan soft-delete mantan penyewa, dan penguncian kamar.
5. **[AdminPenyewaService](file:///c:/xampp/htdocs/asri-boarding-house/app/Services/AdminPenyewaService.php)**: Mengelola pendaftaran manual penyewa offline/walk-in oleh administrator kost, validasi status kamar, dan memicu tagihan awal lunas cash.
6. **[TagihanService](file:///c:/xampp/htdocs/asri-boarding-house/app/Services/TagihanService.php)**: Menangani konfirmasi pembayaran tunai (*cash*) oleh kasir/admin dalam database transaction dan menyediakan kueri terpaginasi dengan filter status.
7. **[DashboardAnalyticsService](file:///c:/xampp/htdocs/asri-boarding-house/app/Services/DashboardAnalyticsService.php)**: Agregasi metrik analitik dashboard admin (tingkat okupansi kamar, arus kas masuk/keluar, keuntungan bersih, breakdown tagihan tertunggak, dan klik CTA WhatsApp).
8. **[MidtransService](file:///c:/xampp/htdocs/asri-boarding-house/app/Services/MidtransService.php)**: Berkomunikasi dengan Midtrans Snap API untuk memuat token transaksi pembayaran tagihan rutin dan reservasi online.
9. **[FonnteService](file:///c:/xampp/htdocs/asri-boarding-house/app/Services/FonnteService.php)**: SDK wrapper API gateway WhatsApp Fonnte dengan nomor tujuan terformat internasional (628xxx).
10. **[NotifikasiService](file:///c:/xampp/htdocs/asri-boarding-house/app/Services/NotifikasiService.php)** & **[NotificationTemplateBuilder](file:///c:/xampp/htdocs/asri-boarding-house/app/Services/Notifications/NotificationTemplateBuilder.php)**: Merakit format pesan terpusat untuk penagihan, pengingat jatuh tempo, eskalasi wali, kuitansi pembayaran, tanggapan keluhan, dan pesan selamat datang.
11. **[SanitizerService](file:///c:/xampp/htdocs/asri-boarding-house/app/Services/SanitizerService.php)**: Sanitasi tag iframe Google Maps agar aman, responsif, dan bebas XSS.
12. **[CalendarStyleHelper](file:///c:/xampp/htdocs/asri-boarding-house/app/Services/CalendarStyleHelper.php)**: Pemetaan kelas warna Tailwind CSS pada visualisasi kalender ketersediaan kamar dan status tagihan.

---

### C. Observers, Middlewares, Jobs, Events, Listeners, Commands, & Policies
1. **Observers (5)**:
   - [PenyewaObserver](file:///c:/xampp/htdocs/asri-boarding-house/app/Observers/PenyewaObserver.php): Mengubah status kamar ke `terisi` saat penyewa dibuat, mencatat tanggal keluar saat checkout, dan mencatat audit log.
   - [FasilitasObserver](file:///c:/xampp/htdocs/asri-boarding-house/app/Observers/FasilitasObserver.php): Membersihkan cache fasilitas landing page saat ada modifikasi data.
   - [KamarObserver](file:///c:/xampp/htdocs/asri-boarding-house/app/Observers/KamarObserver.php): Membersihkan cache kamar katalog publik dan mencatat audit log perubahan kamar (`created`, `updated`, `deleted`, `restored`, `forceDeleted`).
   - [PengeluaranObserver](file:///c:/xampp/htdocs/asri-boarding-house/app/Observers/PengeluaranObserver.php): Mencatat audit log mutasi pengeluaran kas operasional.
   - [SettingObserver](file:///c:/xampp/htdocs/asri-boarding-house/app/Observers/SettingObserver.php): Menyinkronkan cache memori pengaturan landing page.
2. **Middlewares & Validation Rules (8)**:
   - [EnsurePasswordChanged](file:///c:/xampp/htdocs/asri-boarding-house/app/Http/Middleware/EnsurePasswordChanged.php): Mewajibkan penggantian password default saat pertama kali login.
   - [EnsureProfileIsComplete](file:///c:/xampp/htdocs/asri-boarding-house/app/Http/Middleware/EnsureProfileIsComplete.php): Memvalidasi kelengkapan NIK, no HP, dan kontak wali sebelum transaksi.
   - [EnsureTenantIsActive](file:///c:/xampp/htdocs/asri-boarding-house/app/Http/Middleware/EnsureTenantIsActive.php): Membatasi area portal penyewa hanya untuk penghuni berstatus kontrak aktif.
   - [RoleMiddleware](file:///c:/xampp/htdocs/asri-boarding-house/app/Http/Middleware/RoleMiddleware.php): Memvalidasi peran hak akses (`admin` / `penyewa`).
   - [VerifyMidtransSignature](file:///c:/xampp/htdocs/asri-boarding-house/app/Http/Middleware/VerifyMidtransSignature.php): Memvalidasi tanda tangan hash SHA-512 `signature_key` callback webhook Midtrans.
   - [KamarTersediaRule](file:///c:/xampp/htdocs/asri-boarding-house/app/Rules/KamarTersediaRule.php): Memvalidasi ketersediaan unit kamar secara aman.
   - [TanpaPenyewaAktifLainRule](file:///c:/xampp/htdocs/asri-boarding-house/app/Rules/TanpaPenyewaAktifLainRule.php): Mencegah penugasan ganda kamar ke lebih dari satu penyewa aktif.
   - [ValidGoogleMapsEmbed](file:///c:/xampp/htdocs/asri-boarding-house/app/Rules/ValidGoogleMapsEmbed.php): Memvalidasi keabsahan tag iframe embed Google Maps.
3. **Queue Jobs & Notifications (11)**:
   - [KirimNotifikasiTagihanJob](file:///c:/xampp/htdocs/asri-boarding-house/app/Jobs/KirimNotifikasiTagihanJob.php): Mengirim tagihan bulanan dan link pembayaran ke WhatsApp penyewa.
   - [KirimNotifikasiPembayaranJob](file:///c:/xampp/htdocs/asri-boarding-house/app/Jobs/KirimNotifikasiPembayaranJob.php): Mengirim bukti pelunasan dan tautan nota kuitansi digital ke WhatsApp penyewa.
   - [KirimNotifikasiAdminReservasiJob](file:///c:/xampp/htdocs/asri-boarding-house/app/Jobs/KirimNotifikasiAdminReservasiJob.php) & [KirimNotifikasiUserReservasiJob](file:///c:/xampp/htdocs/asri-boarding-house/app/Jobs/KirimNotifikasiUserReservasiJob.php): Mengirim notifikasi status reservasi baru/dibayar/disetujui.
   - [KirimReminderJatuhTempoJob](file:///c:/xampp/htdocs/asri-boarding-house/app/Jobs/KirimReminderJatuhTempoJob.php) & [KirimNotifikasiWaliJob](file:///c:/xampp/htdocs/asri-boarding-house/app/Jobs/KirimNotifikasiWaliJob.php): Mengirim pengingat jatuh tempo dan eskalasi tunggakan ke nomor wali.
   - [KirimWelcomeMessageJob](file:///c:/xampp/htdocs/asri-boarding-house/app/Jobs/KirimWelcomeMessageJob.php): Mengirim pesan WhatsApp selamat datang dan panduan masuk kamar.
   - [KirimNotifikasiKustomJob](file:///c:/xampp/htdocs/asri-boarding-house/app/Jobs/KirimNotifikasiKustomJob.php): Mengirim pesan siaran khusus admin ke penyewa.
   - [BaseResetPasswordNotification](file:///c:/xampp/htdocs/asri-boarding-house/app/Notifications/BaseResetPasswordNotification.php), [AdminResetPasswordNotification](file:///c:/xampp/htdocs/asri-boarding-house/app/Notifications/AdminResetPasswordNotification.php), dan [PenyewaResetPasswordNotification](file:///c:/xampp/htdocs/asri-boarding-house/app/Notifications/PenyewaResetPasswordNotification.php): Mengelola surel reset password berbasis inheritance.
4. **Artisan Console Commands (9)**:
   - [GenerateBulananTagihan](file:///c:/xampp/htdocs/asri-boarding-house/app/Console/Commands/GenerateBulananTagihan.php) (`tagihan:generate-bulanan`): Generasi tagihan bulanan rutin tgl 1.
   - [ProsesKeterlambatanTagihan](file:///c:/xampp/htdocs/asri-boarding-house/app/Console/Commands/ProsesKeterlambatanTagihan.php) (`tagihan:proses-keterlambatan`): Evaluasi denda dan grace period.
   - [CancelExpiredReservations](file:///c:/xampp/htdocs/asri-boarding-house/app/Console/Commands/CancelExpiredReservations.php) (`reservasi:cancel-expired`): Auto-cancel reservasi kadaluarsa > 24 jam.
   - [ReminderHabisKontrakCommand](file:///c:/xampp/htdocs/asri-boarding-house/app/Console/Commands/ReminderHabisKontrakCommand.php) (`kontrak:reminder-habis`): Pengingat masa sewa berakhir pada H-14 dan H-7.
   - [AbhPurgeTrash](file:///c:/xampp/htdocs/asri-boarding-house/app/Console/Commands/AbhPurgeTrash.php) (`abh:purge-trash`): Pembersihan aman record soft-deleted berusia > 90 hari.
   - [PruneClosedGuestChats](file:///c:/xampp/htdocs/asri-boarding-house/app/Console/Commands/PruneClosedGuestChats.php) (`chat-guest:prune`): Pembersihan sesi chat tamu tertutup > 90 hari.
   - [ClearOldNotificationLogs](file:///c:/xampp/htdocs/asri-boarding-house/app/Console/Commands/ClearOldNotificationLogs.php) (`log-notifikasi:clear`), [CheckSystemHealthCommand](file:///c:/xampp/htdocs/asri-boarding-house/app/Console/Commands/CheckSystemHealthCommand.php) (`abh:check-status`), dan [TruncateLogFile](file:///c:/xampp/htdocs/asri-boarding-house/app/Console/Commands/TruncateLogFile.php) (`log:truncate`).
5. **Policies Otorisasi (4)**:
   - [ReservasiPolicy](file:///c:/xampp/htdocs/asri-boarding-house/app/Policies/ReservasiPolicy.php): Otorisasi melihat (`view`), berdiskusi chat (`chat`), membayar (`pay`), dan membatalkan/mengubah (`update`) pada reservasi.
   - [TagihanPolicy](file:///c:/xampp/htdocs/asri-boarding-house/app/Policies/TagihanPolicy.php): Otorisasi melihat dan membayar invoice bulanan oleh penyewa sah.
   - [PembayaranPolicy](file:///c:/xampp/htdocs/asri-boarding-house/app/Policies/PembayaranPolicy.php): Otorisasi pengunduhan berkas kuitansi digital resmi (`downloadNota`).
   - [KeluhanPolicy](file:///c:/xampp/htdocs/asri-boarding-house/app/Policies/KeluhanPolicy.php): Otorisasi pelaporan dan pembacaan tiket keluhan oleh penyewa bersangkutan.

---

## 6. Kesimpulan & Status Validasi Dokumen

Spesifikasi Class Diagram ini telah divalidasi dan dinyatakan:
* **Lengkap (100%)**: Seluruh **21 Model Eloquent & 1 Pivot Table (`kamar_fasilitas`)**, **12 Enumerasi Konseptual**, **12 Layanan Bisnis & Interface**, **5 Observers**, **8 Middlewares/Rules**, **9 Console Commands**, **11 Domain Events**, **12 Event Listeners & Subscriber**, **8 Queue Jobs**, dan **4 Policies** terpetakan secara utuh dan transparan.
* **Konsisten Tanpa Ghost Classes**: Artefak usang pasca-refaktorisasi v235.0 (`PdfNotaService`, `GeneratePdfNotaJob`, `GeneratePdfNotaListener`, dan `pembayaran.pdf_path`) telah dieliminasi total, dan arsitektur rendering kuitansi resmi diselaraskan dengan pustaka client-side `html2pdf.js`.
* **Semantik UML Presisi**: Menggunakan notasi Asosiasi (`--`), Komposisi (`*--`), Realisasi Interface (`..|>`), Pewarisan (`--|>`), dan Injeksi Dependensi (`..>`) secara akurat sesuai kaidah Unified Modeling Language.
* **Aset Visual Terpadu**: Dilengkapi **19 visualisasi diagram citra PNG** beresolusi tinggi skala 3x dengan *solid white background* pada direktori `Blueprint/class/`.
