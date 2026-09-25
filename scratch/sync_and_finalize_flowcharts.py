import os
import re
import sys

flowchart_dir = 'Blueprint/flowchart'
md_path = 'Blueprint/Flowchart_Kost.md'

with open(md_path, 'r', encoding='utf-8') as f:
    md_content = f.read()

# Define the replacements mapping section titles to mmd files
mapping = [
    ('## 1.1 Visualisasi Peta Hubungan Antar-Proses Bisnis (Process Architecture & Decision Flow Map)', 'peta_hubungan_proses.mmd'),
    ('## 1.2 Grand Architecture: Peta Visualisasi Alur Proses & Percabangan Terintegrasi (Global Decision & Process Flow Map)', 'grand_process_decision_flow.mmd'),
    ('### 2.2. Sub-Flowchart 1: Pencarian & Cek Ketersediaan Kamar', 'alur_pencarian_kamar.mmd'),
    ('### 2.4. Sub-Flowchart 3: Pembayaran Reservasi, Pre-Chat, & Pembatalan Manual', 'alur_reservasi_pembayaran.mmd'),
    ('### 2.5. Sub-Flowchart 4: Verifikasi & Konfirmasi Reservasi Baru', 'alur_konfirmasi_reservasi.mmd'),
    ('### 2.6. Sub-Flowchart 5: Siklus Billing Rutin Bulanan Otomatis', 'alur_billing_otomatis.mmd'),
    ('### 2.7. Sub-Flowchart 6: Pembayaran Tagihan Bulanan & Eskalasi Wali', 'alur_tagihan_bulanan.mmd'),
    ('### 2.19. Sub-Flowchart 18: Callback Webhook Midtrans & Verifikasi Signature', 'alur_webhook_midtrans.mmd')
]

for section_title, mmd_file in mapping:
    mmd_path = os.path.join(flowchart_dir, mmd_file)
    with open(mmd_path, 'r', encoding='utf-8') as f:
        mmd_code = f.read().strip()

    # Find the section in md_content
    pos = md_content.find(section_title)
    if pos == -1:
        print(f"ERROR: Section '{section_title}' not found in markdown!")
        continue

    # Find the mermaid block after pos
    mermaid_start = md_content.find('```mermaid', pos)
    if mermaid_start == -1:
        print(f"ERROR: ```mermaid not found after '{section_title}'!")
        continue
    mermaid_end = md_content.find('```', mermaid_start + len('```mermaid'))
    if mermaid_end == -1:
        print(f"ERROR: closing ``` not found after '{section_title}'!")
        continue

    old_block = md_content[mermaid_start:mermaid_end + 3]
    new_block = f"```mermaid\n{mmd_code}\n```"

    md_content = md_content[:mermaid_start] + new_block + md_content[mermaid_end + 3:]
    print(f"Successfully replaced mermaid block for '{section_title}' using {mmd_file}")

# Update metadata descriptions in Sub-FC 4 and Sub-FC 5
md_content = md_content.replace(
    "* **Controller Terkait**: [ReservasiController](file:///c:/xampp/htdocs/asri-boarding-house/app/Http/Controllers/Admin/ReservasiController.php) (method: `confirm`)",
    "* **Controller Terkait**: [ReservasiController](file:///c:/xampp/htdocs/asri-boarding-house/app/Http/Controllers/Admin/ReservasiController.php) (method: `konfirmasi`, `batal`, `destroy`), [TransisiPenyewaService](file:///c:/xampp/htdocs/asri-boarding-house/app/Services/TransisiPenyewaService.php)"
)

md_content = md_content.replace(
    "* **Controller Terkait**: [BillingService](file:///c:/xampp/htdocs/asri-boarding-house/app/Services/BillingService.php) (Laravel Command: `billing:generate`)",
    "* **Controller Terkait**: [BillingService](file:///c:/xampp/htdocs/asri-boarding-house/app/Services/BillingService.php) (Laravel Command: `tagihan:generate-bulanan` pada `00:05 WIB`)"
)

with open(md_path, 'w', encoding='utf-8') as f:
    f.write(md_content)

print("\nFlowchart_Kost.md has been fully updated and synchronized!")
