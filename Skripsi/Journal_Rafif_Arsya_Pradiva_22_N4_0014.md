# DESIGN AND IMPLEMENTATION OF AN INTEGRATED BOARDING HOUSE MANAGEMENT INFORMATION SYSTEM WITH MIDTRANS PAYMENT GATEWAY: A CASE STUDY OF ASRI BOARDING HOUSE

1Rafif Arsya Pradiva, 2Andre Kurniawan Pamudji  
Department of Information Systems, Faculty of Computer Science  
Universitas Katolik Soegijapranata, Semarang, Indonesia  
122n40014@student.unika.ac.id, 2andre@unika.ac.id  

---

Abstract—Student accommodation providers in developing higher education clusters frequently rely on physical logbooks and informal messaging. This administrative reliance causes persistent revenue leakage, room double-booking during admission cycles, and prolonged monthly financial reconciliation. This paper details the design, implementation, and empirical evaluation of an integrated management information system for Asri Boarding House in Semarang, Indonesia. Structured under a Waterfall software engineering lifecycle, the web platform integrates Laravel 11, a third-normal-form (3NF) MySQL 8.0 relational schema across 22 entities, the Midtrans Snap v2 payment gateway tailored for Bank BCA Virtual Accounts, and Fonnte WhatsApp automation. Concurrency control is achieved using pessimistic row-level database locks (`lockForUpdate()`), supported by a physical post-checkout quarantine protocol. An automated cron billing pipeline calculates idempotent monthly invoices and applies a flat 5% calendar-rollover late fee alongside automated guardian escalation. Functional evaluation across 60 black box test scenarios yielded a 100% pass rate, backed by 510 automated PHPUnit feature tests executing 2,211 assertions. In-situ operational evaluation with the facility's senior resident manager (20 years of operational experience), recorded via digital audio (`REKAMAN_UX_ADMIN_KOST_2026.m4a`, duration 23m 14s), showed that the system compressed the monthly accounting cycle from 3–5 days to instant reporting, attaining a 9.5/10 usability rating.

Keywords—Automated billing, Concurrency control, Laravel 11, Management information system, Payment gateway

---

## I. INTRODUCTION

Student accommodations near university campuses face sustained demand, particularly in expanding Indonesian education districts such as Tembalang, Semarang. There, privately operated boarding houses (*kost*) provide multi-month residential lodging for thousands of students enrolled at neighboring higher education institutions within the Tembalang university district. Although property management software (*proptech*) and modern management information systems [1], [2] have matured commercially, independent boarding houses in this region continue to depend on physical paper records, informal cash transfers, and unstructured messaging apps. This reliance introduces persistent operational friction: uncollected rent, reconciliation errors, room reservation conflicts during peak admission periods, and unmonitored resident arrears.

Asri Boarding House, located at Jl. Maera Sari No. 1 / No. 12, Tembalang, Semarang (Postal Code 50275), reflects these operational constraints. The property consists of 32 rooms arranged across two floors and organized into three tiers: VIP (6 rooms at IDR 1,400,000/month), Deluxe (3 rooms at IDR 950,000/month), and Standard (23 rooms at IDR 750,000/month). At full capacity, the facility generates a gross monthly revenue baseline of IDR 28,500,000. For over twenty years, daily operations have been administered by a single resident manager, Mr. Asep (48 years old, 20 years of operational tenure), through carbon receipt books and handwritten cash journals.

Direct field observation and archival auditing identified four primary operational bottlenecks:
1. *Billing Delays*: Rent collection depended on individual recall and paper records, causing routine collection lapses and cash flow disruptions.
2. *Reservation Collisions*: During admission cycles, inquiries were handled via verbal promises or ad-hoc chats. Remote applicants frequently selected units already promised to walk-in visitors, causing double bookings.
3. *Unstructured Arrears Escalation*: When accounts fell overdue, the property lacked protocols to alert registered guardians, allowing unpaid balances to accumulate undetected.
4. *Labor-Intensive Reconciliation*: Preparing monthly balances required manual cross-referencing of paper slips against cash drawers, absorbing 3 to 5 working days and introducing bookkeeping discrepancies.

