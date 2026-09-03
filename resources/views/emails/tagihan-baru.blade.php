@extends('layouts.email')

@section('title', 'Tagihan Sewa Kost Baru - Asri Boarding House')

@section('content')
    <h2 style="margin-top: 0; margin-bottom: 12px; color: #000000; font-size: 20px; font-weight: 900; text-transform: uppercase; letter-spacing: 0.5px;">Halo, {{ $namaPenyewa ?? 'Penyewa' }} 👋</h2>
    <p style="margin: 0 0 24px 0; color: #000000; font-size: 15px; line-height: 24px; font-weight: 500;">
        Tagihan sewa kamar Anda untuk periode bulan <strong>{{ $periodeSewa ?? '-' }}</strong> telah diterbitkan. Silakan periksa rincian tagihan di bawah ini dan lakukan pelunasan.
    </p>

    <!-- Neo-Brutalist Invoice Card (Table with thick border and sharp shadow) -->
    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #ffffff; border: 3px solid #000000; box-shadow: 4px 4px 0px 0px #000000; -webkit-box-shadow: 4px 4px 0px 0px #000000; margin-bottom: 28px;">
        <tr>
            <td style="padding: 24px;">
                <table border="0" cellpadding="0" cellspacing="0" width="100%">
                    <!-- Order ID -->
                    <tr>
                        <td style="padding-bottom: 12px; font-size: 11px; color: #000000; font-weight: 900; text-transform: uppercase; letter-spacing: 1px;">ORDER ID</td>
                        <td align="right" style="padding-bottom: 12px; font-size: 14px; color: #000000; font-weight: 900; text-transform: uppercase;">#{{ $orderId ?? '-' }}</td>
                    </tr>
                    <!-- Room Info -->
                    <tr>
                        <td style="padding-bottom: 12px; font-size: 14px; color: #000000; font-weight: 700;">Nomor Kamar</td>
                        <td align="right" style="padding-bottom: 12px; font-size: 14px; color: #000000; font-weight: 900;">No. {{ $nomorKamar ?? '-' }}</td>
                    </tr>
                    <!-- Base Price -->
                    <tr>
                        <td style="padding-bottom: 12px; font-size: 14px; color: #000000; font-weight: 500;">Harga Sewa Pokok</td>
                        <td align="right" style="padding-bottom: 12px; font-size: 14px; color: #000000; font-weight: 700;">{{ $nominalPokokFormatted ?? 'Rp 0' }}</td>
                    </tr>
                    
                    @if(($nominalDeposit ?? 0) > 0)
                        <tr>
                            <td style="padding-bottom: 12px; font-size: 14px; color: #000000; font-weight: 500;">Uang Deposit Jaminan Kamar</td>
                            <td align="right" style="padding-bottom: 12px; font-size: 14px; color: #000000; font-weight: 700;">{{ $nominalDepositFormatted ?? 'Rp 0' }}</td>
                        </tr>
                    @endif
                    <!-- Penalty (If exists) -->
                    @if(($nominalDenda ?? 0) > 0)
                        <tr>
                            <td style="padding-bottom: 12px; font-size: 14px; color: #ef4444; font-weight: 800; text-transform: uppercase;">Denda Keterlambatan</td>
                            <td align="right" style="padding-bottom: 12px; font-size: 14px; color: #ef4444; font-weight: 900;">{{ $nominalDendaFormatted ?? 'Rp 0' }}</td>
                        </tr>
                    @endif
                    <!-- Divider (Solid heavy black line) -->
                    <tr>
                        <td colspan="2" style="border-top: 3px solid #000000; padding-top: 16px; padding-bottom: 4px;"></td>
                    </tr>
                    <!-- Total Bill -->
                    <tr>
                        <td style="font-size: 16px; color: #000000; font-weight: 900; text-transform: uppercase; letter-spacing: 0.5px;">Total Tagihan</td>
                        <td align="right" style="font-size: 20px; color: #000000; font-weight: 900; background-color: #facc15; border: 2px solid #000000; padding: 4px 10px; box-shadow: 2px 2px 0px 0px #000000;">
                            {{ $nominalTotalFormatted ?? 'Rp 0' }}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- Due Date Alert Box (Neo-brutalist alert) -->
    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #fffbeb; border: 3px solid #000000; box-shadow: 3px 3px 0px 0px #000000; -webkit-box-shadow: 3px 3px 0px 0px #000000; margin-bottom: 28px;">
        <tr>
            <td style="padding: 16px 20px;">
                <table border="0" cellpadding="0" cellspacing="0" width="100%">
                    <tr>
                        <td style="font-size: 14px; color: #000000; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px;">
                            ⚠️ JATUH TEMPO: <span style="background-color: #ffffff; border: 1px solid #000000; padding: 2px 6px;">{{ $tanggalJatuhTempoFormatted ?? '-' }}</span>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <p style="margin: 0 0 28px 0; color: #000000; font-size: 14px; line-height: 22px; font-weight: 500;">
        Silakan lakukan pembayaran secara langsung dengan masuk ke dasbor akun Anda melalui tombol di bawah ini:
    </p>

    <!-- Neo-Brutalist CTA Button -->
    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td align="left" style="padding-bottom: 16px;">
                <table border="0" cellpadding="0" cellspacing="0">
                    <tr>
                        <td align="center" bgcolor="#facc15" style="border: 3px solid #000000; box-shadow: 4px 4px 0px 0px #000000; -webkit-box-shadow: 4px 4px 0px 0px #000000;">
                            <a href="{{ $urlLoginPenyewa ?? route('penyewa.login') }}" target="_blank" style="display: inline-block; padding: 14px 28px; font-size: 14px; font-weight: 900; color: #000000; text-decoration: none; text-transform: uppercase; letter-spacing: 0.5px;">
                                BAYAR TAGIHAN SEKARANG &rarr;
                            </a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <p style="margin: 24px 0 0 0; color: #7f7f7f; font-size: 12px; font-style: italic; font-weight: 700; text-transform: uppercase;">
        // ABAIKAN EMAIL INI JIKA ANDA SUDAH MELAKUKAN PEMBAYARAN.
    </p>
@endsection

