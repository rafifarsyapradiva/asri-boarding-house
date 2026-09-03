@extends('pdf.layouts.master')

@section('title', 'Laporan Pengeluaran Kost')
@section('page-size', 'a4 landscape')

@section('additional-styles')
    .summary-container { margin-bottom: 20px; }
    .card-pengeluaran {
        display: inline-block;
        padding: 10px 15px;
        border: 3px solid #000000;
        box-shadow: 4px 4px 0px 0px #000000;
        min-height: 40px;
        background-color: #f87171;
        color: #000000;
        width: 280px;
    }
    .card-title { font-size: 8px; text-transform: uppercase; font-weight: 900; margin-bottom: 4px; }
    .card-value { font-size: 16px; font-weight: 900; text-transform: uppercase; }
@endsection

@section('content')
    <table class="header-table">
        <tr>
            <td>
                <h1 class="brand-title">Laporan Pengeluaran Operasional</h1>
                <p class="brand-subtitle">{{ $appName }} - Rekapitulasi Arus Kas Keluar</p>
            </td>
            <td class="header-right">
                <div><strong>Kategori:</strong> <span class="filter-badge">{{ $filterKategori }}</span></div>
                <div style="margin-top: 3px;"><strong>Rentang Tanggal:</strong> <span class="filter-badge">{{ $filterTanggal }}</span></div>
            </td>
        </tr>
    </table>

    <div class="summary-container">
        <div class="card-pengeluaran">
            <div class="card-title">Total Pengeluaran Terpilih</div>
            <div class="card-value">Rp {{ number_format($totalPengeluaran ?? 0, 0, ',', '.') }}</div>
        </div>
    </div>

    <div class="section-title">Rincian Pengeluaran Operasional</div>
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
                    <td colspan="4" style="text-align: right;">Total Pengeluaran:</td>
                    <td class="amount" style="color: #ef4444;">Rp {{ number_format($totalPengeluaran ?? 0, 0, ',', '.') }}</td>
                    <td></td>
                </tr>
            @endif
        </tbody>
    </table>
@endsection

@section('footer-note')
    Laporan ini dibuat otomatis oleh sistem manajemen {{ $appName }}.<br>
    Simpan cetakan ini sebagai laporan pertanggungjawaban operasional yang sah.
@endsection
