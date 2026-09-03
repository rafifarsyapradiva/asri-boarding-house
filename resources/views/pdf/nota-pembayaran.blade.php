@extends('pdf.layouts.master')

@section('title', 'Nota Pembayaran Kost')
@section('page-size', 'a5 portrait')
@section('page-margin', '10mm')

@section('additional-styles')
    .meta-table { width: 100%; border-collapse: collapse; margin-bottom: 18px; }
    .meta-table td { width: 50%; vertical-align: top; }
    .info-card { background-color: #ffffff; border: 3px solid #000000; padding: 6px 8px; margin-right: 6px; min-height: 75px; box-shadow: 3px 3px 0px 0px #000000; }
    .info-card.right { margin-right: 0; margin-left: 6px; }
    .info-item { margin-bottom: 4px; }
    .info-label { color: #000000; font-weight: bold; text-transform: uppercase; display: inline-block; width: 65px; font-size: 7.5px; }
    .info-value { font-weight: 900; color: #000000; }

    .details-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; border: 3px solid #000000; box-shadow: 3px 3px 0px 0px #000000; }
    .details-table th { background-color: #facc15; color: #000000; font-weight: 900; text-transform: uppercase; font-size: 8.5px; padding: 6px 8px; border-bottom: 3px solid #000000; text-align: left; }
    .details-table td { padding: 7px 8px; border-bottom: 2px solid #000000; font-size: 9px; background-color: #ffffff; color: #000000; }
    .details-table td.amount { text-align: right; font-weight: 900; }
    .details-table tr.total-row td { font-weight: 900; background-color: #facc15; border-top: 3px solid #000000; border-bottom: none; font-size: 9.5px; }

    .stamp { border: 3px solid #000000; color: #000000; background-color: #22c55e; font-weight: 900; text-transform: uppercase; padding: 6px 12px; font-size: 13px; display: inline-block; transform: rotate(-6deg); letter-spacing: 1px; box-shadow: 2px 2px 0px 0px #000000; }
    .signature-area { display: inline-block; text-align: center; width: 130px; background-color: #ffffff; border: 2px solid #000000; padding: 6px; box-shadow: 2px 2px 0px 0px #000000; }
    .signature-title { font-size: 7.5px; font-weight: 900; text-transform: uppercase; color: #000000; margin-bottom: 25px; }
    .signature-line { border-bottom: 2px solid #000000; margin-bottom: 2px; }
    .signature-name { font-weight: 900; font-size: 8px; color: #000000; text-transform: uppercase; }
    .system-note { margin-top: 20px; font-size: 7.5px; font-weight: bold; color: #000000; text-align: center; border-top: 2px dashed #000000; padding-top: 8px; line-height: 1.3; text-transform: uppercase; }
@endsection

@section('content')
    <table class="header-table">
        <tr>
            <td>
                <h1 class="brand-title">{{ $appName }}</h1>
                <p class="brand-subtitle">Sistem Manajemen Kost Modern & Terintegrasi</p>
            </td>
            <td class="header-right">
                <strong>Alamat:</strong> {{ $contactAddress }}<br>
                <strong>WhatsApp:</strong> {{ $contactWhatsapp }}
            </td>
        </tr>
    </table>

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
                <div class="section-title" style="margin-left: 6px;">Detail Pembayaran</div>
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
                        <div style="font-size: 7.5px; color: #000000; font-weight: 900; margin-top: 1px; padding-left: 3px; text-transform: uppercase;">
                            @if(!empty($pembayaran->bank))<strong>{{ strtoupper($pembayaran->bank) }}</strong>@endif
                            @if(!empty($pembayaran->va_number)) VA: {{ $pembayaran->va_number }}@endif
                        </div>
                    @endif
                </div>
            </td>
        </tr>
    </table>

    <table class="details-table">
        <thead>
            <tr>
                <th>Rincian Deskripsi</th>
                <th style="text-align: right; width: 35%;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @php
                $namaBulanTagihan = !empty($tagihan->periode_bulan) && $tagihan->periode_bulan >= 1 && $tagihan->periode_bulan <= 12
                    ? \Carbon\Carbon::createFromDate(null, (int)$tagihan->periode_bulan, 1)->translatedFormat('F')
                    : '-';
            @endphp
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
                    <td style="color: #ef4444; font-weight: bold;">Denda Keterlambatan</td>
                    <td class="amount" style="color: #ef4444;">Rp {{ number_format($tagihan->nominal_denda, 0, ',', '.') }}</td>
                </tr>
            @endif
            <tr class="total-row">
                <td>Total Pembayaran Lunas</td>
                <td class="amount">Rp {{ number_format($tagihan->nominal_total ?? 0, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

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
@endsection

@section('custom-footer')
    <div class="system-note">
        Terima kasih atas pembayaran tepat waktu Anda. Nota ini diterbitkan secara otomatis oleh sistem<br>
        dan merupakan bukti transaksi pembayaran yang sah.
    </div>
@endsection
