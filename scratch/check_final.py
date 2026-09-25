import os
import sys
from PIL import Image

sys.stdout.reconfigure(encoding='utf-8')
erd_dir = r"Blueprint/erd"
all_files = sorted(os.listdir(erd_dir))
mmds = [f for f in all_files if f.endswith('.mmd')]
pngs = [f for f in all_files if f.endswith('.png')]

print(f"Total MMD files: {len(mmds)}")
print(f"Total PNG files: {len(pngs)}")
print("-" * 80)
header = f"{'Berkas Diagram':35} | {'Dimensi PNG':15} | {'Aspect Ratio':12} | {'Ukuran (KB)':10}"
print(header)
print("-" * 80)

for mmd in mmds:
    base = os.path.splitext(mmd)[0]
    png = base + '.png'
    p_path = os.path.join(erd_dir, png)
    if os.path.exists(p_path):
        im = Image.open(p_path)
        sz_kb = os.path.getsize(p_path) / 1024
        ratio = im.size[0] / im.size[1]
        print(f"{png:35} | {im.size[0]:>5}x{im.size[1]:<5} px   | {ratio:>6.2f}       | {sz_kb:>7.1f} KB")
    else:
        print(f"{png:35} | NOT FOUND")
print("-" * 80)
