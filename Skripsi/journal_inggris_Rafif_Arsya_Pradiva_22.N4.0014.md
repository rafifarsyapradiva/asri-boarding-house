<!--
  =============================================================================
  TAHAP 1: PENGATURAN HALAMAN DAN TATA LETAK UMUM (PAGE SETUP)
  - Ukuran Kertas        : A-4 (21.0 cm x 29.7 cm)
  - Margin               : 2.5 cm di setiap sisi (Atas, Bawah, Kiri, Kanan)
  - Format Kolom         : Dua Kolom (Two Columns), jarak antar kolom 0.5 cm
  - Huruf Utama (Font)   : Times New Roman, 12 pt, Regular
  - Spasi (Line Spacing) : 1.0 (Single Spacing)
  - Batas Maksimal       : Maksimal 8 Halaman
  =============================================================================
-->

<!-- TAHAP 2: BAGIAN KEPALA ARTIKEL (JUDUL, PENULIS, AFILIASI, DAN EMAIL) -->
<div align="center">

## **BOARDING HOUSE MANAGEMENT INFORMATION SYSTEM INTEGRATED WITH PAYMENT GATEWAY AT ASRI BOARDING HOUSE**

<p>
  <sup>1</sup>Rafif Arsya Pradiva, <sup>2</sup>Andre Kurniawan Pamudji, <sup>3</sup>Erdhi Widyarto Nugroho
</p>

<p>
  Department of Information Systems, Faculty of Computer Science<br>
  Soegijapranata Catholic University, Semarang, Indonesia
</p>

<p>
  <sup>1</sup>22n40014@student.unika.ac.id, <sup>2</sup>andre.kurniawan@unika.ac.id, <sup>3</sup>erdhi@unika.ac.id
</p>

</div>

---

<!-- TAHAP 3: ABSTRAK DAN KATA KUNCI (ABSTRACT & KEYWORDS) -->
<p align="justify">
  <strong><em>Abstract</em></strong>
</p>

<p align="justify">
  <strong>At Asri Boarding House in Semarang, staff long managed 32 rental rooms using paper ledgers and personal chat. Slow manual updates sometimes caused double bookings. Rent reminders for the 10th due date were easy to miss, and closing the books at month end took three to four days. We built a web-based management information system linked to a payment gateway so room bookings sit beside monthly billing and cash reports in one database. Development followed a Research and Development (R&amp;D) approach under the Waterfall model using Laravel and a 3NF MySQL schema. Pessimistic row locking stops concurrent applicants from claiming the same unit. Midtrans Snap processes cashless transfers and settles each invoice automatically after SHA-512 webhook verification. On the 1st, server cron jobs generate new bills and trigger WhatsApp plus email notices; unpaid rent that crosses into the next month incurs one flat 5% fine. All 60 black-box tests passed across six modules. In on-site trials, the caretaker gave a 9.5/10 score as month-end closing shrank from days to instant exports with zero cash variance. Five student tenants averaged 8.0/10, mostly because of Virtual Account payments and downloadable A5 PDF receipts.</strong>
</p>

<p align="justify">
  <strong><em>Keywords:</em></strong> Asri Boarding House, boarding house management system, Laravel, Midtrans payment gateway, pessimistic row locking
</p>

---

<!-- TAHAP 4: BAB I. INTRODUCTION -->
### **I. INTRODUCTION**

<p align="justify">
  Each new semester brings out-of-town students to Semarang's Tembalang campus district. Because many nearby boarding houses still depend on word of mouth, newcomers often walk door to door or wait on chat replies just to find vacant rooms near lecture halls [1]. Asri Boarding House on Jalan Maera Sari No. 1 operates 32 units under that routine: 6 VIP, 3 Deluxe, and 23 Standard. The caretaker logs occupancy in a paper book and answers personal chats by hand, so delayed entries occasionally let two applicants claim the same room [2].
</p>

