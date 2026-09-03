const fs = require('fs');
const path = require('path');

const mdPath = path.resolve('Blueprint/Flowchart_Kost.md');
const mmdPath = path.resolve('Blueprint/flowchart/grand_process_decision_flow.mmd');

const mmdContent = fs.readFileSync(mmdPath, 'utf-8');
let mdContent = fs.readFileSync(mdPath, 'utf-8');

const section1_2 = `
---

## 1.2 Grand Architecture: Peta Visualisasi Alur Proses & Percabangan Terintegrasi (Global Decision & Process Flow Map)

Diagram alir tingkat arsitektural (*Grand Process & Decision Flow Diagram*) di bawah ini memberikan visualisasi **menyeluruh, terstruktur, dan hierarkis** mengenai bagaimana 6 Fase Utama Sistem berinteraksi secara mulus, bagaimana setiap data berpindah antar-entitas, serta bagaimana **18 Titik Keputusan Kunci (D1 s/d D18)** dievaluasi dan diarahkan ke masing-masing Sub-Flowchart secara presisi:

![Visual Grand Architecture Process & Decision Flow](flowchart/grand_process_decision_flow.png)

\`\`\`mermaid
${mmdContent.trim()}
\`\`\`

### 1.2.1 Matriks 18 Titik Keputusan Logika Kunci (Key Decision Flow Matrix)

Tabel berikut merinci setiap percabangan keputusan (*Decision Point*) yang digambarkan pada diagram arsitektur di atas:

| Kode Keputusan | Lokasi / Fase | Kondisi Evaluasi (*Condition Evaluated*) | Cabang Alur (*Branching*) | Tindakan Sistem (*System Action & Destination*) | Ref. Sub-FC |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **D1** | Fase 1 (Publik) | Status ketersediaan kamar di katalog | \`Maintenance\` / \`Terisi\` / \`Tersedia\` | Sembunyikan unit / Tampilkan CTA WA / Buka detail form reservasi | Sub-FC 1 |
| **D2** | Fase 2 (Auth) | Sesi otentikasi calon penyewa | \`Belum Login\` vs \`Sudah Login\` | Arahkan ke Form Login/Register atau Google OAuth vs Lanjut form | Sub-FC 17 |
| **D3** | Fase 2 (Auth) | Validasi format NIK & Nomor Telepon Wali | \`Gagal\` vs \`Lolos\` | Munculkan Toast Error validation vs Buka transaksi basis data | Sub-FC 2 |
| **D4** | Fase 2 (Auth) | Cek konkurensi kamar (\`lockForUpdate\`) | \`Bentrok\` vs \`Tersedia\` | Rollback DB & toast notifikasi vs Buat reservasi status pending | Sub-FC 2 |
| **D5** | Fase 3 (Payment)| Pilihan interaksi calon penyewa | \`Batalkan\` / \`Chat\` / \`Bayar Now\` | Set status batal & lepas kamar / AJAX Polling / Request Snap Token | Sub-FC 3 |
| **D6** | Fase 3 (Payment)| Verifikasi HMAC SHA-512 Midtrans | \`Mismatch\` vs \`Valid\` | Tolak webhook HTTP 403 Forbidden vs Lanjut routing payload | Sub-FC 18 |
| **D7** | Fase 3 (Payment)| Status transaksi dari Midtrans callback | \`Settlement\` / \`Pending\` / \`Expired\`| Update status bayar / Tunggu 24 jam / Auto-cancel & lepas kamar | Sub-FC 18 |
| **D8** | Fase 3 (Payment)| Skema pembayaran reservasi | \`DP 30%\` vs \`Lunas 100%\` | Update status reservasi 'dp' vs 'lunas' & simpan mutasi | Sub-FC 3, 4 |
| **D9** | Fase 3 (Payment)| Verifikasi berkas NIK & data oleh Admin | \`Minta Koreksi\` vs \`Disetujui\` | Kirim chat/WA revisi data vs DB transaction konfirmasi aktivasi | Sub-FC 4 |
| **D10** | Fase 4 (Security)| Flag \`require_password_change\` user | \`True\` vs \`False\` | Intercept paksa ke form ganti sandi vs Akses dashboard penuh | Sub-FC 13 |
| **D11** | Fase 5 (Billing)| Tanggal pembayaran tagihan bulanan | \`<= Tgl 10\` vs \`> Tgl 10\` | Tagihan nominal pokok biasa vs Evaluasi keterlambatan denda | Sub-FC 6 |
| **D12** | Fase 5 (Billing)| Penyeberangan bulan kalender | \`Bulan Berjalan\` vs \`Bulan Baru\` | Masa keringanan (tanpa denda) vs Terapkan denda flat 5% | Sub-FC 6 |
| **D13** | Fase 5 (Billing)| Durasi akumulasi tunggakan tagihan | \`Tunggakan > 1 Bulan\` | Kirim pesan peringatan eskalasi darurat ke nomor WhatsApp Wali | Sub-FC 6 |
| **D14** | Fase 5 (Billing)| Saluran metode pembayaran tagihan | \`Online Snap\` vs \`Offline Cash\`| Render Midtrans modal vs Serahkan cash / bukti transfer fisik | Sub-FC 6 |
| **D15** | Fase 5 (Billing)| Validasi penerimaan dana fisik/transfer | \`Tidak Valid\` vs \`Valid\` | Admin tolak pembayaran & revisi vs Admin set tagihan lunas | Sub-FC 6 |
| **D16** | Fase 6 (Checkout)| Temuan kerusakan saat inspeksi fisik | \`Tidak Ada\` vs \`Ada Kerusakan\` | Kembalikan deposit 100% penuh vs Evaluasi biaya perbaikan | Sub-FC 11 |
| **D17** | Fase 6 (Checkout)| Komparasi biaya perbaikan vs deposit | \`Biaya <= Deposit\` vs \`Biaya > Deposit\`| Potong biaya & kembalikan sisa saldo vs Hangus 100% & klaim ganti rugi | Sub-FC 11 |
| **D18** | Fase 6 (Room) | Kebutuhan renovasi fisik kamar | \`Perlu Perbaikan\` vs \`Siap Huni\`| Ubah status kamar 'maintenance' vs 'tersedia' untuk publik | Sub-FC 11, 16 |
`;

const targetSplit = '## 2. Detail Visualisasi & Alur Flowchart (Mermaid)';
if (mdContent.includes(targetSplit)) {
    const parts = mdContent.split(targetSplit);
    mdContent = parts[0] + section1_2 + '\n' + targetSplit + parts[1];
    fs.writeFileSync(mdPath, mdContent, 'utf-8');
    console.log('Section 1.2 added successfully to Flowchart_Kost.md!');
} else {
    console.error('Target split header not found in Flowchart_Kost.md');
}
