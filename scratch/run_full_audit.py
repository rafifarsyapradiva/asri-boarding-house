import json
import subprocess
import os
import re

print("=== STARTING LARAVEL ROUTE & SCHEDULER AUDIT ===")

# 1. Fetch route list JSON
result = subprocess.run(["php", "artisan", "route:list", "--json"], capture_output=True, text=True, cwd=r"c:\xampp\htdocs\asri-boarding-house")
if result.returncode != 0:
    print("Error getting route list:", result.stderr)
    exit(1)

routes = json.loads(result.stdout)
print(f"Total Registered Routes: {len(routes)}")

# 2. Check Controller & Method Existence
missing_controllers = []
missing_methods = []
valid_routes = 0

for r in routes:
    action = r.get("action", "")
    uri = r.get("uri", "")
    name = r.get("name", "")
    
    if action == "Closure" or "RedirectController" in action:
        valid_routes += 1
        continue
        
    if "@" in action:
        class_name, method_name = action.split("@")
    else:
        class_name = action
        method_name = "__invoke"
        
    # Convert namespace App\Http\Controllers\Foo to path app/Http/Controllers/Foo.php
    if class_name.startswith("App\\"):
        rel_path = class_name.replace("App\\", "app\\").replace("\\", "/") + ".php"
        full_path = os.path.join(r"c:\xampp\htdocs\asri-boarding-house", rel_path)
        
        if not os.path.exists(full_path):
            missing_controllers.append({'route': uri, 'name': name, 'class': class_name, 'file': full_path})
        else:
            with open(full_path, "r", encoding="utf-8", errors="ignore") as f:
                content = f.read()
                # Check method definition
                pattern = r"function\s+" + re.escape(method_name) + r"\s*\("
                if not re.search(pattern, content):
                    missing_methods.append({'route': uri, 'name': name, 'class': class_name, 'method': method_name, 'file': rel_path})
                else:
                    valid_routes += 1

print(f"Valid Routes with existing Controller & Method: {valid_routes}")
print(f"Missing Controllers: {len(missing_controllers)}")
for mc in missing_controllers:
    print("  - ", mc)
print(f"Missing Methods: {len(missing_methods)}")
for mm in missing_methods:
    print("  - ", mm)

# 3. Check Route Name references across app/ and resources/views/
registered_names = {r["name"] for r in routes if r.get("name")}

route_call_pattern = re.compile(r"route\(\s*['\"]([^'\"]+)['\"]")
broken_route_calls = set()
used_route_names = set()

for root_dir in [r"c:\xampp\htdocs\asri-boarding-house\app", r"c:\xampp\htdocs\asri-boarding-house\resources\views"]:
    for root, dirs, files in os.walk(root_dir):
        for file in files:
            if file.endswith(".php") or file.endswith(".blade.php"):
                filepath = os.path.join(root, file)
                with open(filepath, "r", encoding="utf-8", errors="ignore") as f:
                    content = f.read()
                    matches = route_call_pattern.findall(content)
                    for m in matches:
                        used_route_names.add(m)
                        if m not in registered_names and not m.startswith("http") and not m.startswith("/"):
                            # Filter out dynamic variables like route('admin.'.$var)
                            if not "$" in m and not "{" in m:
                                broken_route_calls.add((m, os.path.relpath(filepath, r"c:\xampp\htdocs\asri-boarding-house")))

print(f"\nTotal Unique Route Names Used in Code: {len(used_route_names)}")
print(f"Broken Route Name References found: {len(broken_route_calls)}")
for br, f in broken_route_calls:
    print(f"  - Missing Route Name '{br}' in file: {f}")

# 4. Check Scheduled Commands Existence
scheduled_commands = [
    'tagihan:generate-bulanan',
    'abh:proses-keterlambatan',
    'kontrak:reminder-habis',
    'reservasi:cancel-expired',
    'log-notifikasi:clear',
    'chat-guest:prune',
    'session:cleanup',
    'log:truncate',
    'abh:purge-trash',
    'inspire'
]

print("\n=== CHECKING SCHEDULER COMMANDS ===")
list_res = subprocess.run(["php", "artisan", "list", "--raw"], capture_output=True, text=True, cwd=r"c:\xampp\htdocs\asri-boarding-house")
artisan_commands = set()
for line in list_res.stdout.splitlines():
    parts = line.strip().split()
    if parts:
        artisan_commands.add(parts[0])

for cmd in scheduled_commands:
    if cmd in artisan_commands:
        print(f"  [OK] Scheduled command '{cmd}' is registered in Artisan.")
    else:
        print(f"  [FAIL] Scheduled command '{cmd}' is NOT registered in Artisan!")

