import re

file_path = r'Skripsi/skripsi_Rafif_Arsya_Pradiva_22.N4.0014.md'

with open(file_path, 'r', encoding='utf-8') as f:
    text = f.read()

# Potong sub-bab 4.2
p1 = text.find('## 4.2 Implementasi Sistem')
p2 = text.find('## DAFTAR PUSTAKA')
subbab_42 = text[p1:p2]

# Ambil semua blok implementasi
# Tiap blok diawali dengan: r'^\d+\.\s+Implementasi'
items = re.split(r'\n(?=\d+\.\s+Implementasi)', subbab_42)
header = items[0]
impl_items = items[1:]

print(f"Total item implementasi ditemukan: {len(impl_items)}")

# Daftar kata klise AI yang dicurigai
cliches = [
    'merupakan bukti nyata', 'menjadi tonggak', 'tak lekang oleh waktu',
    'seperti dua sisi mata uang', 'menyelami', 'dalam era digital',
    'tidak dapat dipungkiri', 'merupakan langkah krusial', 'memainkan peran penting',
    'jantung dari', 'titik balik', 'menawarkan perpaduan', 'tak terelakkan',
    'memberikan angin segar', 'membuka cakrawala', 'kian merebak',
    'merangkul', 'mengukir', 'mengejawantahkan'
]

results = []

def count_sentences(paragraph):
    # Bersihkan spasi berlebih
    p = paragraph.strip()
    if not p:
        return 0, []
    # Pisahkan kalimat berdasarkan titik/tanda seru/tanya yang diikuti spasi atau akhir string
    # Hati-hati dengan Gambar 4.x, dsb.
    # Ganti "Gambar 4.x" sementara agar tidak terpisah
    p_safe = re.sub(r'Gambar (\d+)\.(\d+)', r'Gambar_\1_\2', p)
    p_safe = re.sub(r'No\.', r'No_', p_safe)
    p_safe = re.sub(r'dsb\.', r'dsb_', p_safe)
    p_safe = re.sub(r'dll\.', r'dll_', p_safe)
    p_safe = re.sub(r's\.d\.', r's_d_', p_safe)
    
    sentences = [s.strip() for s in re.split(r'(?<=[.!?])\s+', p_safe) if s.strip()]
    return len(sentences), sentences

for idx, itm in enumerate(impl_items, 1):
    lines = [l for l in itm.strip().splitlines() if l.strip()]
    title = lines[0]
    
    # Cari paragraf narasi
    # Biasanya:
    # lines[0]: Judul
    # lines[1]: Paragraf 1 (3 kalimat)
    # lines[2]: Paragraf 2 (4 kalimat)
    # lines[3..]: <div align="center"> ... </div>
    # setelah </div>: Paragraf deskripsi visual (5 kalimat)
    
    # Ekstrak paragraf
    paras = []
    in_div = False
    current_p = []
    
    for l in lines[1:]:
        if '<div' in l:
            in_div = True
            if current_p:
                paras.append('\n'.join(current_p))
                current_p = []
            continue
        elif '</div>' in l:
            in_div = False
            continue
        elif in_div:
            continue
        elif l.startswith('<') or l.startswith('*Gambar') or l.startswith('###'):
            continue
        else:
            paras.append(l)
            
    # Audit klise
    found_cliches = [c for c in cliches if c in itm.lower()]
    
    # Audit em-dash
    has_emdash = '—' in itm
    
    # Hitung kalimat
    p_counts = []
    for p in paras:
        cnt, sents = count_sentences(p)
        p_counts.append(cnt)
        
    # Cari nomor gambar
    img_match = re.search(r'Gambar (4\.\d+)', itm)
    img_num = img_match.group(1) if img_match else 'N/A'
    
    results.append({
        'index': idx,
        'title': title,
        'img_num': img_num,
        'paras_count': len(paras),
        'sentence_counts': p_counts,
        'found_cliches': found_cliches,
        'has_emdash': has_emdash
    })

print(f"{'No':<3} | {'Gambar':<10} | {'Judul':<45} | {'Jml P':<5} | {'Kalimat per P':<15} | {'Klise':<5} | {'EmDash'}")
print("-" * 95)
for r in results:
    s_cnt_str = str(r['sentence_counts'])
    print(f"{r['index']:<3} | {r['img_num']:<10} | {r['title'][:45]:<45} | {r['paras_count']:<5} | {s_cnt_str:<15} | {len(r['found_cliches']):<5} | {r['has_emdash']}")
