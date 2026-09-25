import re
import sys

if sys.stdout.encoding != 'utf-8':
    sys.stdout.reconfigure(encoding='utf-8')

with open('Blueprint/Flowchart_Kost.md', 'r', encoding='utf-8') as f:
    text = f.read()

diagrams = []
current_diag = None
current_section = ''

for i, line in enumerate(text.splitlines(keepends=True)):
    if line.startswith('### ') or line.startswith('## 1.'):
        current_section = line.strip()
    if line.strip() == '```mermaid':
        current_diag = {'section': current_section, 'start_line': i + 1, 'lines': []}
    elif line.strip() == '```' and current_diag is not None:
        current_diag['end_line'] = i + 1
        diagrams.append(current_diag)
        current_diag = None
    elif current_diag is not None:
        current_diag['lines'].append(line)

node_def_re = re.compile(r'([a-zA-Z0-9_]+)\s*(\(\[.*?\]\)|\{\{.*?\}\}|\[\[.*?\]\]|\[\(.*?\)\]|\(\(.*?\)\)|\[\/.*?\/\]|\[\\.*?\\\]|\{.*?\}|\[.*?\])')
edge_re = re.compile(r'([a-zA-Z0-9_]+)(?:\s*(?:\(\[.*?\]\)|\{\{.*?\}\}|\[\[.*?\]\]|\[\(.*?\)\]|\(\(.*?\)\)|\[\/.*?\/\]|\[\\.*?\\\]|\{.*?\}|\[.*?\]))?\s*(?:(-->|--\s*".*?"\s*-->|-->\|.*?\||<-->|<-->\|.*?\||-\.->|-\.->\|.*?\||==>|==\s*".*?"\s*==>))\s*([a-zA-Z0-9_]+)')

start_idx = int(sys.argv[1]) if len(sys.argv) > 1 else 0
end_idx = int(sys.argv[2]) if len(sys.argv) > 2 else len(diagrams)

for idx in range(start_idx, min(end_idx, len(diagrams))):
    d = diagrams[idx]
    nodes = {}
    edges = []
    for line in d['lines']:
        l = line.strip()
        if l.startswith('%%') or l.startswith('subgraph') or l == 'end' or l.startswith('flowchart') or l.startswith('graph'): continue
        for m in node_def_re.finditer(l):
            nid = m.group(1)
            raw = m.group(2)
            nodes[nid] = raw
        pos = 0
        while True:
            m = edge_re.search(l, pos)
            if not m: break
            src, op, dst = m.group(1), m.group(2), m.group(3)
            lbl = ''
            if '|' in op: lbl = op.split('|')[1].strip()
            elif '"' in op: lbl = op.split('"')[1].strip()
            edges.append((src, dst, lbl))
            pos = m.start() + len(src) + 1

    decisions = [nid for nid, raw in nodes.items() if raw.startswith('{') and raw.endswith('}') and not raw.startswith('{{')]
    if decisions:
        print(f"\n=======================================================")
        print(f"Diagram {idx+1}: {d['section']}")
        for dec in decisions:
            outs = [(dst, lbl) for src, dst, lbl in edges if src == dec]
            print(f"  Decision: {dec} -> {nodes[dec]}")
            for dst, lbl in outs:
                target_raw = nodes.get(dst, dst)
                print(f"    -- [{lbl}] --> {dst} ({target_raw})")
