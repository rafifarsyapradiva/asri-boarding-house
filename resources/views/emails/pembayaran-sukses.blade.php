@extends('layouts.email')

@section('title', 'Konfirmasi Pembayaran Berhasil - Asri Boarding House')

@section('content')
    <!-- Neo-Brutalist Success Alert Header -->
    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #86efac; border: 3px solid #000000; box-shadow: 4px 4px 0px 0px #000000; -webkit-box-shadow: 4px 4px 0px 0px #000000; margin-bottom: 28px;">
        <tr>
            <td style="padding: 24px; text-align: center;">
                <span style="font-size: 36px; line-height: 1; display: block; margin-bottom: 12px;">✅</span>
                <h2 style="margin: 0; color: #000000; font-size: 20px; font-weight: 900; text-transform: uppercase; letter-spacing: 0.5px;">PEMBAYARAN DIKONFIRMASI</h2>
            </td>
        </tr>
    </table>

    <p style="margin: 0 0 20px 0; color: #000000; font-size: 15px; line-height: 24px; font-weight: 500;">
        Halo <strong>{{ $namaPenyewa ?? 'Penyewa' }}</strong>, pembayaran sewa kamar Anda untuk periode bulan <strong>{{ $periodeSewa ?? '-' }}</strong> telah berhasil kami terima dan verifikasi.
    </p>

    <!-- Payment Details Table -->
    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #ffffff; border: 3px solid #000000; box-shadow: 4px 4px 0px 0px #000000; -webkit-box-shadow: 4px 4px 0px 0px #000000; margin-bottom: 28px;">
        <tr>
            <td style="padding: 20px;">
                <table border="0" cellpadding="0" cellspacing="0" width="100%">
                    <!-- Payment Method -->
                    <tr>
                        <td style="padding-bottom: 12px; font-size: 14px; color: #000000; font-weight: 700; text-transform: uppercase;">Metode Pembayaran</td>
                        <td align="right" style="padding-bottom: 12px; font-size: 14px; color: #000000; font-weight: 900; text-transform: uppercase;">{{ $metodePembayaran ?? 'Transfer Bank' }}</td>
                    </tr>
                    <!-- Amount -->
                    <tr>
                        <td style="padding-bottom: 12px; font-size: 14px; color: #000000; font-weight: 700; text-transform: uppercase;">Jumlah Pembayaran</td>
                        <td align="right" style="padding-bottom: 12px; font-size: 14px; color: #000000; font-weight: 900;">{{ $nominalBayarFormatted ?? 'Rp 0' }}</td>
                    </tr>
                    <!-- Status -->
                    <tr>
                        <td style="font-size: 14px; color: #000000; font-weight: 700; text-transform: uppercase;">Status</td>
                        <td align="right" style="font-size: 14px;">
                            <span style="background-color: #86efac; border: 1.5px solid #000000; padding: 2px 8px; font-weight: 900; color: #000000;">LUNAS</span>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <p style="margin: 0 0 24px 0; color: #000000; font-size: 14px; line-height: 22px; font-weight: 500;">
        Nota pembayaran resmi telah diterbitkan dalam format PDF. Silakan klik tombol di bawah untuk mengunduh nota Anda:
    </p>

    <!-- Action Button (Brutalist Green) -->
    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td align="left" style="padding-bottom: 16px;">
                <table border="0" cellpadding="0" cellspacing="0">
                    <tr>
                        <td align="center" bgcolor="#86efac" style="border: 3px solid #000000; box-shadow: 4px 4px 0px 0px #000000; -webkit-box-shadow: 4px 4px 0px 0px #000000;">
                            <a href="{{ $urlNota ?? '#' }}" target="_blank" style="display: inline-block; padding: 14px 28px; font-size: 14px; font-weight: 900; color: #000000; text-decoration: none; text-transform: uppercase; letter-spacing: 0.5px;">
                                UNDUH NOTA PDF &rarr;
                            </a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <p style="margin: 24px 0 0 0; color: #7f7f7f; font-size: 12px; font-style: italic; font-weight: 700; text-transform: uppercase;">
        // JIKA ADA PERTANYAAN MENGENAI NOTA, SILAKAN HUBUNGI TIM ADMINISTRASI.
    </p>
@endsection

