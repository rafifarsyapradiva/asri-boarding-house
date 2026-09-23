<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Nota Pembayaran &mdash; {{ $pembayaran->transaction_id }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700;900&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <style>
        /* ====================================================
         * BASE RESET & TYPOGRAPHY
         * ==================================================== */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', 'Helvetica Neue', Arial, sans-serif;
            background-color: #f1f5f9;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 32px 16px;
        }

        /* ====================================================
         * TOP ACTION BAR (tidak masuk ke dalam PDF)
         * ==================================================== */
        .action-bar {
            width: 100%;
            max-width: 580px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }
        .action-bar-title {
            font-size: 13px;
            font-weight: 900;
            color: #1e293b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .btn-download {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            background-color: #facc15;
            color: #000000;
            font-family: 'Inter', sans-serif;
            font-size: 13px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: 3px solid #000000;
            box-shadow: 4px 4px 0px 0px #000000;
            cursor: pointer;
            transition: transform 0.08s ease, box-shadow 0.08s ease;
            background-clip: padding-box;
        }
        .btn-download:hover {
            transform: translate(-2px, -2px);
            box-shadow: 6px 6px 0px 0px #000000;
        }
        .btn-download:active {
            transform: translate(2px, 2px);
            box-shadow: 2px 2px 0px 0px #000000;
        }
        .btn-download.loading {
            opacity: 0.65;
            cursor: not-allowed;
            pointer-events: none;
        }
        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 14px;
            background-color: #ffffff;
            color: #000000;
            font-family: 'Inter', sans-serif;
            font-size: 12px;
            font-weight: 700;
            border: 2px solid #000000;
            box-shadow: 2px 2px 0px 0px #000000;
            cursor: pointer;
            text-decoration: none;
            transition: transform 0.08s ease, box-shadow 0.08s ease;
        }
        .btn-back:hover {
            transform: translate(-1px, -1px);
            box-shadow: 3px 3px 0px 0px #000000;
        }

        /* ====================================================
         * NOTA CONTAINER (yang akan dirender jadi PDF)
         * ==================================================== */
        #nota-container {
            width: 148mm;
            background-color: #ffffff;
            border: 3px solid #000000;
            box-shadow: 6px 6px 0px 0px #000000;
            padding: 10mm;
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 8.5px;
            line-height: 1.35;
            color: #000000;
        }

        /* ====================================================
         * HEADER
         * ==================================================== */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
            border: 3px solid #000000;
            background-color: #facc15;
            box-shadow: 4px 4px 0px 0px #000000;
        }
        .header-table td { vertical-align: middle; padding: 10px 14px; }
        .brand-title {
            font-size: 17px;
            font-weight: 900;
            color: #000000;
            text-transform: uppercase;
            margin: 0;
            letter-spacing: 0.5px;
        }
        .brand-subtitle {
            font-size: 8px;
            font-weight: bold;
            color: #000000;
            margin: 2px 0 0 0;
            text-transform: uppercase;
        }
        .header-right { text-align: right; font-size: 8px; color: #000000; font-weight: bold; }

        /* ====================================================
         * META TABLE (Info Penyewa + Info Pembayaran)
         * ==================================================== */
        .meta-table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        .meta-table td { width: 50%; vertical-align: top; }
        .section-title {
            font-size: 10px;
            font-weight: 900;
            color: #000000;
            background-color: #facc15;
            border: 3px solid #000000;
            box-shadow: 2px 2px 0px 0px #000000;
            display: inline-block;
            padding: 3px 8px;
            margin-bottom: 8px;
            text-transform: uppercase;
        }
        .info-card {
            background-color: #ffffff;
            border: 3px solid #000000;
            padding: 6px 8px;
            margin-right: 6px;
            min-height: 72px;
            box-shadow: 3px 3px 0px 0px #000000;
        }
        .info-card.right { margin-right: 0; margin-left: 6px; }
        .info-item { margin-bottom: 4px; }
        .info-label {
            color: #000000;
            font-weight: bold;
            text-transform: uppercase;
            display: inline-block;
            width: 65px;
            font-size: 7.5px;
        }
        .info-value { font-weight: 900; color: #000000; }

        /* ====================================================
         * DETAILS TABLE (Rincian Tagihan)
         * ==================================================== */
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 18px;
            border: 3px solid #000000;
            box-shadow: 3px 3px 0px 0px #000000;
        }
        .details-table th {
            background-color: #facc15;
            color: #000000;
            font-weight: 900;
            text-transform: uppercase;
            font-size: 8px;
            padding: 6px 8px;
            border-bottom: 3px solid #000000;
            text-align: left;
        }
        .details-table td {
            padding: 7px 8px;
            border-bottom: 2px solid #000000;
            font-size: 8.5px;
            background-color: #ffffff;
            color: #000000;
        }
        .details-table td.amount { text-align: right; font-weight: 900; }
        .details-table tr.total-row td {
            font-weight: 900;
            background-color: #facc15;
            border-top: 3px solid #000000;
            border-bottom: none;
            font-size: 9px;
        }

        /* ====================================================
         * FOOTER TABLE (Stamp + Tanda Tangan)
         * ==================================================== */
        .footer-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 22px;
            border-top: 2px solid #000000;
            padding-top: 8px;
        }
        .footer-left { vertical-align: top; }
        .footer-right { text-align: right; vertical-align: top; }
        .stamp {
            border: 3px solid #000000;
            color: #000000;
            background-color: #22c55e;
            font-weight: 900;
            text-transform: uppercase;
            padding: 6px 14px;
            font-size: 14px;
            display: inline-block;
            transform: rotate(-6deg);
            letter-spacing: 1px;
            box-shadow: 2px 2px 0px 0px #000000;
        }
        .signature-area {
            display: inline-block;
            text-align: center;
            width: 130px;
            background-color: #ffffff;
            border: 2px solid #000000;
            padding: 6px;
            box-shadow: 2px 2px 0px 0px #000000;
        }
        .signature-title {
            font-size: 7.5px;
            font-weight: 900;
            text-transform: uppercase;
            color: #000000;
            margin-bottom: 25px;
        }
        .signature-line { border-bottom: 2px solid #000000; margin-bottom: 2px; }
        .signature-name { font-weight: 900; font-size: 8px; color: #000000; text-transform: uppercase; }
        .system-note {
            margin-top: 18px;
            font-size: 7.5px;
            font-weight: bold;
            color: #000000;
            text-align: center;
            border-top: 2px dashed #000000;
            padding-top: 8px;
            line-height: 1.3;
            text-transform: uppercase;
        }

        @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
        .icon-spin { animation: spin 1s linear infinite; display: inline-block; }

        @media print {
            body { background: white; padding: 0; }
            .action-bar { display: none !important; }
            #nota-container { border: none; box-shadow: none; padding: 0; }
        }
    </style>
</head>
<body>

    {{-- ============================================================
         ACTION BAR — Tidak masuk ke dalam PDF
         ============================================================ --}}
    <div class="action-bar" id="action-bar">
        <a href="javascript:history.back()" class="btn-back">
            &#8592; Kembali
        </a>
        <span class="action-bar-title">Pratinjau Nota Pembayaran</span>
        <button class="btn-download" id="btn-download" onclick="downloadPdf()">
            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="currentColor" viewBox="0 0 16 16">
                <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/>
                <path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3z"/>
            </svg>
            Download PDF
        </button>
    </div>

    {{-- ============================================================
         NOTA CONTAINER — Isi yang akan dirender menjadi PDF
         ============================================================ --}}
    <div id="nota-container">

        {{-- Header --}}
        <table class="header-table">
            <tr>
                <td>
                    <h1 class="brand-title">{{ $appName }}</h1>
                    <p class="brand-subtitle">Sistem Manajemen Kost Modern &amp; Terintegrasi</p>
                </td>
                <td class="header-right">
                    <strong>Alamat:</strong> {{ $contactAddress ?? '-' }}<br>
                    <strong>WhatsApp:</strong> {{ $contactWhatsapp ?? '-' }}
                </td>
            </tr>
        </table>

        {{-- Info Penyewa & Detail Pembayaran --}}
        <table class="meta-table">
            <tr>
                <td>
                    <div class="section-title">Ditagihkan Kepada</div>
                    <div class="info-card">
                        <div class="info-item">
                            <span class="info-label">Nama</span>
                            <span class="info-value">: {{ $penyewa?->user?->nama ?? '-' }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Kamar</span>
                            <span class="info-value">: Kamar {{ $penyewa?->kamar?->nomor_kamar ?? '-' }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Tipe</span>
                            <span class="info-value">: {{ ucfirst($penyewa?->kamar?->tipe ?? '-') }}</span>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="section-title" style="margin-left:6px;">Detail Pembayaran</div>
                    <div class="info-card right">
                        <div class="info-item">
                            <span class="info-label">No. Invoice</span>
                            <span class="info-value">: {{ $pembayaran->transaction_id ?? '-' }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Tanggal</span>
                            <span class="info-value">: {{ $pembayaran->tanggal_bayar?->translatedFormat('d M Y H:i') ?? '-' }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Metode</span>
                            <span class="info-value">: {{ ucfirst($pembayaran->payment_type ?? 'Cash') }}</span>
                        </div>
                        @if(!empty($pembayaran->bank) || !empty($pembayaran->va_number))
                            <div style="font-size:7.5px;color:#000;font-weight:900;margin-top:1px;padding-left:3px;text-transform:uppercase;">
                                @if(!empty($pembayaran->bank))<strong>{{ strtoupper($pembayaran->bank) }}</strong>@endif
                                @if(!empty($pembayaran->va_number)) VA: {{ $pembayaran->va_number }}@endif
                            </div>
                        @endif
                    </div>
                </td>
            </tr>
        </table>

        {{-- Tabel Rincian Tagihan --}}
        @php
            $namaBulanTagihan = !empty($tagihan->periode_bulan) && $tagihan->periode_bulan >= 1 && $tagihan->periode_bulan <= 12
                ? \Carbon\Carbon::createFromDate(null, (int)$tagihan->periode_bulan, 1)->translatedFormat('F')
                : '-';
        @endphp
        <table class="details-table">
            <thead>
                <tr>
                    <th>Rincian Deskripsi</th>
                    <th style="text-align:right;width:35%;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Sewa Kost Periode <strong>{{ $namaBulanTagihan }} {{ $tagihan->periode_tahun ?? '' }}</strong></td>
                    <td class="amount">Rp {{ number_format($tagihan->nominal_pokok ?? 0, 0, ',', '.') }}</td>
                </tr>
                @if(($nominalDeposit ?? 0) > 0)
                    <tr>
                        <td>Uang Deposit Jaminan Kamar</td>
                        <td class="amount">Rp {{ number_format($nominalDeposit, 0, ',', '.') }}</td>
                    </tr>
                @endif
                @if(($tagihan->nominal_denda ?? 0) > 0)
                    <tr>
                        <td style="color:#ef4444;font-weight:bold;">Denda Keterlambatan</td>
                        <td class="amount" style="color:#ef4444;">Rp {{ number_format($tagihan->nominal_denda, 0, ',', '.') }}</td>
                    </tr>
                @endif
                <tr class="total-row">
                    <td>Total Pembayaran Lunas</td>
                    <td class="amount">Rp {{ number_format($tagihan->nominal_total ?? 0, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>

        {{-- Stamp LUNAS + Area Tanda Tangan --}}
        <table class="footer-table">
            <tr>
                <td class="footer-left">
                    <div class="stamp">LUNAS</div>
                </td>
                <td class="footer-right">
                    <div class="signature-area">
                        <div class="signature-title">Pengelola Kost</div>
                        <div class="signature-line"></div>
                        <div class="signature-name">{{ $appName }}</div>
                    </div>
                </td>
            </tr>
        </table>

        <div class="system-note">
            Terima kasih atas pembayaran tepat waktu Anda. Nota ini diterbitkan secara otomatis oleh sistem<br>
            dan merupakan bukti transaksi pembayaran yang sah.
        </div>

    </div>{{-- end #nota-container --}}

    {{-- ============================================================
         SCRIPT: html2pdf.js — 100% berjalan di browser user
         ============================================================ --}}
    <script>
        function downloadPdf() {
            var btn = document.getElementById('btn-download');
            btn.classList.add('loading');
            btn.innerHTML = '<span class="icon-spin">&#8635;</span>&nbsp; Memproses...';

            var element = document.getElementById('nota-container');
            var filename = 'Nota-{{ addslashes($pembayaran->transaction_id) }}.pdf';

            var opt = {
                margin:      [8, 8, 8, 8],
                filename:    filename,
                image:       { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2, useCORS: true, logging: false },
                jsPDF:       { unit: 'mm', format: 'a5', orientation: 'portrait' }
            };

            html2pdf()
                .set(opt)
                .from(element)
                .save()
                .then(function() {
                    btn.classList.remove('loading');
                    btn.innerHTML = '&#10003; Selesai! Download Lagi?';
                    setTimeout(function() {
                        btn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="currentColor" viewBox="0 0 16 16"><path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/><path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3z"/></svg> Download PDF';
                    }, 2500);
                });
        }
    </script>
</body>
</html>