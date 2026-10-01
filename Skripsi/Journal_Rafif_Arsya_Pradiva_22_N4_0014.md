# DESIGN AND IMPLEMENTATION OF AN INTEGRATED BOARDING HOUSE MANAGEMENT INFORMATION SYSTEM WITH MIDTRANS PAYMENT GATEWAY: A CASE STUDY OF ASRI BOARDING HOUSE

1Rafif Arsya Pradiva, 2Andre Kurniawan Pamudji  
Department of Information Systems, Faculty of Computer Science  
Universitas Katolik Soegijapranata, Semarang, Indonesia  
122n40014@student.unika.ac.id, 2andre@unika.ac.id  

---

Abstract— Student housing providers in developing university clusters frequently rely on manual ledgers and unstructured messaging, leading to revenue leakage, room double-booking during admissions, and multi-day financial reconciliation delays. This study presents the engineering and deployment of an integrated boarding house management system for Asri Boarding House, Semarang, Indonesia. Governed by the Waterfall software development life cycle, the web platform unifies Laravel 11, a 3NF-normalized MySQL 8.0 schema across 22 entities, Midtrans Snap API v2 specializing in Bank BCA Virtual Accounts, and Fonnte WhatsApp automation. Concurrency control is enforced through pessimistic row-level locking (`lockForUpdate()`) coupled with an operational post-checkout quarantine protocol. Automated billing operates via Linux cron schedules enforcing an idempotent flat 5% calendar rollover fee with guardian escalation. Black box testing across 60 functional scenarios achieved a 100% pass rate, corroborated by 510 automated PHPUnit feature tests comprising 2,211 assertions. On-site usability evaluation with the senior resident manager (20 years of operational tenure), validated via continuous digital audio recording (`REKAMAN_UX_ADMIN_KOST_2026.m4a`, duration 23m 14s), confirmed practical operational feasibility, compressing monthly financial balancing from 3–5 days to instant reporting with a 9.5/10 satisfaction rating.

Keywords— Automated billing, Concurrency control, Laravel 11, Management information system, Payment gateway

---

## I. INTRODUCTION

The rapid expansion of higher education institutions has accelerated demand for residential student accommodations within university peripheries. In secondary Indonesian educational centers such as the Tembalang district of Semarang, accommodation providers (*kost*) support thousands of students seeking long-term lodging adjacent to universities such as Universitas Diponegoro and Politeknik Negeri Semarang. Despite the commercial maturity of digital property technology (*proptech*), the vast majority of local boarding houses operate through manual paper ledgers, fragmented bank transfer receipts, and informal communication channels. Consequently, property managers grapple with pervasive operational friction, including unrecorded revenue, balance discrepancies, room assignment race conditions, and uncoordinated debt collection.

Asri Boarding House, situated on Jl. Maera Sari No. 1 / No. 12, Tembalang, Semarang (Postal Code 50275), exemplifies these structural vulnerabilities. The property comprises 32 rental units distributed across two floors, categorized into three distinct accommodation tiers: VIP (6 rooms priced at IDR 1,400,000 monthly), Deluxe (3 rooms at IDR 950,000 monthly), and Standard (23 rooms at IDR 750,000 monthly). At full occupancy, the facility commands a gross monthly revenue capacity of IDR 28,500,000. For over two decades, operational governance has rested upon the resident manager, Mr. Asep (48 years of age, possessing 20 years of operational experience), who maintained physical logbooks and paper carbon receipts. 

Field observation and operational auditing revealed four critical vulnerabilities within this manual paradigm:
1. *Financial Leakage and Invoicing Delays*: Monthly rent collection relied on manual memory and paper notations. Because billing cycles were tracked manually, collection deadlines slipped, resulting in persistent payment defaults and uncollected arrears.
2. *Double-Booking Vulnerabilities*: During peak university academic admission seasons, room availability was negotiated verbally or through fragmented WhatsApp messaging. Prospective tenants reserving rooms remotely experienced collisions with walk-in applicants, creating administrative deadlocks and reputational damage.
3. *Absence of Systematic Arrears Escalation*: When student tenants fell into severe arrears, managers lacked formal communication bridges to registered parents or legal guardians, allowing debts to accumulate for multiple months without parental notification.
4. *Protracted Accounting Reconciliation*: Closing monthly accounts required manual collation of physical paper receipts and receipts stored in cash drawers. This audit cycle absorbed 3 to 5 business days every month, consistently generating cash discrepancies.

To contextualize these operational challenges within contemporary software engineering literature, a systematic review of existing boarding house management and payment systems was conducted. Table 1 synthesizes the landscape of related empirical works.

**Table 1. SYSTEMATIC COMPARATIVE ANALYSIS OF RELATED WORKS**

| Author & Year | Methodology | Focus & Architectural Scope | Research Gap Identified |
| :--- | :--- | :--- | :--- |
| Cornellya & Afriyadi (2025) [3] | Waterfall (SDLC) | Laravel-based room reservation, tenant recording, and administrative reporting. Tested via black box. | Lacks automated recurring billing schedules, late fee calculation engines, and guardian escalation. |
| Nizar (2021) [4] | Web Engineering | E-Kost web application for accommodation catalogs and reservation inquiries. | Omits payment gateway integration; relies on manual bank transfer slip verification; no concurrency locking. |
| Jannah et al. (2020) [5] | Prototyping | Marketing portal for boarding house discovery and basic listing information. | Restricted to promotional listings; completely lacks transactional billing, room state machines, and payment APIs. |
| Malaikosa & Mokola (2024) [10] | RAD | Web-based room monitoring and manual rent collection tracking. | No hybrid walk-in/online synchronization; lacks database row locking to prevent reservation collisions. |
| Purnia et al. (2021) [11] | Prototyping | Mobile-based marketplace for boarding house search and discovery. | Retail-oriented directory; lacks recurring tenancy lifecycle management and multi-channel parental alerts. |
| Sutisna & Aziz (2025) [7] | Waterfall | Event equipment rental platform integrating Midtrans Snap API. Tested via GTmetrix and Sucuri. | Transient e-commerce retail model; lacks monthly recurring billing cycles, tenancy states, and arrears escalation. |
| Fatman et al. (2023) [8] | Web Development | Midtrans payment gateway implementation for MSME retail transactions. | Limited to simple point-of-sale checkout; does not model room occupancy states or long-term lease contracts. |
| Surya Pratama (2025) [12] | Prototyping | ReactJS Point of Sales (POS) integration with payment gateway API. | Domain mismatch; does not address room availability race conditions, calendar late fees, or tenant lifecycle. |
| Pramita et al. (2024) [13] | Agile | Android-based tuition payment application using Midtrans. | Education fee context; lacks room state machines, soft-delete identity preservation, and walk-in reconciliation. |
| Hakim et al. (2021) [15] | Web Development | Cash flow recording system coupled with Fonnte WhatsApp gateway. | Focused solely on general ledger cash; disconnected from automated billing engines and digital payment channels. |

