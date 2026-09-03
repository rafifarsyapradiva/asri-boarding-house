import subprocess, sys

sys.stdout.reconfigure(encoding='utf-8')

failed_test_classes = [
    "AdminPaginationTest",
    "AdminRefactoredPaginationTest",
    "BusinessPolicyEnforcementTest",
    "ChatPollingTest",
    "FaqDynamicSystemTest",
    "KamarKetersediaanAntiGravityTest",
    "MarketSegmentationValidationTest",
    "PenyewaTagihanTest",
    "ProfileTest",
    "ProfileViewTest",
    "PublicGalleryTest",
    "ReservasiControllerTest",
    "ReservasiEndToEndAntiGravityTest",
    "TenantProfileTest",
    "TentangKamiTest"
]

print("=== DIAGNOSING TEST FAILURES IN DETAIL ===")
for cls_name in failed_test_classes:
    cmd = ['php', 'artisan', 'test', f'--filter={cls_name}']
    res = subprocess.run(cmd, capture_output=True, text=True, encoding='utf-8', errors='ignore', cwd=r'c:\xampp\htdocs\asri-boarding-house')
    print(f"\n==========================================")
    print(f"CLASS: {cls_name}")
    print(f"==========================================")
    lines = res.stdout.splitlines()
    for line in lines:
        if 'FAILED' in line or 'RouteNotFound' in line or 'Exception' in line or 'Expected' in line or 'Failed' in line or '⨯' in line:
            print("  ", line)

