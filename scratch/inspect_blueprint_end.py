import sys

with open(r'Blueprint/Blueprint_Projek_Web_Asri_Boarding_House.md', 'r', encoding='utf-8', errors='ignore') as f:
    content = f.read()

lines = content.splitlines()
print(f"Total lines: {len(lines)}")

# Find all level 1 and level 2 headings
headings = [(i+1, line) for i, line in enumerate(lines) if line.startswith("# ") or line.startswith("## ")]
print(f"Total H1/H2 Headings: {len(headings)}")
print("Last 15 Headings:")
for line_num, h in headings[-15:]:
    print(f"Line {line_num}: {h}")

print("\n--- Last 30 lines of file ---")
for i, line in enumerate(lines[-30:], start=len(lines)-29):
    print(f"{i}: {line}")