To contextualize these practical challenges within the software engineering literature, we evaluated existing boarding house management platforms and payment integrations. Prior works explored web catalogs [4], promotional portals [5], [6], and mobile rental marketplaces [11], but consistently omitted integrated payment channels. Table 1 summarizes related empirical studies.

**Table 1. SYSTEMATIC COMPARATIVE ANALYSIS OF RELATED WORKS**

| Author & Year | Domain & Method | Key Architectural Scope | Identified Gap / Limitation |
| :--- | :--- | :--- | :--- |
| Cornellya & Afriyadi (2025) [3] | Kost Management (Waterfall) | Laravel room booking, tenant logs, administrative reporting; black box tested. | Lacks automated recurring billing schedules, late fee calculations, and guardian escalation. |
| Nizar (2021) [4] | E-Kost Portal (Web Eng.) | Web catalog for accommodation listings and reservation inquiries. | Omits payment gateway integration; relies on manual bank transfer slips; no concurrency locking. |
| Malaikosa & Mokola (2024) [10] | Kost Monitoring (RAD) | Web-based room monitoring and manual rent collection tracking. | No hybrid walk-in/online synchronization; lacks database row locking to prevent booking collisions. |
| Sutisna & Aziz (2025) [7] | Rental Commerce (Waterfall) | Event equipment rental platform integrating Midtrans Snap API. | Transient e-commerce retail model; lacks monthly recurring billing cycles and arrears escalation. |
| Fatman et al. (2023) [8] | Retail POS (Web Dev.) | Midtrans payment gateway implementation for MSME retail transactions. | Limited to point-of-sale checkout; does not model room occupancy states or cyclical lease contracts. |
| Hakim et al. (2021) [15] | Cash Ledger (Web Dev.) | Cash flow recording coupled with Fonnte WhatsApp gateway. | Focused solely on cash recording; disconnected from automated billing engines and payment gateways. |

As shown in Table 1, prior systems treat payment gateways as one-off retail checkouts or provide informational catalogs lacking lifecycle governance. Existing architectures omit cyclical tenancy billing across calendar boundaries, database concurrency controls against reservation collisions, physical post-checkout quarantine holds, and automated guardian escalation protocols.

To resolve these limitations, this paper presents an integrated management information system for Asri Boarding House. The primary engineering contributions are:
1. An automated cron billing engine enforcing an idempotent flat 5% calendar-rollover late fee secured by database transactions;
2. An automated two-stage arrears notification pipeline alerting tenants and escalating severe arrears to registered guardians via WhatsApp;
3. Elimination of room reservation collisions via atomic database transactions and pessimistic `SELECT ... FOR UPDATE` row locks;
4. An operational quarantine workflow retaining vacated rooms in an occupied state until physical staff sanitization is completed;
5. A client-side receipt compilation architecture utilizing `html2pdf.js`, generating downloadable payment proofs without server CPU or storage overhead.

---

## II. METHOD

### A. Research Paradigm and Data Triangulation
This work follows a Software Engineering Research and Development (R&D) methodology based on the classical Waterfall Software Development Life Cycle (SDLC) [16], [17], [18]. A sequential progression—encompassing requirements analysis, architectural design, implementation, verification testing, and operational deployment—was chosen to guarantee strict traceability between field operational rules and software components. Baseline functional requirements were gathered through a three-pronged empirical triangulation process:
1. *Physical Facility Inspection*: An exhaustive survey of all 32 rooms across 2 floors, utilities, and front-desk workflows at Asri Boarding House, documenting unit layouts, amenities, and pricing bands.
2. *Semi-Structured Operational Interviews*: In-depth sessions with resident manager Mr. Asep (48 years old, 20 years of experience) to elicit unwritten operational practices, grace periods, remittance habits, and cash balancing routines.
3. *Archival Ledger Audit*: An empirical audit of physical cash journals, carbon receipt archives, master tenant logs, and technical blueprint specifications.

