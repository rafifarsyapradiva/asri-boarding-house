import re
import sys
from collections import defaultdict

if sys.stdout.encoding != 'utf-8':
    sys.stdout.reconfigure(encoding='utf-8')

with open('Blueprint/Flowchart_Kost.md', 'r', encoding='utf-8') as f:
    text = f.read()
    lines = text.splitlines(keepends=True)

diagrams = []
current_diag = None
current_section = ''

for i, line in enumerate(lines):
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

print(f"Total diagrams: {len(diagrams)}")

def get_node_type(raw):
    if raw.startswith('([') and raw.endswith('])'): return 'terminator'
    if raw.startswith('[[') and raw.endswith(']]'): return 'subprocess'
    if raw.startswith('[(') and raw.endswith(')]'): return 'database'
    if raw.startswith('((') and raw.endswith('))'): return 'circle'
    if raw.startswith('[/') and raw.endswith('/]'): return 'io'
    if raw.startswith('{\{') and raw.endswith('}\}'): return 'hexagon'
    if raw.startswith('{') and raw.endswith('}'): return 'decision'
    if raw.startswith('[') and raw.endswith(']'): return 'process'
    return 'unknown'

node_def_re = re.compile(r'([a-zA-Z0-9_]+)\s*(\(\[.*?\]\)|\{\{.*?\}\}|\[\[.*?\]\]|\[\(.*?\)\]|\(\(.*?\)\)|\[\/.*?\/\]|\[\\.*?\\\]|\{.*?\}|\[.*?\])')
edge_re = re.compile(r'([a-zA-Z0-9_]+)(?:\s*(?:\(\[.*?\]\)|\{\{.*?\}\}|\[\[.*?\]\]|\[\(.*?\)\]|\(\(.*?\)\)|\[\/.*?\/\]|\[\\.*?\\\]|\{.*?\}|\[.*?\]))?\s*(?:(-->|--\s*".*?"\s*-->|-->\|.*?\||<-->|<-->\|.*?\||-\.->|-\.->\|.*?\||==>|==\s*".*?"\s*==>))\s*([a-zA-Z0-9_]+)')

for idx, d in enumerate(diagrams):
    nodes = {}
    edges = []
    
    clean_lines = []
    in_init = False
    for l in d['lines']:
        ls = l.strip()
        if ls.startswith('%%{init:'): in_init = True; continue
        if in_init:
            if ls.startswith('}}%%'): in_init = False
            continue
        if ls.startswith('%%') or ls.startswith('subgraph') or ls == 'end' or ls.startswith('flowchart') or ls.startswith('graph'):
            continue
        if not ls: continue
        clean_lines.append(ls)

    for line in clean_lines:
        for m in node_def_re.finditer(line):
            nid = m.group(1)
            raw = m.group(2)
            nodes[nid] = {'shape': get_node_type(raw), 'raw': raw}
        
        pos = 0
        while True:
            m = edge_re.search(line, pos)
            if not m: break
            src = m.group(1)
            op = m.group(2)
            dst = m.group(3)
            label = ''
            if '|' in op: label = op.split('|')[1].strip()
            elif '"' in op: label = op.split('"')[1].strip()
            edges.append((src, dst, label, op))
            pos = m.start() + len(src) + 1

    all_nodes = set(nodes.keys())
    for s, t, _, _ in edges:
        all_nodes.add(s)
        all_nodes.add(t)
        if s not in nodes: nodes[s] = {'shape': 'implicit', 'raw': s}
        if t not in nodes: nodes[t] = {'shape': 'implicit', 'raw': t}

    in_deg = defaultdict(int)
    out_deg = defaultdict(int)
    out_edges = defaultdict(list)
    for s, t, lbl, op in edges:
        out_deg[s] += 1
        in_deg[t] += 1
        out_edges[s].append((t, lbl))
        if '<-->' in op:
            out_deg[t] += 1
            in_deg[s] += 1
            out_edges[t].append((s, lbl))

    starts = [n for n in all_nodes if in_deg[n] == 0]
    ends = [n for n in all_nodes if out_deg[n] == 0]
    
    # Check decisions
    decision_errors = []
    for n, data in nodes.items():
        if data['shape'] == 'decision':
            outs = out_edges[n]
            if len(outs) < 2:
                decision_errors.append(f"Decision '{n}' has {len(outs)} outgoing edge(s).")
            for target, lbl in outs:
                if not lbl:
                    decision_errors.append(f"Decision '{n}' -> '{target}' has NO label.")

    # Check dead ends (out_deg == 0 but not terminator)
    dead_ends = []
    for n in ends:
        if nodes[n]['shape'] != 'terminator':
            dead_ends.append(f"Node '{n}' ({nodes[n]['shape']}) has out-degree 0 but is not a terminator.")

    # Check start nodes
    start_errors = []
    for n in starts:
        if nodes[n]['shape'] != 'terminator':
            start_errors.append(f"Start node '{n}' ({nodes[n]['shape']}) is not a terminator.")

    # Check I/O vs Process semantic mismatches:
    # E.g. keywords in label
    semantic_warnings = []
    for n, data in nodes.items():
        shape = data['shape']
        raw = data['raw'].lower()
        # If shape is process [...] but does user input or output
        if shape == 'process':
            if any(k in raw for k in ['tampil', 'buka', 'input', 'isi form', 'klik', 'kirim wa', 'kirim email', 'pesan wa', 'toast', 'redirect', 'download', 'render']):
                semantic_warnings.append(f"Potential I/O labeled as Process: '{n}' -> {data['raw']}")
        # If shape is io [/ ... /] but is purely internal DB or calculation
        elif shape == 'io':
            if any(k in raw for k in ['lockforupdate', 'hitung', 'kalkulasi', 'generate order id', 'simpan hash', 'update status di database']) and not any(k in raw for k in ['tampil', 'input', 'klik', 'form', 'kirim', 'view', 'halaman']):
                semantic_warnings.append(f"Potential Process labeled as I/O: '{n}' -> {data['raw']}")

    print(f"\n=======================================================")
    print(f"Diagram {idx+1}: {d['section']} (lines {d['start_line']}-{d['end_line']})")
    print(f"Nodes: {len(all_nodes)}, Edges: {len(edges)}, Starts: {len(starts)}, Ends: {len(ends)}")
    if dead_ends:
        print("🚨 DEAD ENDS:")
        for de in dead_ends: print("  ", de)
    if start_errors:
        print("⚠️ START ERRORS:")
        for se in start_errors: print("  ", se)
    if decision_errors:
        print("⚠️ DECISION ERRORS:")
        for de in decision_errors: print("  ", de)
    if semantic_warnings:
        print("ℹ️ SEMANTIC NOTATION WARNINGS:")
        for sw in semantic_warnings: print("  ", sw)
