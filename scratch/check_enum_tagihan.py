import json

with open("scratch/db_schema_actual.json", "r", encoding="utf-8") as f:
    actual = json.load(f)

for c in actual["columns"]["tagihan"]:
    if c["COLUMN_NAME"] == "status":
        print("tagihan.status current DB enum:", c["COLUMN_TYPE"])

for c in actual["columns"]["penyewa"]:
    print(f"penyewa.{c['COLUMN_NAME']}: {c['COLUMN_TYPE']} (Key: {c['COLUMN_KEY']})")

