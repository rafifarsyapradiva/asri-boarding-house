import os, glob

cmd_dir = r"c:\xampp\htdocs\asri-boarding-house\app\Console\Commands"
print(f"Commands in {cmd_dir}:")
for fpath in glob.glob(os.path.join(cmd_dir, "*.php")):
    bname = os.path.basename(fpath)
    with open(fpath, 'r', encoding='utf-8') as f:
        content = f.read()
    signature = ""
    for line in content.splitlines():
        if "$signature =" in line or "$signature=" in line:
            signature = line.strip()
            break
    print(f"  - {bname} -> {signature}")
