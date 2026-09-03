import os
import re

print("=== CHECKING BLADE VIEW RESOLUTION IN CONTROLLERS ===")

views_dir = r"c:\xampp\htdocs\asri-boarding-house\resources\views"
controllers_dir = r"c:\xampp\htdocs\asri-boarding-house\app\Http\Controllers"

view_pattern = re.compile(r"view\(\s*['\"]([^'\"]+)['\"]")

missing_views = []
found_views = 0

for root, dirs, files in os.walk(controllers_dir):
    for file in files:
        if file.endswith(".php"):
            filepath = os.path.join(root, file)
            with open(filepath, "r", encoding="utf-8", errors="ignore") as f:
                content = f.read()
                matches = view_pattern.findall(content)
                for v in matches:
                    # e.g. "admin.kamar.index" -> "admin/kamar/index.blade.php"
                    view_file_path = os.path.join(views_dir, v.replace(".", "/") + ".blade.php")
                    if not os.path.exists(view_file_path):
                        rel_ctrl = os.path.relpath(filepath, controllers_dir)
                        missing_views.append({'view': v, 'controller': rel_ctrl, 'expected_path': view_file_path})
                    else:
                        found_views += 1

print(f"Total `view(...)` calls checked: {found_views + len(missing_views)}")
print(f"Valid Views: {found_views}")
print(f"Missing Views: {len(missing_views)}")
for mv in missing_views:
    print(f"  - View '{mv['view']}' in {mv['controller']} not found!")

