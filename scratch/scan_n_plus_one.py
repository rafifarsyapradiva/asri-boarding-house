import os
import re
import sys

sys.stdout.reconfigure(encoding='utf-8')
base_dir = r"c:\xampp\htdocs\asri-boarding-house"

print("==================================================")
print("QUERY PERFORMANCE & N+1 / BLADE DIRECT QUERY SCANNER")
print("==================================================")

# 1. Scan Blade views for direct Model calls (e.g., App\Models\..., Model::where, etc.)
views_dir = os.path.join(base_dir, "resources", "views")
blade_findings = []

for root, dirs, files in os.walk(views_dir):
    for f in files:
        if f.endswith(".blade.php"):
            fpath = os.path.join(root, f)
            rel = os.path.relpath(fpath, base_dir)
            with open(fpath, "r", encoding="utf-8", errors="ignore") as file:
                lines = file.readlines()
            for idx, line in enumerate(lines):
                # Search for Model queries in Blade, e.g. ::where, ::find, ::all, ::get
                matches = re.finditer(r'([A-Z][a-zA-Z0-9]+::(?:where|find|all|get|paginate|count|sum|pluck)\s*\()', line)
                for m in matches:
                    # Ignore Setting::get if it's cached / helper
                    blade_findings.append({
                        'file': rel,
                        'line': idx + 1,
                        'match': m.group(1),
                        'code': line.strip()
                    })

print(f"\n1. Direct Model Queries inside Blade Views: {len(blade_findings)}")
setting_gets = 0
other_queries = 0
for b in blade_findings:
    if "Setting::get" in b['match']:
        setting_gets += 1
    else:
        other_queries += 1
        print(f"  * {b['file']}:{b['line']} -> {b['match']}")
        print(f"    Line: {b['code']}")

print(f"  - Setting::get (Cached in memory): {setting_gets}")
print(f"  - Other direct Model queries: {other_queries}")

# 2. Check controllers for foreach loops with Model queries inside
controllers_dir = os.path.join(base_dir, "app", "Http", "Controllers")
loop_findings = []

for root, dirs, files in os.walk(controllers_dir):
    for f in files:
        if f.endswith(".php"):
            fpath = os.path.join(root, f)
            rel = os.path.relpath(fpath, base_dir)
            with open(fpath, "r", encoding="utf-8", errors="ignore") as file:
                code = file.read()
            
            # Simple check for foreach with ->save or query inside
            foreach_blocks = re.findall(r'foreach\s*\([^{]+\)\s*\{([^}]+)\}', code)
            for block in foreach_blocks:
                if any(q in block for q in ['::where', '::find', '->where(', '->get(', '->first(']):
                    loop_findings.append({
                        'file': rel,
                        'block': block.strip()
                    })

print(f"\n2. Queries inside Foreach Loops in Controllers: {len(loop_findings)}")
for l in loop_findings:
    print(f"  * {l['file']}")

