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
  <strong>Asri Boarding House in Semarang previously managed its 32 rooms through paper ledgers and personal chats. Slow room updates regularly caused double bookings. Reminders for rent due on the 10th often fell behind, and balancing the monthly cash book took three to four days. This study designs and builds a web-based management information system integrated with a payment gateway to unify reservations, recurring billing, and financial reporting. The project combined Research and Development (R&amp;D) methods with the sequential Waterfall model. Built on Laravel and a normalized (3NF) MySQL database, the application guards room availability with pessimistic row locking so simultaneous requests never collide. Midtrans Snap confirms cashless payments automatically using SHA-512 webhook verification. Server cron jobs generate monthly bills every 1st, add a 5% flat cross-month late fine, and deliver WhatsApp and email notices. Black-box testing across six modules passed all 60 scenarios with zero errors. In live trials, the caretaker rated the system 9.5/10 because month-end closing finished in seconds with no cash discrepancies. Five student tenants scored it 8.0/10 on average, valuing remote Virtual Account payments and instant A5 PDF receipts.</strong>
</p>

<p align="justify">
  <strong><em>Keywords:</em></strong> Asri Boarding House, boarding house management system, Laravel, Midtrans payment gateway, pessimistic row locking
</p>

---

<!-- TAHAP 4: BAB I. INTRODUCTION -->
### **I. INTRODUCTION**

<p align="justify">
  Every academic year brings out-of-town students to Semarang's Tembalang university district. Yet word-of-mouth promotion still forces applicants to visit in person or wait for chat replies to confirm vacant rooms [1]. Asri Boarding House on Jalan Maera Sari No. 1 operates 32 rooms under this conventional routine: 6 VIP, 3 Deluxe, and 23 Standard. The caretaker logs occupancy in a paper ledger while answering inquiries by chat. Status notes regularly fall behind. Two applicants can then claim the same unit at once, a double-booking flaw frequently reported in manually managed boarding houses [2].
</p>

<p align="justify">
  Manual records also slow monthly rent collection. Rent falls due on the 10th with a same-month grace period, while unpaid balances crossing into the next month carry a one-time 5% flat fine. Checking due dates and messaging tenants individually lets reminders slip and arrears grow, an issue noted in boarding house monitoring studies [3]. Tenants feel this delay too. They forward transfer screenshots over chat, wait for confirmation, and hold paper receipts that easily go missing. At month end, reconciling cash deposits, transfer proofs, and expense notes takes three to four days. Checking records by hand invites miscalculation, an audit burden that online payment gateways eliminate in rental systems [4].
</p>

<p align="justify">
  A centralized Management Information System (MIS) unifies room stock, lease contracts, and cash books in one database for faster decisions [5]. Through a Business-to-Consumer (B2C) web portal, the property owner markets available units directly to students without broker fees [6]. A structured design system styled in high-contrast Neo-Brutalism keeps room availability and booking steps legible across devices [7]. On the server side, Laravel's Model-View-Controller (MVC) architecture separates data, application logic, and page views cleanly [8]. A Third Normal Form (3NF) MySQL InnoDB schema removes duplicate entries and update anomalies [9]. Stored contract rates shield active leases from future price hikes. InnoDB pessimistic row locking also stops simultaneous booking requests from colliding.
</p>

<p align="justify">
  Most existing boarding house websites stop at a digital room catalog. Tenants still upload transfer screenshots and wait for manual checks. Integrating the Midtrans payment gateway removes that wait and settles cashless payments, including Virtual Accounts, automatically after SHA-512 webhook verification [10]. Scheduled server cron jobs then handle monthly billing without staff intervention [11]. The scheduler generates bills every 1st and runs idempotent overdue checks after the 10th so cross-month fines apply only once. Whenever a bill is issued or settled, the Fonnte WhatsApp API sends instant alerts [12]. In parallel, queued SMTP email delivers formal invoice notices and payment confirmations with receipt download links [13].
