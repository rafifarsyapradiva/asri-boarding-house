import json
import os
import re
import glob

def analyze_full_db():
    with open("scratch/db_schema_actual.json", "r", encoding="utf-8") as f:
        actual = json.load(f)

    print("==================================================")
    print("ANALYSIS OF ACTUAL MYSQL DATABASE SCHEMA")
    print("==================================================")
    
    # 1. Inspect generated columns / unique columns
    print("\n--- 1. Generated & Unique Columns / SoftDeletes Columns ---")
    for tbl, cols in actual["columns"].items():
        for c in cols:
            extra = c.get("EXTRA", "")
            col_key = c.get("COLUMN_KEY", "")
            if "STORED GENERATED" in extra or "VIRTUAL GENERATED" in extra or "active_" in c["COLUMN_NAME"] or col_key in ["UNI", "PRI"]:
                print(f"Table {tbl}.{c['COLUMN_NAME']}: TYPE={c['COLUMN_TYPE']}, NULL={c['IS_NULLABLE']}, KEY={col_key}, EXTRA={extra}, DEFAULT={c['COLUMN_DEFAULT']}")

    # 2. Check all Enum columns
    print("\n--- 2. Enum Columns in Actual Database ---")
    for tbl, cols in actual["columns"].items():
        for c in cols:
            if "enum" in c["DATA_TYPE"].lower():
                print(f"Table {tbl}.{c['COLUMN_NAME']}: {c['COLUMN_TYPE']} (Default: {c['COLUMN_DEFAULT']}, Nullable: {c['IS_NULLABLE']})")

    # 3. Check all Decimal columns (Financial data)
    print("\n--- 3. Decimal & Financial Columns ---")
    for tbl, cols in actual["columns"].items():
        for c in cols:
            if c["DATA_TYPE"] in ["decimal", "float", "double", "int", "bigint"] and any(k in c["COLUMN_NAME"] for k in ["harga", "nominal", "deposit", "denda", "total", "saldo"]):
                print(f"Table {tbl}.{c['COLUMN_NAME']}: {c['COLUMN_TYPE']} (Precision: {c['NUMERIC_PRECISION']}, Scale: {c['NUMERIC_SCALE']}, Default: {c['COLUMN_DEFAULT']}, Nullable: {c['IS_NULLABLE']})")

    # 4. Check all Foreign Keys and Delete rules
    print("\n--- 4. Foreign Keys and Delete Rules ---")
    for fk in actual["foreign_keys"]:
        print(f"Table {fk['TABLE_NAME']}.{fk['COLUMN_NAME']} -> {fk['REFERENCED_TABLE_NAME']}.{fk['REFERENCED_COLUMN_NAME']} [ON DELETE {fk['DELETE_RULE']}, ON UPDATE {fk['UPDATE_RULE']}]")

    # 5. Check all Indexes
    print("\n--- 5. All Indexes per Table ---")
    for tbl, idxs in actual["indexes"].items():
        print(f"Table: {tbl}")
        for idx_name, cols in idxs.items():
            is_unique = actual.get("index_meta", {}).get(tbl, {}).get(idx_name, {}).get("non_unique") == 0
            u_str = "UNIQUE" if is_unique else "INDEX"
            print(f"   - {idx_name} ({u_str}): {cols}")

if __name__ == "__main__":
    analyze_full_db()