### B. Software Architecture and Presentation Layer
The platform employs a 3-tier Model-View-Controller (MVC) architecture built with Laravel 11 on PHP 8.2. Domain operations are decoupled into dedicated service classes in `app/Services/` (`BillingService`, `MidtransService`, `ReservasiService`, `TransisiPenyewaService`, `FonnteService`), maintaining lean controllers and isolating transactional logic from HTTP routing. The presentation tier follows Neo-Brutalist design principles with high-contrast borders and an OLED Black administrative theme. Interactive elements feature a 56-pixel touch target, exceeding Web Content Accessibility Guidelines (WCAG 2.1) minimum recommendations (44 pixels).

### C. Relational Schema and Soft-Delete Unicity
The persistence tier runs on MySQL 8.0 InnoDB (normalized to 3NF across 22 entities). Core entities utilize soft deletes (`deleted_at` timestamp) for audit compliance. Because standard unique constraints conflict with soft deletion by blocking re-registration of archived values (e.g., NIK, email, room numbers), we enforced unicity at the database engine level using **Virtual Generated Columns** paired with unique indexes. As shown in Listing 1, virtual columns evaluate to the original value only when `deleted_at IS NULL`, returning `NULL` once deleted. Under standard SQL semantics, multiple `NULL` entries do not violate uniqueness, maintaining integrity strictly across active records.

```php
// Listing 1. Virtual Generated Columns for Soft-Delete Unicity
Schema::create('users', function (Blueprint $table) {
    $table->id();
    $table->string('email', 100);
    $table->softDeletes();
    $table->string('active_email')
          ->virtualAs('CASE WHEN deleted_at IS NULL THEN email ELSE NULL END');
    $table->unique('active_email', 'uq_users_active_email');
});
```

### D. Concurrency Control and Transactional Invariants
During peak admission periods, simultaneous booking requests create race conditions that permit double bookings. To prevent this anomaly, the system enforces **pessimistic row locking** via `SELECT ... FOR UPDATE` within atomic database transactions. As shown in Listing 2, `ReservasiService` locks the target room row upon booking initiation. Competing transactions are blocked until the active transaction commits or rolls back, serializing access to inventory.

```php
// Listing 2. Pessimistic Locking Implementation in ReservasiService
return DB::transaction(function () use ($data) {
    $kamar = Kamar::lockForUpdate()->findOrFail($data['kamar_id']);

    if ($this->cekDoubleBooking($kamar, $data['tanggal_mulai'], $data['tanggal_selesai'])) {
        throw ValidationException::withMessages([
            'kamar_id' => 'Kamar sudah ter-booking pada rentang tanggal tersebut.'
        ]);
    }

    $rincianHarga = $this->hitungHarga($kamar, $data['tipe_sewa'], $data['durasi']);

    return Reservasi::create(array_merge($data, [
        'order_id'     => "RSV-{$data['user_id']}-" . time(),
        'total_harga'  => $rincianHarga['total_harga'],
        'nominal_dp'   => $rincianHarga['nominal_dp'],
        'nominal_sisa' => $rincianHarga['nominal_sisa'],
        'status'       => 'pending',
    ]));
});
```

### E. Autonomous Billing Pipeline and Late Fee Algorithm
Billing schedules are orchestrated by host cron invoking Laravel's task scheduler every minute (`* * * * * php artisan schedule:run`). The monthly billing job executes on the 1st day of each month at 00:05 WIB in `BillingService`:
1. *Invoice Generation (1st of Month)*: Active agreements (`penyewa.status = 'aktif'`) are evaluated, creating `pending` invoices due on the 10th. WhatsApp payment notices containing virtual account numbers are dispatched via Fonnte.
2. *Grace Period Phase (11th to Month-End)*: Overdue invoices transition to `terlambat`, carrying an **IDR 0** penalty during the current month, accompanied by periodic automated WhatsApp payment reminders.
3. *Calendar-Rollover Penalty Application (1st of Month $M+1$)*: If unpaid at the start of the subsequent calendar month, an idempotent flat 5% fee is applied:

