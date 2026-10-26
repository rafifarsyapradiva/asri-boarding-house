import re

file_path = r'Skripsi/skripsi_Rafif_Arsya_Pradiva_22.N4.0014.md'
with open(file_path, 'r', encoding='utf-8') as f:
    lines = f.readlines()

print("=== PENCARIAN KATA KUNCI KHUSUS ===")
keywords = ['Rafif', 'Arsya', 'Pradiva', 'Bu Asri', 'Ibu Asri', 'Pak ', 'Ibu ', 'Bapak ']

for kw in keywords:
    matches = [(i+1, l.strip()) for i, l in enumerate(lines) if re.search(r'\b' + re.escape(kw.strip()) + r'\b', l, re.IGNORECASE)]
    print(f"\nKeyword '{kw.strip()}': {len(matches)} kemunculan")
    for ln, l in matches:
        # Hanya print jika di luar halaman awal (baris > 310) dan sebelum daftar pustaka (baris < 1530)
        if 310 < ln < 1530:
            print(f"   [ISI SKRIPSI] Baris {ln}: {l[:110]}...")
        elif ln <= 310:
            print(f"   [COVER/AWAL] Baris {ln}: {l[:110]}...")
        else:
            print(f"   [DAFTAR PUSTAKA] Baris {ln}: {l[:110]}...")