<p align="justify">
  Monthly rent collection runs into similar friction. Rent falls due on the 10th, followed by a grace period until month end; only balances carried into the next month incur a flat 5% fine. Checking due dates by hand and messaging residents individually lets reminders slip, so arrears grow [3]. Tenants also wait for manual approval after sending transfer screenshots. Paper receipts easily go missing, and reconciling cash deposits against transfer proofs and expense slips at month end takes three to four days. That manual tally invites arithmetic mistakes, which an online payment gateway removes [4].
</p>

<p align="justify">
  Putting room stock alongside lease and cash records inside one Management Information System (MIS) speeds up daily decisions [5]. A Business-to-Consumer (B2C) catalog cuts out broker fees by linking the owner directly to students [6]. High-contrast Neo-Brutalism styling keeps prices and booking buttons legible on phones and laptops [7]. On the server, Laravel's Model-View-Controller (MVC) architecture keeps data queries separate from business rules and page views [8], and a Third Normal Form (3NF) MySQL InnoDB schema prevents duplicate rows and update anomalies [9]. Each lease saves a fixed rent snapshot so future tariff hikes leave active contracts untouched. InnoDB row locking stops simultaneous bookings.
</p>

<p align="justify">
  Most boarding-house websites stop at showing room photos, so tenants still upload transfer receipts and wait for staff checks. Integrating the Midtrans payment gateway removes that delay because cashless transactions, including Virtual Accounts, settle automatically after SHA-512 webhook signature verification [10]. Monthly billing no longer depends on manual date checks either [11]. Server cron jobs issue new invoices on the 1st and run overdue checks after the 10th so each cross-month fine applies only once. The Fonnte WhatsApp API sends an instant phone alert once a bill goes out or clears [12]. Queued SMTP emails also archive formal payment records and receipt links [13].
</p>

<p align="justify">
  Three objectives guide this work. The first covers designing a web-based management information system for Asri Boarding House to handle bookings alongside recurring billing and cash reports. Next, we build the platform in Laravel and MySQL with pessimistic row locking, SHA-512 Midtrans webhooks, cron billing, and automated WhatsApp plus email alerts. Evaluation then verifies reliability and user acceptance through black-box testing alongside live site trials and interviews with the caretaker and active student tenants.
</p>

<!-- TAHAP 5: BAB II. METHOD -->
### **II. METHOD**

<p align="justify">
  Fieldwork took place at Asri Boarding House in Tembalang, Semarang. The research uses a software engineering Research and Development (R&amp;D) approach within a single case study. We first mapped manual ledger problems across 32 rooms, then carried the work through design, coding, and testing until the web platform produced reliable digital bookkeeping [14]. Requirement analysis drew on both primary fieldwork and secondary documents [15]. Primary evidence came from site observation and audio-recorded interviews with the caretaker and five active student tenants; all sessions were transcribed during initial requirement gathering and final evaluation. Secondary sources included physical cash books, transfer proofs, expense notes, tariff sheets, API manuals, and prior studies.
</p>

<p align="justify">
  We built the application under the Waterfall model shown in Figure 1 [16]. A linear sequence worked well here because Asri Boarding House already had fixed operating rules before coding began. Those rules cover 32 rooms, a choice between a 30% down-payment and full payment, rent due on the 10th, a flat 5% cross-month fine, and post-checkout room quarantine. Development moved across six stages: requirement gathering and analysis, design, implementation, integration and testing, verification, and operation and maintenance. Every stage went through review before the next began. When testing or live trials exposed defects, the feedback arrows in Figure 1 allowed direct revisions in earlier stages.
</p>

<div align="center">
  <img src="images/metode_sdlc_waterfall.png" alt="Waterfall System Development Model" width="95%">
  <br>
  <sub><strong>Figure 1.</strong> WATERFALL SYSTEM DEVELOPMENT MODEL</sub>
</div>