$$\text{Late Fee} = \begin{cases} 0.05 \times \text{Tarif Pokok}, & \text{if } \text{status} = \text{'terlambat'} \land \text{nominal\_denda} = 0 \land \Delta\text{Month} \ge 1 \\ 0, & \text{otherwise} \end{cases} \quad (1)$$

When a resident defaults across two consecutive billing cycles, the engine triggers guardian escalation, dispatching an automated alert to the registered parent or guardian's phone number.

### F. Verification Framework
The platform was evaluated via four complementary verification methods: (1) a 60-scenario behavioral black box test suite adhering to established software testing standards [19], [20]; (2) 510 automated PHPUnit integration tests containing 2,211 assertions; (3) end-to-end sandbox verification using the Midtrans Snap v2 Bank BCA Virtual Account simulator; and (4) an on-site operational evaluation and structured interview with the senior resident manager (20 years of experience), recorded via digital audio (`REKAMAN_UX_ADMIN_KOST_2026.m4a`, duration 23m 14s).

---

## III. RESULTS AND DISCUSSION

### A. RESULT

#### 1. Relational Database Cluster Topology
The persistence architecture comprises 22 relational entities normalized to 3NF, organized into six functional clusters as summarized in Table 2.

**Table 2. CONSOLIDATED RELATIONAL DATABASE SCHEMA TOPOLOGY (22 ENTITIES)**

| Functional Cluster | Associated Entities | Key Invariants & Relational Constraints |
| :--- | :--- | :--- |
| **Identity & Access** | `users` | Roles, soft deletes, virtual unicity (`active_email`, `active_no_hp`, `active_nik`). |
| **Room Master** | `kamar`, `fasilitas`, `kamar_fasilitas` | 32 units, 2 floors, 3 pricing tiers; M:N amenities; `active_nomor_kamar` unicity. |
| **Tenancy Lifecycle** | `penyewa`, `reservasi` | Active contracts, base rates, deposits, guardian contacts; DP (30%) or full payment. |
| **Finance & Billing** | `tagihan`, `pembayaran`, `pengeluaran` | Monthly obligations, invoice numbers, late fees; payment channels; operational logs. |
| **Communication** | `chat_messages`, `guest_chat_threads`, `guest_chat_messages`, `log_notifikasi`, `pengumuman`, `notifikasi_khusus` | Pre-payment chat, public guest inquiry threads, immutable WhatsApp/email logs, broadcasts, priority transactional alerts. |
| **Operations & Content** | `keluhan`, `peraturan`, `customer_reviews`, `faqs`, `galleries`, `settings`, `whatsapp_clicks` | Maintenance tickets with photos, boarding house rules, moderated reviews, FAQs, photo galleries, system config, landing CTA analytics. |

#### 2. Hybrid Dual-Channel Booking Synchronization
The platform unifies online self-service reservations and on-premise walk-in onboarding:
- *Online Self-Service Stepper*: Prospective tenants complete a 5-step wizard: (1) Unit Selection, (2) 16-Digit NIK Validation, (3) Tenancy Term Selection (Daily, Weekly, Monthly) with duration discounts, (4) Payment Schema Selection (30% DP or 100% full), and (5) Midtrans Snap modal checkout.
- *Administrative Walk-In Registration*: For visitors registering on-premise (`/admin/penyewa/create`), the manager inputs tenant and guardian details, logs cash/bank deposits, and assigns rooms. The system creates the user profile, lease contract, and initial invoice within a single atomic database transaction.

#### 3. Cryptographic Webhook Security and Signature Matching
Inbound webhook notifications from Midtrans are verified cryptographically via SHA-512 hashing:

$$\text{Signature}_{\text{calc}} = \text{SHA-512}(\text{order\_id} + \text{status\_code} + \text{gross\_amount} + \text{ServerKey}) \quad (2)$$

The computed hash is compared against the received `signature_key`. Mismatched signatures trigger an immediate HTTP 403 Forbidden response. Upon receiving a verified `settlement` payload, `MidtransCallbackController` executes database mutations inside a transaction using `lockForUpdate()`. If duplicate webhooks arrive, the system detects the settled invoice state and bypasses redundant ledger insertions, guaranteeing ledger idempotency.

