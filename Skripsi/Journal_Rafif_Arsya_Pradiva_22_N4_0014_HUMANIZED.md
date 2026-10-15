# DESIGN AND IMPLEMENTATION OF AN INTEGRATED BOARDING HOUSE MANAGEMENT INFORMATION SYSTEM WITH MIDTRANS PAYMENT GATEWAY: A CASE STUDY OF ASRI BOARDING HOUSE

1Rafif Arsya Pradiva, 2Andre Kurniawan Pamudji, 3Erdhi Widyarto Nugroho  
Department of Information Systems, Faculty of Computer Science  
Universitas Katolik Soegijapranata, Semarang, Indonesia  
122n40014@student.unika.ac.id, 2andre.kurniawan@unika.ac.id, 3erdhi@unika.ac.id  

---

Abstract—Student accommodation facilities in expanding higher education districts often face administrative vulnerabilities stemming from manual bookkeeping, fragmented text messaging, and unverified bank transfers. These informal workflows routinely lead to uncollected rental revenue, room reservation collisions during academic admissions, and labor-intensive financial balancing. To resolve these operational bottlenecks, this research develops, implements, and evaluates an integrated management information system for Asri Boarding House in Semarang, Indonesia. Structured under a Waterfall software engineering lifecycle, the web platform couples Laravel 11 with a third-normal-form (3NF) relational database of 22 entities in MySQL 8.0, integrating Midtrans Snap v2 for Bank BCA Virtual Account settlement and Fonnte for automated WhatsApp notifications. Concurrency control during peak booking demand is enforced through pessimistic row-level database locks (`lockForUpdate()`), complemented by an operational post-checkout sanitation quarantine protocol. An automated cron billing pipeline schedules idempotent monthly invoices, calculates a flat 5% calendar-rollover late fee, and triggers guardian arrears escalation upon consecutive payment defaults. Functional evaluation across 60 black box test scenarios achieved a 100% pass rate, corroborated by 510 automated PHPUnit integration tests executing 2,211 assertions. Field usability testing with the senior resident manager (20 years of operational experience, verified via audio log `REKAMAN_UX_ADMIN_KOST_2026.m4a`, duration 23m 14s) confirmed that the system compressed the monthly reconciliation cycle from 3–5 days to instant ledger generation, earning a 9.5/10 usability score.

Keywords—Automated billing, Concurrency control, Laravel 11, Management information system, Payment gateway

---

## I. INTRODUCTION

Student residential facilities around university campuses experience sustained high demand, especially across expanding higher education clusters such as the Tembalang district in Semarang, Indonesia. Within this educational perimeter, privately owned boarding houses (*kost*) supply multi-month housing for thousands of students attending nearby tertiary institutions. While commercial property management platforms and enterprise software [1], [2] have expanded rapidly, independent boarding facilities in this micro-market still rely predominantly on handwritten journals, physical paper receipts, and unstructured chat applications. This administrative informality introduces chronic operational friction, including uncollected rental arrears, book-balancing errors, booking race conditions during annual university admissions, and uncoordinated debt tracking.

Representing these operational vulnerabilities in practice, Asri Boarding House occupies a two-story residential facility situated on Jl. Maera Sari No. 1 / No. 12, Tembalang, Semarang (Postal Code 50275). Its rental inventory encompasses 32 student rooms categorized into three distinct pricing tiers: VIP (6 rooms tariffed at IDR 1,400,000 monthly), Deluxe (3 rooms at IDR 950,000 monthly), and Standard (23 rooms at IDR 750,000 monthly). When fully tenanted, the accommodation maintains an aggregate monthly gross revenue baseline of IDR 28,500,000. Daily administrative operations have long been managed by a single resident manager, Mr. Asep (48 years old, with 20 years of stewardship), who historically relied on paper carbon receipt books and manual cash ledgers.

