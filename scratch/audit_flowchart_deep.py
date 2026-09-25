import re
import sys

if sys.stdout.encoding != 'utf-8':
    sys.stdout.reconfigure(encoding='utf-8')
from collections import defaultdict

def parse_nodes_and_edges(lines):
    # Strip comments and init blocks
    clean_lines = []
    in_init = False
    for line in lines:
        l = line.strip()
        if l.startswith('%%{init:'):
            in_init = True
            continue
        if in_init:
            if l.startswith('}}%%'):
                in_init = False
            continue
        if l.startswith('%%'):
            continue
        if l.startswith('subgraph') or l == 'end':
            continue
        if l.startswith('flowchart') or l.startswith('graph'):
            continue
        if not l:
            continue
        clean_lines.append(l)

    # Regex patterns for node shapes
    # ([...]) -> Terminator
    # [[...]] -> Subprocess
    # [(...)] -> Database
    # ((...)) -> Circle
    # [/...\/] -> Input/Output
    # { ... } -> Decision
    # [ ... ] -> Process
    
    # We want to identify node IDs and definitions, and edges
    # Example edges:
    # A --> B
    # A -->|"label"| B
    # A -- "label" --> B
    # A <--> B
    
    nodes = {} # id -> {'shape': type, 'label': text, 'raw': str}
    edges = [] # (source, target, label)

    # Let's find node definitions and edges
    # Note: a line can have "A[label] --> B{label2}" or just "A --> B"
    node_pattern = re.compile(r'([a-zA-Z0-9_]+)\s*(\(\[.*?\]\)|\{\{.*?\}\}|\[\[.*?\]\]|\[\(.*?\)\]|\(\(.*?\)\)|\[\/.*?\/\]|\[\\.*?\\\]|\{.*?\}|\[.*?\])')
    
    # Also parse edges:
    # We can split by lines or tokens
    for line in clean_lines:
        # First extract any node definitions in the line
        for match in node_pattern.finditer(line):
            nid = match.group(1)
            raw_shape = match.group(2)
            shape_type = 'unknown'
            if raw_shape.startswith('([') and raw_shape.endswith('])'):
                shape_type = 'terminator'
            elif raw_shape.startswith('[[') and raw_shape.endswith(']]'):
                shape_type = 'subprocess'
            elif raw_shape.startswith('[(') and raw_shape.endswith(')]'):
                shape_type = 'database'
            elif raw_shape.startswith('((') and raw_shape.endswith('))'):
                shape_type = 'circle'
            elif raw_shape.startswith('[/') and raw_shape.endswith('/]'):
                shape_type = 'io'
            elif raw_shape.startswith('{{') and raw_shape.endswith('}}'):
                shape_type = 'hexagon'
            elif raw_shape.startswith('{') and raw_shape.endswith('}'):
                shape_type = 'decision'
            elif raw_shape.startswith('[') and raw_shape.endswith(']'):
                shape_type = 'process'
            
            # extract inner text
            inner = raw_shape
            for prefix in ['([', '[[', '[(', '((', '[/', '{{', '{', '[']:
                if inner.startswith(prefix):
                    inner = inner[len(prefix):]
                    break
            for suffix in ['])', ']]', ')]', '))', '/]', '}}', '}', ']']:
                if inner.endswith(suffix):
                    inner = inner[:-len(suffix)]
                    break
            inner = inner.strip().strip('"')
            nodes[nid] = {'shape': shape_type, 'label': inner, 'raw': raw_shape}

        # Now extract edges in this line
        # Common edge syntaxes:
        # A --> B
        # A -->|"label"| B
        # A -- "label" --> B
        # A -.-> B
        # A == text ==> B
        # A <-->|"label"| B
        edge_re = re.compile(r'([a-zA-Z0-9_]+)(?:\s*(?:\(\[.*?\]\)|\{\{.*?\}\}|\[\[.*?\]\]|\[\(.*?\)\]|\(\(.*?\)\)|\[\/.*?\/\]|\[\\.*?\\\]|\{.*?\}|\[.*?\]))?\s*(?:(-->|--\s*".*?"\s*-->|-->\|.*?\||<-->|<-->\|.*?\||-\.->|-\.->\|.*?\||==>|==\s*".*?"\s*==>))\s*([a-zA-Z0-9_]+)')
        
        # Let's find all edge occurrences
        pos = 0
        while True:
            m = edge_re.search(line, pos)
            if not m:
                break
            src = m.group(1)
            edge_op = m.group(2)
            dst = m.group(3)
            
            label = ''
            if '|' in edge_op:
                label = edge_op.split('|')[1].strip()
            elif '"' in edge_op:
                label = edge_op.split('"')[1].strip()
            
            edges.append((src, dst, label, edge_op))
            # advance pos
            pos = m.start() + len(src) + 1 # don't skip entirely to allow chaining A --> B --> C

    return nodes, edges

