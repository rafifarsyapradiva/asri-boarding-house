# DESIGN AND IMPLEMENTATION OF AN INTEGRATED BOARDING HOUSE MANAGEMENT INFORMATION SYSTEM WITH MIDTRANS PAYMENT GATEWAY: A CASE STUDY OF ASRI BOARDING HOUSE

1Rafif Arsya Pradiva, 2Andre Kurniawan Pamudji  
Department of Information Systems, Faculty of Computer Science  
Universitas Katolik Soegijapranata, Semarang, Indonesia  
122n40014@student.unika.ac.id, 2andre@unika.ac.id  

---

Abstract—Student accommodation providers in developing higher education clusters frequently rely on physical logbooks and informal messaging. This administrative reliance causes persistent revenue leakage, room double-booking during admission cycles, and prolonged monthly financial reconciliation. This paper details the design, implementation, and empirical evaluation of an integrated management information system for Asri Boarding House in Semarang, Indonesia. Structured under a Waterfall software engineering lifecycle, the web platform integrates Laravel 11, a third-normal-form (3NF) MySQL 8.0 relational schema across 22 entities, the Midtrans Snap v2 payment gateway tailored for Bank BCA Virtual Accounts, and Fonnte WhatsApp automation. Concurrency control is achieved using pessimistic row-level database locks (`lockForUpdate()`), supported by a physical post-checkout quarantine protocol. An automated cron billing pipeline calculates idempotent monthly invoices and applies a flat 5% calendar-rollover late fee alongside automated guardian escalation. Functional evaluation across 60 black box test scenarios yielded a 100% pass rate, backed by 510 automated PHPUnit feature tests executing 2,211 assertions. In-situ operational evaluation with the facility's resident manager (20 years of operational experience), recorded via digital audio (`REKAMAN_UX_ADMIN_KOST_2026.m4a`, duration 23m 14s), showed that the system compressed the monthly accounting cycle from 3–5 days to instant reporting, attaining a 9.5/10 usability rating.

Keywords—Automated billing, Concurrency control, Laravel 11, Management information system, Payment gateway

---

## I. INTRODUCTION

Student accommodations near university campuses face sustained demand, particularly in expanding Indonesian education districts such as Tembalang, Semarang. There, privately operated boarding houses (*kost*) provide multi-month residential lodging for thousands of students enrolled at neighboring institutions, including Universitas Diponegoro and Politeknik Negeri Semarang. Although property management software (*proptech*) has matured commercially, independent boarding houses in this region continue to depend on physical paper records, informal cash transfers, and unstructured messaging apps. This reliance on fragmented administrative habits introduces persistent operational friction: uncollected rent, reconciliation errors, room reservation conflicts during peak admission periods, and unmonitored resident arrears.

Asri Boarding House, located at Jl. Maera Sari No. 1 / No. 12, Tembalang, Semarang (Postal Code 50275), clearly reflects these operational constraints. The property consists of 32 rooms arranged across two floors and organized into three tiers: VIP (6 rooms at IDR 1,400,000 per month), Deluxe (3 rooms at IDR 950,000 per month), and Standard (23 rooms at IDR 750,000 per month). At full capacity, the facility generates a gross monthly revenue baseline of IDR 28,500,000. For more than twenty years, daily operations have been managed by a single resident manager, Mr. Asep (48 years old, with 20 years of managerial experience), who historically administered tenancy records through physical carbon receipt books and handwritten cash journals.

Direct field observation and archival auditing identified four primary operational bottlenecks in this manual workflow:
1. *Payment Tracking and Billing Delays*: Rent schedules depended on individual recall and handwritten logs. Without automated billing triggers, collection dates routinely lapsed, creating cumulative arrears and cash flow disruptions.
2. *Room Reservation Collisions*: During university admission cycles, room inquiries were settled through verbal conversations or ad-hoc messaging. Remote applicants frequently selected units that had already been promised to walk-in visitors, causing double-booking conflicts and administrative disputes.
3. *Unstructured Arrears Escalation*: When student accounts fell seriously overdue, the property had no reliable protocol to contact registered parents or guardians, permitting unpaid balances to grow across multiple academic terms without parental awareness.
4. *Labor-Intensive Financial Reconciliation*: Preparing the monthly cash balance required cross-referencing physical paper slips against loose drawer cash. This manual accounting process absorbed 3 to 5 working days each month and frequently produced bookkeeping discrepancies.