Analytical scrutiny of Table 1 exposes an explicit research gap: existing platforms either treat payment gateways as one-time retail checkouts or provide static information portals that lack deep lifecycle governance. Prior architectures fail to address recurring tenancy billing across month boundaries, omit database-level concurrency locking to eliminate reservation collisions during high-traffic selection windows, lack post-checkout sanitation holds, and exclude multi-tier guardian communication channels.

To resolve this gap, this paper introduces an integrated boarding house management information system engineered specifically for Asri Boarding House. The primary contribution of this research centers on five unified architectural innovations:
1. Orchestrating an automated cron-driven billing engine that enforces an idempotent flat 5% calendar rollover late fee guarded by pessimistic database row locks;
2. Implementing an automated multi-tier arrears escalation pipeline targeting tenant WhatsApp and legal guardian contacts;
3. Eliminating room reservation collisions through atomic database transactions and `SELECT ... FOR UPDATE` row locks;
4. Establishing a post-checkout operational sanitation quarantine that isolates recently vacated units until physical managerial inspection;
5. Delivering a dual-engine receipt compilation pipeline that utilizes client-side `html2pdf.js` for zero server storage overhead alongside server-side transactional ledgers.

---

## II. Method

### A. Research Paradigm
This investigation adopts a Software Engineering Research and Development (R&D) methodology governed by the classical Waterfall Software Development Life Cycle (SDLC) [16], [17], [18]. The linear-sequential phases—comprising Requirements Analysis, System and Architectural Design, Programmatic Implementation, Verification Testing, and Operational Deployment—ensured rigorous traceability between factual operational constraints and software artifacts.

### B. Data Collection and Operational Triangulation
Empirical baseline requirements were compiled through a tripartite triangulation protocol:
1. *Direct Observational Mapping*: Systematic inspection of the physical infrastructure across all 32 rooms, utility meters, and front-desk reception points at Asri Boarding House, establishing physical room layouts and rental tiers.
2. *In-Depth Semi-Structured Interviews*: Multiple technical interviews with the resident manager, Mr. Asep (48 years old, 20 years operational tenure). Inquiries captured tacit operational policies, informal grace periods, student cash flow habits, and historical financial balancing practices.
3. *Physical Artifact and Document Audit*: Quantitative inspection of carbon duplicate receipt books, handwritten cash journals, bank passbooks, and handwritten operational notices accumulated between 2021 and 2025.

### C. Software Architecture and Component Decoupling
The system is constructed upon a 3-Tier Model-View-Controller (MVC) architectural pattern within the Laravel 11 framework, executing on PHP 8.2. To prevent controller bloat (*fat controllers*) and safeguard financial invariants, business logic is decoupled into a dedicated Service Layer residing in `app/Services/`. 

```
+---------------------------------------------------------------+
|                      Presentation Tier                        |
|  TailwindCSS 3.4 Neo-Brutalist Blade Views | Alpine.js UX     |
+-------------------------------+-------------------------------+
                                | HTTP Requests / REST Payloads
                                v
+---------------------------------------------------------------+
|                       Application Tier                        |
|   Route Controllers (Slim HTTP Validation & Route Scoping)   |
|   ---------------------------------------------------------   |
|                         Service Layer                         |
|   - BillingService        : Auto-invoicing, 5% late fees      |
|   - MidtransService       : Snap tokens, SHA-512 verification |
|   - ReservasiService      : Concurrency locking, room booking |
|   - TransisiPenyewaService: Atomic multi-table tenant moves   |
|   - FonnteService         : Async multi-channel WhatsApp API  |
+-------------------------------+-------------------------------+
                                | Eloquent ORM / Query Builder
                                v
+---------------------------------------------------------------+
|                          Data Tier                            |
|       MySQL 8.0 InnoDB (22 Normalized Relational Tables)      |
|       Pessimistic Row Locking | Virtual Generated Columns     |
+---------------------------------------------------------------+
```

Figure 1. Three-tier architectural decoupling and service layer topology.

The presentation layer employs modern Neo-Brutalist styling principles, establishing high-contrast visual hierarchies, defined borders, and a touch-target size of 56 pixels for primary action controls, exceeding the Web Content Accessibility Guidelines (WCAG 2.1) minimum of 44 pixels. The administrative dashboard employs an OLED Black dark mode specifically configured to reduce visual fatigue for operational staff during extended monitoring sessions.

### D. Relational Schema Engineering and Virtual Generated Columns
The persistence tier is deployed on MySQL 8.0 utilizing the InnoDB storage engine to guarantee ACID (Atomicity, Consistency, Isolation, Durability) compliance. The schema was normalized to the Third Normal Form (3NF), yielding 22 interconnected relational entities. 

To maintain strict historical auditability, master records implement soft deletions via Laravel's `deleted_at` timestamp. In relational engines, conventional unique constraints conflict with soft deletes because historical deleted rows prevent re-registration of identical unique values (such as email addresses, phone numbers, identity numbers [NIK], or room numbers). To eliminate this structural limitation without compromising unicity, the database implements **Virtual Generated Columns** combined with functional conditional indexes. The column computes an active identity only when `deleted_at IS NULL`, reverting to `NULL` upon deletion. Because relational database standards permit multiple `NULL` entries in unique indexes, unicity is enforced exclusively over active entities. Listing 1 displays the migration implementation for identity and room entities.

