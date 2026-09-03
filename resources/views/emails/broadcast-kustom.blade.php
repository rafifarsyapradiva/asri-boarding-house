@extends('layouts.email')

@section('title', $subject ?? 'Pengumuman - Asri Boarding House')

@section('content')
    <h2 style="margin-top: 0; margin-bottom: 16px; color: #000000; font-size: 20px; font-weight: 900; text-transform: uppercase; letter-spacing: 0.5px;">Halo, {{ $namaPenyewa ?? 'Penyewa' }} 👋</h2>
    
    <!-- Neo-Brutalist Announcement Box (Table with thick border and sharp shadow) -->
    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #ffffff; border: 3px solid #000000; box-shadow: 4px 4px 0px 0px #000000; -webkit-box-shadow: 4px 4px 0px 0px #000000; margin-bottom: 28px;">
        <tr>
            <td style="padding: 24px; font-size: 15px; line-height: 26px; color: #000000; font-weight: 500; white-space: pre-line;">
                {!! nl2br(e($pesan ?? 'Tidak ada pesan pengumuman.')) !!}
            </td>
        </tr>
    </table>

    <!-- Neo-Brutalist CTA Button -->
    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td align="left" style="padding-bottom: 16px;">
                <table border="0" cellpadding="0" cellspacing="0">
                    <tr>
                        <td align="center" bgcolor="#facc15" style="border: 3px solid #000000; box-shadow: 4px 4px 0px 0px #000000; -webkit-box-shadow: 4px 4px 0px 0px #000000;">
                            <a href="{{ $urlDashboard ?? route('penyewa.login') }}" target="_blank" style="display: inline-block; padding: 14px 28px; font-size: 14px; font-weight: 900; color: #000000; text-decoration: none; text-transform: uppercase; letter-spacing: 0.5px;">
                                MASUK KE DASHBOARD &rarr;
                            </a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <p style="margin: 24px 0 0 0; color: #7f7f7f; font-size: 12px; font-style: italic; font-weight: 700; text-transform: uppercase;">
        // EMAIL INI DIKIRIMKAN SECARA OTOMATIS DARI SISTEM ASRI BOARDING HOUSE.
    </p>
@endsection

