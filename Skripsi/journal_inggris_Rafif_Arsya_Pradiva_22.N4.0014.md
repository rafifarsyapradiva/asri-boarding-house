<!--
  =============================================================================
  TAHAP 1: PENGATURAN HALAMAN DAN TATA LETAK UMUM (PAGE SETUP)
  - Ukuran Kertas        : A-4 (21.0 cm x 29.7 cm)
  - Margin               : 2.5 cm di setiap sisi (Atas, Bawah, Kiri, Kanan)
  - Format Kolom         : Dua Kolom (Two Columns), jarak antar kolom 0.5 cm
  - Huruf Utama (Font)   : Times New Roman, 12 pt, Regular
  - Spasi (Line Spacing) : 1.0 (Single Spacing)
  - Batas Maksimal       : Maksimal 7 Halaman
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
  <strong>Daily administration across the 32 rental rooms at Asri Boarding House has long depended on paper ledgers and manual text messages. This conventional workflow frequently triggers room booking collisions, delayed monthly rent collection, and cash discrepancies during multi-day bank transfer checks. This study aims to design and build an integrated web-based boarding house management information system that automates payment verification and billing reminders. System development follows the Waterfall model using the Laravel framework and a MySQL database. To prevent duplicate room allocations during simultaneous checkouts, the database layer applies pessimistic row-level locking, while financial transactions and billing alerts connect to the Midtrans Snap payment gateway, WhatsApp API, and email services. Functional testing across 60 black-box scenarios confirmed that all modules ran reliably without transaction or validation failures. Operational evaluation with the boarding house manager showed that monthly book closing dropped from several days to instant reporting with zero cash discrepancies. Interviews with resident students also confirmed high satisfaction. Ultimately, the system eliminates double bookings, simplifies virtual account rent payments, and delivers transparent digital receipts for both management and tenants.</strong>
</p>

<p align="justify">
  <strong><em>Keywords:</em></strong> Asri Boarding House, boarding house management system, Laravel, Midtrans payment gateway, pessimistic locking
</p>

---

<!-- TAHAP 4: BAB I. INTRODUCTION -->
### **I. INTRODUCTION**

<p align="justify">
  The university district of Tembalang in Semarang attracts thousands of out-of-town students every academic year, creating steady demand for accessible and transparent rental housing. Traditional promotional methods still make it hard for prospective tenants to check vacant units without visiting the property in person [1]. Asri Boarding House, located on Jalan Maera Sari No. 1 in Tembalang, faces this exact challenge across its 32 rental rooms spanning VIP, Deluxe, and Standard tiers. Because room records are still written manually in physical ledgers, the resident manager struggles to update occupancy status quickly. Consequently, double bookings frequently occur whenever multiple applicants reserve the same room within a short time window [2].
</p>

<p align="justify">
  Operational bottlenecks also disrupt monthly rent collection. Due date reminders for the 10th of each month and late fee calculations are sent individually through personal text messages. This manual routine causes billing schedules to slip and unpaid rent to pile up [3]. Financial bookkeeping adds further strain because payment checks rely on bank transfer screenshots and physical cash receipts. During month-end book closing, the manager spends several days matching operational expense notes against incoming payments, leaving the ledger vulnerable to cash discrepancies [4].
</p>

<p align="justify">
  Recent studies show that a Management Information System (MIS) centralizes room inventory, tenant records, and cash reports to support managerial oversight [5]. Applying a Business-to-Consumer (B2C) web model connects property owners directly with prospective tenants while avoiding third-party agent commissions [6]. For the user interface, a high-contrast Neo-Brutalism design system paired with a clear user experience helps visitors complete transactions without confusion [7]. On the backend, the Laravel framework with its Model-View-Controller (MVC) architecture cleanly separates business logic from presentation layers [8]. Pairing this architecture with a Third Normal Form (3NF) MySQL InnoDB database eliminates data redundancy and enforces ACID row-level locking during concurrent data writes [9].
</p>

<p align="justify">
  Although several prior works have digitized boarding house catalogs, most still rely on manual transfer slip verification and lack database concurrency locks paired with automated billing schedulers [10]. Integrating the Midtrans payment gateway via SHA-512 authenticated webhooks verifies Virtual Account and digital payments automatically, removing manual bank mutation checks [11]. Furthermore, configuring server cron jobs automates recurring monthly invoice issuance and idempotent late fee calculation without manual intervention [12]. Real-time billing alerts and official payment receipts can then be dispatched automatically through the Fonnte WhatsApp API [13] and TLS-encrypted SMTP email relays [14]. The Waterfall method organizes these components through sequential stages from requirement analysis and design to coding, testing, and maintenance [15].