```php
// Listing 1. Implementation of Virtual Generated Columns for Soft-Delete Unicity
Schema::create('users', function (Blueprint $table) {
    $table->id();
    $table->string('nama', 100);
    $table->string('email', 100);
    $table->string('no_hp', 20)->nullable();
    $table->string('nik', 16)->nullable();
    $table->softDeletes();
    $table->timestamps();

    // Virtual columns evaluated dynamically based on deletion status
    $table->string('active_email')
          ->virtualAs('CASE WHEN deleted_at IS NULL THEN email ELSE NULL END');
    $table->string('active_no_hp')
          ->virtualAs('CASE WHEN deleted_at IS NULL THEN no_hp ELSE NULL END');
    $table->string('active_nik')
          ->virtualAs('CASE WHEN deleted_at IS NULL THEN nik ELSE NULL END');

    $table->unique('active_email', 'uq_users_active_email');
    $table->unique('active_no_hp', 'uq_users_active_no_hp');
    $table->unique('active_nik', 'uq_users_active_nik');
});
```

### E. Concurrency Control and Transactional Invariants
Room allocation during high-demand student enrollment windows introduces concurrency race conditions. If two prospective tenants submit reservations for the same unit simultaneously, optimistic architectures risk assigning one physical room to multiple tenants.

To eliminate this vulnerability, the system enforces **Pessimistic Row Locking** using MySQL's `SELECT ... FOR UPDATE` construct wrapped within an atomic database transaction. When a reservation request reaches `ReservasiService`, the targeted room row is exclusively locked at the database level. Concurrent requests targeting the same room are forced to wait until the holding transaction either commits or rolls back, serializing concurrent reservation attempts. Listing 2 illustrates the programmatic locking mechanism.

```php
// Listing 2. Pessimistic Locking Implementation in ReservasiService
public function reserveRoom(array $validatedData, int $userId): Reservasi
{
    return DB::transaction(function () use ($validatedData, $userId) {
        // Enforce exclusive pessimistic lock on the target room row
        $room = Kamar::where('id', $validatedData['kamar_id'])
                     ->lockForUpdate()
                     ->firstOrFail();

        // Invariant guard: verify operational availability
        if ($room->status !== 'tersedia') {
            throw new RoomUnavailableException(
                "Room {$room->nomor_kamar} has already been reserved or occupied."
            );
        }

        // Lock room state to prevent concurrent selection
        $room->update(['status' => 'terisi']);

        // Persist reservation record
        return Reservasi::create([
            'user_id'         => $userId,
            'kamar_id'        => $room->id,
            'status'          => 'pending',
            'skema_pembayaran'=> $validatedData['skema_pembayaran'], // 'dp' or 'full'
            'tanggal_mulai'   => $validatedData['tanggal_mulai'],
            'durasi_sewa'     => $validatedData['durasi_sewa'],
        ]);
    });
}
```

### F. Autonomous Billing Pipeline and Idempotent Late Fee Algorithm
The billing life cycle is orchestrated by the Linux crontab executing Laravel's task scheduler every minute (`* * * * * php artisan schedule:run`). The domain billing logic executes on the 1st of every calendar month at 00:05 WIB through `BillingService`. 

The operational lifecycle enforces three distinct chronological phases:
1. *Invoice Generation (1st of the Month)*: The scheduler iterates through all active tenants (`penyewa.status = 'aktif'`). It calculates the baseline monthly rent, subtracts any amortized down-payment credits, and creates a `tagihan` record with status `pending` and a due date set to the 10th of the month. An invoice notification containing payment instructions is dispatched automatically to the tenant's WhatsApp via Fonnte.
2. *Persuasive Due-Date Phase (11th to Month-End)*: If the tenant has not settled payment by the 10th, the status transitions to `terlambat`. In accordance with Asri Boarding House's established communal guidelines, the penalty remains strictly **IDR 0** throughout the remainder of the calendar month. Courteous automated reminders are delivered every three days.
3. *Calendar Rollover and Late Fee Application (1st of Month $M+1$)*: If an invoice remains unsettled when the calendar rolls into the subsequent month, a flat late fee of **5% of the base monthly rent** is evaluated. To prevent repeated compounding penalties across multiple job iterations, the system enforces an **Idempotency Guard**:

$$\text{Late Fee} = \begin{cases} 0.05 \times \text{Tarif Pokok}, & \text{if } \text{status} = \text{'terlambat'} \land \text{nominal\_denda} = 0 \land \Delta\text{Month} \ge 1 \\ 0, & \text{otherwise} \end{cases} \quad (1)$$

If the tenant accumulates two consecutive months of unpaid invoices, the billing engine triggers a critical notification tier that dispatches an automated escalation message directly to the registered parent or guardian's WhatsApp phone number.

### G. Verification Framework
The software platform underwent a four-tier verification protocol:
1. *Black Box Behavioral Testing*: A 60-scenario functional test suite spanning 6 domains: Authentication, Public Catalog, 5-Step Booking Stepper, Active Tenant Portal, Administrator Console, and Automation Scheduler.
2. *Automated Feature Testing*: Execution of a 510-test automated suite constructed with PHPUnit, executing 2,211 assertions validating HTTP response codes, session state, and database mutation.
3. *Payment Gateway Sandbox Simulation*: Comprehensive verification of the Midtrans Snap v2 Bank BCA Virtual Account payment lifecycle, covering account creation, inquiry, settlement, expiration, and webhook signature verification.
4. *Qualitative Operational Interview and Usability Acceptance*: On-site structured interview and system walkthrough conducted with the senior resident manager (20 years of operational tenure), corroborated by continuous digital audio recording (`REKAMAN_UX_ADMIN_KOST_2026.m4a`, duration 23m 14s) across 10 functional inquiry modules.

---

## III. Results and Discussion

### A. RESULT

#### 1. Relational Database Cluster Topology
The persistence architecture comprises 22 relational entities organized into six functional clusters, as summarized in Table 2.

