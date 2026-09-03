import json
import os
import re

with open("scratch/db_schema_actual.json", "r", encoding="utf-8") as f:
    actual = json.load(f)

with open("scratch/forensic_analysis_dump.json", "r", encoding="utf-8") as f:
    forensic = json.load(f)

models = forensic["models"]
db_cols = actual["columns"]

print("==================================================")
print("COMPREHENSIVE MODEL VS DATABASE COLUMN & CAST AUDIT")
print("==================================================")

table_to_model = {
    'users': 'User',
    'penyewa': 'Penyewa',
    'kamar': 'Kamar',
    'fasilitas': 'Fasilitas',
    'kamar_fasilitas': None, # Pivot
    'tagihan': 'Tagihan',
    'pembayaran': 'Pembayaran',
    'log_notifikasi': 'LogNotifikasi',
    'reservasi': 'Reservasi',
    'chat_messages': 'ChatMessage',
    'pengumuman': 'Pengumuman',
    'notifikasi_khusus': 'NotifikasiKhusus',
    'settings': 'Setting',
    'customer_reviews': 'CustomerReview',
    'pengeluaran': 'Pengeluaran',
    'faqs': 'Faq',
    'keluhan': 'Keluhan',
    'peraturan': 'Peraturan',
    'galleries': 'Gallery',
    'guest_chat_threads': 'GuestChatThread',
    'guest_chat_messages': 'GuestChatMessage',
    'whatsapp_clicks': 'WhatsappClick',
    # Laravel system tables
    'cache': None,
    'cache_locks': None,
    'failed_jobs': None,
    'jobs': None,
    'job_batches': None,
    'migrations': None,
    'password_reset_tokens': None,
    'sessions': None
}

for tbl, cols in db_cols.items():
    mname = table_to_model.get(tbl)
    if not mname:
        print(f"\n[System/Pivot Table] {tbl} (Total columns: {len(cols)})")
        continue
    
    m_info = models.get(mname)
    if not m_info:
        print(f"\n[ERROR] Model {mname} not found for table {tbl}!")
        continue
        
    print(f"\n==================================================")
    print(f"TABLE: {tbl} <--> MODEL: {mname}")
    print(f"PK: {m_info['pk']} | KeyType: {m_info['keyType']} | Inc: {m_info['incrementing']} | SoftDeletes: {m_info['soft_deletes']}")
    print(f"==================================================")
    
    col_names = [c["COLUMN_NAME"] for c in cols]
    fillables = m_info["fillable"]
    guarded = m_info["guarded"]
    casts = m_info["casts"]
    
    print(f"DB Columns ({len(col_names)}): {', '.join(col_names)}")
    print(f"Fillable ({len(fillables)}): {', '.join(fillables)}")
    print(f"Guarded ({len(guarded)}): {', '.join(guarded)}")
    print(f"Casts ({len(casts)}): {json.dumps(casts)}")
    
    # Check for un-fillable columns (ignoring id, timestamps, deleted_at, virtual columns)
    ignored = {'id', 'created_at', 'updated_at', 'deleted_at'}
    for c in cols:
        cname = c["COLUMN_NAME"]
        if "active_" in cname:
            ignored.add(cname)
            
    unfillable = [cname for cname in col_names if cname not in ignored and cname not in fillables and guarded != []]
    if unfillable and guarded != ['*']:
        print(f"  [NOTE] Columns in DB not in $fillable (guarded={guarded}): {unfillable}")
        
    # Check relationships
    rels = m_info["relationships"]
    print(f"Relationships ({len(rels)}):")
    for r in rels:
        print(f"  - {r['name']}: {r['type']}({r['target']}, {r['foreign_key']}, {r['other_key']})")