To situate these practical challenges within the software engineering literature, we evaluated existing boarding house management platforms and payment integrations. Table 1 summarizes related empirical studies across methodologies, architectural scopes, and identified gaps.

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

A critical evaluation of Table 1 reveals an identifiable research gap in the literature. Most existing systems treat payment gateways as one-off e-commerce checkout mechanisms or offer informational catalogs that lack ongoing lifecycle governance. Prior software architectures do not account for cyclical tenancy billing across monthly calendar boundaries, omit database-level concurrency controls needed to prevent reservation collisions under concurrent booking requests, overlook physical room-sanitization holds post-checkout, and lack automated parental escalation protocols for prolonged defaults.

To address these limitations, we designed and deployed an integrated management information system tailored to the operational realities of Asri Boarding House. The primary engineering contributions of this work are:
1. An automated cron-driven billing engine that enforces an idempotent flat 5% calendar-rollover late fee secured by pessimistic database transactions;
2. An automated two-stage arrears notification pipeline that alerts tenants and escalates severe arrears to registered guardians via WhatsApp;
3. Strict prevention of room reservation collisions through atomic database transactions and pessimistic `SELECT ... FOR UPDATE` row locks;
4. An operational quarantine workflow that holds vacated rooms in an occupied state until physical staff inspection and maintenance are completed;
5. A client-side receipt rendering architecture utilizing `html2pdf.js`, generating downloadable payment proofs in the browser without server CPU or persistent storage consumption.

---

## II. Method

### A. Research Paradigm
This work follows a Software Engineering Research and Development (R&D) methodology based on the classical Waterfall Software Development Life Cycle (SDLC) [16], [17], [18]. A linear-sequential sequence—comprising requirements analysis, architectural and system design, implementation, verification testing, and operational deployment—was chosen to guarantee strict traceability between field operational rules and the corresponding software models.

### B. Data Collection and Operational Triangulation
Baseline functional requirements were gathered through a three-pronged empirical triangulation process:
1. *Physical Facility Inspection*: An exhaustive survey of all 32 rooms across both floors, communal utility connections, and front-desk workflows at Asri Boarding House, documenting unit layouts, amenities, and pricing bands.
2. *Semi-Structured Operational Interviews*: In-depth interviews with the resident manager, Mr. Asep (48 years of age, 20 years of operational tenure). These sessions elicited unwritten operational practices, customary payment grace periods, typical student remittance schedules, and manual cash balancing routines.
3. *Physical Document and Ledger Audit*: A quantitative review of carbon-copy receipt books, handwritten cash ledgers, bank passbooks, and administrative logs recorded between 2021 and 2025.

### C. Software Architecture and Component Decoupling
The platform is organized around a 3-tier Model-View-Controller (MVC) architecture using the Laravel 11 framework on PHP 8.2. To maintain lean controllers and isolate transactional business logic from HTTP transport concerns, domain operations are separated into dedicated service classes in `app/Services/`.

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

The user interface follows Neo-Brutalist design principles, using high-contrast borders and clear visual boundaries. Primary interactive elements feature a 56-pixel touch target, surpassing the Web Content Accessibility Guidelines (WCAG 2.1) minimum target recommendation of 44 pixels. In addition, the administrative console incorporates an OLED Black dark theme to reduce eye strain during extended administrative sessions.

### D. Relational Schema Engineering and Virtual Generated Columns
The persistence tier runs on MySQL 8.0 with the InnoDB storage engine to preserve ACID guarantees. The relational schema is normalized to Third Normal Form (3NF), comprising 22 entities.

To support historical auditing, core records use soft deletes via a `deleted_at` timestamp. Standard relational unique constraints conflict with soft deletion because discarded rows remain in the table, preventing legitimate re-registration of previously used values (such as national identity numbers [NIK], email addresses, telephone numbers, or room numbers). Rather than relaxing constraints to application-level checks, we resolved this conflict at the database level using **Virtual Generated Columns** paired with unique indexes. As shown in Listing 1, each virtual column evaluates to the original identifier only when `deleted_at IS NULL`, returning `NULL` once soft-deleted. Under standard relational index semantics, multiple `NULL` values are permitted in unique indexes, enforcing uniqueness strictly across currently active records while retaining historical rows for audit trails.

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
Concurrent room selection during university admission periods presents a race condition. If two prospective tenants attempt to reserve the same unit simultaneously, non-locking architectures can allow both requests to read the room as available, resulting in a double-booking anomaly.

