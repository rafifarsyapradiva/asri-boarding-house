import re

file_path = r'Skripsi/skripsi_Rafif_Arsya_Pradiva_22.N4.0014.md'

with open(file_path, 'r', encoding='utf-8') as f:
    text = f.read()
    lines = text.splitlines()

print("==================================================")
print("AUDIT 1: TANDA BACA EM-DASH (—) & EN-DASH (–)")
print("==================================================")
em_dash_lines = [(i+1, l) for i, l in enumerate(lines) if '\u2014' in l]
en_dash_lines = [(i+1, l) for i, l in enumerate(lines) if '\u2013' in l]
print(f"Jumlah baris dengan em-dash (—): {len(em_dash_lines)}")
for ln, l in em_dash_lines:
    print(f"  Baris {ln}: {l}")

print(f"\nJumlah baris dengan en-dash (–): {len(en_dash_lines)}")
for ln, l in en_dash_lines:
    print(f"  Baris {ln}: {l}")

print("\n==================================================")
print("AUDIT 2: NAMA ORANG DI BAB 1 - BAB 3")
print("==================================================")
bab1_line = 0
bab4_line = 0
for i, l in enumerate(lines, 1):
    if "## BAB 1" in l:
        bab1_line = i
    elif "## BAB 4" in l:
        bab4_line = i
        break

bab1_3_lines = lines[bab1_line-1:bab4_line-1]
print(f"Rentang Bab 1 s.d. Bab 3: Baris {bab1_line} s.d. {bab4_line-1}")

# Cek nama perorangan
name_patterns = [
    r'\bRafif\b', r'\bArsya\b', r'\bPradiva\b', 
    r'\bBu\s+[A-Z][a-z]+', r'\bPak\s+[A-Z][a-z]+',
    r'\bIbu\s+[A-Z][a-z]+', r'\bBapak\s+[A-Z][a-z]+',
    r'\bpemilik\s+kost\s+bernama\b', r'\bpengelola\s+bernama\b'
]
found_names = []
for idx, l in enumerate(bab1_3_lines, bab1_line):
    for pat in name_patterns:
        m = re.findall(pat, l, re.IGNORECASE)
        if m:
            found_names.append((idx, pat, m, l))

print(f"Nama perorangan ditemukan di Bab 1-3: {len(found_names)}")
for fn in found_names:
    print(f"  Baris {fn[0]}: {fn[2]} (pola: {fn[1]}) -> {fn[3][:80]}...")

print("\n==================================================")
print("AUDIT 3: STRUKTUR HEADING DI BAB 4 & SELURUH DOKUMEN")
print("==================================================")
for i, l in enumerate(lines, 1):
    if l.startswith('#'):
        print(f"Baris {i:4d}: {l}")

print("\n==================================================")
print("AUDIT 4: BUTIR-BUTIR IMPLEMENTASI DI SUB-BAB 4.2")
print("==================================================")
subbab_42_items = []
in_42 = False
for i, l in enumerate(lines, 1):
    if "## 4.2 Implementasi Sistem" in l:
        in_42 = True
    elif "## DAFTAR PUSTAKA" in l:
        in_42 = False
    if in_42:
        if l.startswith("### 4.2"):
            print(f"\nSub-bab level 3: {l}")
        elif re.match(r'^\d+\.\s+Implementasi', l):
            print(f"  Baris {i:4d}: {l}")
