import os
import re

base_dir = r"c:\xampp\htdocs\asri-boarding-house\app"

raw_patterns = [
    (r'DB::raw\s*\((.*?)\)', 'DB::raw'),
    (r'->whereRaw\s*\((.*?)\)', 'whereRaw'),
    (r'->selectRaw\s*\((.*?)\)', 'selectRaw'),
    (r'->orderByRaw\s*\((.*?)\)', 'orderByRaw'),
    (r'->havingRaw\s*\((.*?)\)', 'havingRaw'),
    (r'DB::statement\s*\((.*?)\)', 'DB::statement'),
    (r'DB::unprepared\s*\((.*?)\)', 'DB::unprepared'),
    (r'DB::select\s*\((.*?)\)', 'DB::select')
]

print("==================================================")
print("RAW SQL QUERY AUDIT FOR SECURITY & SQL INJECTION")
print("==================================================")

findings = []

for root, dirs, files in os.walk(base_dir):
    for f in files:
        if f.endswith(".php"):
            fpath = os.path.join(root, f)
            rel = os.path.relpath(fpath, base_dir)
            with open(fpath, "r", encoding="utf-8", errors="ignore") as file:
                lines = file.readlines()
                
            for idx, line in enumerate(lines):
                for pat, name in raw_patterns:
                    matches = re.finditer(pat, line)
                    for m in matches:
                        expr = m.group(1).strip()
                        findings.append({
                            'file': rel,
                            'line': idx + 1,
                            'type': name,
                            'expression': expr,
                            'full_line': line.strip()
                        })

print(f"Total Raw SQL expressions found in app/: {len(findings)}\n")
for f in findings:
    print(f"[{f['type']}] app/{f['file']}:{f['line']}")
    print(f"   Expr: {f['expression']}")
    print(f"   Line: {f['full_line']}\n")

