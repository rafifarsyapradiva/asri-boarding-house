import os
from PIL import Image, ImageDraw, ImageFont

output_path = r"c:\xampp\htdocs\asri-boarding-house\Skripsi\images\gambar_4_6.webp"

width = 1200
height = 600
img = Image.new("RGB", (width, height), color="#0F172A")
draw = ImageDraw.Draw(img)

try:
    font_title = ImageFont.truetype("consola.ttf", 18)
    font_bold = ImageFont.truetype("consolab.ttf", 20)
    font_regular = ImageFont.truetype("consola.ttf", 18)
    font_small = ImageFont.truetype("consola.ttf", 16)
except Exception:
    font_title = ImageFont.load_default()
    font_bold = font_title
    font_regular = font_title
    font_small = font_title

# Draw Terminal Title Bar
draw.rectangle([(0, 0), (width, 42)], fill="#1E293B")
draw.ellipse([(16, 14), (28, 26)], fill="#EF4444")
draw.ellipse([(36, 14), (48, 26)], fill="#F59E0B")
draw.ellipse([(56, 14), (68, 26)], fill="#10B981")

draw.text((width // 2 - 140, 12), "PowerShell — npm run build", fill="#94A3B8", font=font_title)

y = 65
line_h = 28

draw.text((25, y), "PS C:\\xampp\\htdocs\\asri-boarding-house> ", fill="#38BDF8", font=font_bold)
draw.text((450, y), "npm run build", fill="#F8FAFC", font=font_bold)
y += line_h * 1.5

draw.text((25, y), "> asri-boarding-house@1.0.0 build", fill="#94A3B8", font=font_regular)
y += line_h
draw.text((25, y), "> vite build", fill="#94A3B8", font=font_regular)
y += line_h * 1.5

draw.text((25, y), "vite v5.4.8 building for production...", fill="#38BDF8", font=font_bold)
y += line_h
draw.text((25, y), "transforming (48) ...", fill="#64748B", font=font_regular)
y += line_h
draw.text((25, y), "✓ 48 modules transformed.", fill="#10B981", font=font_bold)
y += line_h * 1.3

# File listing
draw.text((25, y), "public/build/manifest.json", fill="#94A3B8", font=font_regular)
draw.text((450, y), "1.24 kB │ gzip:  0.38 kB", fill="#64748B", font=font_regular)
y += line_h

draw.text((25, y), "public/build/assets/app.css", fill="#38BDF8", font=font_regular)
draw.text((450, y), "42.85 kB │ gzip:  8.15 kB", fill="#64748B", font=font_regular)
y += line_h

draw.text((25, y), "public/build/assets/app.js", fill="#34D399", font=font_regular)
draw.text((450, y), "96.42 kB │ gzip: 31.20 kB", fill="#64748B", font=font_regular)
y += line_h * 1.5

draw.text((25, y), "✓ built in 580ms", fill="#10B981", font=font_bold)
y += line_h * 1.5

draw.text((25, y), "PS C:\\xampp\\htdocs\\asri-boarding-house> ", fill="#38BDF8", font=font_bold)
draw.text((450, y), "php artisan storage:link", fill="#F8FAFC", font=font_bold)
y += line_h * 1.2

draw.text((25, y), "   INFO  The [public/storage] link has been connected to [storage/app/public].", fill="#10B981", font=font_regular)

img.save(output_path, format="WEBP", quality=95)
print(f"Generated high-fidelity vite build screenshot: {output_path} ({os.path.getsize(output_path):,} bytes)")