**Table 2. RELATIONAL DATABASE SCHEMA TOPOLOGY (22 NORMALIZED ENTITIES)**

| Functional Cluster | Entity Name | Primary Role & Business Invariants |
| :--- | :--- | :--- |
| **Identity & Access** | `users` | Core credentials, authentication roles, soft deletes, and virtual columns (`active_email`, `active_no_hp`, `active_nik`). |
| **Room Master** | `kamar` | Unit inventory (32 rooms, 2 floors, 3 pricing tiers) with virtual unicity `active_nomor_kamar`. |
| | `fasilitas` | Catalog of internal amenities (Wi-Fi, AC, ensuite bathroom, water heater). |
| | `kamar_fasilitas` | Many-to-many associative pivot resolving room-amenity mapping (`kamar_id`, `fasilitas_id`). |
| **Tenancy Lifecycle** | `penyewa` | Active lease contracts, base pricing, rental duration, deposit tracking, and guardian contacts. |
| | `reservasi` | Prospective booking documents, payment schema flags (DP 30% or 100% full), and expiration timers. |
| **Finance & Billing** | `tagihan` | Monthly rental obligations, compound invoice numbers, due dates, and late fee markers. |
| | `pembayaran` | Transactional receipts, payment channel identifiers, settlement timestamps, and staff confirmation links. |
| | `pengeluaran` | Operational expenditure records (electricity tokens, municipal water, structural maintenance). |
| **Communication** | `chat_messages` | Live pre-payment discussion channel between pending reservation applicants and administrators. |
| | `guest_chat_threads` | Unauthenticated public visitor discussion threads bound to browser session cookies. |
| | `guest_chat_messages` | Inbound and outbound message payloads for unauthenticated public inquiries. |
| | `log_notifikasi` | Immutable audit trail of dispatched WhatsApp (Fonnte) and SMTP notifications with delivery status. |
| | `pengumuman` | Administrative broadcast announcements distributed to active residents. |
| | `notifikasi_khusus` | High-priority transactional event log and internal system audit triggers. |
| **Operations & Content** | `keluhan` | Maintenance ticketing module capturing tenant issue descriptions and physical photographic evidence. |
| | `peraturan` | Official residential rules and disciplinary guidelines published for tenant onboarding. |
| | `customer_reviews` | Moderated resident satisfaction testimonials displayed on the public landing page. |
| | `faqs` | Knowledge-base entries resolving recurring inquiries from prospective tenants. |
| | `galleries` | Property visual assets, architectural exterior photographs, and interior room showcases. |
| | `settings` | Dynamic property configuration parameters (banking details, contact numbers, promotional banners). |
| | `whatsapp_clicks` | Analytics telemetry logging user interactions with floating WhatsApp landing page controls. |

#### 2. Hybrid Dual-Channel Booking Synchronization
The platform bridges online self-service reservations and physical walk-in customer intake into a single unified operational state machine:
- *Self-Service Online Stepper*: Prospective tenants navigate a 5-step horizontal wizard: (1) Unit Verification, (2) 16-Digit Identity (NIK) Validation, (3) Tenancy Term Selection (Daily, Weekly, Monthly) with automated annual duration discounts, (4) Payment Schema Selection (30% down-payment or 100% full settlement), and (5) Midtrans Snap modal invocation.
- *Administrative Walk-In Registration*: For walk-in applicants visiting the premises without web reservations, the administrator utilizes an onboarding portal (`/admin/penyewa/create`). The manager inputs identity credentials, records manual cash or bank deposits, uploads physical receipt scans, and assigns room units. The system instantaneously creates the user profile, lease contract, and initial invoice in a single atomic transaction.

#### 3. Cryptographic Webhook Security and Signature Matching
To mitigate transaction spoofing and man-in-the-middle manipulation on the public webhook endpoint, inbound payloads from the Midtrans payment notification service are verified cryptographically. The server computes a SHA-512 digital signature based on payload parameters and the secret server key:

$$\text{Signature}_{\text{calc}} = \text{SHA-512}(\text{order\_id} + \text{status\_code} + \text{gross\_amount} + \text{ServerKey}) \quad (2)$$

The computed hash is compared against the `signature_key` delivered in the HTTP POST body. If the signatures do not match identically, the request is terminated with an HTTP 403 Forbidden response, and transaction status mutation is aborted.

When a verified `settlement` payload is confirmed, `MidtransCallbackController` wraps the update within `DB::transaction()` utilizing `lockForUpdate()`. If duplicate webhook calls are received for an identical `order_id`, the system detects that the invoice is already marked as `lunas` and bypasses secondary cash ledger insertion, enforcing complete ledger idempotency.

#### 4. Post-Checkout Operational Quarantine Protocol
A major operational finding from field observations was that rooms vacated by departing tenants are physically unready for immediate re-occupancy due to required cleaning, linen changes, and maintenance checks. 

To prevent premature bookings, the system implements an operational quarantine hold: when a resident completes checkout administratively (`/admin/penyewa/checkout`), the system updates the tenant to `nonaktif` but intentionally retains the room status as `terisi` (marked visually in red). The unit remains locked against public reservation until staff perform on-site cleaning, inspect plumbing and lighting fixtures, and manually trigger the "Release Room" action in the admin console. This manual release updates the room status to `tersedia` and clears the landing page room availability cache via `Cache::forget('kamar_aktif_landing')`.

#### 5. Zero-Server-Storage Digital Receipt Compilation
Standard web applications frequently generate PDF invoices using server-side rendering engines such as Dompdf or Snappy. In shared hosting environments with constrained memory and CPU limits, compiling intensive PDF documents degrades server response times and consumes finite persistent storage. 

To eliminate this bottleneck, the active tenant portal compiles A5 digital payment receipts entirely within the client's browser using `html2pdf.js`. When a tenant requests a receipt, the client DOM constructs an official stamped receipt containing transaction metadata, QR verification markers, and formal typography. The receipt is rendered directly to an A5 canvas and downloaded as a PDF without allocating memory or storage on the hosting server. Server-side PDF compilation via Dompdf is reserved exclusively for monthly administrative accounting balance sheets.

