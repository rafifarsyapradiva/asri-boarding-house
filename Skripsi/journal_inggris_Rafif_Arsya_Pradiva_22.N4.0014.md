<style>
  /* TAHAP 1: PENGATURAN HALAMAN DAN TATA LETAK UMUM (PAGE SETUP) */
  /* Batas Halaman Keseluruhan Artikel: Maksimal 7 Halaman */
  @page {
    size: A4 portrait; /* Ukuran Kertas: A-4 (21.0cm x 29.7cm) */
    margin: 2.5cm;     /* Margin: Batas tepi halaman 2,5 cm di setiap sisi */
  }

  body, .journal-page-setup {
    font-family: "Times New Roman", Times, serif; /* Huruf Utama: Times New Roman */
    font-size: 12pt;                              /* Ukuran Huruf Utama: 12 pt */
    line-height: 1.0;                             /* Spasi Utama: 1 (single spacing) */
    color: #000000;
    text-align: justify;
  }

  /* TAHAP 2: BAGIAN KEPALA ARTIKEL (JUDUL, PENULIS, AFILIASI, DAN EMAIL) */
  .journal-header {
    text-align: center;
    margin-bottom: 12pt;
  }

  .journal-title {
    font-family: "Times New Roman", Times, serif;
    font-size: 16pt;
    font-weight: bold;
    line-height: 1.15;
    margin-top: 0;
    margin-bottom: 10pt;
  }

  .journal-authors {
    font-family: "Times New Roman", Times, serif;
    font-size: 12pt;
    font-weight: normal;
    line-height: 1.15;
    margin-bottom: 6pt;
  }

  .journal-affiliation {
    font-family: "Times New Roman", Times, serif;
    font-size: 12pt;
    font-weight: normal;
    line-height: 1.15;
    margin-bottom: 6pt;
  }

  .journal-emails {
    font-family: "Times New Roman", Times, serif;
    font-size: 12pt;
    font-weight: normal;
    line-height: 1.15;
  }

  /* TAHAP 3: ABSTRAK DAN KATA KUNCI (ABSTRACT & KEYWORDS) */
  .journal-abstract-section {
    font-family: "Times New Roman", Times, serif;
    font-size: 12pt;
    line-height: 1.0;
    text-align: justify;
    margin-top: 12pt;
    margin-bottom: 12pt;
  }

  .abstract-title {
    font-family: "Times New Roman", Times, serif;
    font-size: 12pt;
    font-weight: bold;
    font-style: italic;
    margin-bottom: 4pt;
  }

  .abstract-body {
    font-family: "Times New Roman", Times, serif;
    font-size: 12pt;
    font-weight: bold;
    line-height: 1.0;
    text-align: justify;
    margin-top: 0;
    margin-bottom: 6pt;
  }

  .journal-keywords {
    font-family: "Times New Roman", Times, serif;
    font-size: 12pt;
    font-weight: normal;
    line-height: 1.0;
    text-align: justify;
    margin-top: 0;
  }

  /* Format Kolom Isi Artikel: Dua Kolom (Two Columns) dengan jarak antar kolom 0,5 cm */
  .journal-two-columns {
    column-count: 2;
    column-gap: 0.5cm;
    column-fill: balance;
    text-align: justify;
    width: 100%;
  }

  /* TAHAP 4: BAB I. INTRODUCTION & FORMAT TEKS ISI ARTIKEL */
  .section-heading {
    font-family: "Times New Roman", Times, serif;
    font-size: 12pt;
    font-weight: bold;
    line-height: 1.0;
    margin-top: 10pt;
    margin-bottom: 6pt;
    text-align: left;
    break-after: avoid;
    page-break-after: avoid;
  }

  .subsection-heading {
    font-family: "Times New Roman", Times, serif;
    font-size: 12pt;
    font-weight: bold;
    line-height: 1.0;
    margin-top: 8pt;
    margin-bottom: 4pt;
    text-align: left;
    break-after: avoid;
    page-break-after: avoid;
  }

  .journal-paragraph {
    font-family: "Times New Roman", Times, serif;
    font-size: 12pt;
    font-weight: normal;
    line-height: 1.0;
    text-align: justify;
    text-indent: 0.75cm;
    margin-top: 0;
    margin-bottom: 6pt;
  }

  /* TAHAP 5: BAB II. METHOD, FORMAT TABEL, & SPESIFIKASI UKURAN HURUF */
  .table-container {
    margin-top: 8pt;
    margin-bottom: 8pt;
    break-inside: avoid;
    page-break-inside: avoid;
    width: 100%;
  }

  .table-caption {
    font-family: "Times New Roman", Times, serif;
    font-size: 10pt;
    line-height: 1.0;
    text-align: center;
    margin-bottom: 4pt;
  }

  /* Penulisan Tabel: Times New Roman 10 pt Bold */
  .table-label {
    font-family: "Times New Roman", Times, serif;
    font-size: 10pt;
    font-weight: bold;
  }

  /* Keterangan Tabel (in Caps): Times New Roman 10 pt Regular */
  .table-caption-text {
    font-family: "Times New Roman", Times, serif;
    font-size: 10pt;
    font-weight: normal;
    text-transform: uppercase;
  }

  /* Isi Sel di Dalam Tabel: Times New Roman 12 pt Regular */
  .journal-table {
    width: 100%;
    border-collapse: collapse;
    font-family: "Times New Roman", Times, serif;
    font-size: 12pt;
    font-weight: normal;
    line-height: 1.0;
  }

  .journal-table th,
  .journal-table td {
    font-family: "Times New Roman", Times, serif;
    font-size: 12pt;
    font-weight: normal;
    line-height: 1.0;
    border-top: 1px solid #000000;
    border-bottom: 1px solid #000000;
    padding: 3pt 4pt;
    text-align: left;
    vertical-align: top;
  }

  /* TAHAP 6: FORMAT GAMBAR (CENTER, 10 PT REGULAR) & PERSAMAAN MATEMATIKA IEEE */
  .figure-container {
    text-align: center;
    margin-top: 8pt;
    margin-bottom: 8pt;
    break-inside: avoid;
    page-break-inside: avoid;
    width: 100%;
  }

  .figure-image {
    display: block;
    margin-left: auto;
    margin-right: auto;
    max-width: 95%;
    max-height: 155px;
    height: auto;
  }

  /* Keterangan Gambar: Times New Roman 10 pt Regular */
  .figure-caption {
    font-family: "Times New Roman", Times, serif;
    font-size: 10pt;
    font-weight: normal;
    line-height: 1.0;
    text-align: center;
    margin-top: 4pt;
    margin-bottom: 4pt;
  }

  .equation-container {
    font-family: "Times New Roman", Times, serif;
    font-size: 12pt;
    line-height: 1.0;
    text-align: center;
    margin-top: 6pt;
    margin-bottom: 6pt;
    break-inside: avoid;
    page-break-inside: avoid;
  }

  /* TAHAP 9: REFERENCES (10 PT REGULAR & 10 PT ITALIC IEEE) */
  .reference-item {
    font-family: "Times New Roman", Times, serif;
    font-size: 10pt;
    font-weight: normal;
    line-height: 1.0;
    text-align: justify;
    margin-bottom: 4pt;
  }

  .reference-item em {
    font-family: "Times New Roman", Times, serif;
    font-size: 10pt;
    font-style: italic;
  }

  /* Elemen penuh satu halaman (Header/Judul/Abstrak) */
  .journal-full-width {
    column-span: all;
    width: 100%;
  }

  /* Pencegahan pemotongan elemen tabel/gambar di tengah kolom */
  figure, table, .keep-together {
    break-inside: avoid;
    page-break-inside: avoid;
  }