</p>

<p align="justify">
  Addressing these operational and technical gaps, this study pursues three main objectives: (1) to design the architecture of a boarding house management information system integrated with a payment gateway at Asri Boarding House to resolve room booking collisions, manual bookkeeping delays, and late rent collections; (2) to implement the web application using the Laravel framework integrated with the Midtrans payment gateway, scheduled billing automation, and WhatsApp and email notifications; and (3) to evaluate system effectiveness through functional testing and interviews with the boarding house manager and resident tenants.
</p>

<!-- TAHAP 5: BAB II. METHOD -->
### **II. METHOD**

<p align="justify">
  This study was conducted at Asri Boarding House on Jalan Maera Sari No. 1, Tembalang, Semarang, which manages 32 rental rooms across VIP, Deluxe, and Standard tiers. The investigation applies a software engineering Research and Development (R&amp;D) approach combined with a single case study. This research design connects four sequential components within the conceptual framework: (1) Input, covering existing manual ledger bottlenecks and operational requirements; (2) Process, spanning system design, coding, and functional testing; (3) Output, delivering a verified web-based boarding house management application; and (4) Outcome, establishing accurate cash bookkeeping and practical rent settlement for management and tenants [16].
</p>

<p align="justify">
  Primary data were collected through face-to-face in-depth interviews recorded and transcribed with two core informant groups: the resident manager regarding daily bookkeeping and room monitoring, and active student tenants regarding room booking and monthly payment experiences. Secondary data were gathered from internal paper ledgers, transfer slips, operational expense notes, the tariff list of all 32 rooms, technical documentation for the Midtrans API, Fonnte WhatsApp API, and Laravel framework, as well as related scientific literature [17].
</p>

<p align="justify">
  Application development applied the five stages of the Waterfall method, as summarized in Table 1. First, requirement gathering and analysis mapped operational rules from field interviews. Second, system design modeled access boundaries across three actors (prospective tenant, administrator, and active tenant) using a Use Case Diagram, Activity Diagrams, and a System Flowchart, alongside a Third Normal Form (3NF) Entity Relationship Diagram (ERD) divided into core transactional and supporting operational modules [18]. Third, implementation built the application on the Laravel MVC framework and a MySQL InnoDB database. Concurrent booking collisions were prevented using ACID database transactions and pessimistic row-level locking (<em>lockForUpdate()</em>) [19]. Cashless payments were integrated with Midtrans Snap tokens (24-hour expiry) and verified via SHA-512 webhook callbacks (<em>hash_equals()</em>), while a server cron scheduler was configured to generate recurring bills on the 1st of each month (<em>tagihan:generate-bulanan</em>), evaluate late fees daily (<em>tagihan:proses-keterlambatan</em>), and dispatch automated alerts via the Fonnte WhatsApp API and queued SMTP/TLS email relays [20].
</p>

<div align="center">
  <sub><strong>Table 1.</strong> WATERFALL RESEARCH STAGES AND TESTING DESIGN</sub>
</div>

| Stage | Procedure and Testing Scope |
| :--- | :--- |
| 1. Analysis | Field interviews with manager and tenants; audit of 32-room ledger and cash slips [17]. |
| 2. Design | UML modeling (3 actors) and 3NF relational MySQL schema across core and support modules [18]. |
| 3. Coding | Laravel MVC, ACID *lockForUpdate()*, Midtrans SHA-512 webhook, cron scheduler, Fonnte, and SMTP [19]. |
| 4. Testing | Black-box testing across 6 domains (60 scenarios) and user evaluation with 6 informants [10]. |
| 5. Rollout | Production hosting deployment, daily operational monitoring, and periodic maintenance. |

