import json
import inspect
import os
import re

def audit_routes():
    routes_path = r"c:\xampp\htdocs\asri-boarding-house\scratch\routes.json"
    try:
        with open(routes_path, "r", encoding="utf-16") as f:
            routes = json.load(f)
    except Exception:
        with open(routes_path, "r", encoding="utf-8") as f:
            routes = json.load(f)

    print(f"Total Routes Loaded: {len(routes)}")

    # 1. Inspect Route Wildcard and Order Shadowing
    # Check if a parameterized route shadows a static route or if static routes come before parameterized ones
    routes_by_prefix = {}
    for r in routes:
        uri = r.get("uri", "")
        method = r.get("method", "")
        parts = uri.split("/")
        prefix = parts[0] if len(parts) > 0 else ""
        routes_by_prefix.setdefault(prefix, []).append(r)

    print("\n--- Route Order & Shadowing Analysis ---")
    shadow_warnings = []
    for prefix, r_list in routes_by_prefix.items():
        seen_wildcard = []
        for r in r_list:
            uri = r.get("uri", "")
            method = r.get("method", "")
            # Check if any earlier wildcard in same method matches this static uri
            parts = uri.split("/")
            has_wildcard = any(p.startswith("{") for p in parts)
            
            for w_method, w_uri, w_parts in seen_wildcard:
                if w_method == method and len(w_parts) == len(parts):
                    # Check if w_uri could shadow uri
                    match = True
                    for wp, up in zip(w_parts, parts):
                        if not wp.startswith("{") and wp != up:
                            match = False
                            break
                    if match and not has_wildcard:
                        shadow_warnings.append(f"POTENTIAL SHADOW: Wildcard '{w_method} {w_uri}' registered before static '{method} {uri}'")
            
            if has_wildcard:
                seen_wildcard.append((method, uri, parts))

    print(f"Shadow Warnings: {len(shadow_warnings)}")
    for sw in shadow_warnings:
        print(f"  [WARN] {sw}")

    # 2. Check Route Model Parameter Bindings in Controllers
    print("\n--- Route Model Parameter Binding Analysis ---")
    binding_issues = []
    app_base = r"c:\xampp\htdocs\asri-boarding-house\app"

    for r in routes:
        uri = r.get("uri", "")
        action = r.get("action", "")
        method = r.get("method", "")
        
        # Extract uri params: {param} or {param?}
        params = re.findall(r"\{([a-zA-Z0-9_]+)\??\}", uri)
        
        if "@" in action:
            ctrl_class, ctrl_method = action.split("@", 1)
            if ctrl_class.startswith("App\\"):
                rel_file = ctrl_class.replace("App\\", "").replace("\\", os.sep) + ".php"
                full_path = os.path.join(app_base, rel_file)
                if os.path.exists(full_path):
                    with open(full_path, "r", encoding="utf-8", errors="ignore") as f:
                        code = f.read()
                        # Find the method signature
                        pattern = rf"public\s+function\s+{ctrl_method}\s*\((.*?)\)"
                        match = re.search(pattern, code, re.DOTALL)
                        if match:
                            sig = match.group(1)
                            # Extract arguments
                            args = [a.strip() for a in sig.split(",") if a.strip()]
                            # Look for variable names like $param, $id, etc.
                            arg_vars = [re.search(r"\$([a-zA-Z0-9_]+)", a).group(1) for a in args if re.search(r"\$([a-zA-Z0-9_]+)", a)]
                            
                            # Compare params vs arg_vars (excluding Request, Service, etc.)
                            # Typically route params should match one of arg_vars or $id
                            for p in params:
                                if p not in arg_vars and "id" not in arg_vars and "request" not in arg_vars:
                                    binding_issues.append((uri, action, p, arg_vars))

    print(f"Parameter Binding Discrepancies: {len(binding_issues)}")
    for bi in binding_issues:
        print(f"  [INFO] URI: {bi[0]} | Action: {bi[1]} | Route Param: '{bi[2]}' | Method Args: {bi[3]}")

    # 3. Categorize Routes: Web vs API vs System
    web_routes = []
    api_routes = []
    system_routes = []
    
    for r in routes:
        uri = r.get("uri", "")
        middleware = r.get("middleware", [])
        if uri.startswith("api/") or "api" in middleware:
            api_routes.append(r)
        elif uri in ["up", "sanctum/csrf-cookie", "_ignition/health-check", "_ignition/execute-solution", "_ignition/update-config"]:
            system_routes.append(r)
        else:
            web_routes.append(r)

    print(f"\nRoute Inventory Breakdown:")
    print(f" - Web Routes: {len(web_routes)}")
    print(f" - API Routes: {len(api_routes)}")
    print(f" - System / Internal Routes: {len(system_routes)}")
    print(f" - Total: {len(routes)}")

if __name__ == "__main__":
    audit_routes()
