import json
import re

with open("scratch/db_schema_actual.json", "r", encoding="utf-8") as f:
    actual = json.load(f)

print("==================================================")
print("COMPREHENSIVE ENUM AUDIT ACROSS ALL TABLES")
print("==================================================")

for tbl, cols in actual["columns"].items():
    for c in cols:
        if "enum" in c["DATA_TYPE"].lower():
            print(f"Table: {tbl} | Column: {c['COLUMN_NAME']}")
            print(f"  - Actual MySQL Definition: {c['COLUMN_TYPE']}")
            print(f"  - Default: {c['COLUMN_DEFAULT']}")
            print(f"  - Nullable: {c['IS_NULLABLE']}")