<p align="justify">
  Fourth, data and system testing combined functional verification with empirical user evaluation. Functional correctness was tested using black-box testing across 60 test scenarios grouped into six operational domains: (1) authentication, access rights, and user profiles (12 scenarios); (2) public portal, room catalog, and guest chat (10 scenarios); (3) prospective tenant five-step booking flow (10 scenarios); (4) active tenant portal, billing, and maintenance complaints (9 scenarios); (5) administrator operational and financial management (13 scenarios); and (6) task scheduler automation, late fees, and external APIs (6 scenarios) [10]. Finally, after deploying the platform to production hosting for operation and maintenance, operational effectiveness was evaluated through side-by-side usability trials and semi-structured interviews with the boarding house manager (Mr. Asep) and five active student tenants across a 10-point assessment scale [17].
</p>

<!-- TAHAP 6: BAB III. RESULTS AND DISCUSSION -->
### **III. RESULTS AND DISCUSSION**

#### **A. RESULT**

<p align="justify">
  Architectural modeling established clear operational boundaries across prospective tenants, the boarding house administrator, and active tenants. The 3NF relational database schema separates core residency and financial tables (<em>users</em>, <em>kamar</em>, <em>penyewa</em>, <em>reservasi</em>, <em>tagihan</em>, and <em>pembayaran</em>) from supporting operational tables (<em>keluhan</em>, <em>pengeluaran</em>, <em>log_notifikasi</em>, <em>guest_chat_threads</em>, <em>chat_messages</em>, <em>customer_reviews</em>, <em>peraturan</em>, and <em>faqs</em>) [18]. Through a one-to-many relationship, the <em>penyewa</em> table locks a snapshot of the agreed room rate, entry date, lease expiration, and emergency guardian contact so historical contracts remain intact even when master room tariffs change.
</p>

<p align="justify">
  In the end-to-end reservation workflow, when an applicant selects a vacant unit from the 32 available rooms, completes identity verification, and initiates payment through Midtrans, the backend executes an ACID transaction using <em>lockForUpdate()</em> and schedule overlap checks to block duplicate reservations [19]. System implementation produced 36 responsive Neo-Brutalism web interfaces [7] alongside nine core backend modules covering multi-role middleware, post-login routing, ACID room locking, schedule conflict detection, 24-hour Midtrans Snap token generation, SHA-512 webhook verification via <em>hash_equals()</em> [20], cron task scheduling with <em>withoutOverlapping()</em> [12], Fonnte WhatsApp dispatches [13], and queued SMTP/TLS email delivery via <em>ShouldQueue</em> [14]. For daily property oversight, the administrator dashboard employs an OLED dark theme that automatically aggregates room occupancy, incoming rent, operational expenses, and net cash balance.
</p>

<p align="justify">
  Functional verification across all 60 black-box test scenarios in the six operational domains achieved a 100% pass rate without validation or transaction errors [10]. Every form filter, 16-digit National Identity (NIK) check, post-checkout room quarantine hold, PDF receipt download, and PDF/Excel financial export performed as specified. Subsequent User Acceptance Testing (UAT) conducted through side-by-side usability trials and audio-recorded face-to-face interviews with the senior boarding house manager and five active student tenants yielded high satisfaction scores, as summarized in Table 2 [17].
</p>

<div align="center">
  <sub><strong>Table 2.</strong> USER ACCEPTANCE EVALUATION SCORES AND KEY FINDINGS</sub>
</div>

| Informant (Role) | Score | Primary Operational Finding |
| :--- | :---: | :--- |
| Mr. Asep (Manager) | 9.5 / 10 | Book closing cut from 3 to 4 days to seconds; zero cash discrepancy; post-checkout lock prevents premature booking. |
| Arjun (UNDIP Tenant) | 8.5 / 10 | Clear Neo-Brutalism catalog; fast BCA Virtual Account settlement; A5 PDF receipt practical for parents. |
| Farrel (USM Tenant) | 7.0 / 10 | Easy remote Android booking; avoids night ATM queues; suggested a dedicated mobile app and digital contract. |
| Iman (UDINUS Tenant) | 9.0 / 10 | Instant payment verification prevents lost-receipt disputes; neat monthly history; suggested peak-date server tuning. |
| Ammar (PKN STAN Tenant) | 7.5 / 10 | Smooth iPhone Safari checkout; easy holiday rent payment from out of town; suggested direct WhatsApp payment links. |
| Sindhu (UI Tenant) | 8.0 / 10 | Transparent pricing; removes awkward face-to-face cash handoffs; suggested an initial onboarding tour. |

#### **B. DISCUSSION**

