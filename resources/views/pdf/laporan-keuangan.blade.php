@extends('pdf.layouts.master')

@section('title', 'Laporan Keuangan Kost')
@section('page-size', 'a4 landscape')

@section('additional-styles')
    .summary-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
    .summary-table td { width: 33.33%; padding: 0; vertical-align: top; }
    .card { margin: 0 6px; padding: 10px 12px; border: 3px solid #000000; box-shadow: 4px 4px 0px 0px #000000; min-height: 45px; }
    .card-left { margin-left: 0; margin-right: 8px; }
    .card-middle { margin-left: 4px; margin-right: 4px; }
    .card-right { margin-right: 0; margin-left: 8px; }
    .card-masuk { background-color: #34d399; color: #000000; }
    .card-keluar { background-color: #f87171; color: #000000; }
    .card-saldo { background-color: #facc15; color: #000000; }
    .card-saldo-minus { background-color: #ef4444; color: #ffffff; }
    .card-title { font-size: 8px; text-transform: uppercase; font-weight: 900; color: #000000; margin-bottom: 4px; }
    .card-value { font-size: 16px; font-weight: 900; text-transform: uppercase; }
@endsection

@section('content')
    <table class="header-table">
        <tr>
            <td>
                <h1 class="brand-title">Laporan Keuangan & Penagihan</h1>
                <p class="brand-subtitle">{{ $appName }} - Dashboard Terintegrasi Sistem</p>
            </td>
            <td class="header-right">
                <div><strong>Periode Laporan:</strong> <span class="filter-badge">{{ $filterPeriode }}</span></div>
                <div style="margin-top: 3px;"><strong>Status Tagihan:</strong> <span class="filter-badge">{{ $filterStatus }}</span></div>
            </td>
        </tr>
    </table>

    <!-- Summary Cards -->
    <table class="summary-table">
        <tr>
            <td>
                <div class="card card-masuk card-left">
                    <div class="card-title">Total Arus Kas Masuk (Penerimaan)</div>
                    <div class="card-value">Rp {{ number_format($totalMasuk ?? 0, 0, ',', '.') }}</div>
                </div>
            </td>
            <td>
                <div class="card card-keluar card-middle">
                    <div class="card-title">Total Arus Kas Keluar (Pengeluaran)</div>
                    <div class="card-value">Rp {{ number_format($totalKeluar ?? 0, 0, ',', '.') }}</div>
                </div>
            </td>
            <td>
                <div class="card {{ ($saldoBersih ?? 0) >= 0 ? 'card-saldo' : 'card-saldo-minus' }} card-right">
                    <div class="card-title">Saldo Bersih Akhir</div>
                    <div class="card-value">Rp {{ number_format($saldoBersih ?? 0, 0, ',', '.') }}</div>
                </div>
            </td>
        </tr>
    </table>

    <div class="section-title">Arus Kas Masuk (Pemasukan Tagihan)</div>
    <table class="report-table">
        <thead>
            <tr>
                <th style="width: 12%;">Order ID</th>
                <th style="width: 10%;">Periode</th>
                <th>Nama Penyewa</th>
                <th style="width: 12%;">Nomor Kamar</th>
                <th style="text-align: right; width: 12%;">Sewa Pokok</th>
                <th style="text-align: right; width: 10%;">Denda</th>
                <th style="text-align: right; width: 12%;">Total Tagihan</th>
                <th style="width: 10%;">Metode</th>
                <th style="width: 10%;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($tagihan as $item)
                @php
                    $namaBulan = !empty($item->periode_bulan) && $item->periode_bulan >= 1 && $item->periode_bulan <= 12
                        ? \Carbon\Carbon::createFromDate(null, (int)$item->periode_bulan, 1)->translatedFormat('F')
                        : '-';
                @endphp
                <tr>
                    <td><strong>{{ $item->order_id }}</strong></td>
                    <td>{{ $namaBulan }} {{ $item->periode_tahun }}</td>
                    <td>{{ $item->penyewa?->user?->nama ?? '-' }}</td>
                    <td>Kamar {{ $item->penyewa?->kamar?->nomor_kamar ?? '-' }}</td>
                    <td class="amount">Rp {{ number_format($item->nominal_pokok ?? 0, 0, ',', '.') }}</td>
                    <td class="amount" style="{{ ($item->nominal_denda ?? 0) > 0 ? 'color: #ef4444;' : '' }}">
                        Rp {{ number_format($item->nominal_denda ?? 0, 0, ',', '.') }}
                    </td>
                    <td class="amount">Rp {{ number_format($item->nominal_total ?? 0, 0, ',', '.') }}</td>
                    <td style="text-transform: capitalize;">{{ $item->metode_pembayaran ?: '-' }}</td>
                    <td>
                        @php
                            $badgeClass = match($item->status) {
                                'lunas' => 'badge-lunas',
                                'pending' => 'badge-pending',
                                'terlambat' => 'badge-terlambat',
                                default => ''
                            };
                        @endphp
                        <span class="badge {{ $badgeClass }}">{{ ucfirst($item->status ?? '-') }}</span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" style="text-align: center; color: #64748b; padding: 15px;">Tidak ada data tagihan pada periode ini.</td>
                </tr>
            @endforelse
            @if($tagihan->isNotEmpty())
                <tr class="total-row">
                    <td colspan="4" style="text-align: right;">Total Pemasukan di Tabel:</td>
                    <td class="amount">Rp {{ number_format($totalPokokTabel ?? 0, 0, ',', '.') }}</td>
                    <td class="amount">Rp {{ number_format($totalDendaTabel ?? 0, 0, ',', '.') }}</td>
                    <td class="amount">Rp {{ number_format($totalTotalTabel ?? 0, 0, ',', '.') }}</td>
                    <td colspan="2"></td>
                </tr>
            @endif
        </tbody>
    </table>

    <div class="section-title">Arus Kas Keluar (Rekapitulasi Pengeluaran)</div>
    <table class="report-table">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 15%;">Tanggal</th>
                <th style="width: 30%;">Nama Pengeluaran</th>
                <th style="width: 15%;">Kategori</th>
                <th style="text-align: right; width: 15%;">Nominal</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pengeluaran as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->tanggal_pengeluaran?->translatedFormat('d M Y') ?? '-' }}</td>
                    <td><strong>{{ $item->nama_pengeluaran }}</strong></td>
                    <td style="text-transform: capitalize;">{{ $item->kategori }}</td>
                    <td class="amount" style="color: #ef4444;">Rp {{ number_format($item->nominal ?? 0, 0, ',', '.') }}</td>
                    <td>{{ $item->keterangan ?: '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; color: #64748b; padding: 15px;">Tidak ada data pengeluaran pada periode ini.</td>
                </tr>
            @endforelse
            @if($pengeluaran->isNotEmpty())
                <tr class="total-row">
                    <td colspan="4" style="text-align: right;">Total Pengeluaran di Tabel:</td>
                    <td class="amount" style="color: #ef4444;">Rp {{ number_format($totalPengeluaranTabel ?? 0, 0, ',', '.') }}</td>
                    <td></td>
                </tr>
            @endif
        </tbody>
    </table>
@endsection

@section('footer-note')
    * Penerimaan mencakup pembayaran tagihan lunas bulanan, serta transaksi DP/Pelunasan Reservasi aktif.<br>
    Laporan ini dibuat otomatis oleh sistem manajemen {{ $appName }}.
@endsection
