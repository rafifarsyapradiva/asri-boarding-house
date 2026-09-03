@extends('pdf.layouts.master')

@section('title', 'Laporan Data Penyewa Kost')
@section('page-size', 'a4 landscape')

@section('additional-styles')
    .summary-container { margin-bottom: 20px; }
    .card-penyewa {
        display: inline-block;
        padding: 10px 15px;
        border: 3px solid #000000;
        box-shadow: 4px 4px 0px 0px #000000;
        min-height: 40px;
        background-color: #38bdf8;
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
                <h1 class="brand-title">Laporan Data Penyewa</h1>
                <p class="brand-subtitle">{{ $appName }} - Rekapitulasi Informasi Penyewa Kost</p>
            </td>
            <td class="header-right">
                <div><strong>Tanggal Cetak:</strong> <span class="filter-badge">{{ now()->translatedFormat('d M Y H:i:s') }}</span></div>
            </td>
        </tr>
    </table>

    <div class="summary-container">
        <div class="card-penyewa">
            <div class="card-title">Total Akumulasi Uang Jaminan</div>
            <div class="card-value">Rp {{ number_format($totalDeposit ?? 0, 0, ',', '.') }}</div>
        </div>
    </div>

    <div class="section-title">Daftar Rincian Penyewa Kost</div>
    <table class="report-table">
        <thead>
            <tr>
                <th style="width: 3%;">No</th>
                <th style="width: 15%;">Nama Penyewa</th>
                <th style="width: 8%;">No. Kamar</th>
                <th style="width: 10%;">Tipe Kamar</th>
                <th style="width: 12%;">NIK</th>
                <th style="width: 10%;">No. HP</th>
                <th style="width: 8%;">Tipe Sewa</th>
                <th style="width: 8%;">Durasi</th>
                <th style="width: 10%;">Tanggal Masuk</th>
                <th style="width: 6%;">Status</th>
                <th style="text-align: right; width: 10%;">Nominal Deposit</th>
            </tr>
        </thead>
        <tbody>
            @forelse($penyewaList as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $item->user?->nama ?? '-' }}</strong><br>
                        <span style="font-size: 7px; color: #64748b;">{{ $item->user?->email ?? '-' }}</span>
                    </td>
                    <td>{{ $item->kamar?->nomor_kamar ? 'Kamar ' . $item->kamar->nomor_kamar : '-' }}</td>
                    <td style="text-transform: capitalize;">{{ $item->kamar?->tipe ?? '-' }}</td>
                    <td>{{ $item->nik ?? '-' }}</td>
                    <td>{{ $item->user?->no_hp ?? '-' }}</td>
                    <td style="text-transform: capitalize;">{{ $item->tipe_sewa ?? '-' }}</td>
                    <td>{{ $item->durasi_formatted ?? '-' }}</td>
                    <td>{{ $item->tanggal_masuk?->translatedFormat('d M Y') ?? '-' }}</td>
                    <td>
                        @if(($item->status ?? '') === 'aktif')
                            <span style="font-weight: bold; color: #10b981;">Aktif</span>
                        @else
                            <span style="font-weight: bold; color: #ef4444;">Nonaktif</span>
                        @endif
                    </td>
                    <td class="amount">Rp {{ number_format($item->deposit ?? 0, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" style="text-align: center; color: #64748b; padding: 15px;">Tidak ada data penyewa yang sesuai filter.</td>
                </tr>
            @endforelse
            @if($penyewaList->isNotEmpty())
                <tr class="total-row">
                    <td colspan="10" style="text-align: right;">Total Akumulasi Uang Jaminan:</td>
                    <td class="amount">Rp {{ number_format($totalDeposit ?? 0, 0, ',', '.') }}</td>
                </tr>
            @endif
        </tbody>
    </table>
@endsection

@section('footer-note')
    Laporan ini dibuat secara otomatis oleh sistem manajemen {{ $appName }}.<br>
    Simpan cetakan ini sebagai arsip data keaktifan penyewa kost yang valid.<br>
    <span style="font-weight: normal; text-transform: none; color: #475569;">* Catatan: Mulai periode berjalan, denda keterlambatan pembayaran tagihan diberlakukan flat {{ $dendaPersen ?? 0 }}% dari nominal sewa per pergantian bulan kalender.</span>
@endsection