<p align="justify">
  During design, we modeled workflows for prospective tenants, active residents, and the administrator using Unified Modeling Language (UML) use case, activity, and flowchart diagrams. Database tables were normalized into a 3NF MySQL InnoDB Entity Relationship Diagram (ERD) that keeps six core lease and payment tables separate from eight supporting tables [17]. High-contrast Neo-Brutalism wireframes kept mobile and desktop views legible. We coded the platform in Laravel 12 MVC with a dedicated service layer and role middleware for private routes. Every room booking executes inside an ACID transaction with <em>lockForUpdate()</em> row locking and date-overlap checks so simultaneous requests cannot claim one unit [18]. For cashless checkout, the server requests 24-hour Midtrans Snap tokens and validates incoming SHA-512 webhook signatures in constant time via <em>hash_equals()</em> [19]. Server cron jobs guarded by <em>withoutOverlapping()</em> issue bills on the 1st and run late-fee checks after the 10th; an hourly job also clears expired bookings. Fonnte WhatsApp API and queued SMTP jobs dispatch every alert automatically.
</p>

<p align="justify">
  Integration testing connected every internal module to the external APIs and exercised 60 black-box test scenarios across six domains: authentication and profile, public catalog, five-step booking, tenant portal, admin operations, and background schedulers [20]. Those runs checked normal flows alongside invalid-input guards, including 16-digit identity validation and locks against deleting occupied rooms. Verification then shifted on site to Asri Boarding House. There, the caretaker and five active student tenants completed real tasks on their own devices before answering semi-structured 10-point interviews. After production deployment, operation and maintenance kept cron jobs active with ongoing checks on database health and notification logs alongside user feedback.
</p>

<!-- TAHAP 6: BAB III. RESULTS AND DISCUSSION -->
### **III. RESULTS AND DISCUSSION**

#### **A. RESULTS**

<p align="justify">
  Access on the live platform separates prospective tenants, active residents, and the administrator. Prospective and active tenants share one <em>tenant</em> role in <em>users</em>; after login, a redirect service routes each user based on active contract status. Figure 2 shows the core 3NF MySQL InnoDB schema across six transactional tables: <em>users</em>, <em>kamar</em>, <em>penyewa</em>, <em>reservasi</em>, <em>tagihan</em>, and <em>pembayaran</em>. Inside <em>penyewa</em>, each contract freezes the agreed rent, move-in date, planned end date, and guardian phone so later price adjustments never alter active leases. Eight supporting tables store repair complaints, expenses, notification logs, guest chat threads and messages, reviews, house rules, and FAQs. Production columns retain Indonesian names. In <em>kamar</em>, a <em>maintenance</em> status sits beside <em>available</em> and <em>occupied</em>.
</p>

<div align="center">
  <img src="images/gambar_4_2_a_erd_modul_inti_en.png" alt="Core Room, Tenancy, and Financial 3NF ERD" width="84%">
  <br>
  <sub><strong>Figure 2.</strong> CORE ROOM, TENANCY, AND FINANCIAL 3NF ENTITY RELATIONSHIP DIAGRAM</sub>
</div>

<p align="justify">
  Figure 3 outlines the reservation flow from room lookup to key handover. An applicant first chooses a vacant unit and enters a 16-digit National Identity Number (NIK) with guardian contacts and stay duration. The conceptual flowchart labels this step as an ID card upload, yet the live site keeps only the 16-digit number online for privacy and leaves physical ID inspection for arrival. On submission, the backend runs an ACID transaction with <em>lockForUpdate()</em> row locking and schedule-overlap checks. Two applicants cannot lock the same room at once. Once Midtrans clears payment, an asynchronous webhook verifies the SHA-512 signature through <em>hash_equals()</em> and confirms both the amount and idempotency log to lock the booking. At check-in, the caretaker inspects the physical ID and activates tenancy before handing over the room key. WhatsApp and queued SMTP email notices follow immediately. Scheduled <em>withoutOverlapping()</em> cron jobs issue new bills on the 1st and monitor grace windows past the 10th. They also add a single 5% cross-month fine and clear expired unpaid bookings hourly.
</p>

<div align="center">
  <img src="images/gambar_4_6_flowchart_sistem_en.png" alt="End-to-End Room Reservation, Midtrans Payment, and Tenant Activation Flowchart" width="95%">
  <br>
  <sub><strong>Figure 3.</strong> END-TO-END ROOM RESERVATION, MIDTRANS PAYMENT, AND TENANT ACTIVATION FLOWCHART</sub>
</div>