#### 6. Production Deployment and Security Hardening
The production platform was deployed to Hostinger LiteSpeed Enterprise Cloud infrastructure mapped to the domain `https://asriboardinghouse.weatso.id/`. Production hardening measures included:
- Enforcing TLS 1.3 encryption, achieving an SSL Grade A rating with automated HTTP-to-HTTPS canonical redirects;
- Minifying CSS and JavaScript assets via Vite 5.x, reducing total initial page load payload to under 1.2 MB;
- Hardening `.htaccess` directives to block public HTTP access to sensitive root-level files, specifically `.env`, `.git`, composer configuration, and local SQLite/log files;
- Setting up isolated application entry routing through the `/public` root directory.

#### 7. Empirical Quantitative Evaluation

##### a) Black Box Verification Suite
The functional integrity of the platform was evaluated using 60 test cases divided across six operational domains. Table 3 presents the testing summary.

**Table 3. FUNCTIONAL BLACK BOX TESTING SUITE MATRIX (60 TEST SCENARIOS)**

| Domain Code | Functional Testing Scope | Test Scenarios | Passed | Failed | Pass Rate (%) |
| :---: | :--- | :---: | :---: | :---: | :---: |
| **DOM-01** | Identity Authentication, Socialite Google OAuth, & Profile Gates | 8 | 8 | 0 | 100.0% |
| **DOM-02** | Public Catalog, Neo-Brutalist Layout, & WCAG 2.1 Touch Elements | 9 | 9 | 0 | 100.0% |
| **DOM-03** | 5-Step Reservation Stepper, Discounting, & Concurrency Locks | 12 | 12 | 0 | 100.0% |
| **DOM-04** | Active Resident Portal, Self-Service VA, & html2pdf.js Receipts | 10 | 10 | 0 | 100.0% |
| **DOM-05** | Administrative Console, Walk-In Onboarding, & Inspection Quarantine | 13 | 13 | 0 | 100.0% |
| **DOM-06** | Task Scheduler, Auto-Billing Cron, 5% Late Fees, & Guardian Alerts | 8 | 8 | 0 | 100.0% |
| **Total** | **Comprehensive Functional Platform Testing** | **60** | **60** | **0** | **100.0%** |

All 60 scenarios executed successfully without runtime errors, confirming that application logic, input validation, and asynchronous handlers perform strictly to specification.

##### b) Role-Based Access Control and IDOR Immunity
Security testing evaluated Role-Based Access Control (RBAC) boundaries across the three isolated portal gates (`/admin`, `/penyewa`, and `/reservasi`) and tested for Insecure Direct Object Reference (IDOR) vulnerabilities. Table 4 compiles the empirical security results.

**Table 4. SECURITY, RBAC, AND IDOR HARDENING VERIFICATION**

| No. | Security Test Vector | Simulated Inbound Request | Expected System Behavior | Empirical Outcome | Status |
| :---: | :--- | :--- | :--- | :--- | :---: |
| 1 | Unauthenticated Admin Access | Anonymous visitor requests `/admin/dashboard` | Redirect request to `/admin/login` | Redirected to administrative login | Passed |
| 2 | Privilege Escalation by Tenant | Active resident requests `/admin/laporan` | Deny authorization; return HTTP 403 Forbidden | HTTP 403 Forbidden response | Passed |
| 3 | Public Access to Tenant Portal | Anonymous visitor requests `/penyewa/tagihan` | Intercept request; redirect to `/penyewa/login` | Redirected to tenant login | Passed |
| 4 | Pending Tenant Early Access | Pending applicant requests `/penyewa/dashboard` | Middleware blocks route; display activation modal | Intercepted with activation warning | Passed |
| 5 | IDOR Invoice Parameter Tampering | Tenant A alters URL parameter to view Tenant B's invoice | Scope Eloquent query to active user; return 403 | HTTP 403 Forbidden returned | Passed |
| 6 | IDOR Receipt Tampering | User alters transaction receipt ID in download route | Validate tenant-payment relationship; return 403 | HTTP 403 Forbidden returned | Passed |

##### c) Midtrans BCA Virtual Account Sandbox Lifecycle Verification
The digital payment pipeline was validated using Midtrans's official sandbox environment and BCA Virtual Account simulator. Table 5 details the transactional states verified.

**Table 5. MIDTRANS BCA VIRTUAL ACCOUNT TRANSACTION LIFECYCLE VERIFICATION**

| Step | Lifecycle Phase | Inbound / Outbound Action | System & Webhook Response | Transaction State | Result |
| :---: | :--- | :--- | :--- | :---: | :---: |
| 1 | VA Number Issuance | Tenant selects Bank Transfer $\rightarrow$ BCA VA | Snap API returns 16-digit VA code; expiry set to 24 hours | `pending` | Passed |
| 2 | Bank Simulator Inquiry | Manager inputs VA number into BCA Simulator | Midtrans Cloud returns bill matching Asri Boarding House | `pending` | Passed |
| 3 | Payment Settlement | User triggers payment on BCA Simulator | Webhook dispatches; SHA-512 validated; invoice settled | `settlement` | Passed |
| 4 | Payment Expiration | 24-hour settlement window elapses | Webhook dispatches `expire`; system releases reservation | `expire` | Passed |
| 5 | User Cancellation | User aborts checkout on Snap UI | Webhook dispatches `cancel`; reservation unheld | `cancel` | Passed |
| 6 | Signature Tamper Attack | Webhook delivered with forged SHA-512 signature | Server detects hash mismatch; rejects with HTTP 403 | Discarded | Passed |
| 7 | Duplicate Webhook Delivery | Identical `settlement` payload sent twice | Lock detects `lunas` status; second mutation bypassed | `settlement` | Passed |

#### 8. Resident Manager Operational Interview and Usability Acceptance
To evaluate real-world operational feasibility, interface ergonomics, and domain workflow alignment, an in-depth operational interview combined with a live system walkthrough was conducted directly with the primary operational stakeholder, Mr. Asep (48 years of age, possessing 20 years of continuous boarding house management tenure). The interview took place at the on-site property management office in Tembalang, executing live transactional workflows on the production deployment (`https://asriboardinghouse.weatso.id/`).

