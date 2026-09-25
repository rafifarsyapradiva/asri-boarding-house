import os
import re
import glob

# Parse asri_kost_db.sql for table columns, PKs, FKs, Unique keys
with open('asri_kost_db.sql', 'r', encoding='utf-8', errors='ignore') as f:
    sql_text = f.read()

# We want domain tables
domain_tables = [
    'chat_messages', 'customer_reviews', 'faqs', 'fasilitas', 'galleries',
    'guest_chat_messages', 'guest_chat_threads', 'kamar', 'kamar_fasilitas',
    'keluhan', 'log_notifikasi', 'notifikasi_khusus', 'pembayaran',
    'pengeluaran', 'pengumuman', 'penyewa', 'peraturan', 'reservasi',
    'settings', 'tagihan', 'users', 'whatsapp_clicks'
]

table_defs = {}
for dt in domain_tables:
    m = re.search(r'CREATE TABLE (?:IF NOT EXISTS )?`?' + dt + r'`?\s*\((.*?)\)\s*(?:ENGINE|;)', sql_text, re.DOTALL | re.IGNORECASE)
    if m:
        tbody = m.group(1)
        cols = {}
        pks = []
        fks = []
        uniques = []
        for line in tbody.splitlines():
            line = line.strip().rstrip(',')
            if not line:
                continue
            # Check constraints
            if re.match(r'PRIMARY KEY', line, re.I):
                pk_m = re.findall(r'`([a-zA-Z0-9_]+)`', line)
                pks.extend(pk_m)
            elif re.match(r'(?:CONSTRAINT\s+`?[a-zA-Z0-9_]+`?\s+)?FOREIGN KEY', line, re.I):
                fks.append(line)
            elif re.match(r'UNIQUE KEY', line, re.I):
                uniques.append(line)
            elif line.startswith('KEY ') or line.startswith('FULLTEXT '):
                pass
            else:
                # Column definition
                col_m = re.match(r'`([a-zA-Z0-9_]+)`\s+([^,]+)', line)
                if col_m:
                    cols[col_m.group(1)] = col_m.group(2).strip()
        table_defs[dt] = {
            'columns': cols,
            'pks': pks,
            'fks': fks,
            'uniques': uniques
        }

# Parse Mermaid ERD from Blueprint/Entity_Relationship_Diagram_Kost.md
with open('Blueprint/Entity_Relationship_Diagram_Kost.md', 'r', encoding='utf-8') as f:
    erd_text = f.read()

mermaid_match = re.search(r'erDiagram\s*\n(.*?)\n```', erd_text, re.DOTALL)
mermaid_content = mermaid_match.group(1) if mermaid_match else ""

erd_entities = {}
current_entity = None
for line in mermaid_content.splitlines():
    line = line.strip()
    if not line or line.startswith('%%'):
        continue
    if any(op in line for op in ['||--', '}o--', '|o--', 'o{--', '}o..', '||..', '|{--', '--|{', '--o{', '--||']):
        continue
    m_ent = re.match(r'^([a-zA-Z0-9_]+)\s*\{', line)
    if m_ent:
        current_entity = m_ent.group(1)
        erd_entities[current_entity] = {}
        continue
    if line == '}':
        current_entity = None
        continue
    if current_entity:
        parts = line.split(maxsplit=2)
        col_type = parts[0]
        col_name = parts[1] if len(parts) > 1 else ""
        col_extra = parts[2] if len(parts) > 2 else ""
        erd_entities[current_entity][col_name] = f"{col_type} {col_extra}".strip()

# Also parse Kamus Data section in ERD doc
# e.g. - **col_name**: description (type, modifiers)
kamus_data = {}
curr_kamus_tbl = None
for line in erd_text.splitlines():
    m_head = re.match(r'###\s*2\.\d+\.\s*Tabel\s*`?([a-zA-Z0-9_]+)`?(?:\s*&\s*`?([a-zA-Z0-9_]+)`?)?', line)
    if m_head:
        curr_kamus_tbl = m_head.group(1)
        kamus_data[curr_kamus_tbl] = []
        if m_head.group(2):
            kamus_data[m_head.group(2)] = []
        continue
    # Check subhead in 2.18
    m_subhead = re.match(r'-\s*\*\*([a-zA-Z0-9_]+)\*\*:\s*$', line)
    if m_subhead and m_subhead.group(1) in ['guest_chat_threads', 'guest_chat_messages']:
        curr_kamus_tbl = m_subhead.group(1)
        continue
    m_col = re.match(r'\s*-\s*\*\*([a-zA-Z0-9_]+)\*\*:\s*(.*)', line)
    if m_col and curr_kamus_tbl:
        cname = m_col.group(1)
        cdesc = m_col.group(2)
        kamus_data[curr_kamus_tbl].append((cname, cdesc))

print("=== COMPARISON RESULTS ===")
for t in domain_tables:
    sql_cols = set(table_defs.get(t, {}).get('columns', {}).keys())
    erd_cols = set(erd_entities.get(t, {}).keys())
    kamus_cols = set(c[0] for c in kamus_data.get(t, []))
    
    # Filter out virtual generated columns in SQL (active_*) for pure schema comparison if desired, but let's see them
    in_sql_not_erd = sql_cols - erd_cols
    in_erd_not_sql = erd_cols - sql_cols
    in_erd_not_kamus = erd_cols - kamus_cols
    in_kamus_not_erd = kamus_cols - erd_cols
    
    diff = in_sql_not_erd or in_erd_not_sql or in_erd_not_kamus or in_kamus_not_erd
    if diff:
        print(f"\n[Table: {t}]")
        if in_sql_not_erd:
            print(f"  In SQL but NOT in ERD Mermaid: {sorted(in_sql_not_erd)}")
        if in_erd_not_sql:
            print(f"  In ERD Mermaid but NOT in SQL: {sorted(in_erd_not_sql)}")
        if in_erd_not_kamus:
            print(f"  In ERD Mermaid but NOT in Kamus: {sorted(in_erd_not_kamus)}")
        if in_kamus_not_erd:
            print(f"  In Kamus but NOT in ERD Mermaid: {sorted(in_kamus_not_erd)}")
    else:
        print(f"Table {t}: Perfect column match across SQL, ERD Mermaid, and Kamus ({len(erd_cols)} cols).")
