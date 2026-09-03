import json
import os
import re

def generate_matrices():
    routes_path = r"c:\xampp\htdocs\asri-boarding-house\scratch\routes.json"
    try:
        with open(routes_path, "r", encoding="utf-16") as f:
            routes = json.load(f)
    except Exception:
        with open(routes_path, "r", encoding="utf-8") as f:
            routes = json.load(f)

    print(f"Total routes in json: {len(routes)}")

    # Classify routes
    # Filter out internal routes (like 'up') if desired or keep
    web_routes = []
    api_routes = []

    for r in routes:
        uri = r.get("uri", "")
        mw = r.get("middleware", [])
        if uri.startswith("api/") or "api" in mw:
            api_routes.append(r)
        else:
            web_routes.append(r)

    print(f"Web routes count: {len(web_routes)}")
    print(f"API routes count: {len(api_routes)}")

    # Analyze Controller mappings & FormRequests / Policies / Services
    controllers_dir = r"c:\xampp\htdocs\asri-boarding-house\app\Http\Controllers"
    
    # We will output details for matrices
    with open(r"c:\xampp\htdocs\asri-boarding-house\scratch\matrix_summary.txt", "w", encoding="utf-8") as out:
        out.write(f"WEB_COUNT={len(web_routes)}\n")
        out.write(f"API_COUNT={len(api_routes)}\n")
        out.write(f"TOTAL_COUNT={len(routes)}\n\n")

        out.write("--- API ROUTES ---\n")
        for ar in api_routes:
            out.write(f"METHOD: {ar.get('method')} | URI: {ar.get('uri')} | NAME: {ar.get('name')} | ACTION: {ar.get('action')} | MW: {','.join(ar.get('middleware', []))}\n")

if __name__ == "__main__":
    generate_matrices()