#### 4. Operational Quarantine and Digital Receipts
Vacated rooms undergo a physical quarantine workflow: when a checkout is processed administratively (`/admin/penyewa/checkout`), tenant status shifts to `nonaktif` while room status remains locked as `terisi` (displayed in red). The unit remains unbookable until staff complete physical sanitization, fixture repairs, and manually execute 'Release Room', resetting status to `tersedia` and invalidating cache via `Cache::forget('kamar_aktif_landing')`.

For payment receipts, the active tenant portal compiles A5 digital payment receipts client-side using `html2pdf.js`. Constructing receipts in the browser canvas eliminates server CPU bottlenecks and persistent storage consumption on shared hosting. Server-side PDF generation via Dompdf is reserved strictly for monthly administrative balance sheets.

#### 5. Production Deployment and Security Hardening
The platform was deployed on Hostinger LiteSpeed Enterprise Cloud infrastructure (`https://asriboardinghouse.weatso.id/`). Hardening measures included enforcing TLS 1.3 encryption (SSL Grade A), Vite 5.x asset minification and compression, `.htaccess` rules restricting access to sensitive configuration files (`.env`, `.git`), and enforcing routing strictly through `/public`.

#### 6. Empirical Quantitative Evaluation
The functional integrity of the platform was evaluated across 60 test scenarios spanning six operational domains, as summarized in Table 3. All 60 scenarios executed with a 100% pass rate. Security testing evaluated Role-Based Access Control (RBAC) across portal gates (`/admin`, `/penyewa`, `/reservasi`) and OAuth compliance [9], confirming Insecure Direct Object Reference (IDOR) immunity as detailed in Table 4. The digital payment pipeline was validated using Midtrans's sandbox and BCA Virtual Account simulator across all seven lifecycle states, as summarized in Table 5.

**Table 3. FUNCTIONAL BLACK BOX TESTING SUITE MATRIX (60 SCENARIOS)**

| Domain Code | Functional Testing Scope | Scenarios | Passed | Failed | Pass Rate |
| :---: | :--- | :---: | :---: | :---: | :---: |
| **DOM-01** | Identity Authentication, Socialite Google OAuth, & Profile Gates | 12 | 12 | 0 | 100% |
| **DOM-02** | Public Catalog, Neo-Brutalist Layout, & WCAG 2.1 Touch Targets | 10 | 10 | 0 | 100% |
| **DOM-03** | 5-Step Reservation Stepper, Discounts, & Concurrency Locks | 10 | 10 | 0 | 100% |
| **DOM-04** | Active Tenant Portal, Self-Service VA, & html2pdf.js Receipts | 9 | 9 | 0 | 100% |
| **DOM-05** | Admin Console, Walk-In Onboarding, & Inspection Quarantine | 13 | 13 | 0 | 100% |
| **DOM-06** | Task Scheduler, Auto-Billing Cron, 5% Late Fees, & Guardian Alerts | 6 | 6 | 0 | 100% |
| **Total** | **Comprehensive Functional Platform Testing** | **60** | **60** | **0** | **100%** |

**Table 4. SECURITY, RBAC, AND IDOR HARDENING VERIFICATION**

| No. | Security Test Vector | Simulated Inbound Request | Expected System Behavior | Empirical Outcome | Status |
| :---: | :--- | :--- | :--- | :--- | :---: |
| 1 | Unauthenticated Admin Access | Anonymous visitor requests `/admin/dashboard` | Redirect to `/admin/login` | Redirected to administrative login | Passed |
| 2 | Privilege Escalation by Tenant | Active resident requests `/admin/laporan` | Deny authorization; return HTTP 403 | HTTP 403 Forbidden returned | Passed |
| 3 | Public Access to Tenant Portal | Anonymous visitor requests `/penyewa/tagihan` | Intercept; redirect to `/penyewa/login` | Redirected to tenant login | Passed |
| 4 | Pending Tenant Early Access | Pending applicant requests `/penyewa/dashboard` | Middleware blocks; display modal | Intercepted with activation warning | Passed |
| 5 | IDOR Invoice Tampering | Tenant A alters URL parameter to view Tenant B invoice | Scope Eloquent query to active user | HTTP 403 Forbidden returned | Passed |
| 6 | IDOR Receipt Tampering | User alters transaction receipt ID in download route | Validate tenant-payment relationship | HTTP 403 Forbidden returned | Passed |

