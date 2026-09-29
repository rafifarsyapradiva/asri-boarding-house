import os
import shutil
import re
from PIL import Image

WORKSPACE_ROOT = r"c:\xampp\htdocs\asri-boarding-house"
BLUEPRINT_DIR = os.path.join(WORKSPACE_ROOT, "Blueprint")
SKRIPSI_IMAGES_DIR = os.path.join(WORKSPACE_ROOT, "Skripsi", "images")
SKRIPSI_MD_PATH = os.path.join(WORKSPACE_ROOT, "Skripsi", "Skripsi_Rafif_Arsya_Pradiva_22N40014.md")
SKRIPSI_RECOVERED_PATH = os.path.join(WORKSPACE_ROOT, "Skripsi", "Skripsi_Rafif_Arsya_Pradiva_22N40014_recovered.md")

SUBDIRS_TO_REMOVE = [
    "activity",
    "class",
    "component",
    "deployment",
    "erd",
    "flowchart",
    "prd",
    "sequence",
    "Skenario",
    "state",
    "UjiCoba",
    "use_case",
]

def step1_clean_unneeded_subdirs():
    print("=== TAHAP 1: Pembersihan 12 Subfolder Tak Terpakai di Skripsi/images ===")
    for subdir in SUBDIRS_TO_REMOVE:
        target_dir = os.path.join(SKRIPSI_IMAGES_DIR, subdir)
        if os.path.exists(target_dir):
            shutil.rmtree(target_dir)
            print(f"  [DELETED] Berhasil menghapus subfolder: {subdir}")
        else:
            print(f"  [SKIP] Subfolder tidak ada: {subdir}")

def step2_verify_academic_diagrams():
    print("\n=== TAHAP 2: Verifikasi Integritas Diagram Metodologi Skripsi ===")
    kerangka = os.path.join(SKRIPSI_IMAGES_DIR, "kerangka_pemikiran_diagram.png")
    waterfall = os.path.join(SKRIPSI_IMAGES_DIR, "waterfall_model_diagram.png")
    
    assert os.path.exists(kerangka), f"Missing: {kerangka}"
    assert os.path.exists(waterfall), f"Missing: {waterfall}"
    print(f"  [OK] kerangka_pemikiran_diagram.png ({os.path.getsize(kerangka):,} bytes) AMAN.")
    print(f"  [OK] waterfall_model_diagram.png ({os.path.getsize(waterfall):,} bytes) AMAN.")

