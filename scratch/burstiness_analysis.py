import re

file_path = r'Skripsi/skripsi_Rafif_Arsya_Pradiva_22.N4.0014.md'

with open(file_path, 'r', encoding='utf-8') as f:
    text = f.read()

p1 = text.find('## 4.2 Implementasi Sistem')
p2 = text.find('## DAFTAR PUSTAKA')
subbab_42 = text[p1:p2]

# Split per paragraf
paragraphs = [p.strip() for p in subbab_42.split('\n\n') if p.strip()]

print(f"Total blok paragraf dalam sub-bab 4.2: {len(paragraphs)}")

all_sentence_lengths = []
total_words = 0

for p in paragraphs:
    if p.startswith('#') or p.startswith('<') or p.startswith('*Gambar') or re.match(r'^\d+\.\s+Implementasi', p):
        continue
    # Bersihkan teks
    p_clean = re.sub(r'<[^>]+>', '', p)
    words = p_clean.split()
    total_words += len(words)
    
    # Hitung kalimat dan panjang kata per kalimat
    p_safe = re.sub(r'Gambar (\d+)\.(\d+)', r'Gambar_\1_\2', p_clean)
    p_safe = re.sub(r'No\.', r'No_', p_safe)
    p_safe = re.sub(r'dsb\.', r'dsb_', p_safe)
    p_safe = re.sub(r'dll\.', r'dll_', p_safe)
    p_safe = re.sub(r's\.d\.', r's_d_', p_safe)
    
    sents = [s.strip() for s in re.split(r'(?<=[.!?])\s+', p_safe) if s.strip()]
    for s in sents:
        w_cnt = len(s.split())
        all_sentence_lengths.append(w_cnt)

print(f"Total kata dalam narasi 4.2: {total_words}")
print(f"Total kalimat narasi: {len(all_sentence_lengths)}")
if all_sentence_lengths:
    short_s = len([w for w in all_sentence_lengths if w < 15])
    med_s = len([w for w in all_sentence_lengths if 15 <= w <= 25])
    long_s = len([w for w in all_sentence_lengths if w > 25])
    avg_len = sum(all_sentence_lengths) / len(all_sentence_lengths)
    print(f"Rata-rata kata per kalimat: {avg_len:.1f} kata")
    print(f"Distribusi Burstiness:")
    print(f"  - Kalimat Pendek (< 15 kata)  : {short_s} ({short_s/len(all_sentence_lengths)*100:.1f}%)")
    print(f"  - Kalimat Sedang (15 - 25 kata): {med_s} ({med_s/len(all_sentence_lengths)*100:.1f}%)")
    print(f"  - Kalimat Panjang (> 25 kata) : {long_s} ({long_s/len(all_sentence_lengths)*100:.1f}%)")