<p align="justify">
  Qualitative findings from the audio-recorded interviews confirm that integrating automated payment verification with database concurrency control directly resolves manual boarding house bottlenecks [5]. From the management perspective, the senior resident manager (Mr. Asep) awarded a 9.5/10 rating and highlighted that automated ledger reconciliation reduced month-end book closing from three or four days of matching paper receipts to instant PDF and Excel exports with zero cash discrepancy. Within the billing engine, the daily scheduler enforces an intra-month grace period past the 10th due date and automatically applies an idempotent 5% rollover fine only when an unpaid invoice crosses into the next calendar month [12]. Furthermore, the post-checkout room quarantine lock prevents newly vacated units from reappearing in the public catalog before physical cleaning and inspection are completed.
</p>

<p align="justify">
  From the resident perspective, audio-recorded interviews with the five student tenants produced an average satisfaction score of 8.0/10. Tenants emphasized that the B2C self-service catalog [6] and high-contrast Neo-Brutalism interface [7] allowed them to inspect facilities, book rooms remotely from their hometowns, and pay monthly rent via Virtual Account on Android and iPhone browsers without queuing at ATMs at night. In addition, downloadable A5 landscape PDF receipts gave students official proof of payment to forward to their parents, eliminating disputes over misplaced paper receipts. Constructive feedback captured during the interviews, including H-3 WhatsApp reminders with direct payment links, a visual onboarding guide, a dedicated mobile app, and digital lease contracts, provides clear input for future upgrades.
</p>

<p align="justify">
  Compared with earlier boarding house systems by Cornellya and Afriyadi [1] and Nizar [2], which relied on manual bank slip uploads and lacked row-level locking, our implementation pairs ACID transactions with <em>lockForUpdate()</em> and a post-checkout quarantine hold [19]. This combination guarantees zero double bookings across all 32 rooms even when multiple applicants submit reservations simultaneously. Moreover, whereas the monitoring platform by Malaikosa and Mokola [3] still required manual payment checks, integrating Midtrans Snap with SHA-512 signature validation via <em>hash_equals()</em> updates ledger records automatically upon payment settlement [11]. Finally, unlike single-checkout retail payment integrations [4], coupling payment webhooks with scheduled cron jobs [12], the Fonnte WhatsApp API [13], and queued SMTP email relays automates the entire multi-month tenancy lifecycle [14].
</p>

<!-- TAHAP 7: BAB IV. CONCLUSION -->
### **IV. CONCLUSION**

<p align="justify">
  The system architecture for Asri Boarding House uses use case diagrams, Entity Relationship Diagrams (ERD), activity diagrams, and flowcharts. A 3NF MySQL database and three distinct user roles unify the management of 32 rental rooms and cash records in one platform. This structure prevents double bookings while replacing manual paper ledgers.
</p>

<p align="justify">
  The web application combines the Laravel framework, database row locking (<em>lockForUpdate</em>), and Midtrans Snap payment integration. Automated server cron jobs issue monthly invoices, calculate late fees, and dispatch reminders via the Fonnte WhatsApp API and SMTP email. All features operated reliably across 60 black-box test scenarios.
</p>

<p align="justify">
  Interview evaluation with the boarding house manager recorded a 9.5/10 satisfaction score as monthly book closing became instant without cash discrepancies. Five resident students gave an average score of 8.0/10 for practical remote booking, Virtual Account payment, and PDF receipts. The system effectively streamlines daily operations at Asri Boarding House.
</p>

<p align="justify">
  Based on the research findings and field evaluation, future system development can implement five targeted improvements:
</p>

1. Hardware integration can connect IoT smart door locks and digital electricity meters in each room directly to active lease records.
2. Platform architecture may expand into a multi-branch system to manage room inventory and finances across new boarding house locations.
3. WhatsApp notifications should include an H-3 due date reminder with a direct payment link to the invoice page.
4. Subsequent releases could provide Android and iOS mobile apps equipped with a visual onboarding tour for new tenants.
5. Portal services can incorporate digital lease contracts (<em>e-contract</em>) and an automated chatbot to assist prospective tenants 24 hours a day.

<!-- TAHAP 8: ACKNOWLEDGMENT (TANPA NOMOR BAB) -->
### **ACKNOWLEDGMENT**