**Table 5. MIDTRANS BCA VIRTUAL ACCOUNT TRANSACTION LIFECYCLE VERIFICATION**

| Step | Lifecycle Phase | Inbound / Outbound Action | System & Webhook Response | Transaction State | Result |
| :---: | :--- | :--- | :--- | :--- | :---: |
| 1 | VA Number Issuance | Tenant selects Bank Transfer $\rightarrow$ BCA VA | Snap API returns 16-digit VA code; 24h expiry | `pending` | Passed |
| 2 | Bank Simulator Inquiry | Manager inputs VA into BCA Simulator | Midtrans Cloud returns Asri Boarding House bill | `pending` | Passed |
| 3 | Payment Settlement | User triggers payment on BCA Simulator | Webhook dispatches; SHA-512 validated; settled | `settlement` | Passed |
| 4 | Payment Expiration | 24-hour settlement window elapses | Webhook dispatches `expire`; room released | `expire` | Passed |
| 5 | User Cancellation | User aborts checkout on Snap UI | Webhook dispatches `cancel`; reservation unheld | `cancel` | Passed |
| 6 | Signature Tamper Attack | Webhook delivered with forged SHA-512 signature | Server detects hash mismatch; rejects with 403 | Discarded | Passed |
| 7 | Duplicate Webhook Delivery | Identical `settlement` payload sent twice | Lock detects `lunas` status; mutation bypassed | `settlement` | Passed |

#### 7. Empirical Operational Usability & Stakeholder Evaluation
Operational feasibility and ergonomics were evaluated on-site at Asri Boarding House with senior resident manager Mr. Asep (48 years old, 20 years of operational tenure), using the production deployment (`https://asriboardinghouse.weatso.id/`). The entire session was recorded via continuous digital audio (`REKAMAN_UX_ADMIN_KOST_2026.m4a`, duration 23m 14s) following verbal informed consent. Ten operational inquiry modules were systematically tested, synthesized into Table 6.

**Table 6. EMPIRICAL OPERATIONAL VALIDATION AND RESIDENT MANAGER ASSESSMENT**

| Evaluated Module | Previous Manual Operational Condition | Post-Deployment Assessment (Mr. Asep, 20 Yrs Experience) | Operational Impact |
| :--- | :--- | :--- | :--- |
| **Public Storefront & Media** | Inquiries answered via repetitive phone calls; rates unlisted. | High-contrast typography is clear for older eyes; upfront pricing eliminates repetitive rate inquiries from out-of-town parents. | Information transparency; reduced redundant inquiries. |
| **Docked WhatsApp CTA** | Contact info buried in offline business cards or ad-hoc chats. | Perfectly positioned for mobile thumbs; connects directly to manager for instant prospective tenant inquiries. | Streamlined inquiry conversion. |
| **OLED Dark Admin Console** | Manual tallying of occupied/vacant rooms in paper books. | Gentle on eyes during long sessions; auto-calculates room occupancy and net monthly cashflow with zero arithmetic error. | Eliminated ledger calculation errors. |
| **Walk-In Intake Form** | Manual paper intake forms; guardian contacts frequently lost. | Concise intake fields; permanently registers guardian numbers for immediate emergency and payment coordination. | Established immutable guardian records. |
| **Cash Settlement & Receipts** | Carbon receipt books; manual writing and tearing of paper slips. | Settles invoices and issues stamped digital receipts in one click; no risk of lost paper slips or handwriting disputes. | Instant digital proof; zero paper waste. |
| **Flat 5% Late Fee Policy** | Vague oral reminders; arbitrary penalties creating friction. | Fair and appropriate; IDR 0 grace period during the month accommodates delayed allowances; flat 5% on rollover enforces discipline. | Predictable billing enforcement. |
| **Post-Checkout Quarantine** | Vacated rooms verbally promised before physical cleaning. | Vital operational necessity; red-lock prevents premature booking while linens and bathrooms are sanitized, avoiding complaints. | Protected service quality and hospitality standards. |
| **One-Click Financial Reports** | 3 to 5 business days spent gathering drawer receipts. | Compiles complete monthly revenue, expense, and net profit PDF/Excel reports instantly for owner submission. | Cycle compressed from 3–5 days to instant. |
| **Tenant Self-Service & Tickets** | Verbal maintenance reports forgotten or lost in chat apps. | Students pay via BCA VA without visiting ATMs; photo-documented tickets allow immediate dispatch of plumbers or electricians. | Faster maintenance resolution; zero ATM travel. |
| **Overall Retrospective** | 20 years of manual books with periodic cash discrepancies. | *"Compared to the last twenty years of handwritten books, the difference is night and day! Managing the boarding house feels immensely lighter, and my mind is at ease because cash discrepancies and misplaced paper notes are completely eliminated."* Score: **9.5/10**. | Confirmed production readiness and operational adoption. |

