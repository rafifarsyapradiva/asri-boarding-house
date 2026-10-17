import subprocess
import os
from PIL import Image
import numpy as np

WORKSPACE = r"c:\xampp\htdocs\asri-boarding-house"
CHROME_PATH = r"C:\Program Files\Google\Chrome\Application\chrome.exe"
SCRATCH_DIR = os.path.join(WORKSPACE, "scratch")
SKRIPSI_IMAGES = os.path.join(WORKSPACE, "Skripsi", "images")

def render_html_to_png(html_content, output_png, width=1220, height=1260, scale=2.5):
    html_path = os.path.join(SCRATCH_DIR, "temp_render_waterfall_concise.html")
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
        arr = np.array(img.convert('L'))
        mask = arr < 252
        coords = np.argwhere(mask)
        if len(coords) > 0:
            y0, x0 = coords.min(axis=0)
            y1, x1 = coords.max(axis=0)
            pad_x = int(35 * scale)
            pad_y = int(30 * scale)
            crop_x0 = max(0, x0 - pad_x)
            crop_y0 = max(0, y0 - pad_y)
            crop_x1 = min(img.size[0], x1 + pad_x)
            crop_y1 = min(img.size[1], y1 + pad_y)
            
            cropped = img.crop((crop_x0, crop_y0, crop_x1, crop_y1))
            rgb_img = Image.new("RGB", cropped.size, (255, 255, 255))
            if cropped.mode == 'RGBA':
                rgb_img.paste(cropped, mask=cropped.split()[3])
            else:
                rgb_img.paste(cropped)
            rgb_img.save(output_png, "PNG", optimize=True)
            print(f"Rendered & Cropped: {os.path.basename(output_png)} ({rgb_img.size[0]}x{rgb_img.size[1]})")
        return True
    return False

