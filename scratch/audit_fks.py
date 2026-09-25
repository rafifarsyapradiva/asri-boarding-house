import os
import re
import glob

# Extract foreign keys from all migrations
migration_dir = 'database/migrations'
migrations = sorted(glob.glob(os.path.join(migration_dir, '*.php')))

fks_found = []
for m_path in migrations:
    m_name = os.path.basename(m_path)
    with open(m_path, 'r', encoding='utf-8') as f:
        content = f.read()
    
    # Check Schema::create or Schema::table
    # Find table name
    # We can match $table->foreign('...')->references('...')->on('...')->onDelete('...')
    # or $table->foreignId('...')->constrained('...')->onDelete('...')
    # and also dropForeign
    table_matches = re.finditer(r'Schema::(create|table)\s*\(\s*[\'"]([^\'"]+)[\'"]\s*,\s*function\s*\([^)]*\)\s*\{(.*?)\}\s*\);', content, re.DOTALL)
    for tm in table_matches:
        action_type = tm.group(1)
        tbl_name = tm.group(2)
        tbl_body = tm.group(3)
        
        # Search for foreign key additions
        fk_matches = re.finditer(r'\$table->foreign\s*\(\s*[\'"]([^\'"]+)[\'"]\s*\)\s*->references\s*\(\s*[\'"]([^\'"]+)[\'"]\s*\)\s*->on\s*\(\s*[\'"]([^\'"]+)[\'"]\s*\)(?:->onDelete\s*\(\s*[\'"]([^\'"]+)[\'"]\s*\))?(?:->onUpdate\s*\(\s*[\'"]([^\'"]+)[\'"]\s*\))?', tbl_body)
        for fk in fk_matches:
            col = fk.group(1)
            ref_col = fk.group(2)
            ref_tbl = fk.group(3)
            on_del = fk.group(4) or "RESTRICT/NO ACTION"
            fks_found.append({
                'table': tbl_name,
                'col': col,
                'ref_table': ref_tbl,
                'ref_col': ref_col,
                'on_delete': on_del.upper(),
                'migration': m_name,
                'action': 'add'
            })
            
        # Also dropForeign
        drop_fks = re.finditer(r'\$table->dropForeign\s*\(\s*[\'"]([^\'"]+)[\'"]\s*\)', tbl_body)
        for dfk in drop_fks:
            fks_found.append({
                'table': tbl_name,
                'fk_name': dfk.group(1),
                'migration': m_name,
                'action': 'drop'
            })

print("Found FK operations in migrations:")
for fk in fks_found:
    print(fk)