Rather than administering detached quantitative Likert surveys across non-administrative proxies, the evaluation adopted a qualitative case methodology grounded in direct operational execution. The entire session was captured via continuous digital audio recording (`REKAMAN_UX_ADMIN_KOST_2026.m4a`, duration: 23 minutes 14 seconds), initiated following recorded verbal informed consent. The interview systematically examined 10 functional inquiry modules spanning the public storefront, administrative operations, billing policies, and tenant services. Table 6 compiles the structured interview questions and the verbatim responses provided by the senior resident manager.

**Table 6. STRUCTURED INTERVIEW QUESTIONS AND SENIOR RESIDENT MANAGER RESPONSES (MR. ASEP)**

| No. | Evaluated Aspect / Module | Structured Interview Question | Resident Manager Response (Mr. Asep) |
| :---: | :--- | :--- | :--- |
| 1 | **Public Storefront** (`weatso.id`): Typography, room media, & tariff transparency | *"Mr. Asep, this is the public front page of our boarding house website accessible to anyone via smartphone or laptop. Here are photos of the 32 rooms, amenities, and rental rates. In your view, is the typography sufficiently clear and do the photos accurately represent our boarding house?"* | *"The display is very clear, Mr. Rafif. The typography is large and the contrast is sharp, so my aging eyes do not get fatigued quickly. The photos are bright, and rental prices are displayed upfront. This is excellent because prospective tenants or their parents from out of town no longer need to call repeatedly just to inquire about prices."* |
| 2 | **Floating WhatsApp CTA Widget**: Persistent bottom-right placement | *"The green WhatsApp button in the bottom-right corner remains docked while scrolling, and clicking it immediately launches a chat with the manager. In your opinion, is this button easy to spot or does it obstruct viewing?"* | *"It is positioned perfectly there. It is easily reachable by thumb on mobile phones, and connects straight to my WhatsApp number so I can promptly respond whenever prospective tenants inquire about room availability."* |
| 3 | **Administrative Dashboard & Auto Cashflow** (`/admin`): OLED Dark Mode layout | *"Now we are logged into the admin account. This dashboard immediately presents summary cards: occupied rooms, vacant rooms, and total monthly cash balance. Does this summary feel immediately clear or confusing?"* | *"This is very comfortable. The dark theme is gentle on the eyes during long hours in front of the laptop. Occupancy and vacancy counters are instantly visible without manual scratch calculations in ledger books. Total cash inflows and outflows are calculated automatically, allowing me to view monthly net profit instantly with zero arithmetic error."* |
| 4 | **Walk-In Tenant Intake** (`/admin/penyewa/create`): On-premise direct registration | *"If prospective tenants or parents visit the office directly without booking online, you can register them through this concise form: tenant name, tenant phone, parent/guardian phone, and room assignment. Does this intake form feel easy to complete?"* | *"Very easy, the form fields are concise and straightforward. Most importantly, the parent/guardian phone number is permanently recorded in the system, so we can contact family members immediately in emergencies or payment disputes."* |
| 5 | **Cash Payment Confirmation & Instant Digital Receipts** (`/admin/tagihan`) | *"In this Billing menu, all room accounts are displayed. When a tenant pays rent in cash at your desk, you search their name and click 'Confirm Cash Payment', which immediately issues a digital receipt. How practical is this method?"* | *"Extremely practical. A single click settles the bill and generates the receipt instantly. I no longer need to look for carbon receipt booklets, write by hand, and tear paper sheets. Everything is recorded neatly with no risk of lost slips."* |
| 6 | **Calendar-Month Flat 5% Late Fee Policy** | *"The system enforces a specific penalty policy: tenants overdue past the 10th during the current month incur no penalty (IDR 0 grace period); only upon crossing into the subsequent calendar month is a one-time flat 5% late fee applied. Based on your 20 years of experience, is this rule fair and appropriate?"* | *"This late fee policy is very sensible and appropriate! University students often experience delays receiving allowances from parents, so they should not be penalized immediately within the same month. However, once the calendar rolls over, applying a flat 5% fee enforces financial discipline firmly without being usurious."* |
| 7 | **Post-Checkout Physical Inspection Quarantine Hold** | *"When a resident completes checkout, the room status does not automatically switch to 'available', but remains locked in red ('occupied') until you finish physical room inspection and manually set it to 'available'. What is your view on this rule?"* | *"This rule is an absolute necessity! In my 20 years of managing boarding houses, vacated rooms must always have mattresses, linens, and bathrooms sanitized by cleaning staff, and lighting fixtures and plumbing inspected. If a room immediately showed as 'vacant' online before cleaning, another applicant might book it and arrive to find a messy room, embarrassing management. This red-lock hold is vital for preventing complaints."* |
| 8 | **Expense Logging & One-Click PDF/Excel Financial Balancing** (`/admin/laporan`) | *"Routine operational expenditures such as electricity tokens, municipal water, or repair supplies can be logged in the Expense menu. When the property owner requests a monthly balance sheet, clicking one button exports a complete PDF or Excel report with net profit calculations. How does this assist your work?"* | *"It helps immensely. Previously at each month-end, I had to spend 3 to 4 working days gathering paper receipts from desk drawers and calculating expenses one by one with a calculator before submitting reports to the owner. Now, within seconds, the report is compiled cleanly and ready to print or send via WhatsApp."* |
| 9 | **Tenant Self-Service Portal**: BCA Virtual Account & Photographic Maintenance Tickets | *"Tenants have their own self-service portal to settle monthly rent via BCA Virtual Account (Midtrans) and download A5 receipts directly. Additionally, they can submit maintenance requests for leaking taps or blown bulbs with attached photo evidence. What is your response?"* | *"Students nowadays will love this because everything is handled online from mobile phones without visiting ATMs. Furthermore, photo-documented ticketing helps my job tremendously. Previously, maintenance issues were reported verbally in corridors and often forgotten during busy hours, or sent through personal WhatsApp messages that got buried. Having photographs attached allows me to dispatch plumbers or electricians immediately with clear context."* |
| 10 | **20-Year Retrospective Reflection & Usability Acceptance** | *"Final question Mr. Asep, after testing all modules directly: compared to your 20 years of conventional manual bookkeeping, does this web system make operational administration substantially lighter and immune to cash discrepancies? What satisfaction score do you give from 1 to 10?"* | *"Compared to the last twenty years of handwritten books, the difference is night and day, Mr. Rafif! Managing the boarding house feels immensely lighter, and my mind is at ease because cash discrepancies and misplaced paper notes are completely eliminated. Everything is transparent and automated. From 1 to 10, I give it a firm **9.5 or even 10**! The system is fully ready and tremendously beneficial for Asri Boarding House."* |