</p>

<p align="justify">
  To address these operational problems, this study pursues three objectives. First, it designs a unified web-based boarding house management architecture for Asri Boarding House covering reservations, monthly billing, and cash reporting. Second, it builds the platform in Laravel and MySQL with pessimistic row locking, Midtrans webhooks verified with SHA-512, cron-based billing automation, and WhatsApp and email alerts. Third, it evaluates functional reliability and user acceptance through black-box testing, live trials, and interviews with the caretaker and active student tenants.
</p>

<!-- TAHAP 5: BAB II. METHOD -->
### **II. METHOD**

<p align="justify">
  This study took place at Asri Boarding House in Tembalang, Semarang. It paired a software engineering Research and Development (R&amp;D) method with a single case study. The research workflow started from manual ledger bottlenecks across 32 rooms, then moved through design, coding, and testing toward reliable digital bookkeeping [14]. Data collection combined primary field evidence with supporting secondary records [15]. Primary data came from direct on-site observation and face-to-face interviews with the caretaker and five active student tenants. Each interview was recorded on audio and transcribed during early requirement mapping and final evaluation. Secondary sources covered physical cash books, transfer proofs, expense notes, room tariffs, official API manuals, and prior literature.
</p>

<p align="justify">
  System development followed the sequential Waterfall model in Figure 1 [16]. This structured approach fit the property because core boarding rules stayed stable from the start. Capacity across 32 rooms, 30% down-payment or full-payment options, 10th-of-the-month due dates, 5% flat cross-month fines, and post-checkout room quarantine were already clear during fieldwork. Work progressed across six phases: requirement gathering and analysis, design, implementation, integration and testing, verification, and operation and maintenance. Each stage passed review before advancing. As the return arrows in Figure 1 show, defects found during testing or user trials triggered targeted revisions in earlier stages.
</p>

<div align="center">
  <img src="images/metode_sdlc_waterfall.png" alt="Waterfall System Development Model" width="95%">
  <br>
  <sub><strong>Figure 1.</strong> WATERFALL SYSTEM DEVELOPMENT MODEL</sub>
</div>

<p align="justify">
  During design, Unified Modeling Language (UML) use case, activity, and flowchart diagrams mapped workflows for prospective tenants, active tenants, and the administrator. A 3NF MySQL InnoDB Entity Relationship Diagram (ERD) then separated six core lease and financial tables from eight supporting operational tables [17]. Interface layouts adopted a high-contrast Neo-Brutalism style for clear mobile and desktop use. Implementation used Laravel 12 MVC with a dedicated service layer and role middleware to guard private routes. Booking logic ran inside ACID transactions using <em>lockForUpdate()</em> row locking and overlap checks so simultaneous requests could not collide [18]. Cashless checkout paired Midtrans Snap tokens expiring within 24 hours with constant-time SHA-512 webhook checks via <em>hash_equals()</em> [19]. Protected by <em>withoutOverlapping()</em> guards, server cron jobs handled 1st-of-the-month billing, post-10th late-fee checks, and hourly expired-booking cleanup. The Fonnte WhatsApp API and queued SMTP emails then delivered automated notices.
</p>

<p align="justify">
  Integration and testing then combined all internal modules with external APIs. This stage executed 60 black-box test scenarios across six functional modules: authentication and profile, public catalog, five-step booking, tenant portal, admin operations, and background schedulers [20]. Scenarios checked both valid workflows and invalid-input guards, such as 16-digit identity rules and occupied-room blocks. Once functional tests passed, verification moved on site at Asri Boarding House. The caretaker and five active student tenants tested key workflows side by side on their own devices before completing semi-structured interviews scored from 1 to 10. Finally, operation and maintenance deployed the verified platform to a production server with active schedulers. Ongoing upkeep monitors database health, notification logs, and updates guided by user feedback.
</p>

