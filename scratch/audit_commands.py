import os
import re

commands_dir = r"c:\xampp\htdocs\asri-boarding-house\app\Console\Commands"
console_file = r"c:\xampp\htdocs\asri-boarding-house\routes\console.php"

with open(console_file, "r", encoding="utf-8") as f:
    console_code = f.read()

# Extract scheduled command names from routes/console.php
scheduled = re.findall(r"Schedule::command\(\s*['\"]([^'\"]+)['\"]\s*\)", console_code)
print(f"Scheduled Commands in routes/console.php ({len(scheduled)}):")
for s in scheduled:
    print(f" - {s}")

print("\n--- Command Class Signatures & Files ---")
signatures = {}
for file in os.listdir(commands_dir):
    if file.endswith(".php"):
        filepath = os.path.join(commands_dir, file)
        with open(filepath, "r", encoding="utf-8") as cf:
            content = cf.read()
            sig_match = re.search(r"protected\s+\$signature\s*=\s*['\"]([^'\"]+)['\"]", content)
            sig = sig_match.group(1) if sig_match else "NONE"
            signatures[sig] = file
            print(f"File: {file} => Signature: '{sig}'")

print("\n--- Cross-Check Scheduled vs Registered Commands ---")
missing_in_commands = []
for s in scheduled:
    # Command signature might have arguments/options, compare base name
    base_s = s.split()[0]
    matched = False
    for sig in signatures:
        base_sig = sig.split()[0]
        if base_s == base_sig:
            matched = True
            break
    if not matched:
        missing_in_commands.append(s)

if missing_in_commands:
    print(f"MISSING SCHEDULED COMMANDS: {missing_in_commands}")
else:
    print("ALL scheduled commands map to actual Artisan Command classes!")

print("\n--- Unscheduled Commands in app/Console/Commands ---")
for sig, file in signatures.items():
    base_sig = sig.split()[0]
    matched = any(s.split()[0] == base_sig for s in scheduled)
    if not matched:
        print(f" - Standalone/Manual Command: '{sig}' in {file}")