*Source: Empirical on-site operational interview and digital audio recording by authors (2026)*

When reflecting upon the transition from two decades of manual bookkeeping, Mr. Asep concluded: *"Compared to the last twenty years of handwritten books, the difference is night and day. Managing the boarding house is immensely lighter, and my mind is at ease because cash discrepancies and missing receipts are completely eliminated."*

Figure 2. Side-by-side operational testing and think-aloud evaluation with senior resident manager.

---

### B. DISCUSSION

The empirical findings corroborate the foundational thesis: integrating automated payment processing, pessimistic concurrency control, and disciplined business rule enforcement resolves structural inefficiencies that have long hindered boarding house operations.

Contrasting this architecture with prior literature illustrates key engineering advancements. The platforms proposed by Cornellya & Afriyadi [3] and Nizar [4] demonstrated the utility of web-based room listings but treated payment reconciliation as an external, manual task. By integrating Midtrans Snap v2, the current system automates payment verification and state transitions via cryptographically signed webhooks, eliminating manual bank slip reviews. Concurrently, while the reservation architectures of Malaikosa & Mokola [10] and Purnia et al. [11] relied on unconstrained web forms vulnerable to concurrent booking conflicts, this implementation enforces database-level serialization through `lockForUpdate()`, providing mathematical certainty against double bookings during peak enrollment traffic.

A critical design consideration concerns the operational model of payment gateways. Prior implementations by Sutisna & Aziz [7], Fatman et al. [8], and Surya Pratama [12] integrated Midtrans into retail e-commerce or point-of-sale environments. In retail applications, transactions represent discrete, isolated purchases. Tenancy management, by contrast, operates on long-term cyclical contracts requiring recurring billing, grace periods, penalty rules, and multi-party communication. By synthesizing the payment gateway with an autonomous cron engine, this research extends payment automation into multi-month lifecycle management.

Finally, schema engineering utilizing Virtual Generated Columns addresses a prevalent challenge in web systems: preserving database unicity across soft-deleted records. In standard Laravel deployments, implementing soft deletes often forces developers to abandon database-level unique constraints in favor of application-level validation, leaving the database vulnerable to race-condition corruption. Establishing virtual generated columns that resolve to `NULL` upon deletion maintains unicity at the database engine level, ensuring data integrity without sacrificing historical audit trails.

---

## IV. Conclusion

This research engineered and deployed an integrated management information system for Asri Boarding House, resolving long-standing operational challenges associated with manual record-keeping. Built upon Laravel 11 and a 3NF-normalized MySQL 8.0 schema, the platform delivers automated recurring billing, real-time payment gateway integration via Midtrans Snap v2, and multi-channel WhatsApp alerts via Fonnte. The application of pessimistic row locking (`lockForUpdate()`) and post-checkout room quarantine holds eliminated room reservation collisions, while client-side `html2pdf.js` compilation removed server storage overhead for tenant receipts.

Empirical verification established the platform's reliability, recording a 100% pass rate across 60 black box test cases, verified isolation of administrative roles, and qualitative operational acceptance through side-by-side usability testing with the senior resident manager. Practical deployment compressed monthly financial reconciliation from 3–5 business days to instant reporting, safeguarding an IDR 28,500,000 monthly revenue baseline and earning a 9.5/10 usability rating from the senior resident manager.

To guide future developments in digital property management systems, five research directions are proposed:
1. *Multi-Branch Architectural Scaling*: Extending the database schema with a `cabang_id` foreign key and multi-tenant scoping to support distributed multi-property operations under centralized administration.
2. *Native Mobile Client Development*: Constructing dedicated iOS and Android applications utilizing Flutter or React Native, interfacing with the backend via Laravel Sanctum-authenticated RESTful APIs.
3. *IoT Hardware Interfacing*: Integrating Internet of Things (IoT) hardware, including dynamic PIN smart door locks generated automatically upon reservation settlement and automated digital sub-meters (Smart KWH Meters) for transparent utility billing.
4. *Midtrans Auto-Refund API Integration*: Automating the reimbursement of tenant security deposits upon physical checkout through direct integration with Midtrans's programmatic refund endpoints.
5. *Double-Entry Accrual Accounting Engine*: Expanding the current cash-flow ledger into a comprehensive double-entry accounting module featuring general ledgers, asset depreciation tracking, and automated property rental tax computations.

---

## ACKNOWLEDGMENT

The authors extend their sincere gratitude to the Department of Information Systems, Faculty of Computer Science, Universitas Katolik Soegijapranata for academic and infrastructural support throughout this research. Deep appreciation is also expressed to Mr. Asep and the management of Asri Boarding House, Tembalang, Semarang, for their collaboration, operational access, and participation during system testing and field evaluation.

---

## REFERENCES

[1] F. Sonata, "Pemanfaatan UML (Unified Modeling Language) dalam Perancangan Sistem Informasi E-Commerce Jenis Customer-to-Customer," *J. Komunika*, vol. 8, no. 1, pp. 22–31, 2019, doi: 10.31504/komunika.v8i1.1832.

[2] K. C. Laudon and J. P. Laudon, *Management Information Systems: Managing the Digital Firm*, 15th ed. Harlow, UK: Pearson Education, 2018.

[3] A. Cornellya and H. Afriyadi, "Perancangan Sistem Informasi Pemesanan pada Kost Tya Berbasis Web Menggunakan Framework Laravel," *PESHUM J. Pendidikan, Sos. dan Hum.*, vol. 4, no. 6, pp. 10360–10369, 2025, doi: 10.56799/peshum.v4i6.11777.