<p align="justify">
  Built with 36 responsive Neo-Brutalism screens, the website serves public visitors alongside the applicant, resident, and admin portals. Visitors filter all 32 VIP, Deluxe, and Standard rooms by tier or room number. Each card displays live availability, room dimensions, facilities, and monthly rent so prospects check openings without messaging staff first. Occupied cards swap the booking button for a direct WhatsApp link. Figure 4 shows the five-step reservation tracker (Figure 4a) next to the Midtrans checkout modal (Figure 4b). Inside the tracker, applicants move through booking, discussion, payment, activation, and completion; they confirm room readiness over live chat and pick either a 30% down payment or full settlement. Starting checkout opens Midtrans Snap with a 24-hour Bank BCA Virtual Account number for immediate cashless settlement.
</p>

<div align="center">
  <img src="images/gambar_4_21_pembayaran_5_tahap_reservasi_en.png" alt="Five-Step Reservation Workspace" width="58%">
  <img src="images/gambar_4_22_payment_gateway_midtrans.png" alt="Midtrans Snap Virtual Account Modal" width="35%">
  <br>
  <sub><strong>Figure 4.</strong> ONLINE BOOKING WORKFLOW: (a) FIVE-STEP RESERVATION WORKSPACE; (b) MIDTRANS SNAP VIRTUAL ACCOUNT MODAL</sub>
</div>

<p align="justify">
  Daily management runs through the admin portal shown in Figure 5. At the top of the dashboard (Figure 5a), eight live summary cards track current-month metrics for all 32 rooms: total occupancy, available units, revenue, expenses, net profit, unpaid invoices, incoming bookings, and WhatsApp clicks. Below those cards, a 12-month bar chart plots cash flow beside a room-status donut graph. Staff can also toggle OLED dark mode in the header for comfortable evening viewing. On the financial report screen (Figure 5b), settled rent payments appear alongside daily operating expenses across single-month or cumulative ranges. One click exports clean PDF or Excel ledgers. Active residents have their own portal to review past bills and submit photo-attached repair tickets; once rent clears, they download browser-rendered A5 PDF receipts stamped <em>PAID</em> with the official Midtrans transaction ID.
</p>

<div align="center">
  <img src="images/gambar_4_26_dashboard_admin_en.png" alt="Administrator Executive Dashboard" width="48%">
  <img src="images/gambar_4_33_manajemen_laporan_pemasukan_en.png" alt="Monthly Billing and Financial Report Module" width="48%">
  <br>
  <sub><strong>Figure 5.</strong> ADMINISTRATOR PORTAL: (a) LIVE EXECUTIVE DASHBOARD; (b) FINANCIAL REPORTING AND EXPORT MODULE</sub>
</div>

<p align="justify">
  All 60 black-box tests across six modules passed without validation or transaction failures. Positive checks verified room filters, multi-month discounts, Virtual Account callbacks, client-side A5 receipts, ledger exports, and automated WhatsApp plus email alerts. Negative tests caught invalid actions: 14-digit NIK entries, non-image complaint files, occupied-room deletion, and duplicate monthly fine runs. Field interviews on a 10-point scale backed up those tests. After twenty years of keeping paper ledgers, the senior caretaker gave the platform 9.5/10. Five active student tenants tested the site on Android phones, iPhones, and laptops, and their scores ranged from 7.0 to 9.0 with an 8.0/10 average.
</p>

#### **B. DISCUSSION**

<p align="justify">
  Site interviews show that automatic payment posting and clear operating rules removed the main daily delays at Asri Boarding House. Month-end closing dropped from three or four days of sorting paper notes and chat screenshots to instant PDF and Excel exports. Cash discrepancies fell to zero. Walk-in registration now captures guardian numbers upfront, and one-click cash confirmation replaces handwritten receipt books. Staff also turn on OLED dark mode during evening checks to reduce eye strain, and residents attach photos to repair tickets so hallway fixes stay documented. Two offline steps stayed manual on purpose. Staff still verify original ID cards at arrival so applicants keep sensitive documents offline, and newly vacated rooms remain in quarantine until cleaning and inspection finish. Keeping a same-month grace period past the 10th before a single 5% rollover fine also fits student allowance schedules without harsh daily compounding.