To prevent this condition, the system uses **pessimistic row locking** through MySQL's `SELECT ... FOR UPDATE` directive inside an atomic transaction. Upon receiving a booking submission, `ReservasiService` places an exclusive lock on the requested room record. Any competing transaction attempting to inspect or modify the same room is blocked until the active transaction commits or rolls back, serializing access to shared inventory. Listing 2 details this locking implementation.

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
Billing schedules are executed by the host operating system's cron daemon invoking Laravel's task scheduler every minute (`* * * * * php artisan schedule:run`). The core monthly billing job executes on the 1st day of each month at 00:05 WIB within `BillingService`.

The billing process is divided into three consecutive phases:
1. *Invoice Generation (1st of Month)*: The scheduler iterates through active lease agreements (`penyewa.status = 'aktif'`). It calculates the base rental balance, applies any amortized advance-payment deductions, and generates a new `tagihan` entry in `pending` status with a payment deadline on the 10th. A payment notice containing virtual account details is immediately sent to the resident's WhatsApp via Fonnte.
2. *Grace Period Phase (11th to Month-End)*: If an invoice remains unpaid past the 10th, its status transitions to `terlambat`. In keeping with local operational policy, the penalty remains set to **IDR 0** for the duration of the current calendar month. Friendly automated reminders are sent every three days.
3. *Calendar-Rollover Penalty Application (1st of Month $M+1$)*: If the invoice is still unsettled at the start of the following calendar month, the engine applies a one-time late fee of **5% of the baseline monthly rent**. An idempotency condition prevents the fee from being evaluated or compounded multiple times if the scheduler re-executes:

$$\text{Late Fee} = \begin{cases} 0.05 \times \text{Tarif Pokok}, & \text{if } \text{status} = \text{'terlambat'} \land \text{nominal\_denda} = 0 \land \Delta\text{Month} \ge 1 \\ 0, & \text{otherwise} \end{cases} \quad (1)$$

When a resident defaults on two consecutive monthly cycles, the billing engine triggers an escalation protocol, dispatching an automated alert to the registered parent or legal guardian's phone number.

### G. Verification Framework
The platform was evaluated through four complementary verification methods:
1. *Behavioral Black Box Testing*: A 60-scenario functional test battery assessing six operational domains: authentication, public storefront catalog, 5-step booking wizard, tenant self-service portal, administrative control console, and background task scheduling.
2. *Automated Feature Testing*: A suite of 510 automated PHPUnit integration tests containing 2,211 assertions, verifying route responses, session handling, database state transitions, and edge cases.
3. *Payment Gateway Sandbox Validation*: End-to-end verification within the Midtrans Snap v2 sandbox environment using the Bank BCA Virtual Account simulator, checking virtual account creation, inquiry callbacks, settlement notifications, expiration handling, and cryptographic hash verification.
4. *In-Situ Usability Acceptance*: A live operational walkthrough and structured interview conducted on-site with the senior resident manager (20 years of experience). The entire session was recorded on digital audio (`REKAMAN_UX_ADMIN_KOST_2026.m4a`, duration 23m 14s) across 10 functional inquiry modules.

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
The platform unifies online self-service reservations and on-premise walk-in customer intake into a single operational workflow:
- *Online Self-Service Stepper*: Prospective tenants navigate a 5-step horizontal wizard: (1) Unit Verification, (2) 16-Digit National Identity Number (NIK) Validation, (3) Tenancy Term Selection (Daily, Weekly, Monthly) with automated annual duration discounts, (4) Payment Schema Selection (30% down-payment or 100% full settlement), and (5) Midtrans Snap modal checkout.
- *Administrative Walk-In Registration*: For walk-in applicants visiting the premises without prior online bookings, the administrator uses an onboarding portal (`/admin/penyewa/create`). The manager enters identity details, records manual cash or bank deposits, uploads physical receipt scans, and assigns room units. The system creates the user profile, lease contract, and initial invoice within a single atomic database transaction.

