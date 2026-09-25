import os
import re
import glob

# Read SQL schema from asri_kost_db.sql if available
sql_tables = {}
sql_file = 'asri_kost_db.sql'
if os.path.exists(sql_file):
    with open(sql_file, 'r', encoding='utf-8', errors='ignore') as f:
        sql_content = f.read()
    
    # Extract CREATE TABLE statements
    matches = re.finditer(r'CREATE TABLE (?:IF NOT EXISTS )?`?([a-zA-Z0-9_]+)`?\s*\((.*?)\)\s*(?:ENGINE|;)', sql_content, re.DOTALL | re.IGNORECASE)
    for m in matches:
        tname = m.group(1)
        tbody = m.group(2)
        lines = [line.strip().rstrip(',') for line in tbody.splitlines() if line.strip()]
        sql_tables[tname] = lines

print(f"Found {len(sql_tables)} tables in asri_kost_db.sql:")
for t in sorted(sql_tables.keys()):
    print(f"  - {t}")

# Read all migration files
migration_dir = 'database/migrations'
migrations = sorted(glob.glob(os.path.join(migration_dir, '*.php')))
print(f"\nFound {len(migrations)} migration files.")

# Read models
models_dir = 'app/Models'
models = sorted(glob.glob(os.path.join(models_dir, '*.php')))
print(f"\nFound {len(models)} model files:")
for m in models:
    print(f"  - {os.path.basename(m)}")
