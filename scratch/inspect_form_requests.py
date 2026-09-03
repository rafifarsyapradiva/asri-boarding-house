import glob
import os
import re
import json

base_dir = r"c:\xampp\htdocs\asri-boarding-house"
req_dir = os.path.join(base_dir, "app", "Http", "Requests")

with open("scratch/db_schema_actual.json", "r", encoding="utf-8") as f:
    actual = json.load(f)

print("==================================================")
print("FORM REQUEST VALIDATIONS VS DATABASE CONSTRAINTS")
print("==================================================")

for root, dirs, files in os.walk(req_dir):
    for f in files:
        if f.endswith(".php") and "Traits" not in root:
            fpath = os.path.join(root, f)
            rel = os.path.relpath(fpath, base_dir)
            with open(fpath, "r", encoding="utf-8", errors="ignore") as file:
                code = file.read()
                
            rules_match = re.search(r'public\s+function\s+rules\s*\(\s*\)\s*(?::\s*array\s*)?\{(.*?)(?:return\s+\[(.*?)\];|\}\s*\})', code, re.DOTALL)
            rules_block = ""
            if rules_match:
                rules_block = rules_match.group(2) or rules_match.group(1)
                
            print(f"\n--- REQUEST: {rel} ---")
            rule_lines = re.findall(r'[\'"]([a-zA-Z0-9_.]+)[\'"]\s*=>\s*(.*?)(?:,|$)', rules_block)
            for field, rval in rule_lines:
                rval = rval.strip().rstrip(',')
                print(f"  * Field [{field}]: {rval}")

