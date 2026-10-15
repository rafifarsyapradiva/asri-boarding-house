# DESIGN AND IMPLEMENTATION OF AN INTEGRATED BOARDING HOUSE MANAGEMENT INFORMATION SYSTEM WITH MIDTRANS PAYMENT GATEWAY: A CASE STUDY OF ASRI BOARDING HOUSE

1Rafif Arsya Pradiva, 2Andre Kurniawan Pamudji, 3Erdhi Widyarto Nugroho  
Department of Information Systems, Faculty of Computer Science  
Universitas Katolik Soegijapranata, Semarang, Indonesia  
122n40014@student.unika.ac.id, 2andre.kurniawan@unika.ac.id, 3erdhi@unika.ac.id  

---

Abstract—Student accommodation facilities in expanding higher education districts often suffer from administrative vulnerabilities caused by manual bookkeeping, fragmented messaging, and unverified bank transfers. These informal workflows lead to uncollected revenue, booking collisions during academic admissions, and labor-intensive financial reconciliation. To resolve these bottlenecks, this study designs, implements, and evaluates an integrated management information system for Asri Boarding House in Semarang, Indonesia. Developed using the Waterfall lifecycle, the platform couples Laravel 11 with a 3NF relational database of 22 entities in MySQL 8.0, integrating Midtrans Snap v2 for BCA Virtual Account settlements and Fonnte for automated WhatsApp dispatches. Concurrency control during peak booking demand is enforced via pessimistic row-level locking (`lockForUpdate()`), complemented by an operational post-checkout sanitation hold. An autonomous cron billing pipeline schedules monthly invoices, calculates an idempotent 5% calendar-rollover late fee, and triggers guardian arrears escalation upon consecutive defaults. Functional testing across 60 black-box scenarios achieved a 100% pass rate, corroborated by 510 automated PHPUnit integration tests with 2,211 assertions. Field usability evaluation with the senior resident manager confirmed the system compressed monthly reconciliation from 3–5 days to instant ledger generation, achieving a 9.5/10 usability score.

Keywords—Automated billing, Concurrency control, Laravel 11, Management information system, Payment gateway

---

## I. INTRODUCTION

Student residential facilities surrounding higher education clusters experience sustained, cyclical demand, particularly across growing university districts such as Tembalang in Semarang, Indonesia. Within this perimeter, privately owned boarding houses (*kost*) provide multi-month lodging for thousands of undergraduate and postgraduate scholars. While commercial enterprise platforms and web-based property systems [1], [2] have expanded rapidly, independent boarding houses in this micro-market still rely predominantly on manual carbon-copy receipt books, handwritten cash journals, and unstructured instant messaging. This persistent administrative informality introduces chronic operational friction, including uncollected rental arrears, book-balancing discrepancies, reservation race conditions during annual university intake cycles, and uncoordinated default escalation.

Representing these operational vulnerabilities in practice, Asri Boarding House operates a two-story residential facility located on Jl. Maera Sari No. 1 / No. 12, Tembalang, Semarang (Postal Code 50275). Its rental inventory encompasses 32 student rooms categorized into three distinct pricing tiers: VIP (6 rooms tariffed at IDR 1,400,000 monthly), Deluxe (3 rooms at IDR 950,000 monthly), and Standard (23 rooms at IDR 750,000 monthly). When fully tenanted, the accommodation maintains an aggregate monthly gross revenue baseline of IDR 28,500,000. Daily administrative operations have long been stewarded by a single resident manager, Mr. Asep (48 years of age, with 20 years of operational experience), who historically relied on paper carbon receipt books and physical cash drawers.

Direct on-site observation, operational audits, and interviews highlighted four recurring administrative vulnerabilities:
1. *Delayed Invoicing and Collection Friction*: Rent collection depended on human memory and scattered physical notes. With billing dates dispersed across disparate calendar dates, collection deadlines frequently slipped, causing cash flow instability.
2. *Concurrent Reservation Collisions*: In peak academic admissions periods, reservation inquiries arrived simultaneously through verbal walk-ins and informal messaging. Prospective tenants reserving remotely were often promised units already claimed on-site, producing double bookings.
3. *Uncoordinated Arrears Escalation*: When residents defaulted on monthly rent, the facility lacked a systematic mechanism to notify registered parents or legal guardians, allowing unpaid balances to accumulate unchecked.
4. *Labor-Intensive Account Balancing*: Compiling monthly financial statements required manual collation of carbon receipt slips against physical cash drawers, a process demanding 3 to 5 working days and vulnerable to calculation discrepancies.

