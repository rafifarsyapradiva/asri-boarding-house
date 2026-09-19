@extends('layouts.email')

@section('title', 'Selamat Datang di Asri Boarding House!')

@section('content')
    <!-- Welcome Header -->
    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #fde047; border: 3px solid #000000; box-shadow: 4px 4px 0px 0px #000000; -webkit-box-shadow: 4px 4px 0px 0px #000000; margin-bottom: 28px;">
        <tr>
            <td style="padding: 24px; text-align: center;">
                <span style="font-size: 40px; line-height: 1; display: block; margin-bottom: 12px;">🏠</span>
                <h2 style="margin: 0; color: #000000; font-size: 20px; font-weight: 900; text-transform: uppercase; letter-spacing: 0.5px;">SELAMAT DATANG PENYEWA BARU!</h2>
            </td>
        </tr>
    </table>

    <h2 style="margin-top: 0; margin-bottom: 12px; color: #000000; font-size: 18px; font-weight: 900;">Halo, {{ $namaPenyewa ?? 'Penyewa' }}! 👋</h2>
    <p style="margin: 0 0 24px 0; color: #000000; font-size: 15px; line-height: 24px; font-weight: 500;">
        Selamat bergabung sebagai penyewa resmi di <strong>Asri Boarding House</strong>! Kamar Anda kini telah resmi <strong>AKTIF</strong> dan siap dihuni.
    </p>

    <!-- Detail Kamar -->
    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #ffffff; border: 3px solid #000000; box-shadow: 4px 4px 0px 0px #000000; -webkit-box-shadow: 4px 4px 0px 0px #000000; margin-bottom: 28px;">
        <tr>
            <td style="padding: 20px;">
                <table border="0" cellpadding="0" cellspacing="0" width="100%">
                    <!-- Nomor Kamar -->
                    <tr>
                        <td style="padding-bottom: 12px; font-size: 14px; color: #000000; font-weight: 700; text-transform: uppercase;">Nomor Kamar</td>
                        <td align="right" style="padding-bottom: 12px; font-size: 14px; color: #000000; font-weight: 900;">No. {{ $nomorKamar ?? '-' }}</td>
                    </tr>
                    <!-- Harga Sewa -->
                    @if(!empty($hargaSewa))
                    <tr>
                        <td style="padding-bottom: 12px; font-size: 14px; color: #000000; font-weight: 700; text-transform: uppercase;">Harga Sewa</td>
                        <td align="right" style="padding-bottom: 12px; font-size: 14px; color: #000000; font-weight: 900;">{{ $hargaSewa }}</td>
                    </tr>
                    @endif
                    <!-- Status -->
                    <tr>
                        <td style="font-size: 14px; color: #000000; font-weight: 700; text-transform: uppercase;">Status Hunian</td>
                        <td align="right" style="font-size: 14px;">
                            <span style="background-color: #86efac; border: 1.5px solid #000000; padding: 2px 8px; font-weight: 900; color: #000000;">AKTIF</span>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <p style="margin: 0 0 20px 0; color: #000000; font-size: 14px; line-height: 22px; font-weight: 500;">
        Gunakan portal penyewa kami untuk memantau tagihan, melaporkan keluhan, serta berkomunikasi langsung dengan tim manajemen.
    </p>

    <!-- CTA Button -->
    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td align="left" style="padding-bottom: 24px;">
                <table border="0" cellpadding="0" cellspacing="0">
                    <tr>
                        <td align="center" bgcolor="#fde047" style="border: 3px solid #000000; box-shadow: 4px 4px 0px 0px #000000; -webkit-box-shadow: 4px 4px 0px 0px #000000;">
                            <a href="{{ $urlDashboard ?? '#' }}" target="_blank" style="display: inline-block; padding: 14px 28px; font-size: 14px; font-weight: 900; color: #000000; text-decoration: none; text-transform: uppercase; letter-spacing: 0.5px;">
                                BUKA PORTAL PENYEWA &rarr;
                            </a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <p style="margin: 0; color: #7f7f7f; font-size: 12px; font-style: italic; font-weight: 700; text-transform: uppercase;">
        // SELAMAT BERISTIRAHAT &mdash; MANAJEMEN ASRI BOARDING HOUSE
    </p>
@endsection