def analyze_diagram(d):
    nodes, edges = parse_nodes_and_edges(d['lines'])
    
    # Collect all node IDs seen
    all_node_ids = set(nodes.keys())
    for src, dst, _, _ in edges:
        all_node_ids.add(src)
        all_node_ids.add(dst)
        if src not in nodes:
            nodes[src] = {'shape': 'implicit', 'label': src, 'raw': src}
        if dst not in nodes:
            nodes[dst] = {'shape': 'implicit', 'label': dst, 'raw': dst}

    in_degree = defaultdict(int)
    out_degree = defaultdict(int)
    outgoing_edges = defaultdict(list)
    incoming_edges = defaultdict(list)

    for src, dst, label, op in edges:
        out_degree[src] += 1
        in_degree[dst] += 1
        outgoing_edges[src].append((dst, label, op))
        incoming_edges[dst].append((src, label, op))
        if '<-->' in op:
            out_degree[dst] += 1
            in_degree[src] += 1
            outgoing_edges[dst].append((src, label, op))
            incoming_edges[src].append((dst, label, op))

    starts = [nid for nid in all_node_ids if in_degree[nid] == 0]
    ends = [nid for nid in all_node_ids if out_degree[nid] == 0]
    
    # Decisions check
    decision_issues = []
    for nid, data in nodes.items():
        if data['shape'] == 'decision':
            outs = outgoing_edges[nid]
            if len(outs) < 2:
                decision_issues.append(f"Decision '{nid}' ({data['label']}) has only {len(outs)} outgoing branch(es).")
            # check if branches have labels
            unlabeled = [dst for dst, lbl, _ in outs if not lbl]
            if unlabeled:
                decision_issues.append(f"Decision '{nid}' has {len(unlabeled)} unlabeled branch(es) leading to: {unlabeled}")

    # Terminator check
    terminator_issues = []
    for nid in starts:
        if nodes[nid]['shape'] != 'terminator':
            terminator_issues.append(f"Start node '{nid}' ({nodes[nid]['shape']}: {nodes[nid]['label']}) is not a terminator '([ ... ])'")
    for nid in ends:
        if nodes[nid]['shape'] != 'terminator':
            terminator_issues.append(f"End node '{nid}' ({nodes[nid]['shape']}: {nodes[nid]['label']}) is not a terminator '([ ... ])'")

    # Dead ends / disconnected
    isolated = [nid for nid in all_node_ids if in_degree[nid] == 0 and out_degree[nid] == 0]

    return {
        'total_nodes': len(all_node_ids),
        'total_edges': len(edges),
        'starts': starts,
        'ends': ends,
        'isolated': isolated,
        'decision_issues': decision_issues,
        'terminator_issues': terminator_issues,
        'nodes': nodes,
        'edges': edges,
        'outgoing_edges': outgoing_edges,
        'incoming_edges': incoming_edges
    }

def main():
    with open('Blueprint/Flowchart_Kost.md', 'r', encoding='utf-8') as f:
        lines = f.readlines()

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

    for idx, d in enumerate(diagrams):
        print(f"\n==================================================")
        print(f"DIAGRAM {idx+1}: {d['section']} (Lines {d['start_line']}-{d['end_line']})")
        print(f"==================================================")
        analysis = analyze_diagram(d)
        print(f"Nodes: {analysis['total_nodes']} | Edges: {analysis['total_edges']}")
        print(f"Starts ({len(analysis['starts'])}): {[nid + ' (' + analysis['nodes'][nid]['shape'] + ')' for nid in analysis['starts']]}")
        print(f"Ends ({len(analysis['ends'])}): {[nid + ' (' + analysis['nodes'][nid]['shape'] + ')' for nid in analysis['ends']]}")
        
        if analysis['isolated']:
            print(f"🚨 ISOLATED NODES: {analysis['isolated']}")
        if analysis['terminator_issues']:
            print(f"⚠️ TERMINATOR ISSUES:")
            for iss in analysis['terminator_issues']:
                print(f"   - {iss}")
        if analysis['decision_issues']:
            print(f"⚠️ DECISION ISSUES:")
            for iss in analysis['decision_issues']:
                print(f"   - {iss}")
        
        # Check node shapes breakdown
        shape_counts = defaultdict(int)
        for nid, data in analysis['nodes'].items():
            shape_counts[data['shape']] += 1
        print(f"Shapes breakdown: {dict(shape_counts)}")

if __name__ == '__main__':
    main()
