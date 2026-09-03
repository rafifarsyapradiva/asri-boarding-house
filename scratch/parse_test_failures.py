import re, os, sys

sys.stdout.reconfigure(encoding='utf-8')

log_path = r"C:\Users\RAFIF\.gemini\antigravity-ide\brain\547008c0-b9b7-4b81-b8ef-8cd46c6f9500\.system_generated\tasks\task-12.log"
with open(log_path, 'r', encoding='utf-8', errors='ignore') as f:
    content = f.read()

# Split content by test case headers or FAIL sections
failed_sections = re.split(r'(\s+FAIL\s+Tests\\|\s+⨯\s+)', content)

print(f"Total sections: {len(failed_sections)}")

# Let's extract failures directly with error tracebacks
failures_summary = []

for section in re.finditer(r'(?:FAIL\s+(Tests\\[^\n]+)|⨯\s+([^\n]+))\n(.*?)(?=\n\s*(?:PASS|FAIL|✓|⨯|\Z))', content, re.DOTALL):
    test_cls = section.group(1) or section.group(2)
    details = section.group(3).strip()
    failures_summary.append({
        'test': test_cls.strip(),
        'details': details[:500]
    })

print(f"Extracted {len(failures_summary)} detailed failure entries:")
for idx, item in enumerate(failures_summary, 1):
    print(f"\n--- [{idx}] {item['test']} ---")
    print(item['details'])