Direct on-site observation and operational audits highlighted four recurring operational vulnerabilities:
1. *Delayed Invoicing and Collection Friction*: Rent collection depended on mental recall and physical notes. With billing dates dispersed across disparate schedules, collection deadlines frequently slipped, causing cash flow instability.
2. *Concurrent Reservation Collisions*: In peak enrollment periods, booking requests arrived concurrently through verbal walk-ins and informal messaging. Prospective tenants reserving remotely were often promised units already claimed on-site, producing embarrassing double bookings.
3. *Uncoordinated Arrears Escalation*: When residents defaulted on payments, the property lacked a systematic channel to alert registered parents or legal guardians, allowing unpaid balances to accumulate unchecked.
4. *Labor-Intensive Account Balancing*: Compiling monthly financial statements required manual collation of carbon receipt slips against physical cash drawers, a process demanding 3 to 5 working days and vulnerable to calculation discrepancies.

To contextualize these operational hurdles within software engineering scholarship, we assessed recent boarding house management systems and payment gateway implementations. Existing studies have investigated room catalog portals [4], promotional web applications [5], [6], and mobile lodging marketplaces [11], yet these systems consistently treated financial settlement as an external, manual procedure. Table 1 outlines comparative characteristics of relevant prior literature.

**Table 1. SYSTEMATIC COMPARATIVE ANALYSIS OF RELATED WORKS**

| Author & Year | Domain & Method | Key Architectural Scope | Identified Gap / Limitation |
| :--- | :--- | :--- | :--- |
| Cornellya & Afriyadi (2025) [3] | Kost Management (Waterfall) | Laravel room booking, tenant logs, administrative reporting; black box tested. | Lacks automated recurring billing schedules, late fee calculations, and guardian escalation. |
| Nizar (2021) [4] | E-Kost Portal (Web Eng.) | Web catalog for accommodation listings and reservation inquiries. | Omits payment gateway integration; relies on manual bank transfer slips; no concurrency locking. |
| Malaikosa & Mokola (2024) [10] | Kost Monitoring (RAD) | Web-based room monitoring and manual rent collection tracking. | No hybrid walk-in/online synchronization; lacks database row locking to prevent booking collisions. |
| Sutisna & Aziz (2025) [7] | Rental Commerce (Waterfall) | Event equipment rental platform integrating Midtrans Snap API. | Transient e-commerce retail model; lacks monthly recurring billing cycles and arrears escalation. |
| Fatman et al. (2023) [8] | Retail POS (Web Dev.) | Midtrans payment gateway implementation for MSME retail transactions. | Limited to point-of-sale checkout; does not model room occupancy states or cyclical lease contracts. |
| Hakim et al. (2021) [15] | Cash Ledger (Web Dev.) | Cash flow recording coupled with Fonnte WhatsApp gateway. | Focused solely on cash recording; disconnected from automated billing engines and payment gateways. |

As Table 1 indicates, existing architectures either restrict payment gateways to isolated one-off retail checkouts or provide informational catalogs detached from contractual tenancy cycles. Prior platforms omit recurring billing engines operating across calendar boundaries, database-level locking against concurrent reservation attempts, physical quarantine holds during room turnover, and automated parental escalation.

To systematically overcome these administrative vulnerabilities, this investigation designs, implements, and evaluates an integrated management information system specifically adapted to the operating model of Asri Boarding House. The principal software engineering contributions include:
1. Development of an autonomous scheduling engine that computes idempotent recurring monthly invoices and applies a strict 5% calendar-rollover penalty within transactional boundaries;
2. Formulation of a staged arrears notification protocol that issues tenant reminders and programmatically escalates unresolved defaults to documented guardians through WhatsApp;
3. Elimination of concurrent room reservation collisions during enrollment surges via pessimistic row-level locking (`SELECT ... FOR UPDATE`) within isolated database transactions;
4. Implementation of an administrative post-occupancy quarantine mechanism that locks vacated units in a maintenance hold pending physical room turnover;
5. Introduction of an in-browser receipt compilation pipeline using `html2pdf.js`, supplying cryptographic-styled transaction proofs while relieving server CPU and shared storage resources.

---

## II. METHOD

