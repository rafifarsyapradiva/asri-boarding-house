import os
import re
import glob

models_dir = 'app/Models'
models = sorted(glob.glob(os.path.join(models_dir, '*.php')))

model_relations = {}
for m_path in models:
    m_name = os.path.basename(m_path).replace('.php', '')
    with open(m_path, 'r', encoding='utf-8') as f:
        content = f.read()
    
    # Extract methods returning BelongsTo, HasMany, HasOne, BelongsToMany
    methods = re.finditer(r'public\s+function\s+([a-zA-Z0-9_]+)\s*\([^)]*\)\s*:\s*([a-zA-Z0-9_]+)\s*\{(.*?)\}', content, re.DOTALL)
    rels = []
    for m in methods:
        fn_name = m.group(1)
        ret_type = m.group(2)
        body = m.group(3)
        # find target model: return $this->belongsTo(Target::class, 'foreign_key', 'owner_key')
        target_m = re.search(r'\$this->(belongsTo|hasMany|hasOne|belongsToMany)\s*\(\s*([a-zA-Z0-9_]+)::class(?:,\s*[\'"]([^\'"]+)[\'"])?(?:,\s*[\'"]([^\'"]+)[\'"])?', body)
        if target_m:
            rel_type = target_m.group(1)
            target_class = target_m.group(2)
            fk = target_m.group(3)
            owner_pk = target_m.group(4)
            rels.append({
                'method': fn_name,
                'type': rel_type,
                'target': target_class,
                'fk': fk,
                'pk': owner_pk
            })
    model_relations[m_name] = rels

print("=== ELOQUENT RELATIONSHIPS IN APP/MODELS ===")
for m, rels in model_relations.items():
    print(f"\nModel {m} ({len(rels)} relations):")
    for r in rels:
        print(f"  -> {r['method']}(): {r['type']}({r['target']}, fk={r['fk']}, pk={r['pk']})")
