import re

file_path = r'Skripsi/skripsi_Rafif_Arsya_Pradiva_22.N4.0014.md'

with open(file_path, 'r', encoding='utf-8') as f:
    text = f.read()

p1 = text.find('## 4.2 Implementasi Sistem')
p2 = text.find('## DAFTAR PUSTAKA')
subbab_42 = text[p1:p2]

items = re.split(r'\n(?=\d+\.\s+Implementasi)', subbab_42)[1:]

print(f"Extracted {len(items)} items.")

for idx, itm in enumerate(items, 1):
    lines = [l.strip() for l in itm.strip().splitlines() if l.strip()]
    title = lines[0]
    img_match = re.search(r'Gambar (4\.\d+)', itm)
    img_num = img_match.group(1) if img_match else 'N/A'
    print(f"Item {idx:02d}: {img_num} - {title}")