### A. Research Paradigm and Data Triangulation
This study applies a Software Engineering Research and Development (R&D) methodology grounded in the classical Waterfall Software Development Life Cycle (SDLC) [16], [17], [18]. A structured, phase-oriented progression—spanning requirements analysis, architectural specification, implementation, systematic verification, and production rollout—was selected to guarantee strict alignment between real-world boarding house policies and software constraints. Empirical requirements engineering relied on three triangulated evidence sources:
1. *Comprehensive Facility Survey*: A full physical inspection of all 32 rooms spanning two floors, shared utilities, and administrative desks at Asri Boarding House to catalog unit layouts, physical amenities, and operational tiers.
2. *Semi-Structured Operational Interviews*: Repeated interviews with resident manager Mr. Asep (48 years old, 20 years of operational experience) to formalize unwritten operational conventions, grace period habits, payment patterns, and cash balancing routines.
3. *Archival Documentation Audit*: An exhaustive review of historical physical ledgers, duplicate carbon receipts, active tenant cards, and building layout blueprints.

### B. Software Architecture and Presentation Layer
To ensure architectural separation of concerns, the application is organized under a 3-tier Model-View-Controller (MVC) pattern utilizing Laravel 11 executing in a PHP 8.2 runtime environment. Rather than centralizing transactional processes inside routing handlers, core domain workflows are encapsulated within dedicated service classes residing in `app/Services/` (`BillingService`, `MidtransService`, `ReservasiService`, `TransisiPenyewaService`, and `FonnteService`), keeping controller classes minimal and facilitating isolated integration testing. On the client tier, the user interface adopts a Neo-Brutalist design system characterized by bold high-contrast borders and an OLED Black administrative theme. User interface ergonomics prioritize touch accessibility, providing 56-pixel interactive touch targets that comfortably surpass the 44-pixel baseline mandated by Web Content Accessibility Guidelines (WCAG 2.1).

### C. Relational Schema and Soft-Delete Unicity
The data layer runs on MySQL 8.0 InnoDB, normalized to Third Normal Form (3NF) across 22 relational entities. Critical domain tables incorporate soft-delete timestamps (`deleted_at`) for audit compliance and historical recovery. However, standard SQL unique keys conflict with soft-deletion mechanisms because archived rows prevent re-registration of identical unique values (e.g., NIK, email, room numbers). To resolve this without abandoning database-level unicity, we implemented **Virtual Generated Columns** combined with unique indices. As shown in Listing 1, each virtual column evaluates to the original column value only while `deleted_at IS NULL`, yielding `NULL` once an entry is marked as deleted. Because SQL semantics permit multiple `NULL` values in a unique index, uniqueness is strictly enforced across active records while preserving soft-deleted historical data.

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
Race conditions pose a substantial operational hazard during university intake surges, where prospective tenants may attempt to book the identical unit at the same second. Addressing this concurrency challenge at the data tier, the system employs **pessimistic row-level locking** (`SELECT ... FOR UPDATE`) enclosed within isolated database transactions. Listing 2 details how `ReservasiService` intercepts incoming reservations by acquiring an exclusive row lock on the target room record before evaluating date overlap. Secondary requests attempting to reserve the locked entity are placed in a wait state until the primary transaction completes execution or triggers a rollback, enforcing deterministic serializability across room inventories.