To contextualize these operational hurdles within software engineering scholarship, we assessed recent boarding house management systems and payment gateway implementations. Existing studies have investigated room catalog portals, lodging marketplaces, and web-based rental platforms, yet these systems consistently treated financial settlement as an external, manual procedure. Specifically, Nizar [3] designed an E-Kost web catalog for accommodation listings and reservation inquiries, yet completely omitted payment gateway integration, relying on manual bank transfer slips without database-level concurrency locking. Purnia et al. [4] developed a mobile lodging marketplace using prototyping, yet left rental transactions disconnected from automated billing engines and institutional payment channels. Hidayat et al. [5] applied Model-View-Controller (MVC) architecture to web-based room reservations, but concentrated primarily on client-side catalog navigation rather than cyclical lease contracts. 

In more recent scholarship, Cornellya & Afriyadi [6] engineered a Laravel-based booking platform for Kost Tya with tenant record logging, but omitted automated recurring billing schedules, late fee calculations, and guardian arrears escalation. Similarly, Malaikosa & Mokola [7] presented a web-based boarding house monitoring system developed via Rapid Application Development (RAD); however, their platform lacked hybrid walk-in/online synchronization and database row locking to prevent reservation collisions. When examining payment gateways, Sutisna & Aziz [8] implemented Midtrans Snap API for an event equipment rental platform, but their architecture operated under a transient retail e-commerce model rather than multi-month contractual tenancy cycles. Fatman et al. [9] integrated Midtrans for MSME retail transactions, yet their solution was restricted to point-of-sale checkout without modeling tenancy states or room turnover. Furthermore, Hakim et al. [10] combined web cash ledgers with Fonnte WhatsApp dispatches, but focused purely on cash recording detached from payment gateways and tenancy lifecycle events.

As this analysis indicates, existing architectures either restrict payment gateways to isolated one-off retail checkouts or provide informational catalogs detached from contractual tenancy cycles. Prior platforms omit recurring billing engines operating across calendar boundaries, database-level locking against concurrent reservation attempts, physical quarantine holds during room turnover, and automated parental escalation.

To systematically overcome these administrative vulnerabilities, this investigation designs, implements, and evaluates an integrated management information system specifically adapted to the operating model of Asri Boarding House. Derived from the comprehensive empirical findings of the underlying thesis research, the principal software engineering contributions include:
1. Development of an autonomous scheduling engine that computes idempotent recurring monthly invoices and applies a strict 5% calendar-rollover penalty within transactional boundaries;
2. Formulation of a staged arrears notification protocol that issues tenant reminders and programmatically escalates unresolved defaults to documented guardians through WhatsApp;
3. Elimination of concurrent room reservation collisions during enrollment surges via pessimistic row-level locking (`SELECT ... FOR UPDATE`) within isolated database transactions;
4. Implementation of an administrative post-occupancy quarantine mechanism that locks vacated units in a maintenance hold pending physical room turnover;
5. Introduction of an in-browser receipt compilation pipeline using `html2pdf.js`, supplying cryptographic-styled transaction proofs while relieving server CPU and shared storage resources.

---

## II. METHOD

### A. RESEARCH PARADIGM AND TRIANGULATED REQUIREMENTS ENGINEERING

This study applies a Software Engineering Research and Development (R&D) methodology grounded in the classical Waterfall Software Development Life Cycle (SDLC) [11], [12], [13]. A structured, phase-oriented progression—spanning requirements analysis, architectural specification, implementation, systematic verification, and production rollout—was selected to guarantee strict alignment between real-world boarding house policies and software constraints. 

