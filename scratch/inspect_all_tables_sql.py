import os
import re

with open('asri_kost_db.sql', 'r', encoding='utf-8', errors='ignore') as f:
    sql = f.read()

domain_tables = [
    'chat_messages', 'customer_reviews', 'faqs', 'fasilitas', 'galleries',
    'guest_chat_messages', 'guest_chat_threads', 'kamar', 'kamar_fasilitas',
    'keluhan', 'log_notifikasi', 'notifikasi_khusus', 'pembayaran',
    'pengeluaran', 'pengumuman', 'penyewa', 'peraturan', 'reservasi',
    'settings', 'tagihan', 'users', 'whatsapp_clicks'
]

results = {}
for dt in domain_tables:
    # search CREATE TABLE `dt`
    pattern = r'CREATE TABLE `' + dt + r'` \((.*?)\)\s*ENGINE='
    m = re.search(pattern, sql, re.DOTALL)
    if m:
        body = m.group(1)
        cols = []
        pks = []
        fks = []
        uniques = []
        keys = []
        for line in body.splitlines():
            line = line.strip().rstrip(',')
            if not line:
                continue
            if line.startswith('PRIMARY KEY'):
                pks.append(line)
            elif 'FOREIGN KEY' in line:
                fks.append(line)
            elif line.startswith('UNIQUE KEY'):
                uniques.append(line)
            elif line.startswith('KEY ') or line.startswith('FULLTEXT KEY'):
                keys.append(line)
            else:
                cols.append(line)
        results[dt] = {
            'cols': cols,
            'pks': pks,
            'fks': fks,
            'uniques': uniques,
            'keys': keys
        }

for dt in sorted(results.keys()):
    print(f"\n==================== TABLE: {dt} ====================")
    print(f"Columns ({len(results[dt]['cols'])}):")
    for c in results[dt]['cols']:
        print(f"  {c}")
    print(f"Primary Key: {results[dt]['pks']}")
    if results[dt]['uniques']:
        print("Unique Keys:")
        for u in results[dt]['uniques']:
            print(f"  {u}")
    if results[dt]['fks']:
        print("Foreign Keys:")
        for fk in results[dt]['fks']:
            print(f"  {fk}")
