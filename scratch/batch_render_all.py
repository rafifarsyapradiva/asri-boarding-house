import subprocess
import os
import sys
from PIL import Image

erd_dir = r"Blueprint/erd"

diagrams = [
    ("diagram_erd.mmd", "diagram_erd.png", 3),
    ("taksonomi_relasi_kardinalitas.mmd", "taksonomi_relasi_kardinalitas.png", 2),
    ("peta_relasi_kardinalitas_global.mmd", "peta_relasi_kardinalitas_global.png", 2),
    ("diagram_relasi_one_to_one.mmd", "diagram_relasi_one_to_one.png", 2),
    ("diagram_relasi_many_to_many.mmd", "diagram_relasi_many_to_many.png", 2),
    ("diagram_relasi_one_to_many.mmd", "diagram_relasi_one_to_many.png", 2),
    ("erd_relasi_billing_pembayaran.mmd", "erd_relasi_billing_pembayaran.png", 2),
    ("erd_relasi_kamar_fasilitas.mmd", "erd_relasi_kamar_fasilitas.png", 2),
    ("erd_relasi_keluhan_guest_analytics.mmd", "erd_relasi_keluhan_guest_analytics.png", 2),
    ("erd_relasi_reservasi_chat.mmd", "erd_relasi_reservasi_chat.png", 2),
    ("erd_relasi_users_kamar_penyewa.mmd", "erd_relasi_users_kamar_penyewa.png", 2),
    ("alur_reservasi_pembayaran.mmd", "alur_reservasi_pembayaran.png", 2),
    ("alur_siklus_billing.mmd", "alur_siklus_billing.png", 2),
    ("alur_pengaduan_keluhan.mmd", "alur_pengaduan_keluhan.png", 2),
    ("alur_integrasi_arus_kas.mmd", "alur_integrasi_arus_kas.png", 2),
]

print("=== STARTING BATCH COMPILATION OF 15 MERMAID DIAGRAMS ===")
for mmd, png, scale in diagrams:
    mmd_path = os.path.join(erd_dir, mmd)
    png_path = os.path.join(erd_dir, png)
    
    cmd = f'cmd /c npx @mermaid-js/mermaid-cli -i "{mmd_path}" -o "{png_path}" -s {scale} -b white'
    res = subprocess.run(cmd, shell=True, capture_output=True, text=True)
    if res.returncode != 0:
        print(f"FAILED: {mmd} -> {res.stderr}")
    else:
        im = Image.open(png_path)
        print(f"DONE: {png:35} | Size: {im.size[0]:>5}x{im.size[1]:<5} | Aspect: {im.size[0]/im.size[1]:.2f}")

print("=== ALL DIAGRAMS PROCESSED ===")