def step3_sync_core_diagrams_from_blueprint():
    print("\n=== TAHAP 3: Sinkronisasi Diagram UML & ERD Selektif dari Blueprint ===")
    core_mappings = [
        # Class Diagram
        (os.path.join(BLUEPRINT_DIR, "class", "class_diagram_kost.png"),
         os.path.join(SKRIPSI_IMAGES_DIR, "class_diagram.png")),
        
        # ERD Diagram
        (os.path.join(BLUEPRINT_DIR, "erd", "diagram_erd.png"),
         os.path.join(SKRIPSI_IMAGES_DIR, "gambar_4_5_erd.png")),
        
        # Activity Diagrams
        (os.path.join(BLUEPRINT_DIR, "activity", "alur_billing_otomatis.png"),
         os.path.join(SKRIPSI_IMAGES_DIR, "activity_auto_billing.png")),
        (os.path.join(BLUEPRINT_DIR, "activity", "peta_percabangan_keputusan_bisnis.png"),
         os.path.join(SKRIPSI_IMAGES_DIR, "activity_flat_calendar_late_fee.png")),
        (os.path.join(BLUEPRINT_DIR, "activity", "alur_konfirmasi_reservasi.png"),
         os.path.join(SKRIPSI_IMAGES_DIR, "activity_reservation_to_tenant.png")),
         
        # Sequence Diagrams
        (os.path.join(BLUEPRINT_DIR, "sequence", "seq_reservasi_konfirmasi.png"),
         os.path.join(SKRIPSI_IMAGES_DIR, "sequence_reservasi_online.png")),
        (os.path.join(BLUEPRINT_DIR, "sequence", "seq_siklus_billing.png"),
         os.path.join(SKRIPSI_IMAGES_DIR, "sequence_penagihan_pembayaran.png")),
         
        # Use Case Diagrams (Modul & Flow)
        (os.path.join(BLUEPRINT_DIR, "use_case", "use_case_admin.png"),
         os.path.join(SKRIPSI_IMAGES_DIR, "use_case_admin.png")),
        (os.path.join(BLUEPRINT_DIR, "use_case", "use_case_admin.png"),
         os.path.join(SKRIPSI_IMAGES_DIR, "use_case_administrator.png")),
        (os.path.join(BLUEPRINT_DIR, "use_case", "use_case_publik_reservasi.png"),
         os.path.join(SKRIPSI_IMAGES_DIR, "use_case_publik_reservasi.png")),
        (os.path.join(BLUEPRINT_DIR, "use_case", "use_case_publik_reservasi.png"),
         os.path.join(SKRIPSI_IMAGES_DIR, "use_case_publik_calon_penyewa.png")),
        (os.path.join(BLUEPRINT_DIR, "use_case", "use_case_penyewa_aktif.png"),
         os.path.join(SKRIPSI_IMAGES_DIR, "use_case_penyewa_aktif.png")),
        (os.path.join(BLUEPRINT_DIR, "use_case", "use_case_flow_billing_denda.png"),
         os.path.join(SKRIPSI_IMAGES_DIR, "use_case_flow_billing_denda.png")),
        (os.path.join(BLUEPRINT_DIR, "use_case", "use_case_flow_checkout_perpanjang.png"),
         os.path.join(SKRIPSI_IMAGES_DIR, "use_case_flow_checkout_perpanjang.png")),
        (os.path.join(BLUEPRINT_DIR, "use_case", "use_case_flow_comprehensive_map.png"),
         os.path.join(SKRIPSI_IMAGES_DIR, "use_case_flow_comprehensive_map.png")),
        (os.path.join(BLUEPRINT_DIR, "use_case", "use_case_flow_guest_chat.png"),
         os.path.join(SKRIPSI_IMAGES_DIR, "use_case_flow_guest_chat.png")),
        (os.path.join(BLUEPRINT_DIR, "use_case", "use_case_flow_keluhan_resolusi.png"),
         os.path.join(SKRIPSI_IMAGES_DIR, "use_case_flow_keluhan_resolusi.png")),
        (os.path.join(BLUEPRINT_DIR, "use_case", "use_case_flow_reservasi_transisi.png"),
         os.path.join(SKRIPSI_IMAGES_DIR, "use_case_flow_reservasi_transisi.png")),
        (os.path.join(BLUEPRINT_DIR, "use_case", "use_case_interaction_flow.png"),
         os.path.join(SKRIPSI_IMAGES_DIR, "use_case_interaction_flow.png")),
        (os.path.join(BLUEPRINT_DIR, "use_case", "use_case_master_unified.png"),
         os.path.join(SKRIPSI_IMAGES_DIR, "use_case_master_unified.png")),
        (os.path.join(BLUEPRINT_DIR, "use_case", "use_case_relations_graph.png"),
         os.path.join(SKRIPSI_IMAGES_DIR, "use_case_relations_graph.png")),
    ]
    
    for src, dst in core_mappings:
        assert os.path.exists(src), f"Missing source: {src}"
        shutil.copy2(src, dst)
        print(f"  [SYNCED] {os.path.basename(dst)} ({os.path.getsize(dst):,} bytes) from Blueprint/{os.path.relpath(src, BLUEPRINT_DIR)}")