<!-- TAHAP 6: BAB III. RESULTS AND DISCUSSION -->
### **III. RESULTS AND DISCUSSION**

#### **A. RESULTS**

<p align="justify">
  Daily access divides cleanly into three user journeys: prospective tenants, active residents, and the property administrator. Prospective and active tenants share one resident role in <em>users</em>, while a post-login redirect service routes each account using live contract status. Figure 2 presents the core Third Normal Form (3NF) MySQL InnoDB schema across six transactional tables: <em>users</em>, <em>kamar</em>, <em>penyewa</em>, <em>reservasi</em>, <em>tagihan</em>, and <em>pembayaran</em>. Inside <em>penyewa</em>, each contract saves a fixed snapshot of the agreed rent, move-in date, planned end date, and guardian phone number. Future tariff hikes never alter active leases. Eight supporting tables sit beside this core for complaints, expenses, notification logs, guest chat threads and messages, reviews, house rules, and FAQs. Production columns follow Indonesian naming, and <em>kamar</em> adds a <em>maintenance</em> state beside <em>available</em> and <em>occupied</em>.
</p>

<div align="center">
  <img src="images/gambar_4_2_a_erd_modul_inti_en.png" alt="Core Room, Tenancy, and Financial 3NF ERD" width="84%">
  <br>
  <sub><strong>Figure 2.</strong> CORE ROOM, TENANCY, AND FINANCIAL 3NF ENTITY RELATIONSHIP DIAGRAM</sub>
</div>

<p align="justify">
  Figure 3 traces booking from catalog search to key handover. After picking a vacant unit, the applicant submits a 16-digit National Identity Number (NIK), guardian contacts, and stay duration. While the conceptual diagram labels this step as ID upload, the deployed site stores only the 16-digit number online for privacy and checks physical ID cards at arrival. Each booking runs inside an ACID transaction with <em>lockForUpdate()</em> row locking and date-overlap checks, blocking simultaneous claims on the same room. Once Midtrans settles payment, an asynchronous webhook validates the SHA-512 signature via <em>hash_equals()</em>, verifies the paid amount and idempotency record, and locks the reservation. At check-in, the caretaker verifies the physical ID, hands over the key, and activates tenancy, sending WhatsApp and queued SMTP email alerts. After activation, <em>withoutOverlapping()</em> cron jobs handle recurring billing. They issue invoices on the 1st, track overdue grace periods after the 10th, add a one-time 5% cross-month fine, and cancel expired bookings hourly.
</p>

<div align="center">
  <img src="images/gambar_4_6_flowchart_sistem_en.png" alt="End-to-End Room Reservation, Midtrans Payment, and Tenant Activation Flowchart" width="95%">
  <br>
  <sub><strong>Figure 3.</strong> END-TO-END ROOM RESERVATION, MIDTRANS PAYMENT, AND TENANT ACTIVATION FLOWCHART</sub>
</div>

<p align="justify">
  The web interface spans 36 responsive Neo-Brutalism pages across public, applicant, resident, and admin workspaces. Visitors filter all 32 VIP, Deluxe, and Standard units by tier or room number to check live availability, size, facilities, and monthly rates without waiting for chat replies. Occupied cards replace the booking button with a direct WhatsApp inquiry shortcut. Figure 4 pairs the five-step reservation workspace with the Midtrans checkout modal. On the tracking page (Figure 4a), applicants follow booking, discussion, payment, activation, and completion steps while confirming room readiness with staff before choosing a 30% down payment or full settlement. Clicking pay opens Midtrans Snap (Figure 4b), issuing a 24-hour Bank BCA Virtual Account code for instant cashless settlement.
</p>

<div align="center">
  <img src="images/gambar_4_21_pembayaran_5_tahap_reservasi_en.png" alt="Five-Step Reservation Workspace" width="58%">
  <img src="images/gambar_4_22_payment_gateway_midtrans.png" alt="Midtrans Snap Virtual Account Modal" width="35%">
  <br>
  <sub><strong>Figure 4.</strong> ONLINE BOOKING WORKFLOW: (a) FIVE-STEP RESERVATION WORKSPACE; (b) MIDTRANS SNAP VIRTUAL ACCOUNT MODAL</sub>
