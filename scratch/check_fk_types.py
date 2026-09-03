import json

with open("scratch/db_schema_actual.json", "r", encoding="utf-8") as f:
    actual = json.load(f)

print("==================================================")
print("PRIMARY KEY & FOREIGN KEY DATA TYPE COMPATIBILITY AUDIT")
print("==================================================")

table_cols = {}
for tbl, cols in actual["columns"].items():
    table_cols[tbl] = {c["COLUMN_NAME"]: c for c in cols}

for fk in actual["foreign_keys"]:
    tbl = fk["TABLE_NAME"]
    col = fk["COLUMN_NAME"]
    ref_tbl = fk["REFERENCED_TABLE_NAME"]
    ref_col = fk["REFERENCED_COLUMN_NAME"]
    
    col_info = table_cols.get(tbl, {}).get(col, {})
    ref_col_info = table_cols.get(ref_tbl, {}).get(ref_col, {})
    
    col_type = col_info.get("COLUMN_TYPE", "UNKNOWN")
    ref_col_type = ref_col_info.get("COLUMN_TYPE", "UNKNOWN")
    
    match = col_type == ref_col_type
    status = "PASS" if match else "MISMATCH WARNING"
    
    print(f"FK {tbl}.{col} ({col_type}) -> {ref_tbl}.{ref_col} ({ref_col_type}) | Status: {status} [ON DELETE {fk['DELETE_RULE']}]")