```php
// Listing 2. Pessimistic Locking Implementation in ReservasiService
return DB::transaction(function () use ($data) {
    $kamar = Kamar::lockForUpdate()->findOrFail($data['kamar_id']);

    if ($this->cekDoubleBooking($kamar, $data['tanggal_mulai'], $data['tanggal_selesai'])) {
        throw ValidationException::withMessages([
            'kamar_id' => 'Room is already booked for the selected date range.'
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
Recurring financial routines rely on host daemon cron tasks triggering Laravel's scheduled commands at one-minute intervals (`* * * * * php artisan schedule:run`). On the 1st day of each calendar month at 00:05 WIB, the automated billing routine executes within `BillingService` through a staged pipeline:
1. *Initial Invoice Issuance (1st of the Month)*: The scheduler iterates through active lease agreements (`penyewa.status = 'aktif'`), generating `pending` billing records with a standard settlement window closing on the 10th. Tailored WhatsApp dispatches containing assigned virtual account details are immediately pushed to residents through the Fonnte gateway.
2. *Intra-Month Grace Period (11th to Final Day of Month)*: Invoices that remain unpaid past the 10th transition into `terlambat` status but maintain an **IDR 0** penalty assessment throughout the active month, while periodic automated reminder messages are dispatched via WhatsApp to prompt settlement.
3. *Calendar-Boundary Late Fee Assessment (1st of Month $M+1$)*: Upon crossing into the subsequent calendar month without settlement, the system calculates an idempotent 5% penalty levied against the room's base tariff:

$$\text{Late Fee} = \begin{cases} 0.05 \times \text{Tarif Pokok}, & \text{if } \text{status} = \text{'terlambat'} \land \text{nominal\_denda} = 0 \land \Delta\text{Month} \ge 1 \\ 0, & \text{otherwise} \end{cases} \quad (1)$$

Should default persist across two back-to-back monthly cycles, the platform initiates guardian escalation, dispatching automated arrears notices directly to the verified mobile contact of the tenant's parent or legal guardian.

### F. Verification Framework
Software correctness, robustness, and operational feasibility were verified using a multi-tiered empirical testing framework comprising four coordinated methodologies: (1) black box behavioral validation spanning 60 structured test scenarios developed according to standard software testing guidelines [19], [20]; (2) unit and integration regression suites executed through 510 automated PHPUnit test routines encompassing 2,211 assertions; (3) payment lifecycle simulation conducted within the Midtrans Snap v2 sandbox environment using the Bank BCA Virtual Account testing tool; and (4) direct field usability evaluation alongside structured interviews administered to the senior resident manager (accumulating 20 years of on-site administrative tenure), captured as digital audio evidence (`REKAMAN_UX_ADMIN_KOST_2026.m4a`, total duration 23m 14s).

---

## III. RESULTS AND DISCUSSION

### A. RESULT

#### 1. Relational Database Cluster Topology
Normalized to Third Normal Form (3NF), the persistence layer comprises 22 interrelated database tables grouped into six operational clusters, as outlined in Table 2.

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
To accommodate differing tenant behaviors, the platform coordinates both digital self-service bookings and on-premise administrative walk-ins:
- *Digital Self-Service Flow*: Remote prospective residents navigate a guided 5-step checkout interface: (1) Room Inventory Inspection, (2) 16-Digit National Identity (NIK) Syntax Verification, (3) Rental Duration Selection (Daily, Weekly, or Monthly tiers) with automated tenure discounts, (4) Settlement Scheme Designation (30% Down Payment or 100% Full Payment), and (5) Modal Payment via Midtrans Snap v2.
- *Front-Desk Walk-In Intake*: For prospective tenants appearing directly on site, the resident manager accesses `/admin/penyewa/create` to record resident and emergency guardian credentials, record direct cash or bank transfers, and assign rooms. The application establishes the user account, tenancy contract, and corresponding invoice record inside an isolated atomic database transaction.

#### 3. Cryptographic Webhook Security and Signature Matching
To prevent spoofed payment confirmations, asynchronous HTTP webhook dispatches emitted by the Midtrans gateway are validated against an HMAC-style SHA-512 digest calculation:

$$\text{Signature}_{\text{calc}} = \text{SHA-512}(\text{order\_id} + \text{status\_code} + \text{gross\_amount} + \text{ServerKey}) \quad (2)$$

The resulting digest is verified against the inbound `signature_key` parameter. Any discrepancy causes the callback listener to terminate with an immediate HTTP 403 Forbidden error. When a valid `settlement` signal is confirmed, `MidtransCallbackController` applies state transitions within a transactional block reinforced with `lockForUpdate()`. In scenarios where network retries deliver identical webhooks, the system observes the existing `lunas` or settled status and skips secondary ledger writes, guaranteeing idempotent financial processing.

#### 4. Operational Quarantine and Digital Receipts
Room transitions enforce an explicit physical quarantine protocol upon resident departure. When the manager registers a checkout through `/admin/penyewa/checkout`, the resident’s record is updated to `nonaktif`, yet the room inventory state stays locked as `terisi` (highlighted visually in red on the administrative dashboard). The unit is kept unavailable for new reservations while maintenance personnel perform deep cleaning and facility repairs. Only after physical turnover is signed off does the manager press 'Release Room', returning the unit status to `tersedia` and executing `Cache::forget('kamar_aktif_landing')` to refresh public catalog availability.

To furnish payment proof without burdening hosting infrastructure, the tenant dashboard generates A5 digital receipts on the client side through `html2pdf.js`. Rendering document layouts directly in browser memory protects shared server hosting from CPU contention and storage bloat. Heavyweight server-side document rendering through Dompdf is restricted to monthly financial ledgers compiled exclusively by management.

#### 5. Production Deployment and Security Hardening
The software solution was placed into live production on Hostinger LiteSpeed Enterprise Cloud hosting (`https://asriboardinghouse.weatso.id/`). Platform hardening incorporated TLS 1.3 transport security (attaining an SSL Grade A profile), frontend asset minification and chunk compression via Vite, Web server `.htaccess` directives barring external access to sensitive paths (`.env`, `.git`), and restricting application entry points strictly to the `/public` directory.

