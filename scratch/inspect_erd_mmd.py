import os
import glob

erd_files = sorted(glob.glob('Blueprint/erd/*.mmd'))
for ef in erd_files:
    fname = os.path.basename(ef)
    with open(ef, 'r', encoding='utf-8') as f:
        lines = f.readlines()
    print(f"\n=== File: {fname} ({len(lines)} lines) ===")
    # Print first 10 and last 10 non-empty lines
    non_empty = [l.strip() for l in lines if l.strip()]
    header = [l for l in non_empty[:8]]
    for h in header:
        print(f"  {h}")
    if len(non_empty) > 8:
        print("  ...")
        for t in non_empty[-5:]:
            print(f"  {t}")