#### 3. Cryptographic Webhook Security and Signature Matching
To guard against request tampering and spoofing on the public webhook endpoint, inbound notifications from Midtrans are verified cryptographically. The server computes a SHA-512 digital signature based on transaction parameters and the shared secret server key:

$$\text{Signature}_{\text{calc}} = \text{SHA-512}(\text{order\_id} + \text{status\_code} + \text{gross\_amount} + \text{ServerKey}) \quad (2)$$

The computed hash is matched against the `signature_key` received in the HTTP POST body. If the hashes differ, the request is immediately terminated with an HTTP 403 Forbidden response, and transaction state changes are blocked.

When a verified `settlement` payload is received, `MidtransCallbackController` executes the database update inside a transaction using `lockForUpdate()`. If duplicate webhook calls arrive for the same `order_id`, the system detects that the invoice is already marked as `lunas` and bypasses duplicate ledger insertion, ensuring ledger idempotency.

#### 4. Post-Checkout Operational Quarantine Protocol
Field observations highlighted that rooms vacated by departing tenants require cleaning, linen replacement, and facility repairs before new occupants arrive.

To prevent premature re-booking, the system implements an operational quarantine hold: when a resident's checkout is processed administratively (`/admin/penyewa/checkout`), the system updates the tenant's status to `nonaktif` while retaining the room's status as `terisi` (displayed in red). The unit remains locked against public reservation until staff perform on-site cleaning, inspect fixtures, and manually trigger the 'Release Room' action in the administrative console. This release switches the room status to `tersedia` and clears the room availability cache via `Cache::forget('kamar_aktif_landing')`.

#### 5. Zero-Server-Storage Digital Receipt Compilation
Standard web applications frequently generate PDF invoices using server-side rendering libraries such as Dompdf or Snappy. In shared hosting environments with constrained CPU and RAM quotas, compiling large PDF documents degrades server responsiveness and consumes persistent disk storage.

To resolve this bottleneck, the active tenant portal compiles A5 digital payment receipts entirely within the user's browser using `html2pdf.js`. When a tenant requests a receipt, the client DOM constructs an official stamped receipt containing transaction metadata, QR verification markers, and formal typography. The receipt is rendered directly to an A5 canvas and downloaded as a PDF without allocating memory or storage on the hosting server. Server-side PDF generation via Dompdf is used only for monthly administrative balance sheets.

#### 6. Production Deployment and Security Hardening
The production platform was deployed on Hostinger LiteSpeed Enterprise Cloud infrastructure mapped to `https://asriboardinghouse.weatso.id/`. Production hardening measures included:
- Enforcing TLS 1.3 encryption, achieving an SSL Grade A rating with automated HTTP-to-HTTPS canonical redirects;
- Minifying CSS and JavaScript assets via Vite 5.x, keeping the total initial page load payload under 1.2 MB;
- Hardening `.htaccess` directives to block public HTTP access to sensitive root-level files, including `.env`, `.git`, composer configuration files, and local SQLite/log files;
- Restricting application entry routing strictly through the `/public` root directory.

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
To assess real-world operational feasibility, interface ergonomics, and domain workflow alignment, an in-depth operational interview and live system walkthrough was conducted with the primary operational stakeholder, Mr. Asep (48 years of age, 20 years of continuous boarding house management experience). The evaluation took place at the on-site property management office in Tembalang, using live workflows on the production deployment (`https://asriboardinghouse.weatso.id/`).

