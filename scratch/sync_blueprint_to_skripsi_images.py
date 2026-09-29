import os
import shutil
import re
from PIL import Image

WORKSPACE_ROOT = r"c:\xampp\htdocs\asri-boarding-house"
BLUEPRINT_DIR = os.path.join(WORKSPACE_ROOT, "Blueprint")
SKRIPSI_IMAGES_DIR = os.path.join(WORKSPACE_ROOT, "Skripsi", "images")
SKRIPSI_MD_PATH = os.path.join(WORKSPACE_ROOT, "Skripsi", "Skripsi_Rafif_Arsya_Pradiva_22N40014.md")
SKRIPSI_RECOVERED_PATH = os.path.join(WORKSPACE_ROOT, "Skripsi", "Skripsi_Rafif_Arsya_Pradiva_22N40014_recovered.md")

SUBDIRS = [
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

def step1_verify_academic_diagrams():
    print("=== TAHAP 1: Verifikasi Diagram Metodologi Skripsi ===")
    kerangka = os.path.join(SKRIPSI_IMAGES_DIR, "kerangka_pemikiran_diagram.png")
    waterfall = os.path.join(SKRIPSI_IMAGES_DIR, "waterfall_model_diagram.png")
    
    assert os.path.exists(kerangka), f"Missing: {kerangka}"
    assert os.path.exists(waterfall), f"Missing: {waterfall}"
    print(f"  [OK] kerangka_pemikiran_diagram.png ({os.path.getsize(kerangka):,} bytes) AMAN.")
    print(f"  [OK] waterfall_model_diagram.png ({os.path.getsize(waterfall):,} bytes) AMAN.")

def step2_create_modular_subdirs():
    print("\n=== TAHAP 2: Pembuatan 12 Subdirektori Modular di Skripsi/images ===")
    for subdir in SUBDIRS:
        target_path = os.path.join(SKRIPSI_IMAGES_DIR, subdir)
        os.makedirs(target_path, exist_ok=True)
        print(f"  [OK] Subdirektori siap: {target_path}")

def step3_batch_sync_blueprint_pngs():
    print("\n=== TAHAP 3: Batch Sinkronisasi 176 Diagram PNG dari Blueprint ===")
    total_copied = 0
    for subdir in SUBDIRS:
        src_subdir = os.path.join(BLUEPRINT_DIR, subdir)
        dst_subdir = os.path.join(SKRIPSI_IMAGES_DIR, subdir)
        
        if not os.path.exists(src_subdir):
            print(f"  [WARNING] Sumber tidak ditemukan: {src_subdir}")
            continue
            
        png_files = [f for f in os.listdir(src_subdir) if f.lower().endswith(".png")]
        for f in png_files:
            src_file = os.path.join(src_subdir, f)
            dst_file = os.path.join(dst_subdir, f)
            shutil.copy2(src_file, dst_file)
            total_copied += 1
            
        print(f"  [OK] {subdir}: {len(png_files)} diagram disalin ke Skripsi/images/{subdir}/")
        
    print(f"  Total diagram PNG modular tersalin: {total_copied} berkas.")

def step4_update_root_compatibility_diagrams():
    print("\n=== TAHAP 4: Modernisasi Aset Root Skripsi (Backward Compatibility Layer) ===")
    root_mappings = [
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
         
        # Use Case Diagrams & Aliases
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
    
    for src, dst in root_mappings:
        assert os.path.exists(src), f"Missing source: {src}"
        shutil.copy2(src, dst)
        print(f"  [OK] Updated root: {os.path.basename(dst)} ({os.path.getsize(dst):,} bytes)")

def step5_generate_and_sync_webp_assets():
    print("\n=== TAHAP 5: Penanganan & Pembangkitan Aset WebP Bab 4 ===")
    # Mappings from blueprint diagrams to webp references
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
            print(f"  [OK] Generated WebP: {webp_name} ({os.path.getsize(dst_webp):,} bytes) from {os.path.basename(src_png)}")
        else:
            print(f"  [WARNING] Source PNG not found for {webp_name}: {src_png}")

def step6_audit_markdown_links():
    print("\n=== TAHAP 6: Audit Tautan Gambar pada Skripsi Markdown ===")
    for doc_path in [SKRIPSI_MD_PATH, SKRIPSI_RECOVERED_PATH]:
        doc_name = os.path.basename(doc_path)
        if not os.path.exists(doc_path):
            continue
        with open(doc_path, "r", encoding="utf-8") as f:
            content = f.read()
            
        # Match ![alt](path)
        pattern = r"!\[.*?\]\((.*?)\)"
        matches = re.findall(pattern, content)
        print(f"\nAudit Dokumen: {doc_name} (Ditemukan {len(matches)} tag gambar):")
        
        all_ok = True
        for m in matches:
            # Clean up file:/// and relative paths
            clean_path = m.replace("file:///", "").replace("file://", "").strip()
            # If relative to Skripsi/
            if clean_path.startswith("images/"):
                actual_file = os.path.join(WORKSPACE_ROOT, "Skripsi", clean_path.replace("/", "\\"))
            elif os.path.isabs(clean_path):
                actual_file = clean_path.replace("/", "\\")
            else:
                actual_file = os.path.join(WORKSPACE_ROOT, "Skripsi", clean_path.replace("/", "\\"))
                
            exists = os.path.exists(actual_file)
            size = os.path.getsize(actual_file) if exists else 0
            
            if exists and size > 0:
                print(f"  [VALID] {m} -> {size:,} bytes")
            else:
                print(f"  [BROKEN LINK] {m} -> Tidak ditemukan di: {actual_file}")
                all_ok = False
                
        if all_ok:
            print(f"  >>> SEMUA TAUTAN GAMBAR DI {doc_name} TERVALIDASI 100% SUKSES! <<<")

def step7_summary():
    print("\n=== TAHAP 7: Rekapitulasi Akhir Direktori Skripsi/images ===")
    total_files = 0
    total_bytes = 0
    for root, dirs, files in os.walk(SKRIPSI_IMAGES_DIR):
        for f in files:
            fp = os.path.join(root, f)
            total_files += 1
            total_bytes += os.path.getsize(fp)
            
    print(f"Total berkas dalam Skripsi/images: {total_files} berkas")
    print(f"Total ukuran penyimpanan: {total_bytes / (1024*1024):.2f} MB")

if __name__ == "__main__":
    step1_verify_academic_diagrams()
    step2_create_modular_subdirs()
    step3_batch_sync_blueprint_pngs()
    step4_update_root_compatibility_diagrams()
    step5_generate_and_sync_webp_assets()
    step6_audit_markdown_links()
    step7_summary()