*Source: Empirical on-site operational evaluation and digital audio recording by authors (2026)*

---

### B. DISCUSSION

The empirical findings confirm that integrating automated payment processing, pessimistic concurrency control, and explicit business rules resolves structural inefficiencies inherent in manual boarding house workflows.

Contrasting this architecture with prior literature illustrates key engineering advancements. Systems by Cornellya & Afriyadi [3] and Nizar [4] established web-based room listings but treated payment reconciliation as an external, manual task. By integrating Midtrans Snap v2, our platform automates payment verification and state transitions via cryptographically signed webhooks, eliminating manual bank slip reviews. In parallel, whereas the reservation architectures of Malaikosa & Mokola [10] and Purnia et al. [11] relied on unconstrained web forms vulnerable to concurrent booking conflicts, our implementation enforces database-level serialization through `lockForUpdate()`, providing robust transaction serialization guarantees against double bookings during peak enrollment traffic.

A critical architectural distinction concerns payment gateway application models. Implementations by Sutisna & Aziz [7], Fatman et al. [8], Surya Pratama [12], Pramita et al. [13], and Wijaya et al. [14] integrated Midtrans into retail e-commerce, school fees, or service checkouts. In retail applications, transactions represent discrete, isolated purchases. Tenancy management, by contrast, operates on long-term cyclical contracts requiring recurring billing, grace periods, penalty rules, and multi-party escalation. By synthesizing Midtrans with an autonomous cron engine, this research extends payment automation into multi-month lifecycle governance.

Schema engineering utilizing Virtual Generated Columns addresses a prevalent challenge in web systems: preserving database unicity across soft-deleted records. In standard Laravel deployments, implementing soft deletes often forces developers to abandon database-level unique constraints in favor of application-level validation, leaving the database vulnerable to race conditions. Establishing virtual generated columns that resolve to `NULL` upon deletion maintains unicity at the database engine level, ensuring data integrity without sacrificing historical audit trails.

From an operational standpoint, the usability evaluation with the senior resident manager (20 years of operational tenure) provides grounded empirical validation within the target facility. The compression of monthly financial reconciliation from 3–5 working days to instantaneous reporting demonstrates practical viability for owner-operated properties. However, because this evaluation was conducted within a single-site case study involving one primary administrative stakeholder, these qualitative findings reflect specific local operational workflows rather than a generalized, industry-wide benchmark. Organizational factors, varying staff digital literacy levels, and differing municipal rental conventions across other student housing clusters may introduce alternative workflow requirements. This limitation reinforces the necessity of multi-branch architectural scaling and wider empirical evaluations across diverse property types, as outlined in the future research directions.

---

## IV. CONCLUSION

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