Empirical requirements engineering relied on three triangulated evidence sources:
1. *Comprehensive Facility Survey*: A full physical inspection of all 32 rooms spanning two floors, shared utilities, and administrative desks at Asri Boarding House to catalog unit layouts, physical amenities, and operational tiers.
2. *Semi-Structured Operational Interviews*: Repeated interviews with resident manager Mr. Asep (48 years old, 20 years of operational experience) to formalize unwritten operational conventions, grace period habits, payment patterns, and cash balancing routines.
3. *Archival Documentation Audit*: An exhaustive review of historical physical ledgers, duplicate carbon receipts, active tenant cards, and building layout blueprints.

### B. SOFTWARE ARCHITECTURE AND PRESENTATION LAYER

To ensure architectural separation of concerns, the application is organized under a 3-tier Model-View-Controller (MVC) pattern utilizing Laravel 11 executing in a PHP 8.2 runtime environment. Rather than centralizing transactional processes inside routing handlers, core domain workflows are encapsulated within dedicated service classes residing in `app/Services/` (`BillingService`, `MidtransService`, `ReservasiService`, `TransisiPenyewaService`, and `FonnteService`), keeping controller classes minimal and facilitating isolated integration testing. 

On the client tier, the user interface adopts a Neo-Brutalist design system characterized by bold high-contrast borders and an OLED Black administrative theme. User interface ergonomics prioritize touch accessibility, providing 56-pixel interactive touch targets that comfortably surpass the 44-pixel baseline mandated by Web Content Accessibility Guidelines (WCAG 2.1).

### C. RELATIONAL SCHEMA AND SOFT-DELETE UNICITY

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

### D. CONCURRENCY CONTROL AND TRANSACTIONAL INVARIANTS

Race conditions pose a substantial operational hazard during university intake surges, where prospective tenants may attempt to book the identical unit at the same second. Addressing this concurrency challenge at the data tier, the system employs **pessimistic row-level locking** (`SELECT ... FOR UPDATE`) enclosed within isolated database transactions. 

Listing 2 details how `ReservasiService` intercepts incoming reservations by acquiring an exclusive row lock on the target room record before evaluating date overlap. Secondary requests attempting to reserve the locked entity are placed in a wait state until the primary transaction completes execution or triggers a rollback, enforcing deterministic serializability across room inventories.

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

In execution, when Thread A initiates a booking request for Room ID 5, the database engine locks the row immediately. If Thread B simultaneously attempts to book Room ID 5, it is forced into a queue wait state. Thread A evaluates the date overlap, confirms availability, generates the pending reservation order, and commits the transaction, releasing the lock. Thread B immediately acquires the released lock, evaluates date overlap against the newly committed reservation, detects collision, and throws a validation exception with HTTP 422, cleanly preventing double bookings without race hazards.

### E. AUTONOMOUS BILLING PIPELINE AND LATE FEE ALGORITHM

Recurring financial routines rely on host daemon cron tasks triggering Laravel's scheduled commands at one-minute intervals (`* * * * * php artisan schedule:run`). On the 1st day of each calendar month at 00:05 WIB, the automated billing routine executes within `BillingService` through a staged pipeline:
1. *Initial Invoice Issuance (1st of the Month)*: The scheduler iterates through active lease agreements (`penyewa.status = 'aktif'`), generating `pending` billing records with a standard settlement window closing on the 10th. Tailored WhatsApp dispatches containing assigned virtual account details are immediately pushed to residents through the Fonnte gateway.
2. *Intra-Month Grace Period (11th to Final Day of Month)*: Invoices that remain unpaid past the 10th transition into `terlambat` status but maintain an **IDR 0** penalty assessment throughout the active month, while periodic automated reminder messages are dispatched via WhatsApp to prompt settlement.
3. *Calendar-Boundary Late Fee Assessment (1st of Month $M+1$)*: Upon crossing into the subsequent calendar month without settlement, the system calculates an idempotent 5% penalty levied against the room's base tariff:

$$
\text{Late Fee} = \begin{cases} 0.05 \times \text{Tarif Pokok}, & \text{if } \text{status} = \text{'terlambat'} \land \text{nominal}_{\text{denda}} = 0 \land \Delta\text{Month} \ge 1 \\ 0, & \text{otherwise} \end{cases} \quad (1)
$$

