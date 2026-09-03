@extends('layouts.email')

@php
    $isDenda = ($type ?? '') === 'denda';
    $btnBg = $isDenda ? '#facc15' : '#000000';
    $btnColor = $isDenda ? '#000000' : '#ffffff';
    $btnShadow = $isDenda ? '#000000' : '#facc15';
    $btnText = $isDenda ? 'LUNASI SEKARANG' : 'BAYAR SEKARANG';
@endphp

@section('title', $isDenda ? 'Denda Keterlambatan Diberlakukan' : 'Pengingat Batas Jatuh Tempo')

@section('content')
    @if(! $isDenda)
        <!-- Email Masa Keringanan (Reminder) -->
        <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #facc15; border: 4px solid #000000; box-shadow: 6px 6px 0px 0px #000000; margin-bottom: 28px;">
            <tr>
                <td style="padding: 24px; text-align: center;">
                    <span style="font-size: 36px; line-height: 1; display: block; margin-bottom: 12px;">⚠️</span>
                    <h2 style="margin: 0; color: #000000; font-size: 18px; font-weight: 900; text-transform: uppercase; letter-spacing: 0.5px;">
                        PENGINGAT MASA KERINGANAN (BEBAS DENDA)
                    </h2>
                </td>
            </tr>
        </table>

        <p style="margin: 0 0 20px 0; color: #000000; font-size: 15px; line-height: 24px; font-weight: 500;">
            Halo <strong>{{ $namaPenyewa ?? 'Penyewa' }}</strong>,
        </p>

        <p style="margin: 0 0 24px 0; color: #000000; font-size: 14px; line-height: 22px; font-weight: 500;">
            Mengingatkan bahwa tagihan kost Anda untuk Kamar <strong>{{ $nomorKamar ?? '-' }}</strong> periode <strong>{{ $periodeSewa ?? '-' }}</strong> telah melewati batas jatuh tempo.
        </p>

        <p style="margin: 0 0 24px 0; color: #000000; font-size: 14px; line-height: 22px; font-weight: 500;">
            Saat ini Anda berada dalam <strong>Masa Keringanan (Toleransi)</strong>. Anda masih dapat melakukan pembayaran sebesar <strong>{{ $nominalPokokFormatted ?? 'Rp 0' }}</strong> tanpa dikenakan denda sama sekali hingga akhir bulan ini.
        </p>

        <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #fffbeb; border: 4px solid #000000; box-shadow: 6px 6px 0px 0px #000000; margin-bottom: 28px;">
            <tr>
                <td style="padding: 16px 20px; font-size: 14px; color: #000000; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; line-height: 20px;">
                    ⚠️ PENTING: Jika pembayaran belum diselesaikan hingga berganti bulan, sistem akan otomatis memberlakukan denda keterlambatan 5% pada tagihan ini.
                </td>
            </tr>
        </table>

    @else
        <!-- Email Penegakan Denda -->
        <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #ef4444; border: 4px solid #000000; box-shadow: 6px 6px 0px 0px #000000; margin-bottom: 28px;">
            <tr>
                <td style="padding: 24px; text-align: center;">
                    <span style="font-size: 36px; line-height: 1; display: block; margin-bottom: 12px;">🔴</span>
                    <h2 style="margin: 0; color: #ffffff; font-size: 18px; font-weight: 900; text-transform: uppercase; letter-spacing: 0.5px;">
                        DENDA KETERLAMBATAN DIBERLAKUKAN
                    </h2>
                </td>
            </tr>
        </table>

        <p style="margin: 0 0 20px 0; color: #000000; font-size: 15px; line-height: 24px; font-weight: 500;">
            Halo <strong>{{ $namaPenyewa ?? 'Penyewa' }}</strong>,
        </p>

        <p style="margin: 0 0 24px 0; color: #000000; font-size: 14px; line-height: 22px; font-weight: 500;">
            Kami menginformasikan bahwa tagihan sewa kost Kamar <strong>{{ $nomorKamar ?? '-' }}</strong> untuk periode <strong>{{ $periodeSewa ?? '-' }}</strong> belum diselesaikan hingga melewati batas akhir bulan berjalan. Masa keringanan Anda telah berakhir dan sistem memberlakukan <strong>Denda Keterlambatan 5%</strong>.
        </p>

        <!-- Breakdown Details Table -->
        <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #ffffff; border: 4px solid #000000; box-shadow: 6px 6px 0px 0px #000000; margin-bottom: 28px;">
            <tr>
                <td style="padding: 20px;">
                    <table border="0" cellpadding="0" cellspacing="0" width="100%">
                        <!-- Kamar -->
                        <tr>
                            <td style="padding-bottom: 12px; font-size: 14px; color: #000000; font-weight: 700; text-transform: uppercase;">Nomor Kamar</td>
                            <td align="right" style="padding-bottom: 12px; font-size: 14px; color: #000000; font-weight: 900;">No. {{ $nomorKamar ?? '-' }}</td>
                        </tr>
                        <!-- Sewa Pokok -->
                        <tr>
                            <td style="padding-bottom: 12px; font-size: 14px; color: #000000; font-weight: 500;">Sewa Pokok</td>
                            <td align="right" style="padding-bottom: 12px; font-size: 14px; color: #000000; font-weight: 700;">{{ $nominalPokokFormatted ?? 'Rp 0' }}</td>
                        </tr>
                        <!-- Denda -->
                        <tr>
                            <td style="padding-bottom: 12px; font-size: 14px; color: #ef4444; font-weight: 800; text-transform: uppercase;">Denda Keterlambatan (5%)</td>
                            <td align="right" style="padding-bottom: 12px; font-size: 14px; color: #ef4444; font-weight: 900;">{{ $nominalDendaFormatted ?? 'Rp 0' }}</td>
                        </tr>
                        <!-- Divider -->
                        <tr>
                            <td colspan="2" style="border-top: 4px solid #000000; padding-top: 16px; padding-bottom: 4px;"></td>
                        </tr>
                        <!-- Total -->
                        <tr>
                            <td style="font-size: 15px; color: #000000; font-weight: 900; text-transform: uppercase; letter-spacing: 0.5px;">Total Wajib Bayar</td>
                            <td align="right" style="font-size: 18px; color: #000000; background-color: #facc15; border: 3px solid #000000; padding: 4px 10px; box-shadow: 3px 3px 0px 0px #000000; font-weight: 900;">
                                {{ $nominalTotalFormatted ?? 'Rp 0' }}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    @endif

    <!-- Action Button -->
    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-bottom: 28px;">
        <tr>
            <td align="left">
                <table border="0" cellpadding="0" cellspacing="0">
                    <tr>
                        <td align="center" bgcolor="{{ $btnBg }}" style="border: 4px solid #000000; box-shadow: 6px 6px 0px 0px {{ $btnShadow }};">
                            <a href="{{ $urlTagihan ?? route('penyewa.login') }}" target="_blank" style="display: inline-block; padding: 14px 28px; font-size: 14px; font-weight: 900; color: {{ $btnColor }}; text-decoration: none; text-transform: uppercase; letter-spacing: 0.5px;">
                                {{ $btnText }} &rarr;
                            </a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <p style="margin: 24px 0 0 0; color: #7f7f7f; font-size: 12px; font-style: italic; font-weight: 700; text-transform: uppercase;">
        // ABAIKAN JIKA PEMBAYARAN TELAH DILAKUKAN. SILAKAN HUBUNGI ADMIN JIKA TERJADI KESALAHAN PENCATATAN.
    </p>
@endsection

