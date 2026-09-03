import json
import os
import re
import subprocess
import glob

print("=== 1. ROUTE LIST ANALYSIS ===")
res = subprocess.run(['php', 'artisan', 'route:list', '--json'], capture_output=True, text=True, cwd=r'c:\xampp\htdocs\asri-boarding-house')
routes = json.loads(res.stdout)
print(f"Total routes: {len(routes)}")

web_routes = []
api_routes = []
other_routes = []

for r in routes:
    middleware = r.get('middleware', [])
    uri = r.get('uri', '')
    if uri.startswith('api/') or 'api' in middleware:
        api_routes.append(r)
    elif 'web' in middleware or uri == '/':
        web_routes.append(r)
    else:
        other_routes.append(r)

print(f"Web routes: {len(web_routes)}")
print(f"API routes: {len(api_routes)}")
print(f"Other routes: {len(other_routes)}")

# Check controller action existence
missing_actions = []
for r in routes:
    action = r.get('action', '')
    if '@' in action:
        cls_name, method_name = action.split('@')
        # Check if class exists
        cls_file = cls_name.replace('\\', '/').replace('App/', 'app/') + '.php'
        abs_path = os.path.join(r'c:\xampp\htdocs\asri-boarding-house', cls_file)
        if not os.path.exists(abs_path):
            missing_actions.append({'route': r, 'reason': f'Class file not found: {cls_file}'})
        else:
            with open(abs_path, 'r', encoding='utf-8', errors='ignore') as f:
                content = f.read()
                if f'function {method_name}' not in content:
                    missing_actions.append({'route': r, 'reason': f'Method {method_name} not found in {cls_name}'})

print(f"Missing Controller/Method actions count: {len(missing_actions)}")
for ma in missing_actions:
    print(f"  - Route: {ma['route']['method']} {ma['route']['uri']} -> {ma['route']['action']} | Reason: {ma['reason']}")

print("\n=== 2. SCHEDULER LIST ANALYSIS ===")
res_sched = subprocess.run(['php', 'artisan', 'schedule:list'], capture_output=True, text=True, cwd=r'c:\xampp\htdocs\asri-boarding-house')
print(res_sched.stdout)

print("\n=== 3. COMMAND LIST ANALYSIS ===")
res_cmd = subprocess.run(['php', 'artisan', 'list', '--format=json'], capture_output=True, text=True, cwd=r'c:\xampp\htdocs\asri-boarding-house')
cmds = json.loads(res_cmd.stdout)
custom_cmds = [c for c in cmds.get('commands', []) if not c['name'].startswith(('help', 'list', 'make:', 'migrate', 'db:', 'route:', 'config:', 'cache:', 'view:', 'event:', 'queue:', 'schedule:', 'serve', 'test', 'vendor:', 'storage:', 'key:', 'optimize', 'stub:', 'schema:', 'env:', 'auth:', 'channel:', 'clear-resets', 'docs', 'down', 'up', 'inspire', 'notifications:', 'sail:'))]
print(f"Custom Artisan Commands ({len(custom_cmds)}):")
for cc in custom_cmds:
    print(f"  - {cc['name']}: {cc['description']}")

print("\n=== 4. TEST FAILURE LOG PARSING ===")
log_path = r"C:\Users\RAFIF\.gemini\antigravity-ide\brain\547008c0-b9b7-4b81-b8ef-8cd46c6f9500\.system_generated\tasks\task-12.log"
if os.path.exists(log_path):
    with open(log_path, 'r', encoding='utf-8', errors='ignore') as f:
        log_content = f.read()
    
    failures = re.findall(r'FAILED\s+(Tests\\[^\n]+)', log_content)
    print(f"Total FAILED test cases matched: {len(failures)}")
    for idx, fail in enumerate(failures, 1):
        print(f"  {idx}. {fail.strip()}")