Should default persist across two back-to-back monthly cycles (reaching the 10th day of Month $M+1$), the platform initiates guardian arrears escalation, dispatching automated arrears notices directly to the verified mobile contact of the tenant's parent or legal guardian.

### F. VERIFICATION FRAMEWORK

Software correctness, robustness, and operational feasibility were verified using a multi-tiered empirical testing framework comprising four coordinated methodologies: (1) black box behavioral validation spanning 60 structured test scenarios developed according to standard software testing guidelines [14], [15]; (2) unit and integration regression suites executed through 510 automated PHPUnit test routines encompassing 2,211 assertions; (3) payment lifecycle simulation conducted within the Midtrans Snap v2 sandbox environment using the Bank BCA Virtual Account testing tool; and (4) direct field usability evaluation alongside structured interviews administered to the senior resident manager (accumulating 20 years of on-site administrative tenure), captured as digital audio evidence (`REKAMAN_UX_ADMIN_KOST_2026.m4a`, total duration 23m 14s).

---

## III. RESULTS AND DISCUSSION

### A. RESULT

#### 1. Relational Database Cluster Topology
Normalized to Third Normal Form (3NF), the persistence layer comprises 22 interrelated database tables organized into six operational functional clusters:
- *Identity & Access Cluster*: Anchored by the `users` entity, this cluster manages role-based access control (Admin and Tenant roles), audit trails, and virtual unicity (`active_email`, `active_no_hp`, and `active_nik`) to enforce strict database-level identity uniqueness alongside soft deletes.
- *Room Master Cluster*: Encompassing `kamar`, `fasilitas`, and the pivot entity `kamar_fasilitas`, this cluster models 32 physical units distributed across two floors into three pricing tiers (VIP, Deluxe, Standard). It enforces uniqueness on active room numbers (`active_nomor_kamar`) while supporting M:N relationships for room amenities.
- *Tenancy Lifecycle Cluster*: Comprising `penyewa` and `reservasi`, this cluster governs contractual tenancy agreements, base rental rates, security deposits, emergency guardian contact details, and payment plan designations (30% Down Payment or 100% Full Payment).
- *Finance & Billing Cluster*: Grouping `tagihan`, `pembayaran`, and `pengeluaran`, this cluster tracks monthly rental obligations, sequential invoice numbers, automated late fees, multi-channel payment records (BCA Virtual Account, cash, bank transfer), and operational overhead expenditure logs.
- *Communication Cluster*: Spanning `chat_messages`, `guest_chat_threads`, `guest_chat_messages`, `log_notifikasi`, `pengumuman`, and `notifikasi_khusus`, this cluster coordinates pre-payment prospective tenant chats, public guest inquiries, immutable audit logs for WhatsApp dispatches, institutional broadcasts, and priority transactional alerts.
- *Operations & Content Cluster*: Incorporating `keluhan`, `peraturan`, `customer_reviews`, `faqs`, `galleries`, `settings`, and `whatsapp_clicks`, this cluster manages maintenance tickets with photo uploads, boarding house regulations, moderated public tenant reviews, media galleries, platform configuration parameters, and landing call-to-action click analytics.

#### 2. Hybrid Dual-Channel Booking Synchronization
To accommodate differing tenant behaviors, the platform coordinates both digital self-service bookings and on-premise administrative walk-ins:
- *Digital Self-Service Flow*: Remote prospective residents navigate a guided 5-step checkout interface: (1) Room Inventory Inspection, (2) 16-Digit National Identity (NIK) Syntax Verification, (3) Rental Duration Selection (Daily, Weekly, or Monthly tiers) with automated tenure discounts, (4) Settlement Scheme Designation (30% Down Payment or 100% Full Payment), and (5) Modal Payment via Midtrans Snap v2.
- *Front-Desk Walk-In Intake*: For prospective tenants appearing directly on site, the resident manager accesses `/admin/penyewa/create` to record resident and emergency guardian credentials, record direct cash or bank transfers, and assign rooms. The application establishes the user account, tenancy contract, and corresponding invoice record inside an isolated atomic database transaction.

#### 3. Cryptographic Webhook Security and Signature Matching
To prevent spoofed payment confirmations, asynchronous HTTP webhook dispatches emitted by the Midtrans gateway are validated against an HMAC-style SHA-512 digest calculation:

$$
\text{Signature}_{\text{calc}} = \text{SHA-512}(\text{order}_{\text{id}} + \text{status}_{\text{code}} + \text{gross}_{\text{amount}} + \text{ServerKey}) \quad (2)
$$

The resulting digest is verified against the inbound `signature_key` parameter. Any discrepancy causes the callback listener to terminate with an immediate HTTP 403 Forbidden error. When a valid `settlement` signal is confirmed, `MidtransCallbackController` applies state transitions within a transactional block reinforced with `lockForUpdate()`. In scenarios where network retries deliver identical webhooks, the system observes the existing `lunas` or settled status and skips secondary ledger writes, guaranteeing idempotent financial processing.

#### 4. Operational Quarantine and Digital Receipts
Room transitions enforce an explicit physical quarantine protocol upon resident departure. When the manager registers a checkout through `/admin/penyewa/checkout`, the resident’s record is updated to `nonaktif`, yet the room inventory state stays locked as `terisi` (highlighted visually in red on the administrative dashboard). The unit is kept unavailable for new reservations while maintenance personnel perform deep cleaning and facility repairs. Only after physical turnover is signed off does the manager press 'Release Room', returning the unit status to `tersedia` and executing `Cache::forget('kamar_aktif_landing')` to refresh public catalog availability.

To furnish payment proof without burdening hosting infrastructure, the tenant dashboard generates A5 digital receipts on the client side through `html2pdf.js`. Rendering document layouts directly in browser memory protects shared server hosting from CPU contention and storage bloat. Heavyweight server-side document rendering through Dompdf is restricted to monthly financial ledgers compiled exclusively by management.

#### 5. Production Deployment and Security Hardening
The software solution was placed into live production on Hostinger LiteSpeed Enterprise Cloud hosting (`https://asriboardinghouse.weatso.id/`). Platform hardening incorporated TLS 1.3 transport security (attaining an SSL Grade A profile), frontend asset minification and chunk compression via Vite, Web server `.htaccess` directives barring external access to sensitive paths (`.env`, `.git`), and restricting application entry points strictly to the `/public` directory.

#### 6. Empirical Quantitative Evaluation
Functional compliance and behavioral reliability were tested against 60 predefined test cases divided across six operational subsystems, achieving a 100% pass rate:
- *DOM-01 (Identity Authentication, Socialite Google OAuth, & Profile Gates)*: 12 scenarios evaluated user registration, bcrypt credential hashing, role-based session establishment, OAuth profile completion intercepts, and account deactivation, achieving a 100% pass rate.
- *DOM-02 (Public Catalog, Neo-Brutalist Layout, & WCAG 2.1 Touch Targets)*: 10 scenarios validated room tier filtering, real-time inventory counter caching, responsive mobile breakpoints, and 56-pixel touch target accessibility, achieving a 100% pass rate.
- *DOM-03 (5-Step Reservation Stepper, Discounts, & Concurrency Locks)*: 10 scenarios assessed form step transitions, NIK regex syntax validation, tenure discount calculations, 30% down payment splits, and pessimistic row locking under rapid simulated clicks, achieving a 100% pass rate.
- *DOM-04 (Active Tenant Portal, Self-Service VA, & html2pdf.js Receipts)*: 9 scenarios validated active lease overview displays, dynamic BCA VA modal generation, maintenance ticket submissions with image attachments, and client-side PDF receipt compilation, achieving a 100% pass rate.
- *DOM-05 (Admin Console, Walk-In Onboarding, & Inspection Quarantine)*: 13 scenarios tested dashboard occupancy counters, walk-in registration transactions, cash payment settlements, checkout status transitions, room inspection quarantine locking, and financial statement exports, achieving a 100% pass rate.
- *DOM-06 (Task Scheduler, Auto-Billing Cron, 5% Late Fees, & Guardian Alerts)*: 6 scenarios evaluated monthly invoice generation on the 1st of the month, grace period transitions on the 11th, calendar-boundary 5% late fee calculations on month rollover, and WhatsApp guardian escalation dispatches, achieving a 100% pass rate.