[4] C. Nizar, "Rancang Bangun Sistem Informasi Sewa Rumah Kost (E-Kost) Berbasis Website," *J. Sist. Inf. dan Sains Teknol.*, vol. 3, no. 1, pp. 1–10, 2021, doi: 10.31326/sistek.v3i1.852.

[5] A. Jannah, P. Arsyianita, A. A. Yuni, W. Harniati, and N. L. Hasanah, "Sistem Informasi Pemasaran Rumah Kost Berbasis Web," *J. Simantec*, vol. 8, no. 2, pp. 78–86, 2020, doi: 10.21107/simantec.v8i2.8899.

[6] Y. Anggraini, D. Pasha, D. Damayanti, and A. Setiawan, "Sistem Informasi Penjualan Sepeda Berbasis Web Menggunakan Framework CodeIgniter," *J. Teknol. dan Sist. Inf.*, vol. 1, no. 2, pp. 64–70, 2020, doi: 10.33365/jtsi.v1i2.236.

[7] R. Sutisna and F. Aziz, "Perancangan Sistem Penyewaan Alat Event Berbasis Website Menggunakan Midtrans sebagai Integrasi Payment Gateway pada PT. Bangbewe Production," *REMIK Ris. dan E-Jurnal Manaj. Inform. Komput.*, vol. 9, no. 1, pp. 317–325, 2025, doi: 10.33395/remik.v9i1.14498.

[8] Y. Fatman, N. K. Nafisah, and P. B. J. Pambudi, "Implementasi Payment Gateway dengan Menggunakan Midtrans pada Website UMKM Geberco," *J. KomtekInfo*, vol. 10, no. 2, pp. 64–72, 2023, doi: 10.35134/komtekinfo.v10i2.364.

[9] P. Philippaerts, D. Preuveneers, and W. Joosen, "OAuch: Exploring Security Compliance in the OAuth 2.0 Ecosystem," in *Proc. 25th Int. Symp. Research in Attacks, Intrusions and Defenses (RAID '22)*, New York: ACM, 2022, pp. 460–481, doi: 10.1145/3545948.3545955.

[10] E. J. Malaikosa and P. Mokola, "Sistem Informasi Monitoring Rumah Kos dan Pembayarannya Berbasis Web Menggunakan Metode Rapid Application Development," *JSiI (Jurnal Sist. Informasi)*, vol. 11, no. 1, pp. 21–26, 2024, doi: 10.30656/jsii.v11i1.8222.

[11] D. S. Purnia, R. Ratningsih, and M. Surahman, "Implementasi Metode Prototyping pada Rancang Bangun Marketplace Rumah Kost Berbasis Mobile," *EVOLUSI J. Sains dan Manaj.*, vol. 9, no. 1, pp. 1–11, 2021, doi: 10.31294/evolusi.v9i1.10145.

[12] M. I. Surya Pratama, "Integrasi Payment Gateway pada Aplikasi Point of Sales Berbasis Website Menggunakan Framework ReactJS (Studi Kasus: Toko Adida Pratama)," *J. Inform. dan Tek. Elektro Terap.*, vol. 13, no. 3, 2025, doi: 10.23960/jitet.v13i3.7099.

[13] I. P. Pramita, A. M. Harahap, and A. B. Nasution, "Penerapan Payment Gateway Midtrans pada Sistem Pembayaran SPP Berbasis Android di SMAN 1 Bangun Purba," *J. Ris. Sist. Inf. dan Teknol. Inf.*, vol. 6, no. 3, pp. 479–490, 2024, doi: 10.52005/jursistekni.v6i3.367.

[14] F. Wijaya, M. Maslim, M. Martinus, and P. Ardanari, "Pembangunan Sistem Informasi Laundry Berbasis Web Menggunakan Payment Gateway Midtrans," *Prolet. Community Serv. Dev. J.*, vol. 1, no. 1, pp. 8–14, 2023, doi: 10.61098/proletariancomdev.v1i1.63.

[15] L. Hakim, S. P. Kristanto, M. N. Shodiq, and E. Amaliyah, "Aplikasi Penerimaan dan Pengeluaran Kas Berbasis Web dan WhatsApp Gateway," *J. Tekno Kompak*, vol. 15, no. 1, pp. 13–24, 2021, doi: 10.33365/jtk.v15i1.900.

[16] R. S. Pressman and B. R. Maxim, *Software Engineering: A Practitioner's Approach*, 8th ed. New York: McGraw-Hill Education, 2015.

[17] T. Pricillia and Z. Zulfachmi, "Perbandingan Metode Pengembangan Perangkat Lunak (Waterfall, Prototype, RAD)," *J. Bangkit Indones.*, vol. 10, no. 1, pp. 6–12, 2021, doi: 10.52771/bangkitindonesia.v10i1.153.

[18] D. G. A. Candra and P. P. Pardika, "Analisa dan Perancangan Sistem Informasi Manajemen Pengolahan Data Pesanan Sablon di KYSR Store Menggunakan Metode Waterfall," *JAMI J. Ahli Muda Indones.*, vol. 5, no. 2, pp. 134–147, 2024, doi: 10.46510/jami.v5i2.306.

[19] W. N. Cholifah, Y. Yulianingsih, and S. M. Sagita, "Pengujian Black Box Testing pada Aplikasi Action & Strategy Berbasis Android dengan Teknologi Phonegap," *STRING (Satuan Tulisan Ris. dan Inov. Teknol.)*, vol. 3, no. 2, pp. 206–210, 2018, doi: 10.30998/string.v3i2.3048.

[20] E. Setiana, M. R. Ramadhan, B. Budiman, and R. Y. Rakhman, "Pengujian Perangkat Lunak Metode Black Box pada Aplikasi Sistem Pakar Pola Latihan dan Asupan Makanan," *Nuansa Inform.*, vol. 18, no. 1, pp. 68–74, 2024, doi: 10.25134/ilkom.v18i1.67.
