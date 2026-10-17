# DESIGN AND IMPLEMENTATION OF AN INTEGRATED BOARDING HOUSE MANAGEMENT INFORMATION SYSTEM WITH MIDTRANS PAYMENT GATEWAY: A CASE STUDY OF ASRI BOARDING HOUSE

1Rafif Arsya Pradiva, 2Andre Kurniawan Pamudji, 3Erdhi Widyarto Nugroho  
Department of Information Systems, Faculty of Computer Science  
Universitas Katolik Soegijapranata, Semarang, Indonesia  
122n40014@student.unika.ac.id, 2andre.kurniawan@unika.ac.id, 3erdhi@unika.ac.id  

---

Abstract—Student residential facilities in expanding higher education districts often experience administrative vulnerabilities caused by conventional paper bookkeeping, fragmented messaging, and unverified bank transfer slips. These informal workflows lead to chronic rental arrears, cash reconciliation discrepancies over an aggregate gross turnover of IDR 28,500,000 monthly, and concurrent booking collisions during academic intake cycles. To resolve these bottlenecks, this study designs, implements, and empirically evaluates an integrated Management Information System for Asri Boarding House (32 rooms across VIP, Deluxe, and Standard tiers) in Semarang, Indonesia. Developed via the linear-sequential Waterfall lifecycle, the platform couples Laravel 11 with a 3NF-normalized relational schema of 22 entities in MySQL 8.0 InnoDB, integrating Midtrans Snap v2 for Bank BCA Virtual Account settlements, in-browser client-side A5 receipt compilation via html2pdf.js (zero server storage overhead), and Fonnte for automated WhatsApp dispatches. Concurrency control during peak enrollment surges is enforced through pessimistic row-level locking (*lockForUpdate()*) in atomic database transactions, complemented by an administrative post-checkout sanitation quarantine hold. An autonomous cron billing engine dispatches recurring monthly invoices on the 1st of each calendar month, enforces an idempotent 5% calendar-rollover penalty with an intra-month grace period, and programmatically escalates unresolved defaults to registered guardians. Functional verification across 60 black-box scenarios and 510 automated PHPUnit integration tests with 2,211 assertions achieved a 100% pass rate with zero regression errors. Dual-stakeholder empirical field evaluation post-live deployment (https://asriboardinghouse.weatso.id/) revealed strong adoption: the senior resident manager awarded an operational satisfaction score of 9.5/10, confirming monthly reconciliation was compressed from 3–5 working days to instantaneous ledger generation without cash variance; concurrently, semi-structured interviews across five active student boarding house tenants using a 10-parameter workflow instrument yielded an aggregate satisfaction score of 8.00/10, universally affirming that the platform significantly streamlines lodging reservations, transparent invoice tracking, and cashless settlements.

Keywords—Automated billing, Concurrency control, Laravel 11, Management information system, Midtrans payment gateway, User acceptance

---

## I. INTRODUCTION

Student residential facilities surrounding higher education clusters experience sustained, cyclical demand, particularly across growing university districts such as Tembalang in Semarang, Indonesia. Within this perimeter, privately owned boarding houses (*kost*) provide multi-month lodging for thousands of undergraduate and postgraduate scholars. While commercial enterprise platforms and web-based property systems have expanded rapidly, independent boarding houses in this micro-market still rely predominantly on manual carbon-copy receipt books, handwritten cash journals, and unstructured instant messaging. This persistent administrative informality introduces chronic operational friction, including uncollected rental arrears, book-balancing discrepancies, reservation race conditions during annual university intake cycles, and uncoordinated default escalation.

Representing these operational vulnerabilities in practice, Asri Boarding House operates a two-story residential facility located on Jl. Maera Sari No. 1 / No. 12, Tembalang, Semarang (Postal Code 50275). Its rental inventory encompasses 32 student rooms categorized into three distinct pricing tiers: VIP (6 rooms tariffed at IDR 1,400,000 monthly), Deluxe (3 rooms at IDR 950,000 monthly), and Standard (23 rooms at IDR 750,000 monthly). When fully tenanted, the accommodation maintains an aggregate monthly gross revenue baseline of IDR 28,500,000. Daily administrative operations have long been stewarded by a single senior resident manager, Mr. Asep (48 years of age, with 20 years of operational experience), who historically relied on paper carbon receipt books and physical cash drawers.

Direct on-site observation, operational audits, and stakeholder interviews highlighted three recurring administrative vulnerabilities:
1. *Rent Arrears Administration and Collection Tracking*: Rent collection depended on human memory and scattered physical notes. With billing dates dispersed across disparate calendar dates, collection deadlines frequently slipped, causing cash flow instability. Furthermore, the facility lacked a systematic mechanism to notify registered parents or legal guardians when students defaulted, allowing unpaid balances to accumulate unchecked.
2. *Payment Documentation and Manual Cash Balancing*: Documenting tenant payments through carbon paper slips and manual bank transfer screenshots was highly vulnerable to misplaced records, paper wear, and arithmetic errors. Compiling monthly financial statements required 3 to 5 working days of manual calculator arithmetic to collate paper slips against physical cash drawers, delaying owner reporting.
3. *Concurrent Booking Collisions and Remote Prospective Tenant Friction*: Lacking real-time inventory synchronization, reservation inquiries arriving simultaneously through verbal walk-ins and informal messaging produced double bookings during university intake surges. Furthermore, out-of-town students faced severe travel burdens because the absence of an online catalog and pre-payment inquiry channel forced them to conduct physical on-site surveys.

Reviewing existing literature reveals active efforts to modernize boarding house administration, yet critical software engineering gaps remain. Cornellya & Afriyadi [1] developed a Laravel-based booking platform for Kost Tya, and Nizar [2] engineered an E-Kost web catalog; however, both systems relied on manual bank transfer slips and omitted automated payment gateways alongside database concurrency controls. Regarding promotional accessibility, Jannah et al. [3] examined web-based lodging marketing portals for remote prospective scholars, while Purnia et al. [5] prototyped a mobile lodging marketplace. Nevertheless, these platforms acted primarily as static informational catalogs or third-party intermediary listings, lacking cyclical contractual management. On the financial monitoring front, Malaikosa & Mokola [4] proposed a web monitoring system using Rapid Application Development (RAD); yet transactions remained vulnerable to manual recording errors and cash discrepancies.

To establish automated financial settlements and audit trails, Management Information Systems (MIS) [6] increasingly leverage digital payment gateways. In event rentals, Sutisna & Aziz [7] integrated Midtrans Snap API, while Surya Pratama [8] implemented Midtrans in point-of-sale platforms, Pramita et al. [9] applied Midtrans Virtual Accounts to school tuition, and Wijaya et al. [10] deployed Midtrans for web laundry management. However, these systems operated under transient, one-off retail models terminating upon single-order confirmation, failing to support multi-month contractual tenancy cycles characterized by scheduled billing, intra-month grace periods, rollover late penalties, and guardian escalation dispatches.

To systematically overcome these administrative vulnerabilities, this investigation designs, implements, and evaluates an integrated management information system specifically adapted to the operating model of Asri Boarding House. Derived from the comprehensive empirical findings of the underlying thesis research, the three principal research objectives are:
1. *System Design, Engineering, and Integration*: To design, construct, and integrate a web-based Boarding House Management Information System coupling Laravel 11 and MySQL 8.0 with online reservations, Midtrans Snap BCA Virtual Account payments, autonomous billing schedules, and automated WhatsApp dispatches;
2. *Tri-Fold Operational Bottleneck Resolution*: To empirically demonstrate and analyze the system's effectiveness in resolving recurring tenancy arrears, eliminating manual reconciliation delays (compressing 3–5 working days to instant reporting with zero cash variance over IDR 28,500,000 monthly turnover), and preventing room double bookings (*zero double-booking* via pessimistic database row locking and post-checkout quarantine);
3. *Dual-Stakeholder Empirical Acceptance Evaluation*: To describe and analyze post-implementation user acceptance from both administrative management (the senior resident manager) and end-users (five active student boarding house tenants) on the live production environment.

---

## II. METHOD

### A. RESEARCH PARADIGM AND TRIANGULATED REQUIREMENTS ENGINEERING

This study applies a Software Engineering Research and Development (R&D) methodology grounded in the classical linear-sequential Waterfall Software Development Life Cycle (SDLC) [17], [19], [20]. A structured, phase-oriented progression—spanning requirements analysis, architectural modeling with Unified Modeling Language (UML) [18], implementation, systematic verification, and production rollout—was selected to guarantee strict alignment between real-world boarding house policies and software constraints. 

Empirical requirements engineering relied on four triangulated evidence sources:
1. *Comprehensive Facility Survey*: A full physical inspection of all 32 rooms spanning two floors, shared utilities, and administrative desks at Asri Boarding House to catalog unit layouts, physical amenities, and operational tiers.
2. *Semi-Structured Operational Interviews with Resident Manager*: Repeated in-depth interviews with senior resident manager Mr. Asep (48 years old, 20 years of operational experience) to formalize unwritten operational conventions, grace period habits, payment patterns, and cash balancing routines.
3. *Semi-Structured Evaluative Interviews with Five Active Student Boarding House Tenants*: Evaluative interviews administered to five active student tenants (*surrogate users*) representing diverse room tiers and rental schemes, assessed across 10 continuous operational workflow parameters from room search to monthly bill settlement.
4. *Archival Documentation Audit*: An exhaustive review of historical physical ledgers, duplicate carbon receipts, active tenant cards, and building layout blueprints.

### B. SOFTWARE ARCHITECTURE AND PRESENTATION LAYER

To ensure architectural separation of concerns, the application is organized under a 3-tier Model-View-Controller (MVC) pattern utilizing Laravel 11 executing in a PHP 8.2 runtime environment [11]. Rather than centralizing transactional processes inside routing handlers, core domain workflows are encapsulated within dedicated service classes residing in *app/Services/* (*BillingService*, *MidtransService*, *ReservasiService*, *TransisiPenyewaService*, and *FonnteService*), keeping controller classes minimal and facilitating isolated integration testing. 

On the client tier, the user interface adopts a Neo-Brutalist design system characterized by bold high-contrast borders and an OLED Black administrative theme. User interface ergonomics prioritize touch accessibility, providing 56-pixel interactive touch targets that comfortably surpass the 44-pixel baseline mandated by Web Content Accessibility Guidelines (WCAG 2.1).

### C. RELATIONAL SCHEMA AND SOFT-DELETE UNICITY

The persistence layer executes on MySQL 8.0 InnoDB, normalized to Third Normal Form (3NF) across 22 relational entities. Critical domain tables incorporate soft-delete timestamps (*deleted_at*) for audit compliance and historical recovery. However, standard SQL unique keys conflict with soft-deletion mechanisms because archived rows prevent re-registration of identical unique values, such as national identification numbers (*nik*), email addresses, and room numbers. In standard web engineering, practitioners frequently resolve this conflict by removing database-level unique constraints in favor of application-layer validation rules, leaving the underlying relational engine vulnerable to concurrent insertion race conditions. 

To resolve this architectural trade-off without abandoning database-level unicity, the system implements Virtual Generated Columns paired with native unique indices. For each unique attribute, a virtual column (such as *active_email*, *active_no_hp*, *active_nik*, and *active_nomor_kamar*) is defined via the conditional expression *CASE WHEN deleted_at IS NULL THEN column_val ELSE NULL END*. Because SQL semantics and the InnoDB engine permit multiple *NULL* values within a unique index, uniqueness is strictly enforced across active records while preserving soft-deleted historical rows. Any simultaneous insertion attempt of duplicate active credentials is automatically intercepted and rejected at the database engine tier, guaranteeing relational consistency without compromising audit history.

### D. CONCURRENCY CONTROL AND TRANSACTIONAL INVARIANTS

Race conditions pose a substantial operational hazard during university intake surges, where prospective tenants may attempt to reserve the identical accommodation unit at the same fraction of a second. Addressing this concurrency challenge at the data tier, the system employs pessimistic row-level locking (*SELECT ... FOR UPDATE*) enclosed within isolated, ACID-compliant database transactions. 

Within the core reservation domain (*ReservasiService*), every reservation attempt initiates an atomic database transaction that immediately issues an exclusive row-level lock on the target room record (*Kamar::lockForUpdate()*) prior to evaluating occupancy schedules. Secondary requests attempting to reserve the locked entity are placed in an engine-managed wait queue until the primary transaction completes execution or triggers a rollback. The locked thread executes schedule collision evaluation (*cekDoubleBooking()*), calculating whether the selected start and end dates intersect with existing active reservations or confirmed tenancies. If a conflict is detected, the transaction aborts with a validation exception (HTTP 422). Conversely, if the unit is unheld, the service calculates pricing breakdowns, records the order in *pending* status, and commits the transaction, releasing the lock. The queued secondary transaction then acquires the released lock, evaluates the newly committed reservation interval, detects collision, and terminates cleanly. This transactional serialization enforces deterministic serializability across room inventories, precluding double allocations without application deadlocks.

### E. AUTONOMOUS BILLING PIPELINE AND LATE FEE ALGORITHM

Recurring financial routines rely on host daemon cron tasks triggering Laravel scheduled commands at one-minute intervals. On the 1st day of each calendar month at 00:05 WIB, the automated billing routine executes within *BillingService* through a staged pipeline:
1. *Initial Invoice Issuance (1st of the Month)*: The scheduler iterates through active lease agreements (*status = 'aktif'*), generating *pending* billing records with a standard settlement window closing on the 10th. Tailored WhatsApp dispatches containing assigned virtual account details are immediately pushed to residents through the Fonnte gateway [13].
2. *Intra-Month Grace Period (11th to Final Day of Month)*: Invoices that remain unpaid past the 10th transition into *terlambat* status but maintain an IDR 0 penalty assessment throughout the active month, while periodic automated reminder messages are dispatched via WhatsApp to prompt settlement.
3. *Calendar-Boundary Late Fee Assessment (1st of Month $M+1$)*: Upon crossing into the subsequent calendar month without settlement, the system calculates an idempotent 5% penalty levied against the room base tariff:

$$
\text{Late Fee} = \begin{cases} 0.05 \times \text{Tarif Pokok}, & \text{if } \text{status} = \text{'terlambat'} \land \text{nominal}_{\text{denda}} = 0 \land \Delta\text{Month} \ge 1 \\ 0, & \text{otherwise} \end{cases} \quad (1)
$$

Should default persist across two back-to-back monthly cycles (reaching the 10th day of Month $M+1$), the platform initiates guardian arrears escalation, dispatching automated arrears notices directly to the verified mobile contact of the tenant parent or legal guardian.

### F. VERIFICATION FRAMEWORK

Software correctness, robustness, and operational feasibility were verified using a multi-tiered empirical testing framework comprising four coordinated methodologies: (1) black box behavioral validation spanning 60 structured test scenarios developed according to standard software testing guidelines [15], [16]; (2) unit and integration regression suites executed through 510 automated PHPUnit test routines encompassing 2,211 assertions; (3) payment lifecycle simulation conducted within the Midtrans Snap v2 sandbox environment across all seven lifecycle states with HMAC-style SHA-512 webhook signature verification [12]; and (4) dual-stakeholder field usability evaluation on the live production environment comprising:
- Structured side-by-side administrative testing and semi-structured interviews with the senior resident manager (Mr. Asep, 48 years old, 20 years of administrative tenure), captured as continuous digital audio evidence (*REKAMAN_UX_ADMIN_KOST_2026.m4a*, total duration 23m 14s);
- Structured evaluative interviews with five active student boarding house tenants (*surrogate users*) evaluating the complete journey across 10 operational parameters from online room discovery to monthly recurring bill settlement.

---

## III. RESULTS AND DISCUSSION

### A. RESULT

#### 1. Relational Database Cluster Topology
Normalized to Third Normal Form (3NF), the persistence layer comprises 22 interrelated database tables organized into six operational functional clusters:
- *Identity & Access Cluster*: Anchored by the *users* entity, this cluster manages role-based access control (Admin and Tenant roles), audit trails, and virtual unicity (*active_email*, *active_no_hp*, and *active_nik*) to enforce strict database-level identity uniqueness alongside soft deletes.
- *Room Master Cluster*: Encompassing *kamar*, *fasilitas*, and the pivot entity *kamar_fasilitas*, this cluster models 32 physical units distributed across two floors into three pricing tiers (VIP, Deluxe, Standard). It enforces uniqueness on active room numbers (*active_nomor_kamar*) while supporting M:N relationships for room amenities.
- *Tenancy Lifecycle Cluster*: Comprising *penyewa* and *reservasi*, this cluster governs contractual tenancy agreements, base rental rates, security deposits, emergency guardian contact details, and payment plan designations (30% Down Payment or 100% Full Payment).
- *Finance & Billing Cluster*: Grouping *tagihan*, *pembayaran*, and *pengeluaran*, this cluster tracks monthly rental obligations, sequential invoice numbers, automated late fees, multi-channel payment records (BCA Virtual Account, cash, bank transfer), and operational overhead expenditure logs.
- *Communication Cluster*: Spanning *chat_messages*, *guest_chat_threads*, *guest_chat_messages*, *log_notifikasi*, *pengumuman*, and *notifikasi_khusus*, this cluster coordinates pre-payment prospective tenant chats, public guest inquiries, immutable audit logs for WhatsApp dispatches, institutional broadcasts, and priority transactional alerts.
- *Operations & Content Cluster*: Incorporating *keluhan*, *peraturan*, *customer_reviews*, *faqs*, *galleries*, *settings*, and *whatsapp_clicks*, this cluster manages maintenance tickets with photo uploads, boarding house regulations, moderated public tenant reviews, media galleries, platform configuration parameters, and landing call-to-action click analytics.

#### 2. Hybrid Dual-Channel Booking Synchronization
To accommodate differing tenant behaviors, the platform coordinates both digital self-service bookings and on-premise administrative walk-ins:
- *Digital Self-Service Flow*: Remote prospective residents navigate a guided 5-step checkout interface: (1) Room Inventory Inspection, (2) 16-Digit National Identity (NIK) Syntax Verification, (3) Rental Duration Selection (Daily, Weekly, or Monthly tiers) with automated tenure discounts, (4) Settlement Scheme Designation (30% Down Payment or 100% Full Payment), and (5) Modal Payment via Midtrans Snap v2.
- *Front-Desk Walk-In Intake*: For prospective tenants appearing directly on site, the resident manager accesses the walk-in registration interface to record resident and emergency guardian credentials, record direct cash or bank transfers, and assign rooms. The application establishes the user account, tenancy contract, and corresponding invoice record inside an isolated atomic database transaction.

#### 3. Cryptographic Webhook Security and Signature Matching
To prevent spoofed payment confirmations, asynchronous HTTP webhook dispatches emitted by the Midtrans gateway are validated against an HMAC-style SHA-512 digest calculation:

$$
\text{Signature}_{\text{calc}} = \text{SHA-512}(\text{order}_{\text{id}} + \text{status}_{\text{code}} + \text{gross}_{\text{amount}} + \text{ServerKey}) \quad (2)
$$

The resulting digest is verified against the inbound signature parameter. Any discrepancy causes the callback listener to terminate with an immediate HTTP 403 Forbidden error. When a valid settlement signal is confirmed, *MidtransCallbackController* applies state transitions within a transactional block reinforced with *lockForUpdate()*. In scenarios where network retries deliver identical webhooks, the system observes the existing settled status and skips secondary ledger writes, guaranteeing idempotent financial processing.

#### 4. Operational Quarantine and Digital Receipts
Room transitions enforce an explicit physical quarantine protocol upon resident departure. When the manager registers a checkout through the administrative interface, the resident record is updated to inactive (*nonaktif*), yet the room inventory state stays locked as occupied (*terisi*, highlighted visually in red on the administrative dashboard). The unit is kept unavailable for new reservations while maintenance personnel perform deep cleaning and facility repairs. Only after physical turnover is signed off does the manager release the room, returning the unit status to available (*tersedia*) and invalidating the public catalog cache (*Cache::forget*) to refresh live availability.

To furnish payment proof without burdening hosting infrastructure, the tenant dashboard generates A5 digital receipts on the client side through *html2pdf.js*. Rendering document layouts directly in browser memory protects shared server hosting from CPU contention and storage bloat. Heavyweight server-side document rendering through Dompdf is restricted to monthly financial ledgers compiled exclusively by management.

#### 5. Production Deployment and Security Hardening
The software solution was placed into live production on Hostinger LiteSpeed Enterprise Cloud hosting (*https://asriboardinghouse.weatso.id/*). Platform hardening incorporated TLS 1.3 transport security (attaining an SSL Grade A profile), frontend asset minification and chunk compression via Vite, Web server configuration directives barring external access to sensitive paths (*.env*, *.git*), and restricting application entry points strictly to the public directory.

#### 6. Empirical Quantitative Evaluation
Functional compliance and behavioral reliability were tested against 60 predefined test cases divided across six operational subsystems, achieving a 100% pass rate:
- *DOM-01 (Identity Authentication, Socialite Google OAuth, & Profile Gates)*: 12 scenarios evaluated user registration, bcrypt credential hashing, role-based session establishment, OAuth profile completion intercepts, and account deactivation, achieving a 100% pass rate.
- *DOM-02 (Public Catalog, Neo-Brutalist Layout, & WCAG 2.1 Touch Targets)*: 10 scenarios validated room tier filtering, real-time inventory counter caching, responsive mobile breakpoints, and 56-pixel touch target accessibility, achieving a 100% pass rate.
- *DOM-03 (5-Step Reservation Stepper, Discounts, & Concurrency Locks)*: 10 scenarios assessed form step transitions, NIK regex syntax validation, tenure discount calculations, 30% down payment splits, and pessimistic row locking under rapid simulated clicks, achieving a 100% pass rate.
- *DOM-04 (Active Tenant Portal, Self-Service VA, & Digital Receipts)*: 9 scenarios validated active lease overview displays, dynamic BCA VA modal generation, maintenance ticket submissions with image attachments, and client-side PDF receipt compilation, achieving a 100% pass rate.
- *DOM-05 (Admin Console, Walk-In Onboarding, & Inspection Quarantine)*: 13 scenarios tested dashboard occupancy counters, walk-in registration transactions, cash payment settlements, checkout status transitions, room inspection quarantine locking, and financial statement exports, achieving a 100% pass rate.
- *DOM-06 (Task Scheduler, Auto-Billing Cron, 5% Late Fees, & Guardian Alerts)*: 6 scenarios evaluated monthly invoice generation on the 1st of the month, grace period transitions on the 11th, calendar-boundary 5% late fee calculations on month rollover, and WhatsApp guardian escalation dispatches, achieving a 100% pass rate.

In addition to black box testing, the software underwent targeted security probes appraising Role-Based Access Control (RBAC) boundaries across protected routes alongside OAuth compliance [14]. All six simulated attack vectors were successfully neutralized: (1) unauthenticated requests to administrative dashboards were redirected to login; (2) privilege escalation attempts by active tenants requesting administrative reports were denied with HTTP 403 Forbidden; (3) public requests to tenant billing views were intercepted and redirected; (4) pending applicants attempting early access to tenant dashboards were blocked by middleware; (5) Insecure Direct Object Reference (IDOR) invoice tampering was thwarted by user-scoped Eloquent queries returning HTTP 403; and (6) IDOR receipt tampering was rejected through tenant-payment ownership verification.

Furthermore, payment lifecycle transitions were validated within the Midtrans sandbox environment across all seven lifecycle states: (1) Bank BCA Virtual Account issuance generated a 16-digit code with a 24-hour expiry in *pending* status; (2) simulator payment inquiries retrieved bills accurately; (3) payment triggers dispatched SHA-512 validated webhooks transitioning orders to *settlement*; (4) settlement window expiration automatically transitioned orders to *expire* and released inventory locks; (5) user checkout abortion triggered *cancel* signals; (6) forged SHA-512 signatures were detected and rejected with HTTP 403; and (7) duplicate webhook delivery was processed idempotently without redundant ledger writes.

#### 7. Empirical Operational Usability & Dual-Stakeholder Evaluation
On-site administrative workflow testing and end-user usability validation were conducted on the live production environment (*https://asriboardinghouse.weatso.id/*) using a dual-stakeholder evaluation model encompassing both administrative property management and resident scholars:

##### a. Administrative Operational Usability Evaluation (Senior Resident Manager)
Conducted under informed consent, on-site testing was administered directly to the senior resident manager, Mr. Asep (48 years of age, with 20 years of conventional administrative tenure), and recorded as continuous digital audio evidence (*REKAMAN_UX_ADMIN_KOST_2026.m4a*, total duration 23m 14s). Ten operational modules were systematically examined:
1. *Public Storefront & Media*: High-contrast typography proved legible for older vision, while upfront room pricing eliminated repetitive rate inquiries from out-of-town parents.
2. *Docked WhatsApp CTA*: Ergonomically positioned for mobile thumb interaction, directing prospective tenants instantly to management.
3. *OLED Dark Admin Console*: Minimized eye fatigue during extended administrative sessions, while automated occupancy and cashflow calculations eliminated mental arithmetic errors.
4. *Walk-In Intake Form*: Streamlined intake fields permanently recorded guardian contacts, solving historical problems of misplaced emergency numbers.
5. *Cash Settlement & Digital Receipts*: Single-click invoice settlement and stamped digital receipt generation replaced manual carbon receipt tearing, eliminating handwriting disputes and paper waste.
6. *Flat 5% Late Fee Policy*: The combination of an IDR 0 grace period during the active month and a predictable 5% rollover penalty was judged fair, transparent, and enforceable.
7. *Post-Checkout Quarantine*: The visual red-lock prevented premature room re-allocation while linens and sanitation were verified, protecting accommodation hospitality standards.
8. *One-Click Financial Reports*: Compressed the monthly financial reporting cycle from 3 to 5 business days of manual receipt collation into instant PDF/Excel exports, ensuring *zero cash variance* across the IDR 28,500,000 monthly turnover.
9. *Tenant Self-Service & Tickets*: Students settled bills remotely via BCA VA without visiting off-site ATMs, while photo-documented maintenance ticketing accelerated repair dispatches.
10. *Overall Retrospective Assessment*: Mr. Asep summarized the operational transition: *"Compared to twenty years of handwritten bookkeeping, the difference is night and day. Managing operations is substantially easier, and cash reconciliation errors from misplaced paper slips have been eliminated."* The administrative evaluation concluded with an overall operational usability rating of **9.5/10**, confirming strong field adoption and production readiness.

##### b. End-User Workflow & Acceptance Evaluation (Five Student Boarding House Tenants)
To evaluate the end-user journey across diverse room tiers, rental durations, and devices, semi-structured interviews were administered to five active student tenants (*surrogate users*) based on a 10-parameter continuous operational framework spanning initial room discovery to recurring monthly settlement:
- *Responden 1 (Arjun)*: Undergraduate student (6th semester, originating from Balikpapan), testing via Laptop and Android smartphone. Focused on room catalogs, booking forms, Midtrans BCA VA payments, and A5 PDF receipts. Score: **8.5 / 10** (*"Significantly streamlines lodging; seamless progression from searching rooms to settling bills, and the A5 receipt is neat and official for reporting to parents."*).
- *Responden 2 (Farrel)*: Undergraduate student (Universitas Semarang / USM - Boarding House Resident), testing via Android smartphone. Focused on mobile accessibility, reservation simplicity, and invoice tracking. Score: **7.0 / 10** (*"Greatly assists student migrants; eliminates late-night ATM cash runs by settling rent directly from the room; recommends activating QRIS or e-wallets for non-BCA account holders."*).
- *Responden 3 (Iman)*: Undergraduate student (Universitas Dian Nuswantoro / UDINUS - Boarding House Resident), testing via Laptop. Focused on instant automated verification, historical ledger transparency, and server stability. Score: **9.0 / 10** (*"Highly dependable and accountable; automated verification removes disputes from conventional ledger oversights; recommends server stability on peak billing cycles."*).
- *Responden 4 (Ammar)*: Undergraduate student (Politeknik Keuangan Negara STAN / PKN STAN - Boarding House Resident), testing via iOS smartphone. Focused on remote booking from out-of-town, dignified WhatsApp reminders, and off-campus monthly rent settlement. Score: **7.5 / 10** (*"Highly beneficial for boarding students; enables self-sufficient monthly rent payments from out-of-town; recommends embedding direct payment links in WhatsApp reminders."*).
- *Responden 5 (Sindhu)*: Undergraduate student (Universitas Indonesia / UI - Boarding House Resident), testing via Android smartphone. Focused on tenant portal dashboard navigation and walk-in WhatsApp account provisioning. Score: **8.0 / 10** (*"Significantly modernizes boarding house management; eliminates face-to-face cash handoffs, and verification occurs instantaneously without delays; recommends initial login tour guide."*).

The evaluation across all five student respondents yielded an average satisfaction score of **8.00 / 10**, with unanimous agreement that the system **significantly simplifies** (*sangat mempermudah*) student boarding house life. Qualitative synthesis identified three major operational benefits:
1. *Online Room Reservation*: High-resolution photo galleries and transparent fee calculators eliminated ambiguous room specifications and unnecessary on-site visits;
2. *Cashless Settlements & Instant Proof*: BCA Virtual Account generation in under 60 seconds with real-time webhook confirmation removed the burden of queuing at ATMs and sending transfer slips, while downloadable client-side A5 PDF receipts via *html2pdf.js* furnished immediate accountability to parents;
3. *Transparent Monthly Invoicing*: Automated WhatsApp dispatches on the 1st of each month provided clear due date awareness, polite notification language protected resident dignity, and transparent ledger histories eliminated financial disputes.
Constructive student feedback highlighted two future enhancements: expanding payment channels to include QRIS/e-wallets and introducing a visual "Grace Period" (*Masa Toleransi*) tag during intra-month leeway before transitioning to an overdue alert.

---

### B. DISCUSSION

The empirical findings demonstrate that coupling automated payment orchestration with database-level concurrency locking and structured tenancy logic systematically resolves the core vulnerabilities of manual boarding house management.

A critical comparison with prior software engineering scholarship reveals key architectural advantages:
- *Comparison with Room Booking and Catalog Digitization*: Earlier platforms developed by Cornellya & Afriyadi [1] and Nizar [2] introduced web-based room browsing and record logging; however, both systems relegated payment verification to manual administrative scrutiny of uploaded bank slips and lacked database-level concurrency controls. By embedding Midtrans Snap v2, the platform presented here shifts settlement from subjective human inspection to deterministic, SHA-512 cryptographically authenticated webhooks. Furthermore, while conventional web forms remain vulnerable to concurrent booking collisions, our architecture enforces serializability via pessimistic row-level locking (*lockForUpdate()*) inside atomic database transactions (`DB::transaction`), precluding double bookings (*zero double-booking*) during enrollment surges.
- *Transformation from Promotional Portals to Self-Service B2C Ecosystems*: Studies by Jannah et al. [3] and Purnia et al. [5] examined lodging marketing portals and marketplace prototypes. Unlike third-party aggregators that introduce intermediary commission markups and fragmented communications, Asri Boarding House deploys an independent B2C web platform featuring a high-contrast Neo-Brutalist interface (WCAG 2.1 compliant), real-time *Guest Chat*, and docked WhatsApp CTAs, enabling prospective tenants to negotiate and complete reservations directly without third-party fees.
- *Automated Billing and Elimination of Reconciliation Discrepancies*: In lodging financial monitoring, Malaikosa & Mokola [4] proposed a web monitoring system using RAD; however, transactions remained vulnerable to manual recording errors and bookkeeping delays. This research advances operational management by implementing an autonomous cron billing engine operating on the 1st of each month, an idempotent 5% rollover late fee, automated WhatsApp dispatches to tenants and registered guardians via Fonnte [13], and client-side A5 receipt compilation via *html2pdf.js*. This integrated pipeline guarantees *zero cash variance* across an aggregate monthly turnover baseline of IDR 28,500,000 and compresses administrative ledger balancing from 3–5 working days of manual calculator labor to instantaneous reporting.
- *Extending Payment Gateways Beyond Retail Paradigms*: Previous payment gateway literature by Sutisna & Aziz [7], Surya Pratama [8], Pramita et al. [9], Wijaya et al. [10], and Fatman et al. [12] implemented Midtrans within e-commerce retail, school tuition, or service point-of-sale platforms. Such architectures operate under transient, one-off checkout paradigms terminating upon payment confirmation. In contrast, residential lodging governance operates across multi-month contractual lease agreements governed by recurring billing milestones, designated grace periods, rollover late penalties, and multi-party guardian escalation pathways.

From an architectural perspective, two engineering solutions proved critical:
1. *Soft-Delete Unicity via Virtual Generated Columns*: Enforcing database-level unicity across soft-deleted rows without sacrificing relational integrity was achieved by defining conditional virtual columns (*active_email*, *active_no_hp*, *active_nik*, *active_nomor_kamar*) paired with unique indices, ensuring duplicate credential insertions are rejected at the MySQL engine tier while preserving historical audit trails.
2. *Technical Verification vs. Human User Acceptance*: Perfect functional pass rates across 60 black box test scenarios [15], [16] and 510 automated PHPUnit integration tests (2,211 assertions) confirmed that the system is technically defect-free, deterministic, and resistant to RBAC/IDOR tampering [14]. Nevertheless, software correctness alone does not guarantee field adoption. High operational satisfaction (9.5/10 from management and 8.00/10 from student residents) was achieved because technical invariants were harmonized with human operational habits: high-contrast typography, an eye-friendly OLED Black dark mode, 56-pixel mobile touch targets, an intra-month IDR 0 grace period with a predictable 5% rollover penalty, and polite WhatsApp dispatches.

*Scope Limitations and Research Trajectory*: While testing in a live production environment with the property's senior resident manager and five active student tenants provides grounded operational validity, this investigation remains an in-depth single-site case study. Distinct building physicalities, alternative regional tenancy customs, and varying managerial digital literacy levels across other university districts may yield differing operational requirements. This inherent scope limitation highlights the necessity for multi-branch architectural scaling and comparative empirical investigations across broader accommodation classes, as articulated in the technical roadmap.

---

## IV. CONCLUSION

This study engineered, deployed, and empirically evaluated an integrated Management Information System for Asri Boarding House, resolving entrenched operational friction caused by manual bookkeeping and fragmented communication. Grounded in the underlying thesis research, three primary conclusions directly address the research objectives:
1. *System Design and Architectural Integration*: The web-based system was successfully built on Laravel 11 and a 3NF-normalized MySQL 8.0 InnoDB database spanning 22 relational entities with Virtual Generated Columns for soft-delete unicity. Core functional deliverables—including an accessible Neo-Brutalist catalog (WCAG 2.1), guided 5-step booking stepper, Midtrans Snap v2 Bank BCA Virtual Account integration with SHA-512 signatures, automated billing cron daemon, Fonnte WhatsApp dispatches, and client-side *html2pdf.js* A5 receipt generation (*zero server storage overhead*)—were verified with a 100% pass rate across 60 black box test scenarios and 510 automated PHPUnit integration tests (2,211 assertions), with RBAC/IDOR role isolation strictly enforced.
2. *Operational Effectiveness*: The platform proved highly effective in resolving the property's three chronic administrative bottlenecks:
   - *Rent Collection & Arrears Governance*: Eliminated manual collection slippage through scheduled monthly invoice generation on the 1st of each month at 00:05 WIB with a uniform due date on the 10th, an intra-month IDR 0 grace period, an idempotent 5% calendar-rollover late fee, and tiered WhatsApp default escalation to tenant guardians;
   - *Financial Balancing & Zero Cash Variance*: Secured an aggregate monthly turnover baseline of IDR 28,500,000 against bookkeeping discrepancies (*zero cash variance*) via real-time BCA VA settlements, compressing monthly financial statement collation from 3–5 working days of manual calculator arithmetic into instantaneous PDF/Excel reporting;
   - *Concurrency Control & Anti-Double Booking*: Eliminated room reservation collisions (*zero double-booking*) during university intake surges through pessimistic row-level locking (*lockForUpdate()*) in isolated database transactions, reinforced by an operational post-checkout sanitation hold.
3. *Dual-Stakeholder Empirical Acceptance*: Production deployment (*https://asriboardinghouse.weatso.id/*) demonstrated strong field adoption and operational viability across both administrative and end-user stakeholders:
   - *Administrative Management*: Senior resident manager Mr. Asep (48 years old, 20 years of operational experience) awarded an operational satisfaction score of **9.5 / 10**, commending the eye-friendly OLED Black dark mode, one-touch cash settlements, automated financial ledgers, and fair 5% rollover penalty;
   - *Student Residents*: Semi-structured interviews across five active student tenants (*surrogate users*) produced an aggregate satisfaction score of **8.00 / 10**, with unanimous agreement that the platform **significantly simplifies** (*sangat mempermudah*) student lodging routines through intuitive online reservations, swift BCA VA transactions, neat A5 receipts for family reporting, and courteous WhatsApp notifications.

To guide future technical enhancements, six development directions are identified:
1. *QRIS and Digital E-Wallet Expansion*: Integrating QRIS and e-wallets (GoPay, ShopeePay) into the Midtrans Snap modal to serve prospective residents without BCA bank accounts;
2. *Tiered Reminder Scheduling and Grace Period Labeling*: Automating multi-tiered WhatsApp reminders at H-2 (8th) and Day-of (10th), while refining the portal status tag to display a yellow "Grace Period" (*Masa Toleransi*) badge during intra-month leeway;
3. *Interactive Onboarding for Walk-In Registrants*: Incorporating concise interactive onboarding links within initial WhatsApp dispatches to orient walk-in residents to digital receipt downloads and maintenance ticketing;
4. *Automated Contract Expiration Alerts (H-30)*: Dispatching automated advance renewal reminders via WhatsApp 30 days prior to annual or semester lease expiration;
5. *High-Concurrency Caching and Scalability*: Implementing in-memory caching (e.g., Redis) and LiteSpeed web server optimizations to guarantee responsive portal performance during mass billing dispatches on the 1st of each month;
6. *Multi-Branch Scaling and IoT Interfacing*: Extending the relational schema with a foreign key (*cabang_id*) for multi-property administration, and integrating IoT smart door locks with time-bounded PINs and digital sub-meters for automated utility billing.

---

## ACKNOWLEDGMENT

The authors express their heartfelt appreciation to the Department of Information Systems, Faculty of Computer Science, Universitas Katolik Soegijapranata for institutional and technical guidance provided during this study. Sincere gratitude is also conveyed to Mr. Asep and the proprietors of Asri Boarding House, Tembalang, Semarang, for their invaluable operational partnership, administrative access, and active engagement throughout the field testing and system validation phases.

---

## REFERENCES

[1] A. Cornellya and H. Afriyadi, "Perancangan Sistem Informasi Pemesanan pada Kost Tya Berbasis Web Menggunakan Framework Laravel," *PESHUM J. Pendidikan, Sos. dan Hum.*, vol. 4, no. 6, pp. 10360–10369, 2025, doi: 10.56799/peshum.v4i6.11777.

[2] C. Nizar, "Rancang Bangun Sistem Informasi Sewa Rumah Kost (E-Kost) Berbasis Website," *J. Sist. Inf. dan Sains Teknol.*, vol. 3, no. 1, pp. 1–10, 2021, doi: 10.31326/sistek.v3i1.852.

[3] A. Jannah, P. Arsyianita, A. A. Yuni, W. Harniati, and N. L. Hasanah, "Sistem Informasi Pemasaran Rumah Kost Berbasis Web," *J. Simantec*, vol. 8, no. 2, pp. 78–86, 2020, doi: 10.21107/simantec.v8i2.8899.

[4] E. J. Malaikosa and P. Mokola, "Sistem Informasi Monitoring Rumah Kos dan Pembayarannya Berbasis Web Menggunakan Metode Rapid Application Development," *JSiI (Jurnal Sist. Informasi)*, vol. 11, no. 1, pp. 21–26, 2024, doi: 10.30656/jsii.v11i1.8222.

[5] D. S. Purnia, R. Ratningsih, and M. Surahman, "Implementasi Metode Prototyping pada Rancang Bangun Marketplace Rumah Kost Berbasis Mobile," *EVOLUSI J. Sains dan Manaj.*, vol. 9, no. 1, pp. 1–11, 2021, doi: 10.31294/evolusi.v9i1.10145.

[6] K. C. Laudon and J. P. Laudon, *Management Information Systems: Managing the Digital Firm*, 15th ed. Harlow, UK: Pearson Education, 2018.

[7] R. Sutisna and F. Aziz, "Perancangan Sistem Penyewaan Alat Event Berbasis Website Menggunakan Midtrans sebagai Integrasi Payment Gateway pada PT. Bangbewe Production," *REMIK Ris. dan E-Jurnal Manaj. Inform. Komput.*, vol. 9, no. 1, pp. 317–325, 2025, doi: 10.33395/remik.v9i1.14498.

[8] M. I. Surya Pratama, "Integrasi Payment Gateway pada Aplikasi Point of Sales Berbasis Website Menggunakan Framework ReactJS (Studi Kasus: Toko Adida Pratama)," *J. Inform. dan Tek. Elektro Terap.*, vol. 13, no. 3, 2025, doi: 10.23960/jitet.v13i3.7099.

[9] I. P. Pramita, A. M. Harahap, and A. B. Nasution, "Penerapan Payment Gateway Midtrans pada Sistem Pembayaran SPP Berbasis Android di SMAN 1 Bangun Purba," *J. Ris. Sist. Inf. dan Teknol. Inf.*, vol. 6, no. 3, pp. 479–490, 2024, doi: 10.52005/jursistekni.v6i3.367.

[10] F. Wijaya, M. Maslim, M. Martinus, and P. Ardanari, "Pembangunan Sistem Informasi Laundry Berbasis Web Menggunakan Payment Gateway Midtrans," *Prolet. Community Serv. Dev. J.*, vol. 1, no. 1, pp. 8–14, 2023, doi: 10.61098/proletariancomdev.v1i1.63.

[11] Y. Anggraini, D. Pasha, D. Damayanti, and A. Setiawan, "Sistem Informasi Penjualan Sepeda Berbasis Web Menggunakan Framework CodeIgniter," *J. Teknol. dan Sist. Inf.*, vol. 1, no. 2, pp. 64–70, 2020, doi: 10.33365/jtsi.v1i2.236.

[12] Y. Fatman, N. K. Nafisah, and P. B. J. Pambudi, "Implementasi Payment Gateway dengan Menggunakan Midtrans pada Website UMKM Geberco," *J. KomtekInfo*, vol. 10, no. 2, pp. 64–72, 2023, doi: 10.35134/komtekinfo.v10i2.364.

[13] L. Hakim, S. P. Kristanto, M. N. Shodiq, and E. Amaliyah, "Aplikasi Penerimaan dan Pengeluaran Kas Berbasis Web dan WhatsApp Gateway," *J. Tekno Kompak*, vol. 15, no. 1, pp. 13–24, 2021, doi: 10.33365/jtk.v15i1.900.

[14] P. Philippaerts, D. Preuveneers, and W. Joosen, "OAuch: Exploring Security Compliance in the OAuth 2.0 Ecosystem," in *Proc. 25th Int. Symp. Research in Attacks, Intrusions and Defenses (RAID '22)*, New York, NY, USA: ACM, 2022, pp. 460–481, doi: 10.1145/3545948.3545955.

[15] W. N. Cholifah, Y. Yulianingsih, and S. M. Sagita, "Pengujian Black Box Testing pada Aplikasi Action & Strategy Berbasis Android dengan Teknologi Phonegap," *STRING (Satuan Tulisan Ris. dan Inov. Teknol.)*, vol. 3, no. 2, pp. 206–210, 2018, doi: 10.30998/string.v3i2.3048.

[16] E. Setiana, M. R. Ramadhan, B. Budiman, and R. Y. Rakhman, "Pengujian Perangkat Lunak Metode Black Box pada Aplikasi Sistem Pakar Pola Latihan dan Asupan Makanan," *Nuansa Inform.*, vol. 18, no. 1, pp. 68–74, 2024, doi: 10.25134/ilkom.v18i1.67.

[17] R. S. Pressman and B. R. Maxim, *Software Engineering: A Practitioner's Approach*, 8th ed. New York, NY, USA: McGraw-Hill Education, 2015.

[18] F. Sonata, "Pemanfaatan UML (Unified Modeling Language) dalam Perancangan Sistem Informasi E-Commerce Jenis Customer-to-Customer," *J. Komunika*, vol. 8, no. 1, pp. 22–31, 2019, doi: 10.31504/komunika.v8i1.1832.

[19] T. Pricillia and Z. Zulfachmi, "Perbandingan Metode Pengembangan Perangkat Lunak (Waterfall, Prototype, RAD)," *J. Bangkit Indones.*, vol. 10, no. 1, pp. 6–12, 2021, doi: 10.52771/bangkitindonesia.v10i1.153.

[20] D. G. A. Candra and P. P. Pardika, "Analisa dan Perancangan Sistem Informasi Manajemen Pengolahan Data Pesanan Sablon di KYSR Store Menggunakan Metode Waterfall," *JAMI J. Ahli Muda Indones.*, vol. 5, no. 2, pp. 134–147, 2024, doi: 10.46510/jami.v5i2.306.
