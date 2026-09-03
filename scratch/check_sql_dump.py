import re

with open("asri_kost_db.sql", "r", encoding="utf-8", errors="ignore") as f:
    content = f.read()

tables = [
    'users',
    'settings',
    'fasilitas',
    'kamar',
    'kamar_fasilitas',
    'customer_reviews',
    'faqs',
    'galleries',
    'peraturan',
    'migrations'
]

print("=== PENGECEKAN ISI DUMP SQL: asri_kost_db.sql ===")
for t in tables:
    pattern = rf"INSERT INTO `{t}` VALUES \((.+?)\);"
    match = re.search(pattern, content, re.DOTALL)
    if match:
        # count tuples roughly
        val_str = match.group(1)
        count = val_str.count("),(") + 1
        print(f"[OK] Tabel `{t}`: Ditemukan ({count} record/baris data)")
    else:
        print(f"[FAIL] Tabel `{t}`: Tidak ada data INSERT")

print("==================================================")
