@extends('layouts.email')

@section('title', 'Reset Password - Asri Boarding House')

@section('content')
    <h2 style="margin-top: 0; margin-bottom: 12px; color: #000000; font-size: 20px; font-weight: 900; text-transform: uppercase; letter-spacing: 0.5px;">Halo, {{ $namaPenyewa ?? 'Pengguna' }} 👋</h2>
    <p style="margin: 0 0 20px 0; color: #000000; font-size: 15px; line-height: 24px; font-weight: 500;">
        Anda menerima email ini karena kami menerima permintaan reset password untuk akun <strong>{{ $roleName ?? 'Penyewa' }}</strong> Anda di Asri Boarding House.
    </p>

    <!-- Neo-Brutalist CTA Button -->
    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-top: 24px; margin-bottom: 28px;">
        <tr>
            <td align="left">
                <table border="0" cellpadding="0" cellspacing="0">
                    <tr>
                        <td align="center" bgcolor="#facc15" style="border: 3px solid #000000; box-shadow: 4px 4px 0px 0px #000000; -webkit-box-shadow: 4px 4px 0px 0px #000000;">
                            <a href="{{ $urlReset ?? '#' }}" target="_blank" style="display: inline-block; padding: 14px 28px; font-size: 14px; font-weight: 900; color: #000000; text-decoration: none; text-transform: uppercase; letter-spacing: 0.5px;">
                                RESET PASSWORD AKUN &rarr;
                            </a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- Neo-Brutalist Info Panel (Box with flat border and sharp shadow) -->
    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f3f4f6; border: 3px solid #000000; box-shadow: 3px 3px 0px 0px #000000; -webkit-box-shadow: 3px 3px 0px 0px #000000; margin-bottom: 28px;">
        <tr>
            <td style="padding: 16px 20px; font-size: 13px; line-height: 20px; color: #000000; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">
                ⚠️ TAUTAN INI HANYA BERLAKU SELAMA {{ $expireMinutes ?? 60 }} MENIT.
            </td>
        </tr>
    </table>

    <p style="margin: 0 0 20px 0; color: #000000; font-size: 14px; line-height: 22px; font-weight: 500;">
        Jika Anda tidak merasa mengajukan permintaan ini, silakan abaikan email ini dan akun Anda akan tetap aman.
    </p>

    <!-- Subcopy fallback for older clients / text links -->
    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="border-top: 3px solid #000000; margin-top: 28px; padding-top: 20px;">
        <tr>
            <td style="font-size: 12px; line-height: 18px; color: #000000; font-weight: 500;">
                <span style="font-weight: 800; text-transform: uppercase; display: block; margin-bottom: 4px;">Kendala dengan tombol?</span>
                Salin dan tempel tautan berikut ke peramban web Anda:<br>
                <a href="{{ $urlReset ?? '#' }}" style="color: #4f46e5; text-decoration: underline; font-weight: 700; word-break: break-all;">{{ $urlReset ?? '#' }}</a>
            </td>
        </tr>
    </table>
@endsection