def step4_sync_technical_webp_assets():
    print("\n=== TAHAP 4: Sinkronisasi Aset Teknis Bab 4 (gambar_4_*.webp) ===")
    webp_mappings = {
        "gambar_4_1.webp": os.path.join(BLUEPRINT_DIR, "prd", "prd_diagram_5_billing.png"),
        "gambar_4_2.webp": os.path.join(BLUEPRINT_DIR, "prd", "prd_diagram_4_midtrans.png"),
        "gambar_4_3.webp": os.path.join(BLUEPRINT_DIR, "prd", "prd_diagram_9_google_oauth_flow.png"),
        "gambar_4_4.webp": os.path.join(BLUEPRINT_DIR, "prd", "prd_diagram_13_broadcast_dispatcher_flow.png"),
        "gambar_4_6.webp": os.path.join(BLUEPRINT_DIR, "prd", "prd_diagram_8_component_3tier.png"),
        "gambar_4_7.webp": os.path.join(BLUEPRINT_DIR, "prd", "prd_diagram_1_architecture.png"),
        "gambar_4_8.webp": os.path.join(BLUEPRINT_DIR, "prd", "prd_diagram_7_aruskas.png"),
        "gambar_4_9.webp": os.path.join(BLUEPRINT_DIR, "class", "class_alur_pembayaran_kuitansi_pdf.png"),
        "gambar_4_10.webp": os.path.join(BLUEPRINT_DIR, "prd", "prd_diagram_3_reservasi.png"),
        "gambar_4_11.webp": os.path.join(BLUEPRINT_DIR, "UjiCoba", "ujicoba_architecture_komparasi_fitur.png"),
        "gambar_4_12.webp": os.path.join(BLUEPRINT_DIR, "Skenario", "skenario_journey_penyewa.png"),
    }
    
    for webp_name, src_png in webp_mappings.items():
        dst_webp = os.path.join(SKRIPSI_IMAGES_DIR, webp_name)
        if os.path.exists(src_png):
            img = Image.open(src_png)
            img.save(dst_webp, format="WEBP", quality=92)
            print(f"  [WEBP OK] {webp_name} ({os.path.getsize(dst_webp):,} bytes) from {os.path.relpath(src_png, BLUEPRINT_DIR)}")
        else:
            print(f"  [WARNING] Source PNG not found: {src_png}")

def step5_audit_links():
    print("\n=== TAHAP 5: Audit Komprehensif Tautan Gambar pada Skripsi Markdown ===")
    for doc_path in [SKRIPSI_MD_PATH, SKRIPSI_RECOVERED_PATH]:
        doc_name = os.path.basename(doc_path)
        if not os.path.exists(doc_path):
            continue
        with open(doc_path, "r", encoding="utf-8") as f:
            content = f.read()
            
        pattern = r"!\[.*?\]\((.*?)\)"
        matches = re.findall(pattern, content)
        print(f"\nAudit Dokumen: {doc_name} ({len(matches)} tag gambar ditemukan):")
        
        all_ok = True
        for m in matches:
            clean_path = m.replace("file:///", "").replace("file://", "").strip()
            if clean_path.startswith("images/"):
                actual_file = os.path.join(WORKSPACE_ROOT, "Skripsi", clean_path.replace("/", "\\"))
            elif os.path.isabs(clean_path):
                actual_file = clean_path.replace("/", "\\")
            else:
                actual_file = os.path.join(WORKSPACE_ROOT, "Skripsi", clean_path.replace("/", "\\"))
                
            exists = os.path.exists(actual_file)
            size = os.path.getsize(actual_file) if exists else 0
            
            if exists and size > 0:
                print(f"  [VALID] {os.path.basename(actual_file)} ({size:,} bytes) via {m}")
            else:
                print(f"  [BROKEN] {m} -> NOT FOUND ({actual_file})")
                all_ok = False
                
        if all_ok:
            print(f"  >>> AUDIT STATUS: 100% VALID & PASSED UNTUK {doc_name}! <<<")

def step6_list_final_files():
    print("\n=== TAHAP 6: Daftar Akhir Berkas di Skripsi/images/ ===")
    items = sorted(os.listdir(SKRIPSI_IMAGES_DIR))
    subdirs = [f for f in items if os.path.isdir(os.path.join(SKRIPSI_IMAGES_DIR, f))]
    files = [f for f in items if os.path.isfile(os.path.join(SKRIPSI_IMAGES_DIR, f))]
    
    print(f"Subdirektori tersisa: {len(subdirs)} (Ekspektasi: 0)")
    for d in subdirs:
        print(f"  - [DIR] {d}")
        
    print(f"Total berkas di Skripsi/images/: {len(files)} berkas")
    total_bytes = 0
    for f in files:
        fp = os.path.join(SKRIPSI_IMAGES_DIR, f)
        sz = os.path.getsize(fp)
        total_bytes += sz
        print(f"  - {f:<40} {sz:>10,} bytes")
        
    print(f"\nTotal Ukuran Direktori: {total_bytes / (1024*1024):.2f} MB")

if __name__ == "__main__":
    step1_clean_unneeded_subdirs()
    step2_verify_academic_diagrams()
    step3_sync_core_diagrams_from_blueprint()
    step4_sync_technical_webp_assets()
    step5_audit_links()
    step6_list_final_files()