<p align="justify">
  The authors thank the Department of Information Systems, Faculty of Computer Science, Soegijapranata Catholic University, Semarang, for providing academic guidance and research facilities throughout this study. Special gratitude is also extended to the owner, the resident manager Mr. Asep, and the student tenants of Asri Boarding House in Tembalang for their open cooperation during field interviews and system evaluation.
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
  <sub>[10] J. K. Aditya and A. T. Hidayat, "Sistem Informasi Manajemen Kos Berbasis Web Menggunakan Metode Blackbox: (Studi Kasus: Kos Putri Humba)," <em>JUSIM (Jurnal Sist. Inf. Musirawas)</em>, vol. 10, no. 2, pp. 229-241, 2025, doi: 10.32767/jusim.v10i2.2782.</sub><br>
  <sub>[11] M. Nurhayati and R. Setiawan, "Implementasi Payment Gateway Midtrans Pada Aplikasi Toko Mas Berbasis Web," <em>J. Algoritma</em>, vol. 21, no. 2, pp. 366-377, 2024, doi: 10.33364/algoritma/v.21-2.1686.</sub><br>
  <sub>[12] A. Wibowo, A. Fathimah, D. Waskito, and R. A. Aprilianto, "Cron Job Implementation for Automated Data Processing and Transfer in Cloud Infrastructure," <em>Sainteknol J. Sains dan Teknol.</em>, vol. 23, no. 1, pp. 11-20, 2025, doi: 10.15294/sainteknol.v23i1.22649.</sub><br>
  <sub>[13] I. M. B. Waton, A. N. Weking, and B. Deta, "Penerapan Api Whatsapp Fonnte Untuk Sistem Pengingat Jadwal Bimbingan Tugas Akhir Mahasiswa Berbasis Web," <em>J. Tek. Mesin, Elektro dan Ilmu Komput.</em>, vol. 5, no. 2, pp. 229-241, 2025, doi: 10.55606/teknik.v5i2.7614.</sub><br>
  <sub>[14] M. A. Syahputra and R. A. Putri, "Sistem Informasi Monitoring Pembelajaran dengan Fitur Notifikasi Email Berbasis Web," <em>Decode J. Pendidik. Teknol. Inf.</em>, vol. 5, no. 2, pp. 505-515, 2025, doi: 10.51454/decode.v5i2.1206.</sub><br>
  <sub>[15] E. Rahmi, E. Yumami, and N. Hidayasari, "Analisis Metode Pengembangan Sistem Informasi Berbasis Website: Systematic Literature Review," <em>REMIK Ris. dan E-Jurnal Manaj. Inform. Komput.</em>, vol. 7, no. 1, pp. 821-834, 2023, doi: 10.33395/remik.v7i1.12177.</sub><br>
  <sub>[16] M. W. Mauludy and Fathahillah, "Pengembangan Sistem Informasi Pengarsipan File Menggunakan Metode R&amp;D," <em>J. Informatics Manag. Inf. Technol.</em>, vol. 5, no. 2, pp. 118-125, 2025, doi: 10.47065/jimat.v5i2.484.</sub><br>
  <sub>[17] Sugiyono, <em>Metode Penelitian Kuantitatif, Kualitatif, dan R&amp;D</em>. Bandung: Alfabeta, 2022.</sub><br>
  <sub>[18] Y. Adisa and M. I. P. Nasution, "Konsep Dan Peran Sistem Manajemen Basis Data Relasional Pada Sistem Informasi Manajemen," <em>MASIP J. Manaj. Adm. Bisnis dan Publik Terapan</em>, vol. 1, no. 3, pp. 76-83, 2023, doi: 10.59061/masip.v1i3.314.</sub><br>
  <sub>[19] J. V. Dachi and J. Suhada, "Implementasi Basis Data Sederhana Menggunakan MySQL/PostgreSQL," <em>Router J. Tek. Inform. dan Terap.</em>, vol. 3, no. 1, pp. 49-59, 2025, doi: 10.62951/router.v3i1.621.</sub><br>
  <sub>[20] R. C. Buwono, R. Kartadie, and M. K. Ramadhan, "Perancangan dan Implementasi Sistem Payment Gateway MIDTRANS untuk UMKM Batik Lurik," <em>INFOTECH J. Inform. dan Teknol.</em>, vol. 5, no. 2, pp. 228-238, 2024, doi: 10.37373/infotech.v5i2.1334.</sub>
</p>