</p>

<p align="justify">
  Interviews with the five student tenants explain why their ratings spread from 7.0 to 9.0. Out-of-town newcomers liked checking live room vacancies and transparent tariffs from home before traveling to Semarang. Paying rent through Virtual Accounts on Android browsers, iPhone Safari, or laptops ended late-night trips to the ATM and stopped arguments over missed ledger entries. Once a payment settled, tenants forwarded the A5 PDF receipt straight to their parents or saved a copy in cloud storage. Field testing also uncovered practical rough edges. Wide payment tables forced sideways scrolling on narrow phone screens, and the five-step booking flow felt somewhat long on desktop monitors. First-time users needed a few extra minutes to locate the receipt and repair menus. Those findings shape the next release: H-3 WhatsApp reminders with direct checkout links, stacked mobile table cards, a short onboarding guide, peak-day caching on the 1st, digital lease agreements, and a dedicated mobile app.
</p>

<p align="justify">
  Most small boarding-house websites still depend on manual transfer screenshots and leave booking concurrency unprotected. In our platform, InnoDB <em>lockForUpdate()</em> locks work with date-overlap checks and post-checkout quarantine so simultaneous requests cannot book any of the 32 units. Manual bank audits are no longer needed. Constant-time <em>hash_equals()</em> SHA-512 verification and idempotent callbacks record verified payments straight into the live ledger, with Snap token expiry capped at month end. Linked to monthly <em>withoutOverlapping()</em> schedulers and WhatsApp plus queued SMTP alerts, those webhooks handle billing throughout the entire stay. Field validation still has clear boundaries. Trials involved one 32-room house with six users, and launch enabled only the Bank BCA Virtual Account channel to match the owner's bank. Because the schema and service layer stay decoupled, supporting QRIS and e-wallets or expanding to future branches takes little core rework.
</p>

<!-- TAHAP 7: BAB IV. CONCLUSION -->
### **IV. CONCLUSION**

<p align="justify">
  This study designed a web architecture for Asri Boarding House that handles bookings alongside rent collection and cash reporting across all 32 rooms. UML diagrams split access for prospective applicants, active residents, and management across 36 responsive Neo-Brutalism pages. Underneath, a 3NF MySQL InnoDB database holds six primary tenancy and payment tables plus eight operational tables. Fixed contract snapshots protect active leases from later tariff updates. Physical checks remain in place too: staff inspect original ID cards at move-in and hold vacated rooms until cleaning finishes.
</p>

<p align="justify">
  In production, the web application runs on Laravel 12 MVC with role-based route protection and a dedicated service layer. Atomic ACID transactions with <em>lockForUpdate()</em> locks and schedule-conflict checks block double bookings. Midtrans Snap generates 24-hour Virtual Account numbers for cashless checkout, and SHA-512 webhook checks via <em>hash_equals()</em> reject forged or duplicate callbacks before settlement. Non-overlapping cron jobs clear expired bookings every hour and generate monthly bills on the 1st. Cross-month arrears take a single 5% fine. Every invoice and payment notice goes out automatically through the Fonnte WhatsApp API and queued SMTP email.
</p>

<p align="justify">
  All 60 functional scenarios passed across six modules. In on-site trials, the senior caretaker rated the platform 9.5/10 because month-end closing dropped from several days of manual reconciliation to instant exports with zero cash mismatch. Five student residents scored the portal between 7.0 and 9.0 (8.0/10 average) for remote room browsing and screenshot-free Virtual Account payments plus stamped A5 receipts. Field validation stayed limited to one 32-room property with six participants and the Bank BCA Virtual Account channel. Future releases can add QRIS and e-wallet channels, improve phone layouts with stacked cards and short onboarding hints, and introduce H-3 WhatsApp pay links, peak-day caching, digital leases, and a mobile app.
</p>

<!-- TAHAP 8: ACKNOWLEDGMENT (TANPA NOMOR BAB) -->
### **ACKNOWLEDGMENT**

