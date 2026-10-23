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

def render_html_to_png(html_content, output_png, width=950, height=1300, scale=2.5):
    html_path = os.path.join(SCRATCH_DIR, "temp_render_bw_bold.html")
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
            pad_x = int(30 * scale)
            pad_y = int(25 * scale)
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

html_bw_bold = '''<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<style>
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body {
    background-color: #ffffff;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    color: #000000;
    padding: 30px;
    width: 900px;
  }
  
  .flow-container {
    display: flex;
    flex-direction: column;
    align-items: center;
  }

  /* STAGE BOX */
  .stage-box {
    width: 800px;
    background: #ffffff;
    border-radius: 8px;
    border: 3px solid #000000;
    overflow: hidden;
  }

  .stage-header {
    background: #000000;
    padding: 12px 20px;
    font-size: 19px;
    font-weight: 900;
    color: #ffffff;
    text-align: center;
    letter-spacing: 1px;
    text-transform: uppercase;
  }

  .stage-content {
    padding: 18px 30px 20px 30px;
    background: #ffffff;
  }

  ul.item-list {
    list-style: none;
    margin: 0;
    padding: 0;
  }

  ul.item-list li {
    font-size: 17px;
    font-weight: 800;
    color: #000000;
    line-height: 1.6;
    margin-bottom: 8px;
    position: relative;
    padding-left: 28px;
  }

  ul.item-list li:last-child {
    margin-bottom: 0;
  }

  ul.item-list li::before {
    content: "•";
    position: absolute;
    left: 4px;
    font-size: 26px;
    line-height: 1;
    top: -2px;
    color: #000000;
  }

  /* ARROW CONNECTOR */
  .arrow-connector {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 48px;
  }
  .arrow-connector svg {
    width: 24px;
    height: 38px;
  }
</style>
</head>
<body>

  <div class="flow-container">

    <!-- MASUKAN (INPUT) -->
    <div class="stage-box">
      <div class="stage-header">MASUKAN (INPUT)</div>
      <div class="stage-content">
        <ul class="item-list">
          <li>Kondisi operasional</li>
          <li>Identifikasi masalah</li>
          <li>Spesifikasi kebutuhan sistem</li>
        </ul>
      </div>
    </div>

    <!-- ARROW 1 -> 2 -->
    <div class="arrow-connector">
      <svg viewBox="0 0 24 38">
        <line x1="12" y1="2" x2="12" y2="28" stroke="#000000" stroke-width="3.5" stroke-linecap="round"/>
        <polyline points="5,21 12,31 19,21" fill="none" stroke="#000000" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
    </div>

    <!-- PROSES (PROCESS) -->
    <div class="stage-box">
      <div class="stage-header">PROSES (PROCESS)</div>
      <div class="stage-content">
        <ul class="item-list">
          <li>Analisis & Perancangan</li>
          <li>Implementasi Sistem</li>
          <li>Pengujian & Validasi</li>
        </ul>
      </div>
    </div>

    <!-- ARROW 2 -> 3 -->
    <div class="arrow-connector">
      <svg viewBox="0 0 24 38">
        <line x1="12" y1="2" x2="12" y2="28" stroke="#000000" stroke-width="3.5" stroke-linecap="round"/>
        <polyline points="5,21 12,31 19,21" fill="none" stroke="#000000" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
    </div>

    <!-- KELUARAN (OUTPUT) -->
    <div class="stage-box">
      <div class="stage-header">KELUARAN (OUTPUT)</div>
      <div class="stage-content">
        <ul class="item-list">
          <li>Artefak perangkat lunak teruji</li>
        </ul>
      </div>
    </div>

    <!-- ARROW 3 -> 4 -->
    <div class="arrow-connector">
      <svg viewBox="0 0 24 38">
        <line x1="12" y1="2" x2="12" y2="28" stroke="#000000" stroke-width="3.5" stroke-linecap="round"/>
        <polyline points="5,21 12,31 19,21" fill="none" stroke="#000000" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
    </div>

    <!-- DAMPAK (OUTCOME) -->
    <div class="stage-box">
      <div class="stage-header">DAMPAK (OUTCOME)</div>
      <div class="stage-content">
        <ul class="item-list">
          <li>Manfaat & dampak bagi operasional</li>
        </ul>
      </div>
    </div>

  </div>

</body>
</html>
'''

if __name__ == "__main__":
    out_file = os.path.join(SKRIPSI_IMAGES, "kerangka_pemikiran_bw.png")
    print("Rendering kerangka pemikiran B&W bold to:", out_file)
    success = render_html_to_png(html_bw_bold, out_file, width=950, height=1300, scale=2.5)
    if success:
        im = Image.open(out_file)
        print("SUCCESS! Dimensions:", im.size, "File size:", os.path.getsize(out_file), "bytes")
    else:
        print("FAILED to render image.")