#### 6. Empirical Quantitative Evaluation
Functional compliance and behavioral reliability were tested against 60 predefined test cases divided across six operational subsystems, outlined in Table 3. Across all 60 scenarios, the system exhibited consistent behavioral conformity, registering a 100% pass rate. Targeted security probes appraised Role-Based Access Control (RBAC) boundaries across protected routes (`/admin`, `/penyewa`, `/reservasi`) alongside OAuth compliance [9], affirming effective defense against Insecure Direct Object Reference (IDOR) manipulation as shown in Table 4. Payment lifecycle transitions were likewise verified within the Midtrans sandbox environment across all seven lifecycle conditions, as summarized in Table 5.

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
On-site administrative workflow testing and usability validation were carried out at Asri Boarding House directly with resident manager Mr. Asep (48 years of age, 20 years of administrative experience) using the live production instance (`https://asriboardinghouse.weatso.id/`). Conducted under informed consent, the comprehensive session was recorded as continuous digital audio evidence (`REKAMAN_UX_ADMIN_KOST_2026.m4a`, duration 23m 14s). Ten operational modules were systematically examined, with detailed findings compiled in Table 6.

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
| **Overall Retrospective** | 20 years of manual books with periodic cash discrepancies. | *"Compared to twenty years of handwritten bookkeeping, the difference is night and day. Managing operations is substantially easier, and cash reconciliation errors from misplaced paper slips have been eliminated."* Score: **9.5/10**. | Confirmed production readiness and operational adoption. |

*Source: Empirical on-site operational evaluation and digital audio recording by authors (2026)*

---

### B. DISCUSSION

The empirical evaluation demonstrates that pairing automated payment orchestration with strict database-level concurrency controls and explicit business logic overcomes the core operational vulnerabilities of manual boarding house management.

A critical comparison with existing literature highlights several architectural distinctions. Earlier boarding house systems developed by Cornellya & Afriyadi [3] and Nizar [4] introduced web interfaces for room browsing and record logging, yet they relegated payment verification to manual administrative inspection of uploaded bank slips. By embedding Midtrans Snap v2, the platform presented here shifts transaction settlement from subjective visual checks to deterministic, cryptographically authenticated webhook listeners. Similarly, while lodging architectures presented by Malaikosa & Mokola [10] and Purnia et al. [11] utilized standard web submission forms vulnerable to concurrent booking conflicts, our implementation enforces database-level serialization via `lockForUpdate()`. This mechanism guarantees ACID isolation, precluding double-allocation race conditions during high-volume academic enrollment windows.

The research also addresses a conceptual mismatch in how payment gateways have traditionally been integrated into rental systems. Prior implementations by Sutisna & Aziz [7], Fatman et al. [8], Surya Pratama [12], Pramita et al. [13], and Wijaya et al. [14] deployed Midtrans within retail e-commerce, school fee portals, or service checkouts. Such environments process discrete, one-off purchases that terminate upon payment confirmation. By contrast, residential tenancy management operates across multi-month contractual lifecycles governed by recurring billing milestones, designated grace periods, rollover late fee rules, and multi-party escalation pathways. By integrating Midtrans webhooks with an autonomous cron-driven scheduling pipeline, this work extends payment gateway functionality beyond simple retail checkouts into multi-month contractual lifecycle governance.

