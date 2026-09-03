import re
import json

def parse_erd_entities(erd_path):
    with open(erd_path, "r", encoding="utf-8") as f:
        content = f.read()

    # Extract erDiagram block
    erd_match = re.search(r'erDiagram(.*?)(?:%% Relationship|\Z)', content, re.DOTALL)
    if not erd_match:
        return {}
    
    erd_text = erd_match.group(1)
    # Find entity blocks: entity_name { ... }
    entity_blocks = re.findall(r'([a-zA-Z0-9_]+)\s*\{([^}]+)\}', erd_text)
    
    entities = {}
    for name, body in entity_blocks:
        fields = []
        for line in body.strip().split('\n'):
            line = line.strip()
            if not line or line.startswith('%%'):
                continue
            # e.g., unsigned_bigint id PK
            # string email "varchar(150) UK"
            parts = line.split(maxsplit=2)
            ftype = parts[0]
            fname = parts[1] if len(parts) > 1 else ''
            fextra = parts[2] if len(parts) > 2 else ''
            fields.append({
                'name': fname,
                'type': ftype,
                'extra': fextra.strip('"')
            })
        entities[name] = fields
    return entities

erd_entities = parse_erd_entities("Blueprint/Entity_Relationship_Diagram_Kost.md")
with open("scratch/db_schema_actual.json", "r", encoding="utf-8") as f:
    actual = json.load(f)

print("==================================================")
print("BLUEPRINT ERD vs ACTUAL DB COLUMNS COMPARISON")
print("==================================================")

for tbl, erd_fields in erd_entities.items():
    actual_cols = actual["columns"].get(tbl, [])
    actual_col_map = {c["COLUMN_NAME"]: c for c in actual_cols}
    erd_col_map = {f["name"]: f for f in erd_fields}
    
    print(f"\n--- TABLE: {tbl} (ERD fields: {len(erd_fields)}, Actual DB: {len(actual_cols)}) ---")
    
    # Check fields in ERD but not in DB
    missing_in_db = []
    for fname, f_info in erd_col_map.items():
        if fname not in actual_col_map:
            missing_in_db.append(f"{fname} ({f_info['type']} {f_info['extra']})")
            
    # Check fields in DB but not in ERD
    missing_in_erd = []
    for cname, c_info in actual_col_map.items():
        if cname not in erd_col_map:
            missing_in_erd.append(f"{cname} ({c_info['COLUMN_TYPE']})")
            
    if missing_in_db:
        print(f"  [MISSING IN ACTUAL DB]: {missing_in_db}")
    if missing_in_erd:
        print(f"  [IN DB BUT NOT IN ERD]: {missing_in_erd}")
    if not missing_in_db and not missing_in_erd:
        print(f"  [PERFECT MATCH] All columns match exactly.")
        
    # Check column types & properties
    for fname, f_info in erd_col_map.items():
        if fname in actual_col_map:
            act = actual_col_map[fname]
            # print details
            # print(f"    * {fname}: ERD={f_info['type']} {f_info['extra']} <--> DB={act['COLUMN_TYPE']} NULL={act['IS_NULLABLE']} DEF={act['COLUMN_DEFAULT']}")

