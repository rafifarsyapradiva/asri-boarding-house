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

def render_html_to_png(html_content, output_png, width=1360, height=1920, scale=2.5):
    html_path = os.path.join(SCRATCH_DIR, "temp_kerangka_pemikiran.html")
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
        print(f"Rendered raw: {os.path.basename(output_png)} ({img.size[0]}x{img.size[1]})")
        
        # Auto-crop to content bounding box with balanced padding
        arr = np.array(img.convert('L'))
        # non-white pixels
        mask = arr < 252
        coords = np.argwhere(mask)
        if len(coords) > 0:
            y0, x0 = coords.min(axis=0)
            y1, x1 = coords.max(axis=0)
            
            # Add 35px padding (scaled)
            pad_x = int(35 * scale)
            pad_y = int(35 * scale)
            
            crop_x0 = max(0, x0 - pad_x)
            crop_y0 = max(0, y0 - pad_y)
            crop_x1 = min(img.size[0], x1 + pad_x)
            crop_y1 = min(img.size[1], y1 + pad_y)
            
            cropped = img.crop((crop_x0, crop_y0, crop_x1, crop_y1))
            # Convert to RGB to ensure no weird alpha rendering issues
            rgb_img = Image.new("RGB", cropped.size, (255, 255, 255))
            if cropped.mode == 'RGBA':
                rgb_img.paste(cropped, mask=cropped.split()[3])
            else:
                rgb_img.paste(cropped)
            
            rgb_img.save(output_png, "PNG", optimize=True)
            print(f"Cropped & saved: {os.path.basename(output_png)} ({rgb_img.size[0]}x{rgb_img.size[1]})")
        return True
    return False

