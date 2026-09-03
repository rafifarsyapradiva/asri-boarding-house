<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="color-scheme" content="light">
    <meta name="supported-color-schemes" content="light">
    <title>@yield('title', 'Asri Boarding House')</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;700;800;900&display=swap');
        
        body, table, td, a {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
        }
        
        body {
            width: 100% !important;
            height: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
            background-color: #f3f4f6;
        }
        
        table, td {
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
        }
        
        img {
            -ms-interpolation-mode: bicubic;
            border: 0;
            height: auto;
            line-height: 100%;
            outline: none;
            text-decoration: none;
        }
        
        /* Responsive styles */
        @media only screen and (max-width: 620px) {
            .wrapper {
                padding: 16px 8px !important;
            }
            .container {
                width: 100% !important;
            }
            .content-padding {
                padding: 24px 16px !important;
            }
            .header-padding {
                padding: 24px 16px !important;
            }
            .footer-padding {
                padding: 24px 16px !important;
            }
        }
    </style>
</head>
<body style="margin: 0; padding: 0; background-color: #f3f4f6; font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; -webkit-font-smoothing: antialiased; color: #000000;">
    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="table-layout: fixed; background-color: #f3f4f6;">
        <tr>
            <td align="center" style="padding: 40px 0;" class="wrapper">
                <!-- Neo-Brutalist Container Table (Sharp corners, thick black border, solid shadow) -->
                <table border="0" cellpadding="0" cellspacing="0" width="580" class="container" style="background-color: #ffffff; border: 4px solid #000000; box-shadow: 6px 6px 0px 0px #000000; -webkit-box-shadow: 6px 6px 0px 0px #000000; -moz-box-shadow: 6px 6px 0px 0px #000000;">
                    <!-- Header (Flat Yellow, thick border bottom) -->
                    <tr>
                        <td class="header-padding" style="background-color: #facc15; padding: 32px 40px; border-bottom: 4px solid #000000;">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td align="left">
                                        <!-- Brutalist Brand Box -->
                                        <table border="0" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td bgcolor="#ffffff" style="border: 3px solid #000000; padding: 6px 14px; box-shadow: 3px 3px 0px 0px #000000; -webkit-box-shadow: 3px 3px 0px 0px #000000;">
                                                    <span style="font-size: 20px; font-weight: 900; color: #000000; letter-spacing: 0.5px; text-transform: uppercase;">
                                                        ASRI BOARDING HOUSE
                                                    </span>
                                                </td>
                                            </tr>
                                        </table>
                                        <div style="font-size: 11px; color: #000000; font-weight: 800; margin-top: 12px; letter-spacing: 1.5px; text-transform: uppercase;">
                                            // LAPORAN & NOTIFIKASI RESMI
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <!-- Main Content -->
                    <tr>
                        <td class="content-padding" style="padding: 40px; background-color: #ffffff;">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td style="font-size: 15px; line-height: 24px; color: #000000;">
                                        @yield('content')
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <!-- Footer (Sharp top border, flat grey background) -->
                    <tr>
                        <td class="footer-padding" style="background-color: #ffffff; padding: 32px 40px; border-top: 4px solid #000000;">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td style="font-size: 12px; line-height: 18px; color: #000000; text-align: left;">
                                        <p style="margin: 0 0 8px 0; font-weight: 900; text-transform: uppercase; letter-spacing: 0.5px;">{{ \App\Models\Setting::get('logo_text', 'Asri Boarding House') }}</p>
                                        <p style="margin: 0 0 20px 0; font-weight: 500;">{{ \App\Models\Setting::get('contact_address', 'Jl. Asri No. 42, Sleman, Yogyakarta') }}<br>Hubungi Kami: {{ \App\Models\Setting::get('contact_whatsapp') }} | {{ \App\Models\Setting::get('contact_email') }}</p>
                                        <p style="margin: 0; font-size: 10px; font-weight: 700; color: #7f7f7f; text-transform: uppercase; letter-spacing: 0.5px;">&copy; {{ date('Y') }} Asri Boarding House. All rights reserved.</p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