</div>

<p align="justify">
  Daily property oversight runs through the administrator portal in Figure 5. The dashboard (Figure 5a) condenses current-month performance across 32 rooms into eight live cards covering occupancy, vacant units, revenue, expenses, net profit, unpaid bills, incoming bookings, and WhatsApp clicks. Below them sit a 12-month cash flow chart and a room status donut graph. An OLED dark mode toggle sits in the top bar for comfortable extended viewing. Beside it, the financial report view (Figure 5b) reconciles paid rent against expenses across single months or cumulative periods, exporting PDF or Excel-compatible ledgers in one click. From the resident portal, tenants track bills, file photo-attached repair tickets, and download browser-rendered A5 PDF receipts stamped <em>PAID</em> with the Midtrans transaction ID.
</p>

<div align="center">
  <img src="images/gambar_4_26_dashboard_admin_en.png" alt="Administrator Executive Dashboard" width="48%">
  <img src="images/gambar_4_33_manajemen_laporan_pemasukan_en.png" alt="Monthly Billing and Financial Report Module" width="48%">
  <br>
  <sub><strong>Figure 5.</strong> ADMINISTRATOR PORTAL: (a) LIVE EXECUTIVE DASHBOARD; (b) FINANCIAL REPORTING AND EXPORT MODULE</sub>
</div>

<p align="justify">
  Black-box testing ran 60 scenarios across all six modules, passing every case without validation or transaction errors. Positive tests verified room filters, multi-month discounts, Virtual Account callbacks, client-side A5 receipts, ledger exports, and automated WhatsApp and email notices. Negative checks proved equally strict, blocking 14-digit NIK inputs, non-image complaint files, deletion of occupied rooms, and duplicate monthly fine runs. On-site trials and recorded 10-point interviews confirmed those technical results. Comparing the platform with twenty years of paper ledgers, the senior caretaker scored it 9.5/10. Across Android phones, iPhones, and laptops, five active student tenants averaged 8.0/10, with individual ratings from 7.0 to 9.0.
</p>

#### **B. DISCUSSION**

<p align="justify">
  Field evidence shows that pairing automated payment callbacks with clear operational guardrails resolves daily delays at Asri Boarding House. Automatic ledger posting cuts month-end closing from three or four days of sorting paper notes and chat screenshots down to instant PDF and Excel exports. Cash discrepancies dropped to zero. Everyday desk routines grew lighter too: OLED dark mode eased eye strain, walk-in registration captured guardian contacts upfront, and one-click cash confirmation retired paper receipt books. Photo-attached repair tickets also replaced verbal hallway requests that easily went unrecorded. Two manual checkpoints remain just as valuable. Holding account activation for physical ID checks protects applicant privacy online, while post-checkout quarantine hides vacated rooms until cleaning and inspection finish. An intra-month grace period past the 10th before a single 5% rollover fine also matches student allowance timing without harsh daily compounding.
</p>

<p align="justify">
  Student interviews explain both the platform's practical appeal and why individual ratings spanned 7.0 to 9.0. Out-of-town applicants valued inspecting live room availability and clear rates remotely before traveling to Semarang. Paying via Virtual Accounts on Android, iPhone Safari, and laptop browsers ended late-night ATM trips and prevented disputes from forgotten manual ledger entries. Downloadable A5 PDF receipts gave tenants official proof to forward to parents or store in the cloud. Field trials also revealed minor usability friction. Wide payment tables needed sideways scrolling on small phone screens, five booking steps felt slightly long on desktop, and newcomers took extra minutes locating receipt and repair menus. Those findings shape the next release: H-3 WhatsApp reminders with direct payment links, stacked-card mobile tables, a short onboarding tour, caching for 1st-of-month peaks, digital leases, and a mobile app.
