import subprocess
import os
from PIL import Image

WORKSPACE = r"c:\xampp\htdocs\asri-boarding-house"
CHROME_PATH = r"C:\Program Files\Google\Chrome\Application\chrome.exe"
SCRATCH_DIR = os.path.join(WORKSPACE, "scratch")

def render_html_to_png(html_content, output_png, width=1320, height=1480, scale=2.5):
    html_path = os.path.join(SCRATCH_DIR, "temp_diagram_perfect.html")
    with open(html_path, "w", encoding="utf-8") as f:
        f.write(html_content)
    
    cmd = [
        CHROME_PATH,
        "--headless=new",
        "--disable-gpu",
        f"--screenshot={output_png}",
        f"--window-size={width},{height}",
        f"--force-device-scale-factor={scale}",
        f"file:///{html_path.replace(os.sep, '/')}"
    ]
    res = subprocess.run(cmd, capture_output=True, text=True)
    if res.returncode != 0:
        print("Error rendering:", res.stderr)
        return False
    
    if os.path.exists(output_png):
        img = Image.open(output_png)
        print(f"Rendered: {os.path.basename(output_png)} ({img.size[0]}x{img.size[1]})")
        return True
    return False

html_perfect = '''<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<style>
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body {
    background-color: #ffffff;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    color: #0f172a;
    padding: 35px 35px 35px 35px;
    width: 1320px;
    height: 1460px;
    position: relative;
  }
  
  /* HEADER */
  .header {
    text-align: center;
    margin-bottom: 25px;
    padding-bottom: 18px;
    border-bottom: 2px solid #e2e8f0;
    position: relative;
  }
  .badge-container {
    display: flex;
    justify-content: center;
    gap: 12px;
    margin-bottom: 10px;
  }
  .badge {
    display: inline-block;
    font-size: 11.5px;
    font-weight: 700;
    letter-spacing: 1.2px;
    padding: 5px 14px;
    border-radius: 9999px;
    text-transform: uppercase;
  }
  .badge-primary { background: #dbeafe; color: #1e40af; border: 1px solid #bfdbfe; }
  .badge-secondary { background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; }

  h1 {
    font-size: 26px;
    font-weight: 800;
    color: #0f172a;
    letter-spacing: -0.5px;
    margin-bottom: 6px;
  }
  .subtitle {
    font-size: 14.5px;
    color: #475569;
    font-weight: 500;
    font-style: italic;
  }

  /* SIDE PANELS */
  .side-panel {
    position: absolute;
    width: 330px;
    background: #f8fafc;
    border-radius: 12px;
    padding: 16px 18px;
    border: 1.5px dashed #cbd5e1;
    z-index: 5;
  }
  .panel-title {
    font-size: 12.5px;
    font-weight: 800;
    color: #1e293b;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    gap: 8px;
  }
  .panel-title-icon {
    width: 8px;
    height: 8px;
    border-radius: 50%;
  }
  .side-panel ul {
    list-style: none;
  }
  .side-panel li {
    font-size: 12px;
    color: #334155;
    line-height: 1.55;
    margin-bottom: 7px;
    position: relative;
    padding-left: 16px;
  }
  .side-panel li::before {
    position: absolute;
    left: 0;
    font-weight: bold;
  }

  /* Panel Top Right */
  .panel-top-right {
    top: 135px;
    right: 40px;
    border-color: #93c5fd;
    background: #eff6ff;
  }
  .panel-top-right .panel-title { color: #1e40af; }
  .panel-top-right .panel-title-icon { background: #2563eb; }
  .panel-top-right li::before { content: "✓"; color: #2563eb; }

  /* Panel Bottom Left */
  .panel-bottom-left {
    bottom: 50px;
    left: 40px;
    border-color: #cbd5e1;
    background: #f8fafc;
  }
  .panel-bottom-left .panel-title { color: #334155; }
  .panel-bottom-left .panel-title-icon { background: #64748b; }
  .panel-bottom-left li::before { content: "▪"; color: #0284c7; }

  /* WATERFALL CASCADING CARDS */
  .waterfall-area {
    position: relative;
    width: 100%;
    height: 1280px;
  }

  .step-card {
    position: absolute;
    width: 610px;
    background: #ffffff;
    border-radius: 12px;
    box-shadow: 0 4px 14px -2px rgba(15, 23, 42, 0.08), 0 2px 6px -1px rgba(15, 23, 42, 0.04);
    border: 1.5px solid;
    overflow: hidden;
    z-index: 20;
  }

  .card-header {
    padding: 10px 18px;
    font-weight: 700;
    font-size: 14.5px;
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: space-between;
  }
  .card-header .fase-tag {
    background: rgba(255, 255, 255, 0.22);
    border: 1px solid rgba(255, 255, 255, 0.35);
    padding: 2px 10px;
    border-radius: 6px;
    font-size: 11.5px;
    font-weight: 800;
    letter-spacing: 0.8px;
  }

  .card-body {
    padding: 13px 18px 12px 18px;
    background: #ffffff;
  }
  .card-body ul {
    list-style: none;
    margin-bottom: 9px;
  }
  .card-body li {
    font-size: 12.8px;
    color: #334155;
    line-height: 1.55;
    margin-bottom: 5px;
    position: relative;
    padding-left: 18px;
  }
  .card-body li::before {
    content: "•";
    position: absolute;
    left: 4px;
    font-size: 16px;
    font-weight: bold;
  }
  .card-body li b {
    color: #0f172a;
    font-weight: 600;
  }

  .deliverable {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 11.8px;
    font-weight: 600;
    padding: 6px 12px;
    border-radius: 6px;
  }
  .deliverable-label {
    text-transform: uppercase;
    font-size: 10.5px;
    font-weight: 800;
    letter-spacing: 0.6px;
    padding: 1px 6px;
    border-radius: 4px;
  }

  /* COLOR THEMES FOR EACH STEP */
  /* Step 1 */
  .step-1 { top: 0px; left: 40px; border-color: #2563eb; }
  .step-1 .card-header { background: linear-gradient(135deg, #1e40af, #2563eb); }
  .step-1 li::before { color: #2563eb; }
  .step-1 .deliverable { background: #eff6ff; color: #1e40af; border: 1px solid #bfdbfe; }
  .step-1 .deliverable-label { background: #dbeafe; color: #1e40af; }

  /* Step 2 */
  .step-2 { top: 245px; left: 165px; border-color: #0284c7; }
  .step-2 .card-header { background: linear-gradient(135deg, #0369a1, #0284c7); }
  .step-2 li::before { color: #0284c7; }
  .step-2 .deliverable { background: #f0f9ff; color: #0369a1; border: 1px solid #bae6fd; }
  .step-2 .deliverable-label { background: #e0f2fe; color: #0369a1; }

  /* Step 3 */
  .step-3 { top: 490px; left: 290px; border-color: #4f46e5; }
  .step-3 .card-header { background: linear-gradient(135deg, #4338ca, #4f46e5); }
  .step-3 li::before { color: #4f46e5; }
  .step-3 .deliverable { background: #eef2ff; color: #3730a3; border: 1px solid #c7d2fe; }
  .step-3 .deliverable-label { background: #e0e7ff; color: #3730a3; }

  /* Step 4 */
  .step-4 { top: 735px; left: 415px; border-color: #059669; }
  .step-4 .card-header { background: linear-gradient(135deg, #047857, #059669); }
  .step-4 li::before { color: #059669; }
  .step-4 .deliverable { background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; }
  .step-4 .deliverable-label { background: #d1fae5; color: #065f46; }

  /* Step 5 */
  .step-5 { top: 980px; left: 540px; border-color: #d97706; }
  .step-5 .card-header { background: linear-gradient(135deg, #b45309, #d97706); }
  .step-5 li::before { color: #d97706; }
  .step-5 .deliverable { background: #fffbeb; color: #92400e; border: 1px solid #fde68a; }
  .step-5 .deliverable-label { background: #fef3c7; color: #92400e; }

  /* SVG CONNECTORS */
  .connectors-svg {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    pointer-events: none;
    z-index: 30;
  }

  .waterfall-flow-badge {
    position: absolute;
    background: #ffffff;
    font-size: 10.5px;
    font-weight: 700;
    padding: 2px 8px;
    border-radius: 9999px;
    border: 1px solid;
    box-shadow: 0 1px 3px rgba(0,0,0,0.08);
    display: flex;
    align-items: center;
    gap: 4px;
    z-index: 35;
  }
</style>
</head>
<body>

<div class="header">
  <div class="badge-container">
    <span class="badge badge-primary">Model Pengembangan Waterfall (SDLC)</span>
    <span class="badge badge-secondary">Sekuensial Linier & Terstruktur</span>
  </div>
  <h1>TAHAPAN MODEL WATERFALL PENGEMBANGAN SISTEM</h1>
  <div class="subtitle">Sistem Informasi Manajemen Asri Boarding House Terintegrasi Payment Gateway</div>
</div>

<!-- Side Panel Top Right: Metodologi Waterfall -->
<div class="side-panel panel-top-right">
  <div class="panel-title">
    <span class="panel-title-icon"></span>
    Karakteristik Model
  </div>
  <ul>
    <li><b>Sekuensial Linier:</b> Fase berjalan bertahap dengan batas awal dan akhir yang tegas.</li>
    <li><b>Quality Gate:</b> Setiap fase menghasilkan luaran terverifikasi sebelum masuk fase berikutnya.</li>
    <li><b>Spesifikasi Stabil:</b> Kebutuhan fungsional dan aturan bisnis dikunci sejak awal.</li>
    <li><b>Dokumentasi Lengkap:</b> Menjamin ketertelusuran teknis di setiap tahapan rekayasa.</li>
  </ul>
</div>

<!-- Side Panel Bottom Left: Landasan & Lingkungan Teknis -->
<div class="side-panel panel-bottom-left">
  <div class="panel-title">
    <span class="panel-title-icon"></span>
    Lingkungan Implementasi
  </div>
  <ul>
    <li><b>Backend:</b> Framework Laravel 11 & PHP 8.2</li>
    <li><b>Basis Data:</b> MySQL 8.x InnoDB (Pessimistic Lock)</li>
    <li><b>Payment Gateway:</b> Midtrans Snap API (BCA VA)</li>
    <li><b>Komunikasi:</b> Fonnte WhatsApp API & SMTP Surel</li>
    <li><b>Peladen Produksi:</b> Hostinger LiteSpeed (weatso.id)</li>
  </ul>
</div>

<div class="waterfall-area">
  <!-- Connectors -->
  <svg class="connectors-svg" viewBox="0 0 1250 1280" fill="none" xmlns="http://www.w3.org/2000/svg">
    <defs>
      <marker id="arr1" viewBox="0 0 10 10" refX="5" refY="5" markerWidth="6" markerHeight="6" orient="auto-start-reverse">
        <path d="M 0 1 L 10 5 L 0 9 z" fill="#0284c7"/>
      </marker>
      <marker id="arr2" viewBox="0 0 10 10" refX="5" refY="5" markerWidth="6" markerHeight="6" orient="auto-start-reverse">
        <path d="M 0 1 L 10 5 L 0 9 z" fill="#4f46e5"/>
      </marker>
      <marker id="arr3" viewBox="0 0 10 10" refX="5" refY="5" markerWidth="6" markerHeight="6" orient="auto-start-reverse">
        <path d="M 0 1 L 10 5 L 0 9 z" fill="#059669"/>
      </marker>
      <marker id="arr4" viewBox="0 0 10 10" refX="5" refY="5" markerWidth="6" markerHeight="6" orient="auto-start-reverse">
        <path d="M 0 1 L 10 5 L 0 9 z" fill="#d97706"/>
      </marker>
    </defs>
    
    <!-- Clean Waterfall Drop Arrows: Overlap center drops vertically from Step N bottom into Step N+1 top -->
    <!-- Step 1 -> Step 2 at X = 400 (Step 1 is 40..650, Step 2 is 165..775) -->
    <path d="M 400 198 L 400 242" stroke="#0284c7" stroke-width="3" stroke-linecap="round" fill="none" marker-end="url(#arr1)" />
    
    <!-- Step 2 -> Step 3 at X = 530 (Step 2 is 165..775, Step 3 is 290..900) -->
    <path d="M 530 443 L 530 487" stroke="#4f46e5" stroke-width="3" stroke-linecap="round" fill="none" marker-end="url(#arr2)" />
    
    <!-- Step 3 -> Step 4 at X = 655 (Step 3 is 290..900, Step 4 is 415..1025) -->
    <path d="M 655 688 L 655 732" stroke="#059669" stroke-width="3" stroke-linecap="round" fill="none" marker-end="url(#arr3)" />
    
    <!-- Step 4 -> Step 5 at X = 780 (Step 4 is 415..1025, Step 5 is 540..1150) -->
    <path d="M 780 933 L 780 977" stroke="#d97706" stroke-width="3" stroke-linecap="round" fill="none" marker-end="url(#arr4)" />
  </svg>

  <!-- Flow Badges on Waterfall drops -->
  <div class="waterfall-flow-badge" style="top: 212px; left: 412px; border-color: #0284c7; color: #0284c7;">
    <span>SRS Validated</span> ↓
  </div>
  <div class="waterfall-flow-badge" style="top: 457px; left: 542px; border-color: #4f46e5; color: #4f46e5;">
    <span>Design Approved</span> ↓
  </div>
  <div class="waterfall-flow-badge" style="top: 702px; left: 667px; border-color: #059669; color: #059669;">
    <span>Code Compiled</span> ↓
  </div>
  <div class="waterfall-flow-badge" style="top: 947px; left: 792px; border-color: #d97706; color: #d97706;">
    <span>Tests Passed</span> ↓
  </div>

  <!-- Step 1 -->
  <div class="step-card step-1">
    <div class="card-header">
      <span>1. Analisis Kebutuhan (Requirements Analysis)</span>
      <span class="fase-tag">FASE 1</span>
    </div>
    <div class="card-body">
      <ul>
        <li><b>Observasi Lapangan:</b> Pemetaan operasional 32 unit kamar kos & inventaris fasilitas</li>
        <li><b>Wawancara Pengelola Kost:</b> Identifikasi kendala manual, penagihan, & arus kas</li>
        <li><b>Wawancara Penghuni Kost:</b> Penggalian kebutuhan pembayaran digital & transparansi sewa</li>
        <li><b>Telaah Dokumen:</b> Analisis buku besar kas fisik, format kuitansi nota, & matriks tarif</li>
      </ul>
      <div class="deliverable">
        <span class="deliverable-label">Luaran:</span>
        <span>Dokumen Spesifikasi Kebutuhan Sistem (SRS) & Kamus Data Operasional</span>
      </div>
    </div>
  </div>

  <!-- Step 2 -->
  <div class="step-card step-2">
    <div class="card-header">
      <span>2. Perancangan Sistem (System Design)</span>
      <span class="fase-tag">FASE 2</span>
    </div>
    <div class="card-body">
      <ul>
        <li><b>Arsitektur 3-Tier MVC:</b> Struktur aplikasi Laravel 11 dengan lapisan Service Layer</li>
        <li><b>Pemodelan UML:</b> Diagram Use Case, Activity, Flowchart, dan Sequence Diagram</li>
        <li><b>Desain Basis Data:</b> Normalisasi 3NF, skema relasional, dan ERD 22 tabel data</li>
        <li><b>Desain Antarmuka:</b> Neo-Brutalism dengan kepatuhan aksesibilitas WCAG 2.1</li>
      </ul>
      <div class="deliverable">
        <span class="deliverable-label">Luaran:</span>
        <span>Cetak Biru Arsitektur Aplikasi, Skema Basis Data 3NF & Desain Antarmuka</span>
      </div>
    </div>
  </div>

  <!-- Step 3 -->
  <div class="step-card step-3">
    <div class="card-header">
      <span>3. Implementasi Program (Implementation / Coding)</span>
      <span class="fase-tag">FASE 3</span>
    </div>
    <div class="card-body">
      <ul>
        <li><b>Framework Web:</b> Pengembangan komponen backend & frontend Laravel 11 (PHP 8.2)</li>
        <li><b>Basis Data:</b> Penguncian baris pesimistik MySQL 8.x InnoDB untuk anti-double booking</li>
        <li><b>Payment Gateway:</b> Integrasi Midtrans Snap API (Bank BCA Virtual Account)</li>
        <li><b>Layanan Komunikasi:</b> Integrasi WhatsApp Gateway Fonnte & SMTP Mail Server</li>
      </ul>
      <div class="deliverable">
        <span class="deliverable-label">Luaran:</span>
        <span>Aplikasi Sistem Informasi Terintegrasi & Basis Data Berfungsi Penuh</span>
      </div>
    </div>
  </div>

  <!-- Step 4 -->
  <div class="step-card step-4">
    <div class="card-header">
      <span>4. Pengujian Sistem (System Testing)</span>
      <span class="fase-tag">FASE 4</span>
    </div>
    <div class="card-body">
      <ul>
        <li><b>Black Box Testing:</b> Pengujian fungsionalitas menyeluruh (60 butir skenario uji)</li>
        <li><b>Pengujian Keamanan RBAC:</b> Validasi isolasi 3 portal login bebas kerentanan IDOR</li>
        <li><b>Pengujian Transaksi:</b> Simulasi pembayaran Midtrans Sandbox BCA Virtual Account</li>
        <li><b>Automated Suite Testing:</b> Verifikasi 510 unit pengujian PHPUnit (2.211 assertions)</li>
      </ul>
      <div class="deliverable">
        <span class="deliverable-label">Luaran:</span>
        <span>Matriks Verifikasi Fungsionalitas Kotak Hitam, RBAC & Transaksi Valid 100%</span>
      </div>
    </div>
  </div>

  <!-- Step 5 -->
  <div class="step-card step-5">
    <div class="card-header">
      <span>5. Penerapan & Evaluasi (Deployment & Evaluation)</span>
      <span class="fase-tag">FASE 5</span>
    </div>
    <div class="card-body">
      <ul>
        <li><b>Penerapan Peladen Produksi:</b> Peladen Hostinger LiteSpeed (asriboardinghouse.weatso.id)</li>
        <li><b>Scheduler Otomatis:</b> Konfigurasi Cron Job per menit untuk mesin billing tgl 1 & denda 5%</li>
        <li><b>Optimasi Performa:</b> Konfigurasi cache rute, konfigurasi, tampilan, dan proteksi .htaccess</li>
        <li><b>Evaluasi Pengguna:</b> Usability Testing pengelola operasional (skor 9,5/10) & 5 penghuni</li>
      </ul>
      <div class="deliverable">
        <span class="deliverable-label">Luaran:</span>
        <span>Sistem Beroperasi Live di Produksi & Laporan Evaluasi Uji Kebergunaan</span>
      </div>
    </div>
  </div>
</div>

</body>
</html>
'''

output_perfect = os.path.join(WORKSPACE, "Skripsi", "images", "waterfall_model_diagram.png")
render_html_to_png(html_perfect, output_perfect, width=1320, height=1460, scale=2.5)
print("Rendered perfected waterfall diagram to Skripsi/images/waterfall_model_diagram.png!")
