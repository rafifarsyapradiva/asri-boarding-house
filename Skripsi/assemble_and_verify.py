# -*- coding: utf-8 -*-
"""
Assembler and Verification Script for Skripsi Humanized
"""
import os
import hashlib

BASE_DIR = r'c:\xampp\htdocs\asri-boarding-house\Skripsi'
PARTS_DIR = os.path.join(BASE_DIR, 'humanized_parts')
OUTPUT_FILE = os.path.join(BASE_DIR, 'Skripsi_Rafif_Arsya_Pradiva_22N40014_HUMANIZED.md')
ORIGINAL_FILE = os.path.join(BASE_DIR, 'Skripsi_Rafif_Arsya_Pradiva_22N40014.md')
BACKUP_FILE = os.path.join(BASE_DIR, 'Skripsi_Rafif_Arsya_Pradiva_22N40014_BACKUP_20261001_110800.md')

def sha256_file(filepath):
    h = hashlib.sha256()
    with open(filepath, 'rb') as f:
        while chunk := f.read(8192):
            h.update(chunk)
    return h.hexdigest()

def main():
    parts = [
        'part_01_front.md',
        'part_02_bab1.md',
        'part_03_bab2.md',
        'part_04_bab3.md',
        'part_05_bab4_1.md',
        'part_06_bab4_2.md',
        'part_07_bab4_3_4_5.md',
        'part_08_bab4_6_7.md',
        'part_09_bab5.md',
        'part_10_daftar_pustaka.md'
    ]
    
    full_content = []
    for p in parts:
        part_path = os.path.join(PARTS_DIR, p)
        if not os.path.exists(part_path):
            raise FileNotFoundError(f"Missing part: {part_path}")
        with open(part_path, 'r', encoding='utf-8') as f:
            full_content.append(f.read().strip())
    
    # Join parts with double newline
    assembled_text = "\n\n".join(full_content) + "\n"
    
    with open(OUTPUT_FILE, 'w', encoding='utf-8') as f:
        f.write(assembled_text)
    
    print(f"Successfully assembled: {OUTPUT_FILE}")
    print(f"Total characters: {len(assembled_text)}")
    print(f"Total lines: {assembled_text.count(chr(10))}")
    
    # Integrity Verification
    print("\n--- RUNNING AUDIT & INTEGRITY CHECKS ---")
    
    # 1. Original file intact?
    orig_hash = sha256_file(ORIGINAL_FILE)
    backup_hash = sha256_file(BACKUP_FILE)
    assert orig_hash == backup_hash, "CRITICAL ERROR: Original file was modified!"
    print(f"[PASSED] Original file untouched. Hash: {orig_hash[:16]}...")
    
    # 2. Check headings
    expected_headings = [
        "## HALAMAN PERNYATAAN ORISINALITAS",
        "## HALAMAN PERNYATAAN BEBAS PLAGIASI",
        "## HALAMAN PENGESAHAN",
        "## KATA PENGANTAR",
        "## ABSTRAK",
        "## ABSTRACT",
        "## DAFTAR ISI",
        "## BAB I PENDAHULUAN",
        "## BAB II TINJAUAN PUSTAKA DAN LANDASAN TEORI",
        "## BAB III METODOLOGI PENELITIAN",
        "## BAB IV HASIL DAN PEMBAHASAN",
        "## BAB V KESIMPULAN DAN SARAN",
        "## DAFTAR PUSTAKA"
    ]
    for h in expected_headings:
        assert h in assembled_text, f"Missing heading: {h}"
    print(f"[PASSED] All {len(expected_headings)} main headings present.")
    
    # 3. Check tables
    expected_tables = [
        "Tabel 2.1 Perbandingan Penelitian Terdahulu",
        "Tabel 4.1",
        "Tabel 4.2",
        "Tabel 4.3",
        "Tabel 4.4",
        "Tabel 4.5",
        "Tabel 4.6",
        "Tabel 4.7"
    ]
    for t in expected_tables:
        assert t in assembled_text, f"Missing table: {t}"
    print(f"[PASSED] All {len(expected_tables)} tables present.")
    
    # 4. Check 60 Black Box tests
    for i in range(1, 61):
        test_pattern = f"| {i} |"
        assert test_pattern in assembled_text, f"Missing blackbox test row {i}"
    print("[PASSED] All 60 Black Box testing items present.")
    
    # 5. Check Mermaid diagrams
    mermaid_count = assembled_text.count("```mermaid")
    assert mermaid_count == 3, f"Expected 3 mermaid diagrams, got {mermaid_count}"
    print(f"[PASSED] All {mermaid_count} Mermaid diagrams present.")
    
    # 6. Check citations [1] to [20]
    for i in range(1, 21):
        assert f"[{i}]" in assembled_text, f"Missing citation [{i}]"
    print("[PASSED] Citations [1] to [20] intact.")
    
    # 7. Check key substantive data
    key_facts = [
        "32 unit kamar",
        "Rp28.500.000",
        "Bapak Asep",
        "48 tahun",
        "20 tahun",
        "Nur Haliza",
        "Tyas",
        "lockForUpdate()",
        "510 tests passed",
        "2.211 assertions",
        "9,5 dari 10",
        "https://asriboardinghouse.weatso.id/",
        "REKAMAN_UX_ADMIN_KOST_2026.m4a"
    ]
    for k in key_facts:
        assert k in assembled_text, f"Missing key fact: {k}"
    print(f"[PASSED] All {len(key_facts)} key facts and metrics confirmed.")

    print("\nALL QUALITY GATE CHECKS PASSED SUCCESSFULLY!")

if __name__ == '__main__':
    main()
