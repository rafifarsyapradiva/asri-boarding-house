import sys
if sys.stdout.encoding != 'utf-8':
    sys.stdout.reconfigure(encoding='utf-8')
from audit_flowchart_deep import analyze_diagram

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

for idx in range(len(diagrams)):
    d = diagrams[idx]
    analysis = analyze_diagram(d)
    issues = analysis['terminator_issues'] + analysis['decision_issues']
    if issues or len(analysis['starts']) != 1 or len(analysis['ends']) != 1:
        print(f"\n=== DIAGRAM {idx+1}: {d['section']} (Lines {d['start_line']}-{d['end_line']}) ===")
        print(f"Starts: {analysis['starts']}")
        print(f"Ends: {analysis['ends']}")
        if issues:
            print("Issues:")
            for iss in issues:
                print(f"  * {iss}")
