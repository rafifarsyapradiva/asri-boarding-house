import os
import re
import glob

# 1. Parse Blueprint/Entity_Relationship_Diagram_Kost.md
erd_file = r'Blueprint/Entity_Relationship_Diagram_Kost.md'
with open(erd_file, 'r', encoding='utf-8') as f:
    erd_text = f.read()

# Extract mermaid erDiagram
mermaid_match = re.search(r'```mermaid\s*\n%%\{init:.*?%%(?:\s*\n)?erDiagram\s*\n(.*?)\n```', erd_text, re.DOTALL)
if not mermaid_match:
    mermaid_match = re.search(r'erDiagram\s*\n(.*?)\n```', erd_text, re.DOTALL)

mermaid_content = mermaid_match.group(1) if mermaid_match else ""

# Extract entities and fields from mermaid
entities_mermaid = {}
current_entity = None
for line in mermaid_content.splitlines():
    line = line.strip()
    if not line or line.startswith('%%'):
        continue
    # Check relationship line
    if any(op in line for op in ['||--', '}o--', '|o--', 'o{--', '}o..', '||..']):
        continue
    # Entity definition
    m_ent = re.match(r'^([a-zA-Z0-9_]+)\s*\{', line)
    if m_ent:
        current_entity = m_ent.group(1)
        entities_mermaid[current_entity] = []
        continue
    if line == '}':
        current_entity = None
        continue
    if current_entity:
        entities_mermaid[current_entity].append(line)

print(f"Entities in Mermaid: {len(entities_mermaid)}")
for ent in sorted(entities_mermaid.keys()):
    print(f"  - {ent} ({len(entities_mermaid[ent])} attributes)")

# Extract relationships from mermaid
relationships_mermaid = []
for line in mermaid_content.splitlines():
    line = line.strip()
    if any(op in line for op in ['||--', '}o--', '|o--', 'o{--', '}o..', '||..', '|{--', '--|{', '--o{', '--||']):
        relationships_mermaid.append(line)

print(f"\nRelationships in Mermaid ({len(relationships_mermaid)}):")
for r in relationships_mermaid:
    print(f"  {r}")

# Extract entities in Section 2 (Kamus Data)
kamus_entities = re.findall(r'###\s*2\.\d+\.\s*Tabel\s*`?([a-zA-Z0-9_]+)`?', erd_text)
print(f"\nEntities in Section 2 Kamus Data ({len(kamus_entities)}): {kamus_entities}")

# Check Section 1.1 Matriks Relasi
matriks_rows = re.findall(r'\|\s*\d+\s*\|\s*`?([a-zA-Z0-9_]+)`?\s*\|\s*`?([a-zA-Z0-9_]+)`?\s*\|\s*\*\*([^\*]+)\*\*\s*\|\s*`?([a-zA-Z0-9_]+)`?\s*\|\s*`?([a-zA-Z0-9_]+)`?\s*\|\s*\*\*([^\*]+)\*\*\s*\|', erd_text)
print(f"\nRows in Section 1.1 Matriks ({len(matriks_rows)}):")
for r in matriks_rows:
    print(f"  {r[0]} -> {r[1]} [{r[2]}], FK: {r[3]}, PK: {r[4]}, ON DELETE: {r[5]}")

# Check Section 5.1 FK Policy Table
fk_policy_rows = re.findall(r'\|\s*`?([a-zA-Z0-9_]+)`?\s*\|\s*`?([a-zA-Z0-9_]+)`?\s*\|\s*`?([a-zA-Z0-9_]+)`?\s*\|\s*`?([a-zA-Z0-9_]+)`?\s*\|\s*\*\*([^\*]+)\*\*\s*\|', erd_text)
print(f"\nRows in Section 5.1 FK Policy ({len(fk_policy_rows)}):")
for r in fk_policy_rows:
    print(f"  Child: {r[0]}, FK: {r[1]}, Parent: {r[2]}, PK: {r[3]}, ON DELETE: {r[4]}")