In addition to black box testing, the software underwent targeted security probes appraising Role-Based Access Control (RBAC) boundaries across protected routes (`/admin`, `/penyewa`, `/reservasi`) alongside OAuth compliance [16]. All six simulated attack vectors were successfully neutralized: (1) unauthenticated requests to `/admin/dashboard` were redirected to login; (2) privilege escalation attempts by active tenants requesting `/admin/laporan` were denied with HTTP 403 Forbidden; (3) public requests to `/penyewa/tagihan` were intercepted and redirected; (4) pending applicants attempting early access to `/penyewa/dashboard` were blocked by middleware; (5) Insecure Direct Object Reference (IDOR) invoice tampering was thwarted by user-scoped Eloquent queries returning HTTP 403; and (6) IDOR receipt tampering was rejected through tenant-payment ownership verification.

Furthermore, payment lifecycle transitions were validated within the Midtrans sandbox environment across all seven lifecycle states: (1) Bank BCA Virtual Account issuance generated a 16-digit code with a 24-hour expiry in `pending` status; (2) simulator payment inquiries retrieved bills accurately; (3) payment triggers dispatched SHA-512 validated webhooks transitioning orders to `settlement`; (4) settlement window expiration automatically transitioned orders to `expire` and released inventory locks; (5) user checkout abortion triggered `cancel` signals; (6) forged SHA-512 signatures were detected and rejected with HTTP 403; and (7) duplicate webhook delivery was processed idempotently without redundant ledger writes.

#### 7. Empirical Operational Usability & Stakeholder Evaluation
On-site administrative workflow testing and usability validation were carried out at Asri Boarding House directly with resident manager Mr. Asep (48 years of age, 20 years of administrative experience) using the live production instance (`https://asriboardinghouse.weatso.id/`). Conducted under informed consent, the comprehensive session was recorded as continuous digital audio evidence (`REKAMAN_UX_ADMIN_KOST_2026.m4a`, duration 23m 14s). Ten operational modules were systematically examined:
1. *Public Storefront & Media*: High-contrast typography proved legible for older vision, while upfront room pricing eliminated repetitive rate inquiries from out-of-town parents.
2. *Docked WhatsApp CTA*: Ergonomically positioned for mobile thumb interaction, directing prospective tenants instantly to management.
3. *OLED Dark Admin Console*: Minimized eye fatigue during extended administrative sessions, while automated occupancy and cashflow calculations eliminated mental arithmetic errors.
4. *Walk-In Intake Form*: Streamlined intake fields permanently recorded guardian contacts, solving historical problems of misplaced emergency numbers.
5. *Cash Settlement & Digital Receipts*: Single-click invoice settlement and stamped digital receipt generation replaced manual carbon receipt tearing, eliminating handwriting disputes and paper waste.
6. *Flat 5% Late Fee Policy*: The combination of an IDR 0 grace period during the active month and a predictable 5% rollover penalty was judged fair, transparent, and enforceable.
7. *Post-Checkout Quarantine*: The visual red-lock prevented premature room re-allocation while linens and sanitation were verified, protecting accommodation hospitality standards.
8. *One-Click Financial Reports*: Compressed the monthly financial reporting cycle from 3 to 5 business days of manual receipt collation into instant PDF/Excel exports.
9. *Tenant Self-Service & Tickets*: Students settled bills remotely via BCA VA without visiting off-site ATMs, while photo-documented maintenance ticketing accelerated repair dispatches.
10. *Overall Retrospective Assessment*: Mr. Asep summarized the operational transition: *"Compared to twenty years of handwritten bookkeeping, the difference is night and day. Managing operations is substantially easier, and cash reconciliation errors from misplaced paper slips have been eliminated."* The evaluation concluded with an overall operational usability rating of **9.5/10**, confirming strong field adoption and production readiness.

---

### B. DISCUSSION

The empirical evaluation demonstrates that pairing automated payment orchestration with strict database-level concurrency controls and explicit business logic overcomes the core operational vulnerabilities of manual boarding house management.