Rather than administering detached Likert surveys to non-administrative proxies, the evaluation adopted a qualitative case methodology grounded in direct operational execution. The entire session was captured via continuous digital audio recording (`REKAMAN_UX_ADMIN_KOST_2026.m4a`, duration 23 minutes 14 seconds), initiated following recorded verbal informed consent. The interview systematically examined 10 functional modules across the public storefront, administrative operations, billing policies, and tenant services. Table 6 compiles the structured interview questions and the verbatim responses provided by the senior resident manager.

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
| 10 | **20-Year Retrospective Reflection & Usability Acceptance** | *"Final question Mr. Asep, after testing all modules directly: compared to your 20 years of conventional manual bookkeeping, does this web system make operational administration substantially lighter and effectively prevent manual cash discrepancies? What satisfaction score do you give from 1 to 10?"* | *"Compared to the last twenty years of handwritten books, the difference is night and day, Mr. Rafif! Managing the boarding house feels immensely lighter, and my mind is at ease because cash discrepancies and misplaced paper notes are completely eliminated. Everything is transparent and automated. From 1 to 10, I give it a firm **9.5 or even 10**! The system is fully ready and tremendously beneficial for Asri Boarding House."* |

*Source: Empirical on-site operational interview and digital audio recording by authors (2026)*

Reflecting upon the transition from two decades of manual bookkeeping, Mr. Asep observed: *"Compared to the last twenty years of handwritten books, the difference is night and day. Managing the boarding house is immensely lighter, and my mind is at ease because cash discrepancies and missing receipts are completely eliminated."*

Figure 2. Side-by-side operational testing and think-aloud evaluation with senior resident manager.

---

### B. DISCUSSION

The empirical findings demonstrate that integrating automated payment processing, pessimistic concurrency control, and explicit business rule enforcement resolves structural inefficiencies that have long characterized manual boarding house operations.

Comparing this architecture with prior literature illustrates key engineering advancements. The platforms developed by Cornellya & Afriyadi [3] and Nizar [4] established the utility of web-based room listings but treated payment reconciliation as an external, manual task. By integrating Midtrans Snap v2, the current system automates payment verification and state transitions via cryptographically signed webhooks, removing manual bank slip reviews. Similarly, whereas the reservation architectures of Malaikosa & Mokola [10] and Purnia et al. [11] relied on unconstrained web forms vulnerable to concurrent booking conflicts, this implementation enforces database-level serialization through `lockForUpdate()`, providing robust transaction serialization guarantees against double bookings during peak enrollment traffic.

A central architectural consideration concerns the operational model of payment gateways. Prior implementations by Sutisna & Aziz [7], Fatman et al. [8], and Surya Pratama [12] integrated Midtrans into retail e-commerce or point-of-sale environments. In retail applications, transactions represent discrete, isolated purchases. Tenancy management, by contrast, operates on long-term cyclical contracts requiring recurring billing, grace periods, penalty rules, and multi-party communication. By synthesizing the payment gateway with an autonomous cron engine, this research extends payment automation into multi-month lifecycle management.

Schema engineering utilizing Virtual Generated Columns addresses a prevalent challenge in web systems: preserving database unicity across soft-deleted records. In standard Laravel deployments, implementing soft deletes often forces developers to abandon database-level unique constraints in favor of application-level validation, leaving the database vulnerable to race-condition corruption. Establishing virtual generated columns that resolve to `NULL` upon deletion maintains unicity at the database engine level, ensuring data integrity without sacrificing historical audit trails.

From an operational perspective, the usability evaluation with the senior resident manager (20 years of operational experience) provides grounded empirical validation within the target boarding house. The compression of monthly financial reconciliation from 3–5 working days to instantaneous reporting demonstrates practical viability for owner-operated facilities. However, because this evaluation was conducted within a single-site case study involving one primary administrative stakeholder, these qualitative findings reflect specific local operational workflows rather than a generalized, industry-wide benchmark. Organizational factors, varying staff digital literacy levels, and differing municipal rental conventions across other student housing clusters may introduce alternative workflow requirements. This limitation reinforces the necessity of multi-branch architectural scaling and wider empirical evaluations across diverse property types, as outlined in the future research directions.

---

## IV. Conclusion

This research engineered and deployed an integrated management information system for Asri Boarding House, resolving long-standing operational challenges associated with manual record-keeping. Built upon Laravel 11 and a 3NF-normalized MySQL 8.0 schema, the platform delivers automated recurring billing, real-time payment gateway integration via Midtrans Snap v2, and multi-channel WhatsApp alerts via Fonnte. The application of pessimistic row locking (`lockForUpdate()`) and post-checkout room quarantine holds prevented room reservation collisions across all evaluated concurrent scenarios, while client-side `html2pdf.js` compilation removed server storage overhead for tenant receipts.

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