</style>

<div class="journal-page-setup">

  <!-- TAHAP 2: BAGIAN KEPALA ARTIKEL (JUDUL, PENULIS, AFILIASI, DAN EMAIL) -->
  <div class="journal-full-width">
    <div class="journal-header">
      <div class="journal-title">
        BOARDING HOUSE MANAGEMENT INFORMATION SYSTEM INTEGRATED WITH PAYMENT GATEWAY AT ASRI BOARDING HOUSE
      </div>
      <div class="journal-authors">
        <sup>1</sup>Rafif Arsya Pradiva, <sup>2</sup>Andre Kurniawan Pamudji, <sup>3</sup>Erdhi Widyarto Nugroho
      </div>
      <div class="journal-affiliation">
        Department of Information Systems, Faculty of Computer Science<br>
        Soegijapranata Catholic University, Semarang, Indonesia
      </div>
      <div class="journal-emails">
        <sup>1</sup>22n40014@student.unika.ac.id, <sup>2</sup>andre.kurniawan@unika.ac.id, <sup>3</sup>erdhi@unika.ac.id
      </div>
    </div>

    <!-- TAHAP 3: ABSTRAK DAN KATA KUNCI (ABSTRACT & KEYWORDS) -->
    <div class="journal-abstract-section">
      <div class="abstract-title">
        Abstract
      </div>
      <div class="abstract-body">
        Daily administration across the 32 rental rooms at Asri Boarding House has long depended on paper ledgers and manual text messages. This conventional workflow frequently triggers room booking collisions, delayed monthly rent collection, and cash discrepancies during multi-day bank transfer checks. This study aims to design and build an integrated web-based boarding house management information system that automates payment verification and billing reminders. System development follows the Waterfall model using the Laravel framework and a MySQL database. To prevent duplicate room allocations during simultaneous checkouts, the database layer applies pessimistic row-level locking, while financial transactions and billing alerts connect to the Midtrans Snap payment gateway, WhatsApp API, and email services. Functional testing across 60 black-box scenarios confirmed that all modules ran reliably without transaction or validation failures. Operational evaluation with the boarding house manager showed that monthly book closing dropped from several days to instant reporting with zero cash discrepancies. Interviews with resident students also confirmed high satisfaction. Ultimately, the system eliminates double bookings, simplifies virtual account rent payments, and delivers transparent digital receipts for both management and tenants.
      </div>
      <div class="journal-keywords">
        <strong><em>Keywords:</em></strong> Asri Boarding House, boarding house management system, Laravel, Midtrans payment gateway, pessimistic locking
      </div>
    </div>
  </div>

  <!-- Area Isi Artikel Format Dua Kolom (Jarak Antar Kolom 0,5 cm) -->
  <div class="journal-two-columns">

    <!-- TAHAP 4: BAB I. INTRODUCTION -->
    <div class="section-heading">I. INTRODUCTION</div>

    <p class="journal-paragraph">
      The university district of Tembalang in Semarang attracts thousands of out-of-town students every academic year, creating steady demand for accessible and transparent rental housing. Traditional promotional methods still make it hard for prospective tenants to check vacant units without visiting the property in person [1]. Asri Boarding House, located on Jalan Maera Sari No. 1 in Tembalang, faces this exact challenge across its 32 rental rooms spanning VIP, Deluxe, and Standard tiers. Because room records are still written manually in physical ledgers, the resident manager struggles to update occupancy status quickly. Consequently, double bookings frequently occur whenever multiple applicants reserve the same room within a short time window [2].
    </p>

    <p class="journal-paragraph">
      Operational bottlenecks also disrupt monthly rent collection. Due date reminders for the 10th of each month and late fee calculations are sent individually through personal text messages. This manual routine causes billing schedules to slip and unpaid rent to pile up [3]. Financial bookkeeping adds further strain because payment checks rely on bank transfer screenshots and physical cash receipts. During month-end book closing, the manager spends several days matching operational expense notes against incoming payments, leaving the ledger vulnerable to cash discrepancies [4], [5].
    </p>

    <p class="journal-paragraph">
      Recent studies show that a Management Information System (MIS) centralizes room inventory, tenant records, and cash reports to support managerial oversight [6], [7]. Applying a Business-to-Consumer (B2C) web model connects property owners directly with prospective tenants while avoiding third-party agent commissions [8], [9], [10], [11]. For the user interface, a high-contrast Neo-Brutalism layout paired with a streamlined user experience helps visitors complete transactions without confusion [12], [13]. On the backend, the Laravel framework with its Model-View-Controller (MVC) architecture and a Third Normal Form (3NF) MySQL InnoDB database enforces ACID compliance and row-level locking during concurrent data writes [14], [15], [16], [17], [18], [19].
    </p>

    <p class="journal-paragraph">
      Although several prior works have developed web-based boarding house systems [1], [2], [3], most still rely on manual transfer slip verification and lack database concurrency locks paired with automated billing schedulers. Integrating the Midtrans payment gateway via SHA-512 authenticated webhooks verifies Virtual Account and digital payments automatically, removing manual bank mutation checks [4], [20], [21]. In addition, combining server cron jobs with WhatsApp APIs and TLS-encrypted SMTP email relays automates recurring invoice issuance, idempotent late fee calculation, and official receipt delivery [22], [23], [24], [25], [26], [27].
    </p>

    <p class="journal-paragraph">
      Addressing these operational and technical gaps, this study pursues three main objectives: (1) to design the architecture of a boarding house management information system integrated with a payment gateway at Asri Boarding House to resolve room booking collisions, manual bookkeeping delays, and late rent collections; (2) to implement the web application using the Laravel framework integrated with the Midtrans payment gateway, scheduled billing automation, and WhatsApp and email notifications; and (3) to evaluate system effectiveness through functional testing and interviews with the boarding house manager and resident tenants.
    </p>

    <!-- TAHAP 5: BAB II. METHOD -->
    <div class="section-heading">II. METHOD</div>

    <p class="journal-paragraph">
      This study was conducted at Asri Boarding House on Jalan Maera Sari No. 1, Tembalang, Semarang, which manages 32 rental rooms across VIP, Deluxe, and Standard tiers. The investigation applies a software engineering Research and Development (R&amp;D) approach combined with a single case study [28]. This research design connects four sequential components within the conceptual framework: (1) Input, covering existing manual ledger bottlenecks and operational requirements; (2) Process, spanning system design, coding, and functional testing; (3) Output, delivering a verified web-based boarding house management application; and (4) Outcome, establishing accurate cash bookkeeping and practical rent settlement for management and tenants.
    </p>

    <p class="journal-paragraph">
      Primary data were collected through face-to-face in-depth interviews recorded and transcribed with two core informant groups: the resident manager regarding daily bookkeeping and room monitoring, and active student tenants regarding room booking and monthly payment experiences [29]. Secondary data were gathered from internal paper ledgers, transfer slips, operational expense notes, the tariff list of all 32 rooms, technical documentation for the Midtrans API, Fonnte WhatsApp API, and Laravel framework, as well as related scientific literature [1], [4], [20], [22].
    </p>

    <p class="journal-paragraph">
      Software construction followed the five sequential stages of the Waterfall Software Development Life Cycle (SDLC) [30], as summarized in Table 1. First, requirement gathering and analysis mapped operational rules from field interviews [29]. Second, system design modeled access boundaries across three actors (prospective tenant, administrator, and active tenant) using a Use Case Diagram, Activity Diagrams, and a System Flowchart, alongside a Third Normal Form (3NF) Entity Relationship Diagram (ERD) divided into core transactional and supporting operational modules [16], [17]. Third, implementation built the application on the Laravel MVC framework and a MySQL InnoDB database [14], [15], [18], [19]. Concurrent booking collisions were prevented using ACID database transactions and pessimistic row-level locking (<em>lockForUpdate()</em>). Cashless payments were integrated with Midtrans Snap tokens (24-hour expiry) and verified via SHA-512 webhook callbacks (<em>hash_equals()</em>) [20], [21]. Furthermore, a server cron scheduler was configured to generate recurring bills on the 1st of each month (<em>tagihan:generate-bulanan</em>), evaluate late fees daily (<em>tagihan:proses-keterlambatan</em>), and dispatch automated alerts via the Fonnte WhatsApp API and queued SMTP/TLS email relays [22], [23], [24], [25], [26], [27].
    </p>

    <div class="table-container">
      <div class="table-caption">
        <span class="table-label">Table 1.</span> <span class="table-caption-text">WATERFALL RESEARCH STAGES AND TESTING DESIGN</span>
      </div>
      <table class="journal-table">
        <thead>
          <tr>
            <th>Stage</th>
            <th>Procedure and Testing Scope</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>1. Analysis</td>
            <td>Field interviews with manager and tenants; audit of 32-room ledger and cash slips [29].</td>
          </tr>
          <tr>
            <td>2. Design</td>
            <td>UML modeling (3 actors) and 3NF relational MySQL schema across core and support modules [16], [17].</td>
          </tr>
          <tr>
            <td>3. Coding</td>
            <td>Laravel MVC, ACID <em>lockForUpdate()</em>, Midtrans SHA-512 webhook, cron scheduler, Fonnte, and SMTP [14], [21], [26].</td>
          </tr>
          <tr>
            <td>4. Testing</td>
            <td>Black-box testing across 6 domains (60 scenarios) and user evaluation with 6 informants [7], [29].</td>
          </tr>
          <tr>
            <td>5. Rollout</td>
            <td>Production hosting deployment, daily operational monitoring, and periodic maintenance [30].</td>
          </tr>
        </tbody>
      </table>
    </div>

    <p class="journal-paragraph">
      Fourth, data and system testing combined functional verification with empirical user evaluation. Functional correctness was tested using black-box testing [7] across 60 test scenarios grouped into six operational domains: (1) authentication, access rights, and user profiles (12 scenarios); (2) public portal, room catalog, and guest chat (10 scenarios); (3) prospective tenant five-step booking flow (10 scenarios); (4) active tenant portal, billing, and maintenance complaints (9 scenarios); (5) administrator operational and financial management (13 scenarios); and (6) task scheduler automation, late fees, and external APIs (6 scenarios). Finally, after deploying the platform to production hosting for operation and maintenance [30], operational effectiveness was evaluated through side-by-side usability trials and semi-structured interviews with the boarding house manager (Mr. Asep) and five active student tenants across a 10-point assessment scale [29].
    </p>

    <!-- TAHAP 6: BAB III. RESULTS AND DISCUSSION -->
    <div class="section-heading">III. RESULTS AND DISCUSSION</div>

    <div class="subsection-heading">A. RESULT</div>

    <p class="journal-paragraph">
      Architectural modeling established clear operational boundaries across prospective tenants, the boarding house administrator, and active tenants. The 3NF relational database schema separates core residency and financial tables (<em>users</em>, <em>kamar</em>, <em>penyewa</em>, <em>reservasi</em>, <em>tagihan</em>, and <em>pembayaran</em>) from supporting operational tables (<em>keluhan</em>, <em>pengeluaran</em>, <em>log_notifikasi</em>, <em>guest_chat_threads</em>, <em>chat_messages</em>, <em>customer_reviews</em>, <em>peraturan</em>, and <em>faqs</em>) [16], [17]. Through a one-to-many relationship, the <em>penyewa</em> table locks a snapshot of the agreed room rate, entry date, lease expiration, and emergency guardian contact so historical contracts remain intact when room tariffs change. The end-to-end decision logic from initial room browsing to official tenant activation is illustrated in Figure 1.
    </p>

    <div class="figure-container">
      <img src="images/gambar_4_6_flowchart_sistem.png" alt="Figure 1. System Flowchart" class="figure-image">
      <div class="figure-caption">Figure 1. End-to-end reservation and payment verification system flowchart.</div>
    </div>

    <p class="journal-paragraph">
      As shown in Figure 1, when an applicant selects a vacant unit from the 32 available rooms, completes identity verification, and initiates payment through Midtrans, the backend executes an ACID transaction using <em>lockForUpdate()</em> and schedule overlap checks to block duplicate reservations [2], [18], [19]. System implementation produced 36 responsive Neo-Brutalism web interfaces [12], [13] alongside nine core backend modules covering multi-role middleware, post-login routing, ACID room locking, schedule conflict detection, 24-hour Midtrans Snap token generation, SHA-512 webhook verification via <em>hash_equals()</em> [4], [20], [21], cron task scheduling with <em>withoutOverlapping()</em> [26], Fonnte WhatsApp dispatches [22], and queued SMTP/TLS email delivery via <em>ShouldQueue</em> [24]. For daily property oversight, the administrator dashboard employs an OLED dark theme that automatically calculates room occupancy, incoming rent, operational expenses, and net cash balance, as shown in Figure 2.
    </p>

    <div class="figure-container">
      <img src="images/gambar_4_26_dashboard_admin.png" alt="Figure 2. Administrator Operational Dashboard" class="figure-image">
      <div class="figure-caption">Figure 2. Administrator operational dashboard and automated cash ledger.</div>
    </div>

    <p class="journal-paragraph">
      Functional verification across all 60 black-box test scenarios in the six operational domains achieved a 100% pass rate without validation or transaction errors [7]. Every form filter, 16-digit National Identity (NIK) check, post-checkout room quarantine hold, PDF receipt download, and PDF/Excel financial export performed as specified. Subsequent User Acceptance Testing (UAT) through side-by-side trials and recorded interviews with the senior boarding house manager and five active student tenants yielded high satisfaction scores [29], as summarized in Table 2.
    </p>

    <div class="table-container">
      <div class="table-caption">
        <span class="table-label">Table 2.</span> <span class="table-caption-text">USER ACCEPTANCE EVALUATION SCORES AND KEY FINDINGS</span>
      </div>
      <table class="journal-table">
        <thead>
          <tr>
            <th>Informant (Role)</th>
            <th>Score</th>
            <th>Primary Operational Finding</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>Mr. Asep (Manager)</td>
            <td>9.5 / 10</td>
            <td>Book closing cut from 3 to 4 days to seconds; zero cash discrepancy; post-checkout lock prevents premature booking.</td>
          </tr>
          <tr>
            <td>Arjun (UNDIP Tenant)</td>
            <td>8.5 / 10</td>
            <td>Clear Neo-Brutalism catalog; fast BCA Virtual Account settlement; A5 PDF receipt practical for parents.</td>
          </tr>
          <tr>
            <td>Farrel (USM Tenant)</td>
            <td>7.0 / 10</td>
            <td>Easy remote Android booking; avoids night ATM queues; suggested adding QRIS and e-wallet channels.</td>
          </tr>
          <tr>
            <td>Iman (UDINUS Tenant)</td>
            <td>9.0 / 10</td>
            <td>Instant payment verification prevents lost-receipt disputes; neat monthly history; suggested peak-date server tuning.</td>
          </tr>
          <tr>
            <td>Ammar (PKN STAN Tenant)</td>
            <td>7.5 / 10</td>
            <td>Smooth iPhone Safari checkout; easy holiday rent payment from out of town; suggested direct WhatsApp payment links.</td>
          </tr>
          <tr>
            <td>Sindhu (UI Tenant)</td>
            <td>8.0 / 10</td>
            <td>Transparent pricing; removes awkward face-to-face cash handoffs; suggested an initial onboarding tour.</td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="subsection-heading">B. DISCUSSION</div>

    <p class="journal-paragraph">
      The empirical results confirm that integrating automated payment verification with database concurrency control directly resolves manual boarding house bottlenecks [6], [7]. Within the billing engine, the monthly invoice obligation $T_{\text{bill}}$ for an active room tariff $R_{\text{base}}$ enforces an intra-month grace period past the 10th and applies an idempotent 5% rollover fine upon crossing into the next calendar month, expressed in IEEE equation format as (1):
    </p>

    <div class="equation-container">
      $$T_{\text{bill}} = R_{\text{base}} + \delta \cdot (0.05 \times R_{\text{base}}) \dots (1)$$
    </div>

    <p class="journal-paragraph">
      where $\delta \in \{0, 1\}$ is a binary indicator equal to $1$ if an invoice remains unpaid across the calendar month boundary and $0$ otherwise [26]. Furthermore, monthly financial reconciliation across $n$ settled tenant payments and $m$ recorded operational expenditures $E_{j}$ computes the net cash balance $N_{\text{cash}}$ instantaneously as (2):
    </p>

    <div class="equation-container">
      $$N_{\text{cash}} = \sum_{i=1}^{n} \left( R_{\text{base},i} + \delta_{i} \cdot 0.05 R_{\text{base},i} \right) - \sum_{j=1}^{m} E_{j} \dots (2)$$
    </div>

    <p class="journal-paragraph">
      Compared with earlier boarding house systems by Cornellya and Afriyadi [1] and Nizar [2], which relied on manual bank slip uploads and lacked row-level locking, our implementation pairs ACID transactions with <em>lockForUpdate()</em> and a physical post-checkout room quarantine hold [18], [19]. This combination guarantees zero double bookings across all 32 rooms even when multiple applicants submit reservations simultaneously. Moreover, whereas the monitoring platform by Malaikosa and Mokola [3] still required manual payment checks, integrating Midtrans Snap with SHA-512 signature validation via <em>hash_equals()</em> [4], [20], [21] updates ledger records automatically upon payment settlement.
    </p>

    <p class="journal-paragraph">
      In addition, unlike single-checkout retail payment integrations [4], [20], this system couples payment webhooks with scheduled cron jobs [26], [27], the Fonnte WhatsApp API [5], [22], [23], and queued SMTP email relays [24], [25] to manage multi-month tenancy lifecycles. As reflected in Table 2, the senior manager awarded a 9.5/10 rating because automated ledger aggregation in (2) reduced month-end book closing from several days of manual receipt matching to instant PDF and Excel exports with zero cash variance. Meanwhile, the five student tenants recorded an average satisfaction score of 8.0/10, confirming that B2C self-service transparency [8], [9] and Neo-Brutalism UI ergonomics [12], [13] simplify remote room booking and monthly rent settlement. Constructive tenant feedback regarding QRIS expansion, direct WhatsApp payment links, and a first-login onboarding guide provides clear priorities for future iterative upgrades.
    </p>

    <!-- TAHAP 7: BAB IV. CONCLUSION -->
    <div class="section-heading">IV. CONCLUSION</div>

    <p class="journal-paragraph">
      The design and implementation of the web-based boarding house management information system at Asri Boarding House directly fulfill the three research objectives established in the introduction and verified in the results and discussion. First, centralizing the governance of 32 rental rooms on the Laravel MVC framework and a 3NF MySQL InnoDB database replaces conventional promotions and manual paper ledgers while preventing concurrent double bookings through ACID transactions and pessimistic row-level locking (<em>lockForUpdate()</em>). Second, integrating the Midtrans Snap payment gateway via SHA-512 authenticated webhooks with an automated server cron scheduler executes recurring monthly billing on the 1st of each month, calculates idempotent 5% rollover late fees past the intra-month grace period, and dispatches real-time alerts through the Fonnte WhatsApp API and queued SMTP emails. Third, functional testing across all 60 black-box scenarios achieved a 100% pass rate without technical defects. Field evaluation confirmed strong operational benefits: the resident manager awarded a 9.5/10 satisfaction score as month-end book closing dropped from several days to seconds with zero cash discrepancy, while five resident students recorded an average score of 8.0/10 for straightforward remote booking, virtual account settlements, and official A5 PDF receipts.
    </p>

    <p class="journal-paragraph">
      For future system development and further research, five practical enhancements are recommended based on field evaluation findings. First, WhatsApp billing reminders can embed direct payment links so tenants can settle invoices in a single click without navigating menus manually. Second, adding an interactive visual onboarding tour on the tenant portal will help first-time users locate receipt downloads and maintenance ticketing faster. Third, payment gateway channels should be expanded to include additional national banks, QRIS, and digital e-wallets for broader payment flexibility. Fourth, incorporating digital electricity sub-meter tracking will provide transparent per-room utility billing. Finally, future studies can integrate Internet of Things (IoT) smart door locks synchronized directly with active lease statuses in the database.
    </p>

    <!-- TAHAP 8: ACKNOWLEDGMENT (TANPA NOMOR BAB) -->
    <div class="section-heading">ACKNOWLEDGMENT</div>

    <p class="journal-paragraph">
      The authors thank the Department of Information Systems, Faculty of Computer Science, Soegijapranata Catholic University, Semarang, for providing academic guidance and research facilities throughout this study. Special gratitude is also extended to the owner, the resident manager Mr. Asep, and the student tenants of Asri Boarding House in Tembalang for their open cooperation during field interviews and system evaluation.
    </p>

    <!-- TAHAP 9: REFERENCES (TANPA NOMOR BAB, 10 PT REGULAR & 10 PT ITALIC IEEE) -->
    <div class="section-heading">REFERENCES</div>

    <div class="reference-item">[1] A. Cornellya and H. Afriyadi, "Perancangan Sistem Informasi Pemesanan pada Kost Tya Berbasis Web Menggunakan Framework Laravel," <em>PESHUM J. Pendidikan, Sos. dan Hum.</em>, vol. 4, no. 6, pp. 10360-10369, 2025, doi: 10.56799/peshum.v4i6.11777.</div>
    <div class="reference-item">[2] C. Nizar, "Rancang Bangun Sistem Informasi Sewa Rumah Kost (E-Kost) Berbasis Website," <em>J. Sist. Inf. dan Sains Teknol.</em>, vol. 3, no. 1, pp. 1-10, 2021, doi: 10.31326/sistek.v3i1.852.</div>
    <div class="reference-item">[3] E. J. Malaikosa and P. Mokola, "Sistem Informasi Monitoring Rumah Kos dan Pembayarannya Berbasis Web Menggunakan Metode Rapid Application Development," <em>JSiI (Jurnal Sist. Informasi)</em>, vol. 11, no. 1, pp. 21-26, 2024, doi: 10.30656/jsii.v11i1.8222.</div>
    <div class="reference-item">[4] R. Sutisna and F. Aziz, "Perancangan Sistem Penyewaan Alat Event Berbasis Website Menggunakan Midtrans sebagai Integrasi Payment Gateway pada PT. Bangbewe Production," <em>REMIK Ris. dan E-Jurnal Manaj. Inform. Komput.</em>, vol. 9, no. 1, pp. 317-325, 2025, doi: 10.33395/remik.v9i1.14498.</div>
    <div class="reference-item">[5] L. Hakim, S. P. Kristanto, M. N. Shodiq, and E. Amaliyah, "Aplikasi Penerimaan dan Pengeluaran Kas Berbasis Web dan WhatsApp Gateway," <em>J. Tekno Kompak</em>, vol. 15, no. 1, pp. 13-24, 2021, doi: 10.33365/jtk.v15i1.900.</div>
    <div class="reference-item">[6] A. Syahputra, R. Wiranti, and W. Astita, "Peran Sistem Informasi Manajemen Organisasi Dalam Pengambilan Keputusan," <em>J. Manaj. Sist. Inf.</em>, vol. 1, no. 1, pp. 26-31, 2022, doi: 10.35870/jmasif.v1i1.67.</div>
    <div class="reference-item">[7] J. K. Aditya and A. T. Hidayat, "Sistem Informasi Manajemen Kos Berbasis Web Menggunakan Metode Blackbox: (Studi Kasus: Kos Putri Humba)," <em>JUSIM (Jurnal Sist. Inf. Musirawas)</em>, vol. 10, no. 2, pp. 229-241, 2025, doi: 10.32767/jusim.v10i2.2782.</div>
    <div class="reference-item">[8] I. A. Nurhasanah, M. Brilliant, K. Reni, and A. Mulyanto, "Analisis Perancangan E-Business B2C (Business to Consumer) Upaya Digitalisasi Pengembangan UMKM (Studi Kasus : Kabupaten Pesawaran, Indonesia)," <em>J. Nas. Pendidik. Tek. Inform. JANAPATI</em>, vol. 11, no. 3, pp. 236-248, 2022, doi: 10.23887/janapati.v11i3.49787.</div>
    <div class="reference-item">[9] E. Y. Sutrisno, A. C. Hidayat, and A. Sutanto, "Pemanfaatan E-Commerce dan Property Management System Dalam Kegiatan Bisnis Perhotelan di Era Revolusi Industri 4.0," <em>J. Kepariwisataan Indones.</em>, vol. 17, no. 1, pp. 85-98, 2023, doi: 10.47608/jki.v17i12023.85-98.</div>
    <div class="reference-item">[10] W. Wahyudin and S. Bela, "Rancang Bangun Sistem Informasi Inventory Stock Barang Berbasis Web," <em>J. Tek. Komput.</em>, vol. 7, no. 2, pp. 208-214, 2021, doi: 10.31294/jtk.v7i2.10683.</div>
    <div class="reference-item">[11] Y. Anggraini, D. Pasha, D. Damayanti, and A. Setiawan, "Sistem Informasi Penjualan Sepeda Berbasis Web Menggunakan Framework CodeIgniter," <em>J. Teknol. dan Sist. Inf.</em>, vol. 1, no. 2, pp. 64-70, 2020, doi: 10.33365/jtsi.v1i2.236.</div>
    <div class="reference-item">[12] A. R. Setiawan, M. Asfi, A. Sevtiana, S. Pranata, and W. E. Septian, "Design System pada Perancangan Antarmuka Perangkat Lunak Sistem Akses Digital," <em>J. Teknol. Terpadu</em>, vol. 9, no. 1, pp. 56-64, 2023, doi: 10.54914/jtt.v9i1.619.</div>
    <div class="reference-item">[13] O. N. Faizah, N. R. Oktadini, B. W. Putra, P. E. Sevtiyuni, P. Putra, and A. Meiriza, "UI/UX Design of Web-based Software License Management System using User-Centered Design and System Usability Scale," <em>J. Nas. Teknol. dan Sist. Inf.</em>, vol. 9, no. 3, pp. 255-263, 2023, doi: 10.25077/TEKNOSI.v9i3.2023.255-263.</div>
    <div class="reference-item">[14] L. Rahmawati and Sumarsono, "Desain Pengembangan Website dengan Arsitektur Model View Controller pada Framework Laravel," <em>J. Teknol. dan Sist. Inf. Bisnis</em>, vol. 6, no. 4, pp. 785-790, 2024, doi: 10.47233/jteksis.v6i4.1497.</div>
    <div class="reference-item">[15] A. Prakarsya, L. Rahmadi, and M. S. Nurfatih, "Implementasi Konsep Model View Controller Pada Pengembangan Aplikasi Web Menggunakan Framework Laravel," <em>SISKOMTI J. Sist. Inf. Komput. dan Teknol. Inf.</em>, vol. 5, no. 1, pp. 8-15, 2023, doi: 10.54342/agyr3903.</div>
    <div class="reference-item">[16] Y. Adisa and M. I. P. Nasution, "Konsep Dan Peran Sistem Manajemen Basis Data Relasional Pada Sistem Informasi Manajemen," <em>MASIP J. Manaj. Adm. Bisnis dan Publik Terapan</em>, vol. 1, no. 3, pp. 76-83, 2023, doi: 10.59061/masip.v1i3.314.</div>
    <div class="reference-item">[17] D. F. M. Dina, "Normalisasi Database Rancangan Sistem Penyewaan Buku Berbayar," <em>Computing Insight J. Comput. Sci.</em>, vol. 4, no. 1, pp. 56-61, 2024, doi: 10.30651/comp_insight.v4i1.15814.</div>
    <div class="reference-item">[18] J. V. Dachi and J. Suhada, "Implementasi Basis Data Sederhana Menggunakan MySQL/PostgreSQL," <em>Router J. Tek. Inform. dan Terap.</em>, vol. 3, no. 1, pp. 49-59, 2025, doi: 10.62951/router.v3i1.621.</div>
    <div class="reference-item">[19] I. Thoib, B. P. Candra, N. Sururi, and D. S. Nugraha, "Perbandingan Performa Pencarian Data Berbasis Teks dengan Dan Tanpa Full-text Index pada Basis Data MySQL," <em>INSOLOGI J. Sains dan Teknol.</em>, vol. 3, no. 6, pp. 663-673, 2024, doi: 10.55123/insologi.v3i6.4596.</div>
    <div class="reference-item">[20] M. Nurhayati and R. Setiawan, "Implementasi Payment Gateway Midtrans Pada Aplikasi Toko Mas Berbasis Web," <em>J. Algoritma</em>, vol. 21, no. 2, pp. 366-377, 2024, doi: 10.33364/algoritma/v.21-2.1686.</div>
    <div class="reference-item">[21] R. C. Buwono, R. Kartadie, and M. K. Ramadhan, "Perancangan dan Implementasi Sistem Payment Gateway MIDTRANS untuk UMKM Batik Lurik," <em>INFOTECH J. Inform. dan Teknol.</em>, vol. 5, no. 2, pp. 228-238, 2024, doi: 10.37373/infotech.v5i2.1334.</div>
    <div class="reference-item">[22] I. M. B. Waton, A. N. Weking, and B. Deta, "Penerapan Api Whatsapp Fonnte Untuk Sistem Pengingat Jadwal Bimbingan Tugas Akhir Mahasiswa Berbasis Web," <em>J. Tek. Mesin, Elektro dan Ilmu Komput.</em>, vol. 5, no. 2, pp. 229-241, 2025, doi: 10.55606/teknik.v5i2.7614.</div>
    <div class="reference-item">[23] A. R. Manik, D. Kiswanto, M. B. Akbar, and J. Purba, "Academic Portal with MFA (WhatsApp OTP via Fonnte), Role-Based Access Control, and Logging System for Network Monitoring," <em>J. Ilm. Sist. Inf.</em>, vol. 4, no. 3, pp. 674-687, 2025, doi: 10.51903/qwz6pt87.</div>
    <div class="reference-item">[24] A. Harbani and A. Sidiyantoro, "Implementasi Simple Mail Transfer Protocol Relay Pada Mail Gateway Untuk Menentukan Konten Email Spam," <em>TeknoIS J. Ilm. Teknol. Inf. dan Sains</em>, vol. 12, no. 1, pp. 57-66, 2022, doi: 10.36350/jbs.v12i1.130.</div>
    <div class="reference-item">[25] M. A. Syahputra and R. A. Putri, "Sistem Informasi Monitoring Pembelajaran dengan Fitur Notifikasi Email Berbasis Web," <em>Decode J. Pendidik. Teknol. Inf.</em>, vol. 5, no. 2, pp. 505-515, 2025, doi: 10.51454/decode.v5i2.1206.</div>
    <div class="reference-item">[26] A. Wibowo, A. Fathimah, D. Waskito, and R. A. Aprilianto, "Cron Job Implementation for Automated Data Processing and Transfer in Cloud Infrastructure," <em>Sainteknol J. Sains dan Teknol.</em>, vol. 23, no. 1, pp. 11-20, 2025, doi: 10.15294/sainteknol.v23i1.22649.</div>
    <div class="reference-item">[27] Iqbal, W. Nugraha, and R. Sa'adah, "Optimasi Notifikasi Agenda Kegiatan Pemerintahan Berbasis Web Menggunakan Firebase Cloud Messaging dan WhatsApp Gateway," <em>JSITIK J. Sist. Inf. dan Teknol. Inf. Komput.</em>, vol. 5, no. 1, pp. 29-38, 2026, doi: 10.53624/jsitik.v5i1.951.</div>
    <div class="reference-item">[28] M. W. Mauludy and Fathahillah, "Pengembangan Sistem Informasi Pengarsipan File Menggunakan Metode R&amp;D," <em>J. Informatics Manag. Inf. Technol.</em>, vol. 5, no. 2, pp. 118-125, 2025, doi: 10.47065/jimat.v5i2.484.</div>
    <div class="reference-item">[29] Sugiyono, <em>Metode Penelitian Kuantitatif, Kualitatif, dan R&amp;D</em>. Bandung: Alfabeta, 2022.</div>
    <div class="reference-item">[30] E. Rahmi, E. Yumami, and N. Hidayasari, "Analisis Metode Pengembangan Sistem Informasi Berbasis Website: Systematic Literature Review," <em>REMIK Ris. dan E-Jurnal Manaj. Inform. Komput.</em>, vol. 7, no. 1, pp. 821-834, 2023, doi: 10.33395/remik.v7i1.12177.</div>

  </div>

</div>
