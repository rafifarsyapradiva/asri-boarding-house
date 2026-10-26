import re

file_path = r'Skripsi/skripsi_Rafif_Arsya_Pradiva_22.N4.0014.md'

with open(file_path, 'r', encoding='utf-8') as f:
    text = f.read()

p1 = text.find('## 4.2 Implementasi Sistem')
p2 = text.find('## DAFTAR PUSTAKA')
subbab_42 = text[p1:p2]

# Cari semua div gambar
divs = re.findall(r'(<div align="center">\s*<img [^>]+>\s*<br>\s*\*Gambar 4\.\d+ [^*]+\*\s*</div>)', subbab_42)
print(f"Total div gambar ditemukan: {len(divs)}")
for idx, d in enumerate(divs, 1):
    cap = re.search(r'\*Gambar (4\.\d+) ([^*]+)\*', d)
    if cap:
        print(f"Gambar {cap.group(1)}: {cap.group(2)}")
