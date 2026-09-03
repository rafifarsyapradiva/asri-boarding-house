import os
import re
import json
import glob

base_dir = r"c:\xampp\htdocs\asri-boarding-house"

def get_files(pattern):
    return glob.glob(os.path.join(base_dir, pattern), recursive=True)

def analyze_models():
    models_dir = os.path.join(base_dir, "app", "Models")
    model_data = {}
    for mf in glob.glob(os.path.join(models_dir, "*.php")):
        mname = os.path.splitext(os.path.basename(mf))[0]
        with open(mf, "r", encoding="utf-8", errors="ignore") as f:
            code = f.read()
        
        # Table
        table_match = re.search(r'protected\s+\$table\s*=\s*[\'"]([^\'"]+)[\'"]', code)
        table = table_match.group(1) if table_match else None
        
        # Primary Key
        pk_match = re.search(r'protected\s+\$primaryKey\s*=\s*[\'"]([^\'"]+)[\'"]', code)
        pk = pk_match.group(1) if pk_match else 'id'
        
        # KeyType & Incrementing
        kt_match = re.search(r'protected\s+\$keyType\s*=\s*[\'"]([^\'"]+)[\'"]', code)
        kt = kt_match.group(1) if kt_match else 'int'
        inc_match = re.search(r'public\s+\$incrementing\s*=\s*(true|false)', code)
        inc = inc_match.group(1) if inc_match else 'true'
        
        # SoftDeletes
        has_sd = "use SoftDeletes;" in code or "use Illuminate\\Database\\Eloquent\\SoftDeletes;" in code
        
        # Fillable
        fillable_match = re.search(r'protected\s+\$fillable\s*=\s*\[(.*?)\];', code, re.DOTALL)
        fillable = []
        if fillable_match:
            fillable = re.findall(r'[\'"]([^\'"]+)[\'"]', fillable_match.group(1))
            
        # Guarded
        guarded_match = re.search(r'protected\s+\$guarded\s*=\s*\[(.*?)\];', code, re.DOTALL)
        guarded = []
        if guarded_match:
            guarded = re.findall(r'[\'"]([^\'"]+)[\'"]', guarded_match.group(1))
            
        # Casts
        casts_match = re.search(r'(protected\s+\$casts\s*=\s*\[|function\s+casts\s*\(\s*\)\s*:\s*array\s*\{\s*return\s*\[)(.*?)(?:\];|\}\s*\})', code, re.DOTALL)
        casts = {}
        if casts_match:
            c_block = casts_match.group(2)
            c_pairs = re.findall(r'[\'"]([^\'"]+)[\'"]\s*=>\s*[\'"]?([^\'",\s]+)[\'"]?', c_block)
            casts = {k: v for k, v in c_pairs}
            
        # Relationships
        rel_matches = re.findall(r'public\s+function\s+([a-zA-Z0-9_]+)\s*\([^)]*\)\s*(?::\s*([a-zA-Z0-9_\\<>]+))?\s*\{\s*return\s+\$this->(belongsTo|hasMany|hasOne|belongsToMany|morphTo|morphMany|morphOne|hasManyThrough)\s*\((.*?)\);', code, re.DOTALL)
        relationships = []
        for rname, rtype_hint, rrel, rargs in rel_matches:
            args = [a.strip().strip('\'"') for a in rargs.split(',')]
            relationships.append({
                'name': rname,
                'type': rrel,
                'target': args[0] if len(args) > 0 else '',
                'foreign_key': args[1] if len(args) > 1 else '',
                'other_key': args[2] if len(args) > 2 else ''
            })
            
        # Constants
        const_matches = re.findall(r'public\s+const\s+([A-Z0-9_]+)\s*=\s*[\'"]?([^\'";]+)[\'"]?;', code)
        constants = {k: v for k, v in const_matches}
        
        model_data[mname] = {
            'file': mf,
            'table': table,
            'pk': pk,
            'keyType': kt,
            'incrementing': inc,
            'soft_deletes': has_sd,
            'fillable': fillable,
            'guarded': guarded,
            'casts': casts,
            'relationships': relationships,
            'constants': constants
        }
    return model_data

def analyze_concurrency_and_transactions():
    app_dir = os.path.join(base_dir, "app")
    findings = []
    for root, dirs, files in os.walk(app_dir):
        for f in files:
            if f.endswith(".php"):
                fpath = os.path.join(root, f)
                with open(fpath, "r", encoding="utf-8", errors="ignore") as file:
                    content = file.read()
                
                # Check DB::transaction
                tx_matches = re.findall(r'DB::transaction\s*\(', content)
                # Check lockForUpdate
                lock_matches = re.findall(r'lockForUpdate\s*\(', content)
                # Check raw queries
                raw_matches = re.findall(r'DB::raw\s*\(', content)
                # Check sharedLock
                shared_lock_matches = re.findall(r'sharedLock\s*\(', content)
                
                if tx_matches or lock_matches or raw_matches:
                    findings.append({
                        'file': os.path.relpath(fpath, base_dir),
                        'transactions': len(tx_matches),
                        'lockForUpdate': len(lock_matches),
                        'sharedLock': len(shared_lock_matches),
                        'raw_queries': len(raw_matches)
                    })
    return findings

def analyze_controllers_and_services():
    services_dir = os.path.join(base_dir, "app", "Services")
    controllers_dir = os.path.join(base_dir, "app", "Http", "Controllers")
    
    analysis = {}
    for dirpath in [services_dir, controllers_dir]:
        for root, dirs, files in os.walk(dirpath):
            for f in files:
                if f.endswith(".php"):
                    fpath = os.path.join(root, f)
                    with open(fpath, "r", encoding="utf-8", errors="ignore") as file:
                        code = file.read()
                    
                    rel = os.path.relpath(fpath, base_dir)
                    # Check financial calculations
                    has_denda = "denda" in code.lower()
                    has_dp = "nominal_dp" in code or "is_dp" in code or "dp" in code.lower()
                    has_deposit = "deposit" in code.lower()
                    has_midtrans = "midtrans" in code.lower() or "snap" in code.lower()
                    has_lock = "lockforupdate" in code.lower()
                    has_tx = "db::transaction" in code.lower()
                    has_overlap = "where(" in code and ("tanggal_mulai" in code or "tanggal_masuk" in code)
                    
                    analysis[rel] = {
                        'has_denda': has_denda,
                        'has_dp': has_dp,
                        'has_deposit': has_deposit,
                        'has_midtrans': has_midtrans,
                        'has_lock': has_lock,
                        'has_tx': has_tx,
                        'has_overlap_query': has_overlap
                    }
    return analysis

if __name__ == "__main__":
    models = analyze_models()
    concurrency = analyze_concurrency_and_transactions()
    logic = analyze_controllers_and_services()
    
    with open("scratch/forensic_analysis_dump.json", "w", encoding="utf-8") as out:
        json.dump({
            'models': models,
            'concurrency': concurrency,
            'logic': logic
        }, out, indent=2)
    print("Forensic analysis dump saved to scratch/forensic_analysis_dump.json")