<p align="justify">
  We thank the Department of Information Systems and the Faculty of Computer Science at Soegijapranata Catholic University, Semarang, for their academic guidance and research support. Asri Boarding House's property owner in Tembalang welcomed our fieldwork. We also thank the senior caretaker, who shared daily ledgers and house rules before testing the admin portal firsthand. Our gratitude extends to the five active student tenants who tested the resident portal on their own devices and shared candid feedback.
</p>

<!-- TAHAP 9: REFERENCES (TANPA NOMOR BAB, 10 PT REGULAR & 10 PT ITALIC IEEE) -->
### **REFERENCES**

<p align="justify">
  <sub>[1] A. Cornellya and H. Afriyadi, "Perancangan Sistem Informasi Pemesanan pada Kost Tya Berbasis Web Menggunakan Framework Laravel," <em>PESHUM J. Pendidikan, Sos. dan Hum.</em>, vol. 4, no. 6, pp. 10360-10369, 2025, doi: 10.56799/peshum.v4i6.11777.</sub><br>
  <sub>[2] C. Nizar, "Rancang Bangun Sistem Informasi Sewa Rumah Kost (E-Kost) Berbasis Website," <em>J. Sist. Inf. dan Sains Teknol.</em>, vol. 3, no. 1, pp. 1-10, 2021, doi: 10.31326/sistek.v3i1.852.</sub><br>
  <sub>[3] E. J. Malaikosa and P. Mokola, "Sistem Informasi Monitoring Rumah Kos dan Pembayarannya Berbasis Web Menggunakan Metode Rapid Application Development," <em>JSiI (Jurnal Sist. Informasi)</em>, vol. 11, no. 1, pp. 21-26, 2024, doi: 10.30656/jsii.v11i1.8222.</sub><br>
  <sub>[4] R. Sutisna and F. Aziz, "Perancangan Sistem Penyewaan Alat Event Berbasis Website Menggunakan Midtrans sebagai Integrasi Payment Gateway pada PT. Bangbewe Production," <em>REMIK Ris. dan E-Jurnal Manaj. Inform. Komput.</em>, vol. 9, no. 1, pp. 317-325, 2025, doi: 10.33395/remik.v9i1.14498.</sub><br>
  <sub>[5] A. Syahputra, R. Wiranti, and W. Astita, "Peran Sistem Informasi Manajemen Organisasi Dalam Pengambilan Keputusan," <em>J. Manaj. Sist. Inf.</em>, vol. 1, no. 1, pp. 26-31, 2022, doi: 10.35870/jmasif.v1i1.67.</sub><br>
  <sub>[6] I. A. Nurhasanah, M. Brilliant, K. Reni, and A. Mulyanto, "Analisis Perancangan E-Business B2C (Business to Consumer) Upaya Digitalisasi Pengembangan UMKM (Studi Kasus : Kabupaten Pesawaran, Indonesia)," <em>J. Nas. Pendidik. Tek. Inform. JANAPATI</em>, vol. 11, no. 3, pp. 236-248, 2022, doi: 10.23887/janapati.v11i3.49787.</sub><br>
  <sub>[7] A. R. Setiawan, M. Asfi, A. Sevtiana, S. Pranata, and W. E. Septian, "Design System pada Perancangan Antarmuka Perangkat Lunak Sistem Akses Digital," <em>J. Teknol. Terpadu</em>, vol. 9, no. 1, pp. 56-64, 2023, doi: 10.54914/jtt.v9i1.619.</sub><br>
  <sub>[8] L. Rahmawati and Sumarsono, "Desain Pengembangan Website dengan Arsitektur Model View Controller pada Framework Laravel," <em>J. Teknol. dan Sist. Inf. Bisnis</em>, vol. 6, no. 4, pp. 785-790, 2024, doi: 10.47233/jteksis.v6i4.1497.</sub><br>
  <sub>[9] D. F. M. Dina, "Normalisasi Database Rancangan Sistem Penyewaan Buku Berbayar," <em>Computing Insight J. Comput. Sci.</em>, vol. 4, no. 1, pp. 56-61, 2024, doi: 10.30651/comp_insight.v4i1.15814.</sub><br>
  <sub>[10] M. Nurhayati and R. Setiawan, "Implementasi Payment Gateway Midtrans Pada Aplikasi Toko Mas Berbasis Web," <em>J. Algoritma</em>, vol. 21, no. 2, pp. 366-377, 2024, doi: 10.33364/algoritma/v.21-2.1686.</sub><br>
  <sub>[11] A. Wibowo, A. Fathimah, D. Waskito, and R. A. Aprilianto, "Cron Job Implementation for Automated Data Processing and Transfer in Cloud Infrastructure," <em>Sainteknol J. Sains dan Teknol.</em>, vol. 23, no. 1, pp. 11-20, 2025, doi: 10.15294/sainteknol.v23i1.22649.</sub><br>
  <sub>[12] I. M. B. Waton, A. N. Weking, and B. Deta, "Penerapan Api Whatsapp Fonnte Untuk Sistem Pengingat Jadwal Bimbingan Tugas Akhir Mahasiswa Berbasis Web," <em>J. Tek. Mesin, Elektro dan Ilmu Komput.</em>, vol. 5, no. 2, pp. 229-241, 2025, doi: 10.55606/teknik.v5i2.7614.</sub><br>
  <sub>[13] M. A. Syahputra and R. A. Putri, "Sistem Informasi Monitoring Pembelajaran dengan Fitur Notifikasi Email Berbasis Web," <em>Decode J. Pendidik. Teknol. Inf.</em>, vol. 5, no. 2, pp. 505-515, 2025, doi: 10.51454/decode.v5i2.1206.</sub><br>
  <sub>[14] M. W. Mauludy and Fathahillah, "Pengembangan Sistem Informasi Pengarsipan File Menggunakan Metode R&amp;D," <em>J. Informatics Manag. Inf. Technol.</em>, vol. 5, no. 2, pp. 118-125, 2025, doi: 10.47065/jimat.v5i2.484.</sub><br>
  <sub>[15] Sugiyono, <em>Metode Penelitian Kuantitatif, Kualitatif, dan R&amp;D</em>. Bandung: Alfabeta, 2022.</sub><br>
  <sub>[16] E. Rahmi, E. Yumami, and N. Hidayasari, "Analisis Metode Pengembangan Sistem Informasi Berbasis Website: Systematic Literature Review," <em>REMIK Ris. dan E-Jurnal Manaj. Inform. Komput.</em>, vol. 7, no. 1, pp. 821-834, 2023, doi: 10.33395/remik.v7i1.12177.</sub><br>
  <sub>[17] Y. Adisa and M. I. P. Nasution, "Konsep Dan Peran Sistem Manajemen Basis Data Relasional Pada Sistem Informasi Manajemen," <em>MASIP J. Manaj. Adm. Bisnis dan Publik Terapan</em>, vol. 1, no. 3, pp. 76-83, 2023, doi: 10.59061/masip.v1i3.314.</sub><br>
  <sub>[18] J. V. Dachi and J. Suhada, "Implementasi Basis Data Sederhana Menggunakan MySQL/PostgreSQL," <em>Router J. Tek. Inform. dan Terap.</em>, vol. 3, no. 1, pp. 49-59, 2025, doi: 10.62951/router.v3i1.621.</sub><br>
  <sub>[19] R. C. Buwono, R. Kartadie, and M. K. Ramadhan, "Perancangan dan Implementasi Sistem Payment Gateway MIDTRANS untuk UMKM Batik Lurik," <em>INFOTECH J. Inform. dan Teknol.</em>, vol. 5, no. 2, pp. 228-238, 2024, doi: 10.37373/infotech.v5i2.1334.</sub><br>
  <sub>[20] J. K. Aditya and A. T. Hidayat, "Sistem Informasi Manajemen Kos Berbasis Web Menggunakan Metode Blackbox: (Studi Kasus: Kos Putri Humba)," <em>JUSIM (Jurnal Sist. Inf. Musirawas)</em>, vol. 10, no. 2, pp. 229-241, 2025, doi: 10.32767/jusim.v10i2.2782.</sub>
</p>