A critical comparison with existing literature highlights several architectural distinctions. Earlier boarding house systems developed by Nizar [3], Purnia et al. [4], and Cornellya & Afriyadi [6] introduced web interfaces for room browsing and record logging, yet they relegated payment verification to manual administrative inspection of uploaded bank slips. By embedding Midtrans Snap v2, the platform presented here shifts transaction settlement from subjective visual checks to deterministic, cryptographically authenticated webhook listeners. Similarly, while lodging architectures presented by Malaikosa & Mokola [7] utilized standard web submission forms vulnerable to concurrent booking conflicts, our implementation enforces database-level serialization via `lockForUpdate()`. Supported by transactional locking models in modern database literature [17], this mechanism guarantees ACID isolation, precluding double-allocation race conditions during high-volume academic enrollment windows.

The research also addresses a conceptual mismatch in how payment gateways have traditionally been integrated into rental systems. Prior implementations by Sutisna & Aziz [8], Fatman et al. [9], Surya Pratama [18], Pramita et al. [19], and Wijaya et al. [20] deployed Midtrans within retail e-commerce, school fee portals, or service checkouts. Such environments process discrete, one-off purchases that terminate upon payment confirmation. By contrast, residential tenancy management operates across multi-month contractual lifecycles governed by recurring billing milestones, designated grace periods, rollover late fee rules, and multi-party escalation pathways. By integrating Midtrans webhooks with an autonomous cron-driven scheduling pipeline, this work extends payment gateway functionality beyond simple retail checkouts into multi-month contractual lifecycle governance.

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

[1] H. Kurniawan, F. Sonata, and R. Susanto, "Penerapan Framework Laravel dalam Rancang Bangun Sistem Informasi Pengelolaan Properti Hunian Berbasis Web," *J. Teknoinfo*, vol. 17, no. 1, pp. 45–54, 2023, doi: 10.33365/jti.v17i1.2450.

[2] K. C. Laudon and J. P. Laudon, *Management Information Systems: Managing the Digital Firm*, 16th ed. Harlow, UK: Pearson Education, 2020.

[3] C. Nizar, "Rancang Bangun Sistem Informasi Sewa Rumah Kost (E-Kost) Berbasis Website," *J. Sist. Inf. dan Sains Teknol.*, vol. 3, no. 1, pp. 1–10, 2021, doi: 10.31326/sistek.v3i1.852.

[4] D. S. Purnia, R. Ratningsih, and M. Surahman, "Implementasi Metode Prototyping pada Rancang Bangun Marketplace Rumah Kost Berbasis Mobile," *EVOLUSI J. Sains dan Manaj.*, vol. 9, no. 1, pp. 1–11, 2021, doi: 10.31294/evolusi.v9i1.10145.

[5] R. Hidayat, M. I. Wahyudi, and N. Azizah, "Penerapan Arsitektur MVC pada Rancang Bangun Sistem Informasi Pemesanan Kamar Kos Berbasis Web," *J. SISFOKOM (Sistem Inf. dan Komput.)*, vol. 12, no. 2, pp. 210–218, 2023, doi: 10.32736/sisfokom.v12i2.1645.

[6] A. Cornellya and H. Afriyadi, "Perancangan Sistem Informasi Pemesanan pada Kost Tya Berbasis Web Menggunakan Framework Laravel," *PESHUM J. Pendidikan, Sos. dan Hum.*, vol. 4, no. 6, pp. 10360–10369, 2025, doi: 10.56799/peshum.v4i6.11777.

[7] E. J. Malaikosa and P. Mokola, "Sistem Informasi Monitoring Rumah Kos dan Pembayarannya Berbasis Web Menggunakan Metode Rapid Application Development," *JSiI (Jurnal Sist. Informasi)*, vol. 11, no. 1, pp. 21–26, 2024, doi: 10.30656/jsii.v11i1.8222.

[8] R. Sutisna and F. Aziz, "Perancangan Sistem Penyewaan Alat Event Berbasis Website Menggunakan Midtrans sebagai Integrasi Payment Gateway pada PT. Bangbewe Production," *REMIK Ris. dan E-Jurnal Manaj. Inform. Komput.*, vol. 9, no. 1, pp. 317–325, 2025, doi: 10.33395/remik.v9i1.14498.

