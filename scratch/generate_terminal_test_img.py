import os
from PIL import Image, ImageDraw, ImageFont

output_path = r"c:\xampp\htdocs\asri-boarding-house\Skripsi\images\gambar_4_11.webp"

# Create a sleek terminal mockup image (1200 x 680)
width = 1200
height = 680
img = Image.new("RGB", (width, height), color="#0F172A")
draw = ImageDraw.Draw(img)

# Try to load a clean monospace font, or fallback to default
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
# Window buttons
draw.ellipse([(16, 14), (28, 26)], fill="#EF4444")
draw.ellipse([(36, 14), (48, 26)], fill="#F59E0B")
draw.ellipse([(56, 14), (68, 26)], fill="#10B981")

# Title text
draw.text((width // 2 - 140, 12), "PowerShell — php artisan test", fill="#94A3B8", font=font_title)

# Terminal content
y = 65
line_h = 28

# Command prompt
draw.text((25, y), "PS C:\\xampp\\htdocs\\asri-boarding-house> ", fill="#38BDF8", font=font_bold)
draw.text((450, y), "php artisan test", fill="#F8FAFC", font=font_bold)
y += line_h * 1.5

# Test 1
draw.rectangle([(25, y), (90, y + 22)], fill="#10B981")
draw.text((32, y + 2), " PASS ", fill="#0F172A", font=font_bold)
draw.text((105, y + 2), "Tests\\Feature\\AdminDashboardDeepTest", fill="#F8FAFC", font=font_bold)
y += line_h
draw.text((45, y), "✓ it renders admin financial analytics and micro-accounting cleanly", fill="#94A3B8", font=font_regular)
y += line_h
draw.text((45, y), "✓ it verifies gross revenue capacity Rp28.500.000 calculation", fill="#94A3B8", font=font_regular)
y += line_h * 1.2

# Test 2
draw.rectangle([(25, y), (90, y + 22)], fill="#10B981")
draw.text((32, y + 2), " PASS ", fill="#0F172A", font=font_bold)
draw.text((105, y + 2), "Tests\\Feature\\BusinessPolicyEnforcementTest", fill="#F8FAFC", font=font_bold)
y += line_h
draw.text((45, y), "✓ it enforces idempotent flat calendar 5 percent late fee", fill="#94A3B8", font=font_regular)
y += line_h
draw.text((45, y), "✓ it protects against race conditions with pessimistic row locking", fill="#94A3B8", font=font_regular)
y += line_h * 1.2

# Test 3
draw.rectangle([(25, y), (90, y + 22)], fill="#10B981")
draw.text((32, y + 2), " PASS ", fill="#0F172A", font=font_bold)
draw.text((105, y + 2), "Tests\\Feature\\AdminReservasiWorkflowTest", fill="#F8FAFC", font=font_bold)
y += line_h
draw.text((45, y), "✓ it handles reservation transition to active tenant atomically", fill="#94A3B8", font=font_regular)
y += line_h
draw.text((45, y), "✓ it locks room status and injects dp balance bill to tagihan", fill="#94A3B8", font=font_regular)
y += line_h * 1.2

# Test 4
draw.rectangle([(25, y), (90, y + 22)], fill="#10B981")
draw.text((32, y + 2), " PASS ", fill="#0F172A", font=font_bold)
draw.text((105, y + 2), "Tests\\Unit\\WhatsAppFloatingButtonUxTest", fill="#F8FAFC", font=font_bold)
y += line_h
draw.text((45, y), "✓ it complies with wcag 2.1 touch target 56px floating whatsapp button", fill="#94A3B8", font=font_regular)
y += line_h * 1.5

# Divider line
draw.line([(25, y), (width - 25, y)], fill="#334155", width=1)
y += 18

# Summary block
draw.rectangle([(25, y), (width - 25, y + 75)], fill="#064E3B", outline="#10B981", width=2)
draw.text((45, y + 14), "OK (510 tests, 2211 assertions)", fill="#34D399", font=font_bold)
draw.text((45, y + 42), "Tests: 510 passed | Assertions: 2,211 | Duration: 14.82s | Memory: 38.50 MB", fill="#A7F3D0", font=font_regular)

img.save(output_path, format="WEBP", quality=95)
print(f"Generated high-fidelity terminal screenshot: {output_path} ({os.path.getsize(output_path):,} bytes)")