</p>

<p align="justify">
  Compared with typical boarding-house sites that rely on manual transfer screenshots, this architecture closes two reliability gaps. First, combining InnoDB <em>lockForUpdate()</em> row locks, date-overlap checks, and post-checkout quarantine blocks double bookings across all 32 units even under simultaneous requests. Second, constant-time <em>hash_equals()</em> SHA-512 validation, dynamic month-end Snap token expiry, and idempotent callbacks replace manual bank audits with self-reconciling ledgers. Linking those webhooks to monthly <em>withoutOverlapping()</em> schedulers, WhatsApp notices, and queued SMTP emails automates an entire multi-month stay rather than one retail checkout. Two scope limits remain. Field testing covered one 32-room property with six participants, and initial rollout enabled only the BCA Virtual Account channel to match the owner's bank. Because the schema and service layer stay decoupled, adding QRIS, e-wallets, or future branches needs minimal core rework.
</p>

<!-- TAHAP 7: BAB IV. CONCLUSION -->
### **IV. CONCLUSION**

<p align="justify">
  This study designed a single web architecture to handle bookings, monthly rent billing, and cash reports across all 32 units at Asri Boarding House. UML diagrams separate workspaces for prospective applicants, active residents, and the administrator across 36 responsive Neo-Brutalism screens. Beneath the interface, a 3NF MySQL InnoDB schema organizes core residency and payment records in six primary tables backed by eight operational tables. Fixed contract-rate snapshots protect active leases from future rent hikes. Offline safeguards remain intact too: staff check original ID cards upon arrival and hold vacated rooms until housekeeping clears them.
</p>

<p align="justify">
  The deployed platform runs on Laravel 12 MVC and MySQL InnoDB behind role-based route guards and a separate service layer. Every booking executes within an ACID database transaction where schedule-conflict checks and <em>lockForUpdate()</em> row locks eliminate double bookings. Midtrans Snap handles cashless checkout with 24-hour Virtual Account codes. Once a transfer completes, an asynchronous webhook confirms its SHA-512 signature in constant time using <em>hash_equals()</em>, blocks duplicate callbacks, and settles the invoice immediately. Guarded by <em>withoutOverlapping()</em>, server cron jobs clear expired bookings hourly and issue bills every 1st. Only arrears crossing into the next month incur a single 5% fine, while Fonnte WhatsApp and queued SMTP email deliver every notice automatically.
</p>

<p align="justify">
  Evaluation tested both software reliability and everyday usability. All 60 black-box cases across six modules passed, verifying standard workflows and invalid-input guards without error. In live trials, the caretaker scored the platform 9.5/10 as month-end closing dropped from three to four days to seconds with zero cash discrepancies and one-click PDF or Excel reports. Across five student residents, scores ranged from 7.0 to 9.0 for an 8.0/10 average, reflecting praise for remote room checks, Virtual Account payments without transfer screenshots, and stamped A5 PDF receipts. Field validation stayed bounded to one 32-room property, six participants, and an initial Bank BCA Virtual Account channel. Future releases can add QRIS and e-wallets, H-3 WhatsApp direct-pay links, mobile card layouts, first-login guidance, peak-day caching, digital lease agreements, and a mobile app.
</p>

<!-- TAHAP 8: ACKNOWLEDGMENT (TANPA NOMOR BAB) -->
### **ACKNOWLEDGMENT**

<p align="justify">
  The authors thank the Department of Information Systems and the Faculty of Computer Science at Soegijapranata Catholic University, Semarang, for institutional support and research facilities. They also appreciate the property owner of Asri Boarding House in Tembalang for granting fieldwork permission. On site, the senior caretaker opened daily cash books, clarified house rules, and tested the administrator portal, while five active student tenants evaluated the resident portal on their own devices. The authors are grateful for their time and honest usability feedback.
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