[9] Y. Fatman, N. K. Nafisah, and P. B. J. Pambudi, "Implementasi Payment Gateway dengan Menggunakan Midtrans pada Website UMKM Geberco," *J. KomtekInfo*, vol. 10, no. 2, pp. 64–72, 2023, doi: 10.35134/komtekinfo.v10i2.364.

[10] L. Hakim, S. P. Kristanto, M. N. Shodiq, and E. Amaliyah, "Aplikasi Penerimaan dan Pengeluaran Kas Berbasis Web dan WhatsApp Gateway," *J. Tekno Kompak*, vol. 15, no. 1, pp. 13–24, 2021, doi: 10.33365/jtk.v15i1.900.

[11] R. S. Pressman and B. R. Maxim, *Software Engineering: A Practitioner's Approach*, 9th ed. New York, NY, USA: McGraw-Hill Education, 2020.

[12] T. Pricillia and Z. Zulfachmi, "Perbandingan Metode Pengembangan Perangkat Lunak (Waterfall, Prototype, RAD)," *J. Bangkit Indones.*, vol. 10, no. 1, pp. 6–12, 2021, doi: 10.52771/bangkitindonesia.v10i1.153.

[13] D. G. A. Candra and P. P. Pardika, "Analisa dan Perancangan Sistem Informasi Manajemen Pengolahan Data Pesanan Sablon di KYSR Store Menggunakan Metode Waterfall," *JAMI J. Ahli Muda Indones.*, vol. 5, no. 2, pp. 134–147, 2024, doi: 10.46510/jami.v5i2.306.

[14] M. S. Mustaqbal, R. F. Firdaus, and H. Rahmadi, "Pengujian Aplikasi Menggunakan Black Box Testing Boundary Value Analysis pada Sistem Informasi Akademik," *J. Teknol. dan Inf.*, vol. 11, no. 1, pp. 35–44, 2021, doi: 10.34010/jati.v11i1.4285.

[15] E. Setiana, M. R. Ramadhan, B. Budiman, and R. Y. Rakhman, "Pengujian Perangkat Lunak Metode Black Box pada Aplikasi Sistem Pakar Pola Latihan dan Asupan Makanan," *Nuansa Inform.*, vol. 18, no. 1, pp. 68–74, 2024, doi: 10.25134/ilkom.v18i1.67.

[16] P. Philippaerts, D. Preuveneers, and W. Joosen, "OAuch: Exploring Security Compliance in the OAuth 2.0 Ecosystem," in *Proc. 25th Int. Symp. Research in Attacks, Intrusions and Defenses (RAID '22)*, New York, NY, USA: ACM, 2022, pp. 460–481, doi: 10.1145/3545948.3545955.

[17] B. A. Kurniawan and D. R. Indah, "Implementasi Concurrency Control dan Row-Level Locking pada Sistem Manajemen Transaksi Basis Data Relasional," *J. Media Inform. Budidarma*, vol. 7, no. 2, pp. 889–898, 2023, doi: 10.30865/mib.v7i2.5992.

[18] M. I. Surya Pratama, "Integrasi Payment Gateway pada Aplikasi Point of Sales Berbasis Website Menggunakan Framework ReactJS (Studi Kasus: Toko Adida Pratama)," *J. Inform. dan Tek. Elektro Terap.*, vol. 13, no. 3, 2025, doi: 10.23960/jitet.v13i3.7099.

[19] I. P. Pramita, A. M. Harahap, and A. B. Nasution, "Penerapan Payment Gateway Midtrans pada Sistem Pembayaran SPP Berbasis Android di SMAN 1 Bangun Purba," *J. Ris. Sist. Inf. dan Teknol. Inf.*, vol. 6, no. 3, pp. 479–490, 2024, doi: 10.52005/jursistekni.v6i3.367.

[20] F. Wijaya, M. Maslim, M. Martinus, and P. Ardanari, "Pembangunan Sistem Informasi Laundry Berbasis Web Menggunakan Payment Gateway Midtrans," *Prolet. Community Serv. Dev. J.*, vol. 1, no. 1, pp. 8–14, 2023, doi: 10.61098/proletariancomdev.v1i1.63.
