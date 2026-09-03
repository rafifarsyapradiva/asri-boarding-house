import os
import re
import json

tests_dir = r"c:\xampp\htdocs\asri-boarding-house\tests"

test_files = []
total_test_methods = 0

categories = {
    'database_transactions_and_concurrency': [],
    'double_booking_and_overlap': [],
    'financial_integrity_and_billing': [],
    'soft_deletes_and_unique_constraints': [],
    'midtrans_and_payments': [],
    'tenant_and_reservation_lifecycle': [],
    'security_and_idor_and_sanitization': [],
    'indexes_and_performance': [],
    'foreign_keys_and_cascades': [],
    'negative_and_failure_testing': []
}

for root, dirs, files in os.walk(tests_dir):
    for f in files:
        if f.endswith(".php") and ("Test.php" in f):
            fpath = os.path.join(root, f)
            rel = os.path.relpath(fpath, tests_dir)
            with open(fpath, "r", encoding="utf-8", errors="ignore") as file:
                content = file.read()
                
            test_methods = re.findall(r'public\s+function\s+(test_[a-zA-Z0-9_]+|it_[a-zA-Z0-9_]+|[a-zA-Z0-9_]+)\s*\(\s*\)', content)
            # Filter standard phpunit tests (methods starting with test or with @test docblock)
            docblock_tests = re.findall(r'/\*\*[\s\S]*?@test[\s\S]*?\*/\s*public\s+function\s+([a-zA-Z0-9_]+)\s*\(', content)
            
            all_tests = list(set([m for m in test_methods if m.startswith('test')] + docblock_tests))
            total_test_methods += len(all_tests)
            
            c_low = content.lower()
            
            if "lockforupdate" in c_low or "db::transaction" in c_low or "concurrency" in c_low or "race" in c_low:
                categories['database_transactions_and_concurrency'].append((rel, len(all_tests)))
            if "overlap" in c_low or "double_booking" in c_low or "doublebooking" in c_low:
                categories['double_booking_and_overlap'].append((rel, len(all_tests)))
            if "denda" in c_low or "nominal" in c_low or "deposit" in c_low or "tagihan" in c_low:
                categories['financial_integrity_and_billing'].append((rel, len(all_tests)))
            if "softdelete" in c_low or "trashed" in c_low or "_deleted_" in c_low or "active_nik" in c_low:
                categories['soft_deletes_and_unique_constraints'].append((rel, len(all_tests)))
            if "midtrans" in c_low or "signature" in c_low or "gross_amount" in c_low or "snap" in c_low:
                categories['midtrans_and_payments'].append((rel, len(all_tests)))
            if "reservasi" in c_low or "penyewa" in c_low or "transisi" in c_low or "checkout" in c_low:
                categories['tenant_and_reservation_lifecycle'].append((rel, len(all_tests)))
            if "sqlinjection" in c_low or "xss" in c_low or "idor" in c_low or "unauthorized" in c_low or "sanitize" in c_low:
                categories['security_and_idor_and_sanitization'].append((rel, len(all_tests)))
            if "index" in c_low or "performance" in c_low:
                categories['indexes_and_performance'].append((rel, len(all_tests)))
            if "foreign" in c_low or "cascade" in c_low or "restrict" in c_low:
                categories['foreign_keys_and_cascades'].append((rel, len(all_tests)))
            if "fail" in c_low or "exception" in c_low or "invalid" in c_low or "assertstatus(40" in c_low or "assertstatus(422" in c_low or "assertstatus(403" in c_low or "assertstatus(404" in c_low or "assertstatus(500" in c_low:
                categories['negative_and_failure_testing'].append((rel, len(all_tests)))

print(f"Total Test Files: {len(test_files) or 'Scanned'}")
print(f"Total Test Methods Detected: {total_test_methods}")
print("\n--- TEST CATEGORIES SUMMARY ---")
for cat, items in categories.items():
    print(f"\nCategory: {cat} (Files: {len(items)}, Methods: {sum(x[1] for x in items)})")
    for item in items[:5]:
        print(f"  - {item[0]} ({item[1]} tests)")
    if len(items) > 5:
        print(f"  ... and {len(items) - 5} more files")