From a data modeling perspective, the application of Virtual Generated Columns resolves a frequent dilemma in web application engineering: enforcing database unicity across soft-deleted records. In standard Laravel applications, introducing soft deletes often leads practitioners to remove database-level unique constraints in favor of application-layer validation rules, leaving the underlying relational engine susceptible to concurrent insertion conflicts. By defining virtual columns that evaluate to `NULL` upon record deletion, the system enforces unicity strictly at the MySQL engine layer while preserving complete historical audit trails.

Operationally, testing the platform on-site with the senior resident manager (20 years of operational experience) provides grounded empirical validation within the target facility. Compressing the monthly financial reconciliation cycle from 3–5 working days to immediate automated ledger generation demonstrates practical viability for owner-operated properties. Nonetheless, because this investigation was structured as an in-depth single-site case study involving one primary administrative stakeholder, these qualitative insights reflect local operational workflows rather than a universally standardized benchmark. Diverse property layouts, varying staff digital literacy levels, and differing municipal rental norms across other student housing districts may introduce alternative operational requirements. This inherent scope limitation highlights the importance of multi-branch architectural scaling and comparative empirical investigations across broader accommodation classes, as articulated in the proposed research directions.

---

## IV. CONCLUSION

This study engineered, deployed, and empirically evaluated an integrated management information system for Asri Boarding House, resolving entrenched operational inefficiencies caused by manual bookkeeping and fragmented communications. Implemented on Laravel 11 with a 3NF-normalized MySQL 8.0 relational schema across 22 entities, the platform delivers automated recurring billing, real-time Virtual Account settlement via Midtrans Snap v2, and automated WhatsApp notifications through Fonnte. The integration of pessimistic database row locking (`lockForUpdate()`) and an administrative post-checkout sanitation lock successfully eliminated room reservation collisions across all tested scenarios, while client-side `html2pdf.js` receipt rendering bypassed server-side storage and computational bottlenecks.

Empirical verification established the platform's reliability, recording a 100% pass rate across 60 functional black box test scenarios, confirmed isolation of role-based authorization boundaries, and verified transaction integrity across all seven Midtrans lifecycle states. On-site usability evaluation with the senior resident manager (20 years of operational tenure) demonstrated operational acceptance, compressing the monthly financial balancing workflow from 3–5 working days to immediate automated reporting, protecting a gross monthly revenue baseline of IDR 28,500,000, and earning a 9.5/10 usability score.

To advance research in digital property management systems, five future technical directions are identified:
1. *Multi-Branch Architectural Scaling*: Expanding the relational schema with a `cabang_id` foreign key and multi-tenant scoping to support distributed, multi-property portfolios under centralized administration.
2. *Native Mobile Client Development*: Developing dedicated iOS and Android mobile interfaces using Flutter or React Native, interacting with backend services through Laravel Sanctum-secured RESTful endpoints.
3. *IoT Hardware Interfacing*: Integrating Internet of Things (IoT) hardware, including automated keyless smart door locks provisioned with time-bounded PINs upon settlement and Smart KWH digital sub-meters for automated utility billing.
4. *Midtrans Auto-Refund API Integration*: Automating the reimbursement of tenant security deposits upon physical checkout through direct programmatic integration with Midtrans refund endpoints.
5. *Double-Entry Accrual Accounting Engine*: Expanding the current cash-flow ledger into a comprehensive double-entry accounting module featuring general ledgers, asset depreciation tracking, and automated property rental tax computations.

---

## ACKNOWLEDGMENT

The authors express their heartfelt appreciation to the Department of Information Systems, Faculty of Computer Science, Universitas Katolik Soegijapranata for institutional and technical guidance provided during this study. Sincere gratitude is also conveyed to Mr. Asep and the proprietors of Asri Boarding House, Tembalang, Semarang, for their invaluable operational partnership, administrative access, and active engagement throughout the field testing and system validation phases.

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
