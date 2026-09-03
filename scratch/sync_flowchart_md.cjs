const fs = require('fs');
const path = require('path');

const flowchartDir = path.resolve('Blueprint/flowchart');
const mdPath = path.resolve('Blueprint/Flowchart_Kost.md');

let mdContent = fs.readFileSync(mdPath, 'utf-8');

// List of sub-flowcharts with corresponding mmd file
const diagrams = [
    { file: 'peta_hubungan_proses.mmd', title: '## 1.1 Visualisasi Peta Hubungan Antar-Proses Bisnis' },
    { file: 'flowchart_utama.mmd', title: '### 2.1. Flowchart Utama' },
    { file: 'alur_pencarian_kamar.mmd', title: '### 2.2. Sub-Flowchart 1' },
    { file: 'alur_pembuatan_reservasi.mmd', title: '### 2.3. Sub-Flowchart 2' },
    { file: 'alur_reservasi_pembayaran.mmd', title: '### 2.4. Sub-Flowchart 3' },
    { file: 'alur_konfirmasi_reservasi.mmd', title: '### 2.5. Sub-Flowchart 4' },
    { file: 'alur_billing_otomatis.mmd', title: '### 2.6. Sub-Flowchart 5' },
    { file: 'alur_tagihan_bulanan.mmd', title: '### 2.7. Sub-Flowchart 6' },
    { file: 'alur_pengaduan_keluhan.mmd', title: '### 2.8. Sub-Flowchart 7' },
    { file: 'alur_pencatatan_pengeluaran.mmd', title: '### 2.9. Sub-Flowchart 8' },
    { file: 'alur_manajemen_konten.mmd', title: '### 2.10. Sub-Flowchart 9' },
    { file: 'alur_manajemen_peraturan.mmd', title: '### 2.11. Sub-Flowchart 10' },
    { file: 'alur_penonaktifan_penyewa.mmd', title: '### 2.12. Sub-Flowchart 11' },
    { file: 'alur_live_chat.mmd', title: '### 2.13. Sub-Flowchart 12' },
    { file: 'alur_keamanan_login.mmd', title: '### 2.14. Sub-Flowchart 13' },
    { file: 'alur_penghapusan_reservasi.mmd', title: '### 2.15. Sub-Flowchart 14' },
    { file: 'alur_pendaftaran_offline.mmd', title: '### 2.16. Sub-Flowchart 15' },
    { file: 'alur_manajemen_kamar.mmd', title: '### 2.17. Sub-Flowchart 16' },
    { file: 'alur_google_login.mmd', title: '### 2.18. Sub-Flowchart 17' },
    { file: 'alur_webhook_midtrans.mmd', title: '### 2.19. Sub-Flowchart 18' },
    { file: 'alur_manajemen_fasilitas.mmd', title: '### 2.20. Sub-Flowchart 19' },
    { file: 'alur_manajemen_penyewa.mmd', title: '### 2.21. Sub-Flowchart 20' },
    { file: 'alur_broadcast_notifikasi.mmd', title: '### 2.22. Sub-Flowchart 21' }
];

console.log('Synchronizing Flowchart_Kost.md with cleaned .mmd files...');

// Safe regex replacements for Mermaid tokens in Flowchart_Kost.md as well
mdContent = mdContent.replace(/\{id\}/g, ':id');
mdContent = mdContent.replace(/NOW\(\)/g, 'NOW');
mdContent = mdContent.replace(/lockForUpdate\(\)/g, 'lockForUpdate');
mdContent = mdContent.replace(/firstOrCreate\(\)/g, 'firstOrCreate');
mdContent = mdContent.replace(/Auth::login\([^)]+\)/g, 'Auth::login(user)');
mdContent = mdContent.replace(/Cache::forget\([^)]+\)/g, 'Cache::forget fasilitas_all');
mdContent = mdContent.replace(/hash\([^)]+\)/g, "hash sha512 payload");

fs.writeFileSync(mdPath, mdContent, 'utf-8');
console.log('Flowchart_Kost.md synchronized successfully!');
