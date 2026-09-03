<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>@yield('title', 'Dokumen Laporan Kost')</title>
    <style>
        /* CSS Design Tokens - DomPDF 2.0+ Compatible */
        :root {
            --neo-black: #000000;
            --neo-white: #ffffff;
            --neo-yellow: #facc15;
            --neo-green: #22c55e;
            --neo-red: #ef4444;
            --neo-emerald: #34d399;
            --neo-blue: #38bdf8;
            --neo-zebra: #f1f5f9;
        }

        @page {
            size: @yield('page-size', 'a4 landscape');
            margin: @yield('page-margin', '12mm');
        }

        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 8.5px;
            line-height: 1.35;
            color: var(--neo-black);
            background-color: var(--neo-white);
            margin: 0;
            padding: 0;
        }

        /* Header Styling - Brutalist */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 18px;
            border: 3px solid var(--neo-black);
            background-color: var(--neo-yellow);
            box-shadow: 4px 4px 0px 0px var(--neo-black);
        }
        .header-table td {
            vertical-align: middle;
            padding: 10px 15px;
        }
        .brand-title {
            font-size: 18px;
            font-weight: 900;
            color: var(--neo-black);
            text-transform: uppercase;
            margin: 0;
            letter-spacing: 0.5px;
        }
        .brand-subtitle {
            font-size: 9px;
            font-weight: bold;
            color: var(--neo-black);
            margin: 2px 0 0 0;
            text-transform: uppercase;
        }
        .header-right {
            text-align: right;
            font-size: 8.5px;
            color: var(--neo-black);
            font-weight: bold;
        }
        .filter-badge {
            display: inline-block;
            background-color: var(--neo-white);
            border: 2px solid var(--neo-black);
            padding: 2px 6px;
            font-weight: 900;
            color: var(--neo-black);
            margin-top: 4px;
            text-transform: uppercase;
            box-shadow: 1px 1px 0px 0px var(--neo-black);
        }

        /* Section Title - Brutalist */
        .section-title {
            font-size: 11px;
            font-weight: 900;
            color: var(--neo-black);
            background-color: var(--neo-yellow);
            border: 3px solid var(--neo-black);
            box-shadow: 2px 2px 0px 0px var(--neo-black);
            display: inline-block;
            padding: 4px 8px;
            margin: 15px 0 10px 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Table Styling - Brutalist */
        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            border: 3px solid var(--neo-black);
            box-shadow: 3px 3px 0px 0px var(--neo-black);
        }
        .report-table th {
            background-color: var(--neo-black);
            color: var(--neo-white);
            font-weight: 900;
            text-transform: uppercase;
            font-size: 8px;
            letter-spacing: 0.3px;
            padding: 6px 8px;
            border: 2px solid var(--neo-black);
            text-align: left;
        }
        .report-table td {
            padding: 6px 8px;
            border: 2px solid var(--neo-black);
            font-size: 8px;
            color: var(--neo-black);
            background-color: var(--neo-white);
        }
        .report-table tbody tr:nth-child(even) td {
            background-color: var(--neo-zebra);
        }
        .report-table td.amount {
            text-align: right;
            font-weight: 900;
        }
        .report-table tr.total-row td {
            font-weight: 900;
            background-color: var(--neo-yellow);
            border-top: 3px solid var(--neo-black);
            color: var(--neo-black);
            text-transform: uppercase;
        }

        /* Status Badges */
        .badge {
            display: inline-block;
            padding: 2px 6px;
            font-weight: 900;
            font-size: 7.5px;
            text-transform: uppercase;
            text-align: center;
            border: 2px solid var(--neo-black);
            box-shadow: 1px 1px 0px 0px var(--neo-black);
        }
        .badge-lunas { background-color: var(--neo-green); color: var(--neo-black); }
        .badge-pending { background-color: var(--neo-yellow); color: var(--neo-black); }
        .badge-terlambat { background-color: var(--neo-red); color: var(--neo-white); }

        /* Footer & Page Counter */
        .footer-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
            border-top: 2px solid var(--neo-black);
            padding-top: 8px;
        }
        .footer-left {
            font-size: 7.5px;
            font-weight: bold;
            color: var(--neo-black);
            vertical-align: top;
            text-transform: uppercase;
        }
        .footer-right {
            text-align: right;
            font-size: 8px;
            font-weight: 900;
            color: var(--neo-black);
            vertical-align: top;
            text-transform: uppercase;
        }
        .page-number:before {
            content: " Hal " counter(page);
        }

        @yield('additional-styles')
    </style>
</head>
<body>
    @yield('content')

    @sectionMissing('custom-footer')
        <table class="footer-table">
            <tr>
                <td class="footer-left">
                    @yield('footer-note', 'Laporan ini dibuat otomatis oleh sistem manajemen ' . ($appName ?? 'Kost') . '.')
                </td>
                <td class="footer-right">
                    Dicetak pada: @yield('printed-at', now()->translatedFormat('d M Y H:i:s')) oleh Admin <span class="page-number"></span>
                </td>
            </tr>
        </table>
    @else
        @yield('custom-footer')
    @endif
</body>
</html>
