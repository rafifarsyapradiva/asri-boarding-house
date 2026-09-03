const fs = require('fs');
const path = require('path');

const blueprintPath = path.resolve('Blueprint/Blueprint_Projek_Web_Asri_Boarding_House.md');
let content = fs.readFileSync(blueprintPath, 'utf-8');

// 1. Update version header
content = content.replace('Blueprint Final — Versi 22.0  ·  2026', 'Blueprint Final — Versi 221.0  ·  2026');

// 2. Insert row 100 in table
const row99Target = "99\tAudit, Harmonisasi, & Finalisasi Entity Relationship Diagram (ERD) & Visualisasi Relasi Komprehensif\tSkema relasi database terfragmentasi, diagram ERD belum memetakan seluruh 21 entitas secara modular, enum status tagihan belum mencakup terlambat, dan ketiadaan visualisasi khusus kardinalitas (1:1, N:M Pivot, 1:N).\tAUDIT, HARMONISASI & FINALISASI ERD (v220.0): Sinkronisasi 21 entitas basis data (20 entitas + 1 pivot table kamar_fasilitas), DDL enum status tagihan ('terlambat') & kamar, relasi 1:1 logis vs 1:N historis users ↔ penyewa, MySQL 8 Virtual Generated Unique Columns (active_email, active_nik, dll.), perincian referential integrity actions (RESTRICT, CASCADE, SET NULL), penyediaan 10 berkas Mermaid (.mmd) & 14 visual gambar PNG resolusi tinggi (3x scale) di Blueprint/erd/ dan Blueprint/Entity_Relationship_Diagram_Kost.md termasuk Peta Topologi Relasi 5 Domain, visualisasi khusus 1:1, N:M Pivot, 1:N Rantai Transaksional, serta 4 Flowchart Alur Bisnis.";

const row100 = "100\tAudit, Harmonisasi, & Finalisasi Flowchart Diagram ISO 5807 / ANSI\tFlowchart diagram awal terfragmentasi, notasi simbol belum membedakan sub-proses dan I/O data, loop scheduler belum terisolasi, dan ketiadaan Grand Decision Flow terintegrasi.\tAUDIT, HARMONISASI & FINALISASI FLOWCHART DIAGRAM (v221.0): Menyusun 24 Flowchart Diagram berstandar internasional ISO 5807 / ANSI (1 Peta Hubungan Antar-Proses Bisnis, 1 Grand Architecture Decision Flow D1 s/d D18, 1 Master User Lifecycle, dan 21 Sub-Flowchart Modul Spesifik), mengisolasi atomisitas DB::transaction sebelum panggilan third-party API Fonnte WA, memetakan 18 titik keputusan logika kunci, menyertakan sad path lengkap (race condition overbooking, signature mismatch, pembayaran offline ditolak, klaim kerusakan > deposit), serta menyediakan 24 berkas Mermaid (.mmd) dan 24 aset visual render PNG resolusi tinggi di Blueprint/flowchart/ dan Blueprint/Flowchart_Kost.md.";

if (content.includes(row99Target)) {
    content = content.replace(row99Target, row99Target + '\n' + row100);
} else {
    console.error('Could not find row 99 target in table!');
}

// 3. Insert Revision v221.0 detail paragraph
const rev220Target = "Revisi v220.0 — Audit, Harmonisasi & Finalisasi Entity Relationship Diagram (ERD) & Visualisasi Relasi Komprehensif (v220.0)";

const rev221Text = `Revisi v221.0 — Audit, Harmonisasi & Finalisasi Flowchart Diagram ISO 5807 / ANSI (v221.0)
Revisi v221.0 mencakup audit forensik, standardisasi internasional ISO 5807 / ANSI, penyempurnaan atomisitas transaksi, pemetaan 18 titik keputusan logika kunci, dan harmonisasi menyeluruh pada seluruh diagram alur (Flowchart) sistem Asri Boarding House (Laravel 11 + MySQL 8.x + Midtrans Snap + Fonnte WA API): (1) Master Unified Architecture & Grand Decision Map: Merancang Peta Hubungan Antar-Proses Bisnis Global (peta_hubungan_proses.mmd/.png) dan Grand Architecture Decision Flow (grand_process_decision_flow.mmd/.png) yang memetakan secara presisi keterkaitan 6 Fase Utama Sistem serta mengevaluasi 18 Titik Keputusan Kunci (D1 s/d D18) yang mengalirkan data antar-entitas. (2) Standardisasi Notasi Baku ISO 5807 / ANSI: Melakukan audit menyeluruh terhadap pemakaian simbol flowchart agar 100% konsisten membedakan Terminator ([..]), Input/Output Form & Data [/../], Proses Komputasi & Kebijakan Bisnis [".."], Sub-Proses Transaksional Atomik [[..]], dan Percabangan Keputusan {".."}. (3) Pengamanan Atomisitas Transaksi & Integritas Data: Memastikan seluruh operasi kritis (seperti konfirmasi aktivasi reservasi pada Sub-FC 4) melakukan DB::transaction CommitTx mendahului pemanggilan external API Fonnte WhatsApp, mencegah lock contention atau timeout rollback. (4) Kelengkapan Skenario Kasus Tepi (Sad Paths & Edge Cases): Memetakan secara tuntas alur pemulihan race condition overbooking (Sub-FC 2), penolakan signature mismatch SHA-512 (Sub-FC 18), penolakan pembayaran offline tidak valid (Sub-FC 6), klaim ganti rugi fisik saat biaya kerusakan melebihi deposit (Sub-FC 11), dan reset snap_token untuk tagihan kadaluarsa (Sub-FC 18). (5) Kesiapan Aset Visual & Standar Dokumentasi: Menyediakan 24 berkas kode sumber Mermaid (.mmd) dan 24 gambar render visual resolusi tinggi (.png, 2x scale white background) pada direktori Blueprint/flowchart/, serta menyinkronkan seluruh visualisasi dan matriks keterlacakan ke dalam dokumen induk Blueprint/Flowchart_Kost.md.

`;

if (content.includes(rev220Target)) {
    content = content.replace(rev220Target, rev221Text + rev220Target);
} else {
    console.error('Could not find rev220 target in Blueprint!');
}

fs.writeFileSync(blueprintPath, content, 'utf-8');
console.log('Successfully updated Blueprint_Projek_Web_Asri_Boarding_House.md with Revision v221.0!');
