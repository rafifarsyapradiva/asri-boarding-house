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

def render_html_to_png(html_content, output_png, width=1150, height=800, scale=2.5):
    html_path = os.path.join(SCRATCH_DIR, "temp_render_waterfall_bw.html")
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

html_waterfall_bw = '''<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<style>
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body {
    background-color: #ffffff;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    color: #000000;
    width: 1140px;
    height: 750px;
    position: relative;
    overflow: hidden;
  }

  .canvas {
    position: relative;
    width: 1140px;
    height: 750px;
  }

  /* STAGE BOX */
  .stage-box {
    position: absolute;
    width: 236px;
    height: 80px;
    background: #ffffff;
    border: 3px solid #000000;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    padding: 10px 16px;
    z-index: 10;
  }

  .stage-title {
    font-size: 16.5px;
    font-weight: 900;
    line-height: 1.35;
    color: #000000;
    letter-spacing: -0.2px;
  }

  /* POSITIONS */
  .box-1 { left: 50px;  top: 35px;  }
  .box-2 { left: 205px; top: 135px; }
  .box-3 { left: 360px; top: 235px; }
  .box-4 { left: 515px; top: 335px; }
  .box-5 { left: 670px; top: 435px; }
  .box-6 { left: 825px; top: 535px; }

  /* SVG CONNECTORS OVERLAY */
  svg.connectors {
    position: absolute;
    top: 0;
    left: 0;
    width: 1140px;
    height: 750px;
    z-index: 5;
    pointer-events: none;
  }

  .flow-line {
    fill: none;
    stroke: #000000;
    stroke-width: 2.5;
    stroke-linecap: square;
    stroke-linejoin: miter;
  }

  .arrow-head {
    fill: #000000;
  }
</style>
</head>
<body>

  <div class="canvas">

    <!-- SVG FLOW ARROWS & FEEDBACK LOOPS -->
    <svg class="connectors" viewBox="0 0 1140 750">
      <defs>
        <!-- Sleek Downward Arrow Marker -->
        <marker id="arrow-down" markerWidth="8" markerHeight="8" refX="4" refY="6" orient="auto">
          <polygon points="1,1 7,1 4,7" class="arrow-head"/>
        </marker>
        <!-- Sleek Upward Arrow Marker -->
        <marker id="arrow-up" markerWidth="8" markerHeight="8" refX="4" refY="2" orient="auto">
          <polygon points="1,7 7,7 4,1" class="arrow-head"/>
        </marker>
        <!-- Sleek Rightward Arrow Marker -->
        <marker id="arrow-right" markerWidth="8" markerHeight="8" refX="6" refY="4" orient="auto">
          <polygon points="1,1 7,4 1,7" class="arrow-head"/>
        </marker>
      </defs>

      <!-- 1. CASCADE FORWARD ARROWS (Right-angle connectors) -->
      <!-- Box 1 (right: 286, midY: 75) -> Box 2 (top: 135, midX: 323) -->
      <path d="M 286,75 L 323,75 L 323,133" class="flow-line" marker-end="url(#arrow-down)"/>

      <!-- Box 2 (right: 441, midY: 175) -> Box 3 (top: 235, midX: 478) -->
      <path d="M 441,175 L 478,175 L 478,233" class="flow-line" marker-end="url(#arrow-down)"/>

      <!-- Box 3 (right: 596, midY: 275) -> Box 4 (top: 335, midX: 633) -->
      <path d="M 596,275 L 633,275 L 633,333" class="flow-line" marker-end="url(#arrow-down)"/>

      <!-- Box 4 (right: 751, midY: 375) -> Box 5 (top: 435, midX: 788) -->
      <path d="M 751,375 L 788,375 L 788,433" class="flow-line" marker-end="url(#arrow-down)"/>

      <!-- Box 5 (right: 906, midY: 475) -> Box 6 (top: 535, midX: 943) -->
      <path d="M 906,475 L 943,475 L 943,533" class="flow-line" marker-end="url(#arrow-down)"/>

      <!-- 2. BOTTOM FEEDBACK LOOP (Baseline & Return Line) -->
      <!-- From Box 6 bottom (midX: 943, bottom: 615) down to baseline Y: 665 -->
      <!-- Along baseline to X: 24, then up to Box 1 left (midY: 75) -->
      <path d="M 943,615 L 943,665 L 24,665 L 24,75 L 48,75" class="flow-line" marker-end="url(#arrow-right)"/>

      <!-- Upward feedback vertical arrows from baseline Y: 665 to bottom of each box -->
      <!-- To Box 5 bottom (midX: 788, bottom: 515) -->
      <path d="M 788,665 L 788,517" class="flow-line" marker-end="url(#arrow-up)"/>

      <!-- To Box 4 bottom (midX: 633, bottom: 415) -->
      <path d="M 633,665 L 633,417" class="flow-line" marker-end="url(#arrow-up)"/>

      <!-- To Box 3 bottom (midX: 478, bottom: 315) -->
      <path d="M 478,665 L 478,317" class="flow-line" marker-end="url(#arrow-up)"/>

      <!-- To Box 2 bottom (midX: 323, bottom: 215) -->
      <path d="M 323,665 L 323,217" class="flow-line" marker-end="url(#arrow-up)"/>

      <!-- To Box 1 bottom (midX: 168, bottom: 115) -->
      <path d="M 168,665 L 168,117" class="flow-line" marker-end="url(#arrow-up)"/>
    </svg>

    <!-- BOX 1 -->
    <div class="stage-box box-1">
      <div class="stage-title">Requirement gathering and analysis</div>
    </div>

    <!-- BOX 2 -->
    <div class="stage-box box-2">
      <div class="stage-title">Design</div>
    </div>

    <!-- BOX 3 -->
    <div class="stage-box box-3">
      <div class="stage-title">Implementation</div>
    </div>

    <!-- BOX 4 -->
    <div class="stage-box box-4">
      <div class="stage-title">Integration & testing</div>
    </div>

    <!-- BOX 5 -->
    <div class="stage-box box-5">
      <div class="stage-title">Verification</div>
    </div>

    <!-- BOX 6 -->
    <div class="stage-box box-6">
      <div class="stage-title">Operation & maintenance</div>
    </div>

  </div>

</body>
</html>
'''

if __name__ == "__main__":
    out_file = os.path.join(SKRIPSI_IMAGES, "metode_sdlc_waterfall.png")
    print("Rendering Waterfall B&W bold to:", out_file)
    success = render_html_to_png(html_waterfall_bw, out_file, width=1150, height=800, scale=2.5)
    if success:
        im = Image.open(out_file)
        print("SUCCESS! Dimensions:", im.size, "File size:", os.path.getsize(out_file), "bytes")
    else:
        print("FAILED to render image.")