html_concise_waterfall = '''<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<style>
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body {
    background-color: #ffffff;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    color: #0f172a;
    padding: 35px 35px;
    width: 1220px;
  }
  
  /* HEADER */
  .header {
    text-align: center;
    margin-bottom: 30px;
    padding-bottom: 18px;
    border-bottom: 2px solid #e2e8f0;
  }
  .badge {
    display: inline-block;
    font-size: 11.5px;
    font-weight: 700;
    letter-spacing: 1.2px;
    padding: 5px 16px;
    border-radius: 9999px;
    background: #dbeafe;
    color: #1e40af;
    border: 1px solid #bfdbfe;
    text-transform: uppercase;
    margin-bottom: 10px;
  }
  h1 {
    font-size: 26px;
    font-weight: 800;
    color: #0f172a;
    letter-spacing: -0.5px;
    margin-bottom: 5px;
  }
  .subtitle {
    font-size: 14.5px;
    color: #475569;
    font-weight: 500;
    font-style: italic;
  }

  /* WATERFALL CANVAS */
  .waterfall-canvas {
    position: relative;
    width: 1140px;
    height: 1060px;
    margin: 0 auto;
  }

  /* CARDS */
  .step-card {
    position: absolute;
    width: 500px;
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
  .fase-badge {
    background: rgba(255, 255, 255, 0.22);
    border: 1px solid rgba(255, 255, 255, 0.35);
    padding: 2px 9px;
    border-radius: 5px;
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 0.6px;
  }

  .card-body {
    padding: 14px 20px 12px 20px;
    background: #ffffff;
  }
  .card-body ul {
    list-style: none;
    margin-bottom: 10px;
  }
  .card-body li {
    font-size: 13.5px;
    color: #1e293b;
    font-weight: 600;
    line-height: 1.6;
    margin-bottom: 4px;
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

  .deliverable {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 12px;
    font-weight: 600;
    padding: 5px 12px;
    border-radius: 6px;
    width: 100%;
  }
  .deliv-tag {
    font-size: 10px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 1px 6px;
    border-radius: 4px;
  }

  /* COLOR THEMES */
  /* Step 1 */
  .step-1 { top: 0px; left: 20px; border-color: #2563eb; }
  .step-1 .card-header { background: linear-gradient(135deg, #1e40af, #2563eb); }
  .step-1 li::before { color: #2563eb; }
  .step-1 .deliverable { background: #eff6ff; color: #1e40af; border: 1px solid #bfdbfe; }
  .step-1 .deliv-tag { background: #dbeafe; color: #1e40af; }

  /* Step 2 */
  .step-2 { top: 210px; left: 170px; border-color: #0284c7; }
  .step-2 .card-header { background: linear-gradient(135deg, #0369a1, #0284c7); }
  .step-2 li::before { color: #0284c7; }
  .step-2 .deliverable { background: #f0f9ff; color: #0369a1; border: 1px solid #bae6fd; }
  .step-2 .deliv-tag { background: #e0f2fe; color: #0369a1; }

  /* Step 3 */
  .step-3 { top: 420px; left: 320px; border-color: #4f46e5; }
  .step-3 .card-header { background: linear-gradient(135deg, #4338ca, #4f46e5); }
  .step-3 li::before { color: #4f46e5; }
  .step-3 .deliverable { background: #eef2ff; color: #3730a3; border: 1px solid #c7d2fe; }
  .step-3 .deliv-tag { background: #e0e7ff; color: #3730a3; }

  /* Step 4 */
  .step-4 { top: 630px; left: 470px; border-color: #059669; }
  .step-4 .card-header { background: linear-gradient(135deg, #047857, #059669); }
  .step-4 li::before { color: #059669; }
  .step-4 .deliverable { background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; }
  .step-4 .deliv-tag { background: #d1fae5; color: #065f46; }

  /* Step 5 */
  .step-5 { top: 840px; left: 620px; border-color: #d97706; }
  .step-5 .card-header { background: linear-gradient(135deg, #b45309, #d97706); }
  .step-5 li::before { color: #d97706; }
  .step-5 .deliverable { background: #fffbeb; color: #92400e; border: 1px solid #fde68a; }
  .step-5 .deliv-tag { background: #fef3c7; color: #92400e; }

  /* SVG ARROWS */
  .connectors-svg {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    pointer-events: none;
    z-index: 10;
  }
</style>
</head>
<body>

<div class="header">
  <div class="badge">Model Waterfall SDLC</div>
  <h1>TAHAPAN MODEL WATERFALL PENGEMBANGAN SISTEM</h1>
  <div class="subtitle">Sistem Informasi Manajemen Asri Boarding House Terintegrasi Payment Gateway</div>
</div>

<div class="waterfall-canvas">
  <!-- Connectors -->
  <svg class="connectors-svg" viewBox="0 0 1140 1060" fill="none" xmlns="http://www.w3.org/2000/svg">
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
    
    <!-- Clean Elbow Waterfall Arrows -->
    <!-- 1 -> 2: Drop at X = 370 -->
    <path d="M 370 180 L 370 207" stroke="#0284c7" stroke-width="3" stroke-linecap="round" fill="none" marker-end="url(#arr1)" />
    <!-- 2 -> 3: Drop at X = 520 -->
    <path d="M 520 390 L 520 417" stroke="#4f46e5" stroke-width="3" stroke-linecap="round" fill="none" marker-end="url(#arr2)" />
    <!-- 3 -> 4: Drop at X = 670 -->
    <path d="M 670 600 L 670 627" stroke="#059669" stroke-width="3" stroke-linecap="round" fill="none" marker-end="url(#arr3)" />
    <!-- 4 -> 5: Drop at X = 820 -->
    <path d="M 820 810 L 820 837" stroke="#d97706" stroke-width="3" stroke-linecap="round" fill="none" marker-end="url(#arr4)" />
  </svg>

  <!-- Step 1 -->
  <div class="step-card step-1">
    <div class="card-header">
      <span>1. Analisis Kebutuhan (Requirements Analysis)</span>
      <span class="fase-badge">FASE 1</span>
    </div>
    <div class="card-body">
      <ul>
        <li>Observasi Lapangan Operasional Kos</li>
        <li>Wawancara Pengelola Kost</li>
        <li>Wawancara Penghuni Kost Aktif</li>
        <li>Studi Dokumentasi Arsip Kas</li>
      </ul>
      <div class="deliverable">
        <span class="deliv-tag">Luaran:</span>
        <span>Dokumen Spesifikasi Kebutuhan Sistem (SRS)</span>
      </div>
    </div>
  </div>

  <!-- Step 2 -->
  <div class="step-card step-2">
    <div class="card-header">
      <span>2. Perancangan Sistem (System Design)</span>
      <span class="fase-badge">FASE 2</span>
    </div>
    <div class="card-body">
      <ul>
        <li>Arsitektur 3-Tier MVC & Service Layer</li>
        <li>Pemodelan UML (Use Case, Activity, Sequence)</li>
        <li>Normalisasi Skema Basis Data 3NF & ERD</li>
        <li>Desain Antarmuka Neo-Brutalism (WCAG 2.1)</li>
      </ul>
      <div class="deliverable">
        <span class="deliv-tag">Luaran:</span>
        <span>Cetak Biru Arsitektur & Skema Basis Data 3NF</span>
      </div>
    </div>
  </div>

  <!-- Step 3 -->
  <div class="step-card step-3">
    <div class="card-header">
      <span>3. Implementasi Program (Implementation)</span>
      <span class="fase-badge">FASE 3</span>
    </div>
    <div class="card-body">
      <ul>
        <li>Framework Web Laravel 11 & PHP 8.2</li>
        <li>Basis Data Relasional MySQL 8.x InnoDB</li>
        <li>Integrasi Midtrans Snap API (BCA VA)</li>
        <li>Integrasi WhatsApp Gateway Fonnte & SMTP</li>
      </ul>
      <div class="deliverable">
        <span class="deliv-tag">Luaran:</span>
        <span>Aplikasi Sistem Informasi Terintegrasi</span>
      </div>
    </div>
  </div>

  <!-- Step 4 -->
  <div class="step-card step-4">
    <div class="card-header">
      <span>4. Pengujian Sistem (System Testing)</span>
      <span class="fase-badge">FASE 4</span>
    </div>
    <div class="card-body">
      <ul>
        <li>Black Box Testing (60 Skenario Uji)</li>
        <li>Pengujian Isolasi Hak Akses Peran (RBAC)</li>
        <li>Simulasi Transaksi Midtrans Sandbox BCA VA</li>
        <li>Automated Unit Testing (PHPUnit 510 Tests)</li>
      </ul>
      <div class="deliverable">
        <span class="deliv-tag">Luaran:</span>
        <span>Matriks Pengujian Fungsionalitas & Keamanan</span>
      </div>
    </div>
  </div>

  <!-- Step 5 -->
  <div class="step-card step-5">
    <div class="card-header">
      <span>5. Penerapan & Evaluasi (Deployment & Evaluation)</span>
      <span class="fase-badge">FASE 5</span>
    </div>
    <div class="card-body">
      <ul>
        <li>Deployment Peladen Hostinger LiteSpeed</li>
        <li>Konfigurasi Otomasi Cron Job Penagihan</li>
        <li>Optimasi Performa Cache Produksi</li>
        <li>Usability Testing Pengelola & Responden Penghuni</li>
      </ul>
      <div class="deliverable">
        <span class="deliv-tag">Luaran:</span>
        <span>Sistem Beroperasi Live & Laporan Evaluasi UAT</span>
      </div>
    </div>
  </div>
</div>

</body>
</html>
'''

if __name__ == "__main__":
    target_png = os.path.join(SKRIPSI_IMAGES, "waterfall_model_diagram.png")
    print(f"Rendering concise waterfall directly to: {target_png}")
    success = render_html_to_png(html_concise_waterfall, target_png, width=1220, height=1260, scale=2.5)
    if success:
        img = Image.open(target_png)
        print(f"SUCCESS! Dimensions: {img.size}, bytes: {os.path.getsize(target_png)}")
    else:
        print("FAILED to render concise waterfall diagram.")