html_kerangka_perfected = '''<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<style>
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body {
    background-color: #ffffff;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    color: #0f172a;
    padding: 30px 36px 32px 36px;
    width: 1360px;
    height: auto;
    position: relative;
  }
  
  /* HEADER */
  .header {
    text-align: center;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 2px solid #e2e8f0;
    position: relative;
  }
  .badge-container {
    display: flex;
    justify-content: center;
    gap: 12px;
    margin-bottom: 8px;
  }
  .badge {
    display: inline-block;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 1.2px;
    padding: 4px 14px;
    border-radius: 9999px;
    text-transform: uppercase;
  }
  .badge-primary { background: #dbeafe; color: #1e40af; border: 1px solid #bfdbfe; }
  .badge-accent { background: #fae8ff; color: #86198f; border: 1px solid #f5d0fe; }
  .badge-neutral { background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; }

  h1 {
    font-size: 24px;
    font-weight: 800;
    color: #0f172a;
    letter-spacing: -0.4px;
    margin-bottom: 5px;
  }
  .subtitle {
    font-size: 13.5px;
    color: #475569;
    font-weight: 500;
  }

  /* FLOW CONTAINER */
  .flow-container {
    display: flex;
    flex-direction: column;
    gap: 13px;
  }

  /* STAGE CARD */
  .stage-card {
    background: #ffffff;
    border-radius: 11px;
    border: 1.5px solid;
    box-shadow: 0 3px 10px -2px rgba(15, 23, 42, 0.06), 0 1px 4px -1px rgba(15, 23, 42, 0.03);
    overflow: hidden;
  }

  .stage-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 8px 16px;
    color: #ffffff;
    font-weight: 700;
  }
  .stage-title-left {
    display: flex;
    align-items: center;
    gap: 10px;
  }
  .stage-number {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 25px;
    height: 25px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.28);
    font-size: 13px;
    font-weight: 800;
    border: 1.5px solid rgba(255, 255, 255, 0.5);
  }
  .stage-title-text {
    font-size: 14.5px;
    letter-spacing: 0.2px;
  }
  .stage-tag {
    font-size: 11px;
    font-weight: 700;
    padding: 2px 10px;
    border-radius: 6px;
    background: rgba(255, 255, 255, 0.2);
    border: 1px solid rgba(255, 255, 255, 0.35);
    letter-spacing: 0.5px;
    text-transform: uppercase;
  }

  .stage-body {
    padding: 11px 15px 10px 15px;
  }

  /* GRID COLUMNS */
  .grid-2 {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 14px;
  }
  .grid-3 {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
    gap: 12px;
  }
  .grid-4 {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 10px;
  }

  /* INNER BOX */
  .inner-box {
    background: #f8fafc;
    border-radius: 8px;
    padding: 8px 12px;
    border: 1px solid #e2e8f0;
  }
  .inner-box-title {
    font-size: 11.8px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.6px;
    margin-bottom: 5px;
    display: flex;
    align-items: center;
    gap: 6px;
  }
  .inner-box-icon {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    display: inline-block;
  }

  ul.item-list {
    list-style: none;
  }
  ul.item-list li {
    font-size: 12px;
    color: #334155;
    line-height: 1.48;
    margin-bottom: 4px;
    position: relative;
    padding-left: 14px;
  }
  ul.item-list li::before {
    content: "•";
    position: absolute;
    left: 2px;
    font-weight: bold;
  }
  ul.item-list li b {
    color: #0f172a;
    font-weight: 600;
  }

  /* DELIVERABLE PILL */
  .deliverable-bar {
    margin-top: 8px;
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 11.5px;
    font-weight: 600;
    padding: 5px 12px;
    border-radius: 6px;
  }
  .deliverable-label {
    text-transform: uppercase;
    font-size: 10px;
    font-weight: 800;
    letter-spacing: 0.5px;
    padding: 1px 6px;
    border-radius: 4px;
  }

  /* CONNECTOR ARROWS */
  .flow-arrow-wrap {
    display: flex;
    align-items: center;
    justify-content: center;
    margin: -5px 0;
    z-index: 10;
  }
  .flow-arrow-badge {
    background: #ffffff;
    border: 1.5px solid #cbd5e1;
    border-radius: 9999px;
    padding: 2px 14px;
    font-size: 11px;
    font-weight: 700;
    color: #475569;
    display: flex;
    align-items: center;
    gap: 6px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.06);
  }
  .flow-arrow-badge svg {
    width: 12px;
    height: 12px;
    stroke: #64748b;
  }

  /* COLOR THEMES FOR 6 PHASES */
  /* 1. INPUT */
  .stage-input { border-color: #2563eb; }
  .stage-input .stage-header { background: linear-gradient(135deg, #1d4ed8, #2563eb); }
  .stage-input .inner-box-title { color: #1e40af; }
  .stage-input .inner-box-icon { background: #2563eb; }
  .stage-input ul.item-list li::before { color: #2563eb; }
  .stage-input .deliverable-bar { background: #eff6ff; color: #1e40af; border: 1px solid #bfdbfe; }
  .stage-input .deliverable-label { background: #dbeafe; color: #1e40af; }

  /* 2. DATA COLLECTION */
  .stage-data { border-color: #9333ea; }
  .stage-data .stage-header { background: linear-gradient(135deg, #7e22ce, #9333ea); }
  .stage-data .inner-box-title { color: #7e22ce; }
  .stage-data .inner-box-icon { background: #9333ea; }
  .stage-data ul.item-list li::before { color: #9333ea; }
  .stage-data .deliverable-bar { background: #faf5ff; color: #7e22ce; border: 1px solid #e9d5ff; }
  .stage-data .deliverable-label { background: #f3e8ff; color: #7e22ce; }

  /* 3. PROCESS WATERFALL */
  .stage-process { border-color: #059669; }
  .stage-process .stage-header { background: linear-gradient(135deg, #047857, #059669); }
  .stage-process .inner-box-title { color: #047857; }
  .stage-process .inner-box-icon { background: #059669; }
  .stage-process ul.item-list li::before { color: #059669; }
  .stage-process .deliverable-bar { background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; }
  .stage-process .deliverable-label { background: #d1fae5; color: #047857; }

  /* 4. OUTPUT & LIVE DEPLOYMENT */
  .stage-output { border-color: #d97706; }
  .stage-output .stage-header { background: linear-gradient(135deg, #b45309, #d97706); }
  .stage-output .inner-box-title { color: #b45309; }
  .stage-output .inner-box-icon { background: #d97706; }
  .stage-output ul.item-list li::before { color: #d97706; }
  .stage-output .deliverable-bar { background: #fffbeb; color: #b45309; border: 1px solid #fde68a; }
  .stage-output .deliverable-label { background: #fef3c7; color: #b45309; }

  /* 5. EVALUATION & TESTING */
  .stage-eval { border-color: #0891b2; }
  .stage-eval .stage-header { background: linear-gradient(135deg, #0e7490, #0891b2); }
  .stage-eval .inner-box-title { color: #0e7490; }
  .stage-eval .inner-box-icon { background: #0891b2; }
  .stage-eval ul.item-list li::before { color: #0891b2; }
  .stage-eval .deliverable-bar { background: #ecfeff; color: #0e7490; border: 1px solid #a5f3fc; }
  .stage-eval .deliverable-label { background: #cffafe; color: #0e7490; }

  /* 6. OUTCOME */
  .stage-outcome { border-color: #4f46e5; }
  .stage-outcome .stage-header { background: linear-gradient(135deg, #3730a3, #4f46e5); }
  .stage-outcome .inner-box-title { color: #3730a3; }
  .stage-outcome .inner-box-icon { background: #4f46e5; }
  .stage-outcome ul.item-list li::before { color: #4f46e5; }
  .stage-outcome .deliverable-bar { background: #eef2ff; color: #3730a3; border: 1px solid #c7d2fe; }
  .stage-outcome .deliverable-label { background: #e0e7ff; color: #3730a3; }

  /* FOOTER */
  .footer {
    margin-top: 14px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 10px;
    border-top: 1.5px solid #e2e8f0;
    font-size: 11.5px;
    color: #64748b;
  }
  .footer-bold {
    font-weight: 700;
    color: #334155;
  }
</style>
</head>
<body>

  <!-- HEADER -->
  <div class="header">
    <div class="badge-container">
      <span class="badge badge-primary">Kerangka Pemikiran Penelitian</span>
      <span class="badge badge-accent">Sistem Informasi Manajemen Kost</span>
      <span class="badge badge-neutral">Metodologi R&D Waterfall</span>
    </div>
    <h1>ALUR KERANGKA PEMIKIRAN PENELITIAN & REKAYASA SISTEM</h1>
    <p class="subtitle">Sistem Informasi Manajemen Kost Terintegrasi Payment Gateway pada Asri Boarding House (Tugas Akhir - 2026)</p>
  </div>

  <div class="flow-container">

    <!-- 1. MASUKAN (INPUT) -->
    <div class="stage-card stage-input">
      <div class="stage-header">
        <div class="stage-title-left">
          <span class="stage-number">1</span>
          <span class="stage-title-text">MASUKAN (INPUT) — Masalah Lapangan & Landasan Kebutuhan</span>
        </div>
        <span class="stage-tag">Identifikasi Faktual</span>
      </div>
      <div class="stage-body">
        <div class="grid-2">
          <div class="inner-box">
            <div class="inner-box-title"><span class="inner-box-icon"></span>Permasalahan Operasional Lapangan:</div>
            <ul class="item-list">
              <li><b>Penagihan Sewa Manual:</b> Rawan lupa/terlewat tanpa eskalasi berjenjang ke kontak orang tua/wali.</li>
              <li><b>Risiko Pemesanan Ganda:</b> Ketiadaan sinkronisasi ketersediaan kamar memicu <i>double booking</i>.</li>
              <li><b>Pembukuan Nota Kertas:</b> Rekapitulasi kas fisik rentan selisih saldo & menyita 3–5 hari kerja.</li>
              <li><b>Ketiadaan Reservasi Online:</b> Calon penyewa luar daerah kesulitan akses katalog & reservasi 24/7.</li>
              <li><b>Ketiadaan Jejak Digital:</b> Belum adanya rekam jejak audit transaksi (<i>audit trail</i>) yang transparan.</li>
            </ul>
          </div>
          <div class="inner-box">
            <div class="inner-box-title"><span class="inner-box-icon"></span>Landasan Kebutuhan & Regulasi Bisnis:</div>
            <ul class="item-list">
              <li><b>Kebutuhan Fungsional & Non-Fungsional:</b> Spesifikasi modul terperinci berdasarkan triangulasi data.</li>
              <li><b>Struktur Tarif 3 Tipe Kamar:</b> Standard Room, Deluxe Room, dan VIP Room (32 unit kapasitas).</li>
              <li><b>Regulasi Finansial Terikat Waktu:</b> Siklus billing tgl 1, batas tempo tgl 10, denda flat 5% kalender.</li>
              <li><b>Kebutuhan Multi-Peran:</b> Isolasi portal Tamu/Calon Penyewa, Penyewa Aktif, dan Administrator.</li>
            </ul>
          </div>
        </div>
        <div class="deliverable-bar">
          <span class="deliverable-label">Landasan Awal:</span>
          Rumusan masalah operasional kos & dokumen spesifikasi kebutuhan sistem rekayasa perangkat lunak.
        </div>
      </div>
    </div>

    <!-- ARROW 1 -> 2 -->
    <div class="flow-arrow-wrap">
      <div class="flow-arrow-badge">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><polyline points="19 12 12 19 5 12"></polyline></svg>
        Metode Pengumpulan Fakta & Data Empiris
      </div>
    </div>

    <!-- 2. PENGUMPULAN DATA EMPIRIS -->
    <div class="stage-card stage-data">
      <div class="stage-header">
        <div class="stage-title-left">
          <span class="stage-number">2</span>
          <span class="stage-title-text">PENGUMPULAN DATA EMPIRIS — Triangulasi Empat Pilar</span>
        </div>
        <span class="stage-tag">Metodologi Data</span>
      </div>
      <div class="stage-body">
        <div class="grid-4">
          <div class="inner-box">
            <div class="inner-box-title"><span class="inner-box-icon"></span>Observasi Langsung</div>
            <ul class="item-list">
              <li>Pengamatan alur harian kos.</li>
              <li>Pemetaan fisik <b>32 unit kamar</b>.</li>
              <li>Validasi SOP check-in/out.</li>
            </ul>
          </div>
          <div class="inner-box">
            <div class="inner-box-title"><span class="inner-box-icon"></span>Wawancara Pengelola</div>
            <ul class="item-list">
              <li>Pengelola senior (±20 th kerja).</li>
              <li>Identifikasi titik rawan kasir.</li>
              <li>Regulasi penagihan & denda.</li>
            </ul>
          </div>
          <div class="inner-box">
            <div class="inner-box-title"><span class="inner-box-icon"></span>Wawancara Mahasiswa</div>
            <ul class="item-list">
              <li><b>5 mahasiswa penghuni aktif</b>.</li>
              <li>Preferensi VA non-tunai.</li>
              <li>Kebutuhan kuitansi digital.</li>
            </ul>
          </div>
          <div class="inner-box">
            <div class="inner-box-title"><span class="inner-box-icon"></span>Studi Dokumentasi</div>
            <ul class="item-list">
              <li>Arsip buku kas fisik kos.</li>
              <li>Format kuitansi kertas lama.</li>
              <li>Blueprint spesifikasi sistem.</li>
            </ul>
          </div>
        </div>
        <div class="deliverable-bar">
          <span class="deliverable-label">Keluaran Data:</span>
          Dataset empiris komprehensif, pemetaan alur bisnis kos, dan baseline evaluasi efektivitas sistem.
        </div>
      </div>
    </div>

    <!-- ARROW 2 -> 3 -->
    <div class="flow-arrow-wrap">
      <div class="flow-arrow-badge">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><polyline points="19 12 12 19 5 12"></polyline></svg>
        Siklus Rekayasa Perangkat Lunak Sekuensial Linier
      </div>
    </div>

    <!-- 3. PROSES PENGEMBANGAN SISTEM (SDLC WATERFALL) -->
    <div class="stage-card stage-process">
      <div class="stage-header">
        <div class="stage-title-left">
          <span class="stage-number">3</span>
          <span class="stage-title-text">PROSES PENGEMBANGAN SISTEM — Model SDLC Waterfall</span>
        </div>
        <span class="stage-tag">Rekayasa Software</span>
      </div>
      <div class="stage-body">
        <div class="grid-2">
          <div class="inner-box">
            <div class="inner-box-title"><span class="inner-box-icon"></span>Analisis & Perancangan Arsitektur:</div>
            <ul class="item-list">
              <li><b>Arsitektur 3-Tier MVC:</b> Dilengkapi <i>Service Layer Pattern</i> untuk isolasi logika bisnis.</li>
              <li><b>Pemodelan UML Lengkap:</b> Use Case Diagram, Activity Diagram, Flowchart, & Sequence Diagram.</li>
              <li><b>Normalisasi Basis Data 3NF:</b> Skema relasional kokoh terstruktur dalam ERD 22 tabel.</li>
              <li><b>Rancangan UI Neo-Brutalism:</b> Sesuai standar WCAG 2.1 dengan target sentuh aman (≥44px).</li>
            </ul>
          </div>
          <div class="inner-box">
            <div class="inner-box-title"><span class="inner-box-icon"></span>Implementasi Rekayasa Kode:</div>
            <ul class="item-list">
              <li><b>Backend Framework:</b> Laravel 11 pada PHP 8.2 terstruktur rapi dengan Eloquent ORM.</li>
              <li><b>Basis Data Transaksional:</b> MySQL 8.x InnoDB didukung <i>Pessimistic Row Locking</i>.</li>
              <li><b>Integrasi Payment Gateway:</b> Midtrans Snap API (BCA Virtual Account & Verifikasi Webhook).</li>
              <li><b>Multi-Channel Notification:</b> Fonnte WhatsApp Gateway API & SMTP Surel resmi.</li>
            </ul>
          </div>
        </div>
        <div class="deliverable-bar">
          <span class="deliverable-label">Hasil Tahapan:</span>
          Basis kode sistem informasi terintegrasi, skema basis data relasional 3NF, dan integrasi API eksternal.
        </div>
      </div>
    </div>

    <!-- ARROW 3 -> 4 -->
    <div class="flow-arrow-wrap">
      <div class="flow-arrow-badge">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><polyline points="19 12 12 19 5 12"></polyline></svg>
        Penerapan Nyata pada Peladen Produksi Live
      </div>
    </div>

    <!-- 4. KELUARAN & PENERAPAN SISTEM -->
    <div class="stage-card stage-output">
      <div class="stage-header">
        <div class="stage-title-left">
          <span class="stage-number">4</span>
          <span class="stage-title-text">KELUARAN & PENERAPAN SISTEM — Artefak Web Live Produksi</span>
        </div>
        <span class="stage-tag">Artefak Fungsional</span>
      </div>
      <div class="stage-body">
        <div class="grid-4">
          <div class="inner-box">
            <div class="inner-box-title"><span class="inner-box-icon"></span>Portal Calon Tamu</div>
            <ul class="item-list">
              <li>Katalog kamar interaktif.</li>
              <li>Filter fasilitas & harga.</li>
              <li>Formulir reservasi 24/7.</li>
              <li>Pembayaran DP via Snap VA.</li>
            </ul>
          </div>
          <div class="inner-box">
            <div class="inner-box-title"><span class="inner-box-icon"></span>Portal Penyewa Aktif</div>
            <ul class="item-list">
              <li>Dashboard tagihan sewa.</li>
              <li>Bayar mandiri BCA VA.</li>
              <li>Kuitansi digital instan A5.</li>
              <li>Pelaporan keluhan fasilitas.</li>
            </ul>
          </div>
          <div class="inner-box">
            <div class="inner-box-title"><span class="inner-box-icon"></span>Panel Administrator</div>
            <ul class="item-list">
              <li>Manajemen 32 kamar real-time.</li>
              <li>Rekapitulasi kas instan.</li>
              <li>Persetujuan bukti bayar & DP.</li>
              <li>Audit trail & ekspor laporan.</li>
            </ul>
          </div>
          <div class="inner-box">
            <div class="inner-box-title"><span class="inner-box-icon"></span>Automated Engine</div>
            <ul class="item-list">
              <li>Cron job server tgl 1 (billing).</li>
              <li>Auto-denda flat 5% tgl 11.</li>
              <li>Auto-notifikasi tagihan WA.</li>
              <li>Eskalasi pesan ke wali kos.</li>
            </ul>
          </div>
        </div>
        <div class="deliverable-bar">
          <span class="deliverable-label">Artefak Operasional:</span>
          Sistem live pada peladen Hostinger LiteSpeed (<b>https://asriboardinghouse.weatso.id/</b>) berproteksi TLS 1.3 Grade A.
        </div>
      </div>
    </div>

    <!-- ARROW 4 -> 5 -->
    <div class="flow-arrow-wrap">
      <div class="flow-arrow-badge">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><polyline points="19 12 12 19 5 12"></polyline></svg>
        Pengujian Fungsional, Evaluasi Keamanan, & Uji Penerimaan Pengguna
      </div>
    </div>

    <!-- 5. PENGUJIAN & EVALUASI SISTEM -->
    <div class="stage-card stage-eval">
      <div class="stage-header">
        <div class="stage-title-left">
          <span class="stage-number">5</span>
          <span class="stage-title-text">PENGUJIAN & EVALUASI SISTEM — Menjawab Tiga Rumusan Masalah</span>
        </div>
        <span class="stage-tag">Verifikasi & Validasi</span>
      </div>
      <div class="stage-body">
        <div class="grid-3">
          <div class="inner-box">
            <div class="inner-box-title"><span class="inner-box-icon"></span>RM 1: Mutu & Keandalan Fungsional</div>
            <ul class="item-list">
              <li><b>Black Box Testing:</b> 60 skenario fungsional teruji (100% lulus tanpa kegagalan).</li>
              <li><b>Isolasi RBAC:</b> 3 portal bebas eksploitasi IDOR.</li>
              <li><b>Automated Testing:</b> 510 pengujian PHPUnit (2.211 assertions) lulus 100%.</li>
              <li><b>Simulasi Transaksi:</b> Midtrans Sandbox BCA VA valid.</li>
            </ul>
          </div>
          <div class="inner-box">
            <div class="inner-box-title"><span class="inner-box-icon"></span>RM 2: Efektivitas Solusi Operasional</div>
            <ul class="item-list">
              <li><b>Billing Otomatis:</b> Tagihan terbit tgl 1 & denda kalender flat 5% terhitung idempoten.</li>
              <li><b>Zero Cash Variance:</b> Presisi kas 100% tanpa selisih, memangkas pembukuan dari 3–5 hari.</li>
              <li><b>Zero Double-Booking:</b> Penguncian baris pesimistik & karantina kamar pasca-checkout.</li>
            </ul>
          </div>
          <div class="inner-box">
            <div class="inner-box-title"><span class="inner-box-icon"></span>RM 3: Evaluasi Penerimaan Pengguna</div>
            <ul class="item-list">
              <li><b>Side-by-Side Usability Testing:</b> Pengujian bersama pengelola kos (Skor kepuasan: <b>9,5 / 10</b>).</li>
              <li><b>Wawancara 5 Penghuni Kost:</b> Evaluasi kenyamanan reservasi, bayar mandiri, & unduh kuitansi A5.</li>
              <li><b>Tingkat Adopsi:</b> 100% transaksi live tercatat rapi.</li>
            </ul>
          </div>
        </div>
        <div class="deliverable-bar">
          <span class="deliverable-label">Bukti Evaluasi:</span>
          Sistem terverifikasi bebas cacat fungsional, terbukti mengatasi kendala operasional, dan diterima sangat baik oleh pengguna.
        </div>
      </div>
    </div>

    <!-- ARROW 5 -> 6 -->
    <div class="flow-arrow-wrap">
      <div class="flow-arrow-badge">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><polyline points="19 12 12 19 5 12"></polyline></svg>
        Pencapaian Manfaat & Peningkatan Kinerja Nyata
      </div>
    </div>

    <!-- 6. DAMPAK OPERASIONAL (OUTCOME) -->
    <div class="stage-card stage-outcome">
      <div class="stage-header">
        <div class="stage-title-left">
          <span class="stage-number">6</span>
          <span class="stage-title-text">DAMPAK OPERASIONAL (OUTCOME) — Manfaat & Nilai Tambah Nyata</span>
        </div>
        <span class="stage-tag">Dampak & Hasil Akhir</span>
      </div>
      <div class="stage-body">
        <div class="grid-4">
          <div class="inner-box">
            <div class="inner-box-title"><span class="inner-box-icon"></span>Otomatisasi Penagihan</div>
            <ul class="item-list">
              <li>Tagihan rutin otomatis tgl 1.</li>
              <li>Pengingat WA terjadwal.</li>
              <li>Eskalasi tunggakan ke wali.</li>
              <li>Disiplin pembayaran naik.</li>
            </ul>
          </div>
          <div class="inner-box">
            <div class="inner-box-title"><span class="inner-box-icon"></span>Ketepatan Kas 100%</div>
            <ul class="item-list">
              <li>Bebas risiko selisih uang.</li>
              <li>Rekap kasir real-time.</li>
              <li>Laporan laba-rugi instan.</li>
              <li>Pangkas 3–5 hari kerja.</li>
            </ul>
          </div>
          <div class="inner-box">
            <div class="inner-box-title"><span class="inner-box-icon"></span>Nol Pemesanan Ganda</div>
            <ul class="item-list">
              <li>Pencegahan double-booking.</li>
              <li>Karantina kamar otomatis.</li>
              <li>Status kamar sinkron 100%.</li>
              <li>Kapasitas 32 kamar optimal.</li>
            </ul>
          </div>
          <div class="inner-box">
            <div class="inner-box-title"><span class="inner-box-icon"></span>Kenyamanan & Transparansi</div>
            <ul class="item-list">
              <li>Reservasi mandiri 24/7.</li>
              <li>Bayar instan via BCA VA.</li>
              <li>Kuitansi digital instan A5.</li>
              <li>Kepuasan pengelola 9,5/10.</li>
            </ul>
          </div>
        </div>
        <div class="deliverable-bar">
          <span class="deliverable-label">Dampak Strategis:</span>
          Transformasi digital menyeluruh: efisiensi waktu pembukuan, kepastian arus kas, peniadaan komplain kamar, dan modernisasi tata kelola kos.
        </div>
      </div>
    </div>

  </div>

  <!-- FOOTER -->
  <div class="footer">
    <div><span class="footer-bold">Gambar 3.2:</span> Kerangka Pemikiran Penelitian Asri Boarding House</div>
    <div>Sumber: Rancangan Penulis (2026) | Metodologi Rekayasa R&D & SDLC Waterfall</div>
  </div>

</body>
</html>
'''

if __name__ == "__main__":
    out_file = os.path.join(SKRIPSI_IMAGES, "kerangka_pemikiran_diagram.png")
    print("Rendering final perfected image directly to:", out_file)
    success = render_html_to_png(html_kerangka_perfected, out_file, width=1360, height=1950, scale=2.5)
    if success:
        im = Image.open(out_file)
        print("SUCCESS! Final Dimensions:", im.size, "File size:", os.path.getsize(out_file), "bytes")
