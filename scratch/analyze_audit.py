import json
import os
import re
import glob

def run_analysis():
    routes_path = r"c:\xampp\htdocs\asri-boarding-house\scratch\routes.json"
    try:
        with open(routes_path, "r", encoding="utf-16") as f:
            routes = json.load(f)
    except Exception:
        with open(routes_path, "r", encoding="utf-8") as f:
            routes = json.load(f)

    print(f"Total Routes in registry: {len(routes)}")

    # Group routes by domain / method / uri
    uri_map = {}
    name_map = {}
    action_map = {}
    broken_controllers = []
    
    for r in routes:
        uri = r.get("uri", "")
        method = r.get("method", "")
        name = r.get("name", "")
        action = r.get("action", "")
        middleware = r.get("middleware", [])

        # Check duplicate URI + method
        key = f"{method} {uri}"
        uri_map.setdefault(key, []).append(r)

        if name:
            name_map.setdefault(name, []).append(r)

        # Check action controller@method
        if "@" in action:
            ctrl, mthd = action.split("@", 1)
            action_map.setdefault(ctrl, set()).add(mthd)
        elif "\\" in action and "Closure" not in action:
            action_map.setdefault(action, set()).add("__invoke")

    # Check for duplicate names
    dup_names = {k: v for k, v in name_map.items() if len(v) > 1}
    print(f"\nDuplicate Route Names ({len(dup_names)}):")
    for name, list_r in dup_names.items():
        print(f" - {name}: {[r['uri'] + ' (' + r['method'] + ')' for r in list_r]}")

    # Check for controller existence
    app_path = r"c:\xampp\htdocs\asri-boarding-house\app"
    print("\n--- Controller & Method Verification ---")
    missing_classes = []
    missing_methods = []

    for ctrl_class, methods in action_map.items():
        if ctrl_class.startswith("App\\"):
            rel_file = ctrl_class.replace("App\\", "").replace("\\", os.sep) + ".php"
            full_path = os.path.join(app_path, rel_file)
            if not os.path.exists(full_path):
                missing_classes.append((ctrl_class, full_path))
            else:
                with open(full_path, "r", encoding="utf-8", errors="ignore") as cf:
                    code = cf.read()
                    for m in methods:
                        # check function regex
                        pattern = rf"function\s+{m}\s*\("
                        if not re.search(pattern, code, re.IGNORECASE):
                            missing_methods.append((ctrl_class, m, full_path))

    print(f"Missing Controller Classes: {len(missing_classes)}")
    for mc in missing_classes:
        print(f"  MISSING CLASS: {mc[0]} at {mc[1]}")

    print(f"Missing Controller Methods: {len(missing_methods)}")
    for mm in missing_methods:
        print(f"  MISSING METHOD: {mm[0]}@{mm[1]}")

    # Check Blade Views
    print("\n--- View References in Controllers ---")
    view_dir = r"c:\xampp\htdocs\asri-boarding-house\resources\views"
    missing_views = set()
    view_regex = re.compile(r"view\(\s*['\"]([^'\"]+)['\"]")

    for root, dirs, files in os.walk(app_path):
        for file in files:
            if file.endswith(".php"):
                with open(os.path.join(root, file), "r", encoding="utf-8", errors="ignore") as f:
                    content = f.read()
                    for match in view_regex.finditer(content):
                        view_name = match.group(1)
                        # convert dot notation to path
                        view_file = view_name.replace(".", os.sep) + ".blade.php"
                        full_view_path = os.path.join(view_dir, view_file)
                        if not os.path.exists(full_view_path):
                            # check if it's a php file without blade
                            alt_view_path = os.path.join(view_dir, view_name.replace(".", os.sep) + ".php")
                            if not os.path.exists(alt_view_path):
                                missing_views.add((os.path.join(root, file), view_name))

    print(f"Missing Blade Views: {len(missing_views)}")
    for mv in missing_views:
        print(f"  MISSING VIEW: {mv[1]} referenced in {mv[0]}")

    # Check Route Name usage in Blade files and controllers
    print("\n--- Route Name References Validation ---")
    registered_names = set(name_map.keys())
    route_call_regex = re.compile(r"route\(\s*['\"]([^'\"]+)['\"]")
    missing_route_calls = set()

    for check_dir in [app_path, view_dir]:
        for root, dirs, files in os.walk(check_dir):
            for file in files:
                if file.endswith((".php", ".blade.php")):
                    with open(os.path.join(root, file), "r", encoding="utf-8", errors="ignore") as f:
                        content = f.read()
                        for match in route_call_regex.finditer(content):
                            r_name = match.group(1)
                            if r_name not in registered_names:
                                missing_route_calls.add((os.path.join(root, file), r_name))

    print(f"Missing Route Name calls: {len(missing_route_calls)}")
    for mr in missing_route_calls:
        print(f"  MISSING ROUTE NAME: '{mr[1]}' called in {mr[0]}")

if __name__ == "__main__":
    run_analysis()
