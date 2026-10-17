import subprocess
import os
from PIL import Image
import numpy as np

WORKSPACE = r"c:\xampp\htdocs\asri-boarding-house"
CHROME_PATH = r"C:\Program Files\Google\Chrome\Application\chrome.exe"
SCRATCH_DIR = os.path.join(WORKSPACE, "scratch")
SKRIPSI_IMAGES = os.path.join(WORKSPACE, "Skripsi", "images")
os.makedirs(SCRATCH_DIR, exist_ok=True)
os.makedirs(SKRIPSI_IMAGES, exist_ok=True)

def render_html_to_png(html_content, output_png, width=1100, height=1700, scale=2.5):
    html_path = os.path.join(SCRATCH_DIR, "temp_render_4stage.html")
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

html_4stage = '''<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<style>
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body {
    background-color: #ffffff;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    color: #1e293b;
    padding: 30px 40px;
    width: 1100px;
  }
  
  .flow-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0;
  }

  /* STAGE BOX */
  .stage-box {
    width: 1000px;
    background: #ffffff;
    border-radius: 12px;
    border: 2px solid;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(15, 23, 42, 0.05);
  }

  .stage-header {
    padding: 12px 20px;
    font-size: 16px;
    font-weight: 800;
    color: #ffffff;
    text-align: center;
    letter-spacing: 0.8px;
    text-transform: uppercase;
  }

  .stage-content {
    padding: 16px 24px 18px 24px;
    background: #ffffff;
  }

  .section-title {
    font-size: 13.5px;
    font-weight: 700;
    color: #0f172a;
    margin-bottom: 6px;
    margin-top: 10px;
  }
  .section-title:first-child {
    margin-top: 0;
  }

  ul.item-list {
    list-style: none;
    margin-bottom: 4px;
  }
  ul.item-list li {
    font-size: 12.8px;
    color: #334155;
    line-height: 1.55;
    margin-bottom: 4px;
    position: relative;
    padding-left: 18px;
  }
  ul.item-list li::before {
    content: "•";
    position: absolute;
    left: 4px;
    font-size: 15px;
    color: #475569;
  }
  ul.item-list li b {
    font-weight: 600;
    color: #0f172a;
  }

  /* ARROW CONNECTOR */
  .arrow-connector {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 48px;
  }
  .arrow-connector svg {
    width: 22px;
    height: 38px;
    fill: #334155;
  }

  /* COLOR THEMES FOR 4 STAGES */
  /* Stage 1: Input */
  .box-input { border-color: #2b76c9; background: #f0f7ff; }
  .box-input .stage-header { background: #2b76c9; }
  .box-input .stage-content { background: #f4f9ff; }

  /* Stage 2: Process */
  .box-process { border-color: #278853; background: #effaf3; }
  .box-process .stage-header { background: #278853; }
  .box-process .stage-content { background: #f2fcf6; }

  /* Stage 3: Output */
  .box-output { border-color: #d49b28; background: #fffdf2; }
  .box-output .stage-header { background: #d49b28; }
  .box-output .stage-content { background: #fffdf5; }

  /* Stage 4: Outcome */
  .box-outcome { border-color: #7c4dcf; background: #faf6ff; }
  .box-outcome .stage-header { background: #7c4dcf; }
  .box-outcome .stage-content { background: #fbf8ff; }

</style>
</head>
<body>

  <div class="flow-container">

    <!-- 1. MASUKAN (INPUT) -->
    <div class="stage-box box-input">
      <div class="stage-header">1. MASUKAN (INPUT)</div>
      <div class="stage-content">
        <div class="section-title">Kondisi Operasional & Identifikasi Masalah:</div>
        <ul class="item-list">
          <li>Penagihan sewa bulanan manual yang berisiko terlambat & tanpa peringatan terjadwal</li>
          <li>Ketiadaan eskalasi keterlambatan penagihan sewa terstruktur ke kontak orang tua/wali</li>
          <li>Risiko pemesanan ganda (<i>double booking</i>) akibat ketiadaan sinkronisasi ketersediaan kamar</li>
          <li>Pencatatan kas bertumpu pada nota kertas fisik yang rentan selisih saldo & menyita waktu</li>
          <li>Ketiadaan katalog publik serta modul reservasi daring 24/7 bagi calon penyewa luar daerah</li>
          <li>Ketiadaan rekam jejak digital (<i>audit trail</i>) atas riwayat transaksi penerimaan kas</li>
        </ul>

        <div class="section-title">Spesifikasi Kebutuhan Sistem:</div>
        <ul class="item-list">
          <li>Analisis spesifikasi kebutuhan fungsional & non-fungsional dari triangulasi data empiris</li>
          <li>Struktur tarif 3 tipe kamar (VIP, Deluxe, Standar) untuk kapasitas 32 unit kamar kost</li>
          <li>Ketentuan siklus tagihan rutin tanggal 1, jatuh tempo tanggal 10, dan aturan denda flat 5%</li>
        </ul>
      </div>
    </div>

    <!-- ARROW 1 -> 2 -->
    <div class="arrow-connector">
      <svg viewBox="0 0 24 38">
        <line x1="12" y1="2" x2="12" y2="30" stroke="#334155" stroke-width="2.5" stroke-linecap="round"/>
        <polyline points="6,24 12,32 18,24" fill="none" stroke="#334155" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
    </div>

    <!-- 2. PROSES (PROCESS) -->
    <div class="stage-box box-process">
      <div class="stage-header">2. PROSES (PROCESS)</div>
      <div class="stage-content">
        <div class="section-title">Analisis & Perancangan:</div>
        <ul class="item-list">
          <li>Pemodelan UML: <i>Use Case, Activity, Sequence,</i> dan <i>Flowchart Diagram</i></li>
          <li>Perancangan arsitektur perangkat lunak <i>3-tier</i> MVC dengan <i>Service Layer Pattern</i></li>
          <li>Perancangan basis data relasional ternormalisasi 3NF dalam ERD 22 tabel</li>
          <li>Rancangan antarmuka pengguna Neo-Brutalism berpedoman aksesibilitas WCAG 2.1</li>
        </ul>

        <div class="section-title">Implementasi Teknologis:</div>
        <ul class="item-list">
          <li>Framework & Database: Laravel 11 pada PHP 8.2, MySQL 8.x InnoDB, dan TailwindCSS</li>
          <li>Penerapan <i>Pessimistic Row Locking</i> untuk proteksi konkurensi kamar saat reservasi</li>
          <li>Integrasi Payment Gateway: Midtrans Snap API (saluran Bank BCA Virtual Account)</li>
          <li>Integrasi Komunikasi Multi-Saluran: Fonnte WhatsApp Gateway API & SMTP surel</li>
        </ul>

        <div class="section-title">Pengujian & Validasi:</div>
        <ul class="item-list">
          <li>Pengujian fungsional kotak hitam (<i>Black Box Testing</i>) berbasis 60 butir skenario uji</li>
          <li>Pengujian isolasi peran hak akses (RBAC 3 portal) guna menangkal kerentanan IDOR</li>
          <li>Pengujian otomatis unit & integrasi aplikasi menggunakan PHPUnit (510 <i>tests passed</i>)</li>
          <li>Penerapan sistem live pada peladen Hostinger LiteSpeed berproteksi SSL TLS 1.3 Grade A</li>
          <li>Evaluasi pengguna: <i>Side-by-Side Usability Testing</i> pengelola & wawancara 5 mahasiswa</li>
        </ul>
      </div>
    </div>

    <!-- ARROW 2 -> 3 -->
    <div class="arrow-connector">
      <svg viewBox="0 0 24 38">
        <line x1="12" y1="2" x2="12" y2="30" stroke="#334155" stroke-width="2.5" stroke-linecap="round"/>
        <polyline points="6,24 12,32 18,24" fill="none" stroke="#334155" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
    </div>

    <!-- 3. KELUARAN (OUTPUT) -->
    <div class="stage-box box-output">
      <div class="stage-header">3. KELUARAN (OUTPUT)</div>
      <div class="stage-content">
        <div class="section-title">Artefak Perangkat Lunak Teruji:</div>
        <ul class="item-list">
          <li>Sistem Informasi Manajemen Kost Terintegrasi Payment Gateway live (<b>asriboardinghouse.weatso.id</b>)</li>
          <li>Modul katalog publik interaktif & formulir reservasi online mandiri 24/7 calon penyewa</li>
          <li>Portal mandiri penyewa aktif untuk memantau invoice, bayar BCA VA, & unduh kuitansi PDF A5</li>
          <li>Panel administrator pengawasan kamar real-time, pencatatan mutasi kas, & ekspor laporan</li>
          <li>Mesin penagihan otomatis (<i>automated billing engine</i>) berbasis <i>cron job</i> & notifikasi WhatsApp</li>
        </ul>
      </div>
    </div>

    <!-- ARROW 3 -> 4 -->
    <div class="arrow-connector">
      <svg viewBox="0 0 24 38">
        <line x1="12" y1="2" x2="12" y2="30" stroke="#334155" stroke-width="2.5" stroke-linecap="round"/>
        <polyline points="6,24 12,32 18,24" fill="none" stroke="#334155" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
    </div>

    <!-- 4. DAMPAK (OUTCOME) -->
    <div class="stage-box box-outcome">
      <div class="stage-header">4. DAMPAK (OUTCOME)</div>
      <div class="stage-content">
        <div class="section-title">Manfaat & Dampak Operasional:</div>
        <ul class="item-list">
          <li>Otomatisasi siklus penagihan rutin bulanan & eskalasi penunggakan ke kontak wali mahasiswa</li>
          <li>Penjaminan integritas & akurasi rekapitulasi keuangan tanpa risiko selisih kas (<i>Zero Cash Variance</i>)</li>
          <li>Pemangkasan durasi penyusunan laporan laba-rugi dari 3–5 hari kerja manual menjadi seketika (instan)</li>
          <li>Peniadaan mutlak atas risiko pemesanan ganda (<i>Zero Double-Booking</i>) kamar kost</li>
          <li>Kemudahan akses reservasi daring 24/7 serta peningkatan transparansi transaksi bagi penghuni</li>
        </ul>
      </div>
    </div>

  </div>

</body>
</html>
'''

if __name__ == "__main__":
    out_file = os.path.join(SKRIPSI_IMAGES, "kerangka_pemikiran_diagram.png")
    print("Rendering 4-stage diagram directly to:", out_file)
    success = render_html_to_png(html_4stage, out_file, width=1100, height=1700, scale=2.5)
    if success:
        im = Image.open(out_file)
        print("SUCCESS! Dimensions:", im.size, "File size:", os.path.getsize(out_file), "bytes")
