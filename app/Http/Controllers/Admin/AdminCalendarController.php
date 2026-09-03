<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservasi;
use App\Models\Tagihan;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class AdminCalendarController extends Controller
{
    /**
     * Tampilkan halaman utama kalender kontrol visual.
     */
    public function index(): View
    {
        return view('admin.calendar.index');
    }

    /**
     * Ambil data reservasi dan tagihan untuk rentang tanggal tertentu (JSON).
     */
    public function getEvents(Request $request): JsonResponse
    {
        $startDateStr = $request->input('start_date');
        $endDateStr = $request->input('end_date');

        try {
            $startDate = $startDateStr ? Carbon::parse($startDateStr)->startOfDay() : Carbon::now()->startOfMonth()->startOfDay();
            $endDate = $endDateStr ? Carbon::parse($endDateStr)->endOfDay() : Carbon::now()->endOfMonth()->endOfDay();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Format tanggal tidak valid.'], 400);
        }

        // 1. Ambil Reservasi (Check-in, Check-out, dan rencana Survei)
        // Kueri keyword sensitif untuk survei menggunakan LOWER
        $reservasis = Reservasi::with(['user', 'kamar'])
            ->where('status', '!=', 'batal')
            ->where('tanggal_mulai', '<=', $endDate->toDateString())
            ->where('tanggal_selesai', '>=', $startDate->toDateString())
            ->get();

        $events = [];

        foreach ($reservasis as $r) {
            // Check if it's a survey: status pending ATAU catatan mengandung kata "surve"
            $isSurvey = ($r->status === 'pending' || (is_string($r->catatan_user) && stripos($r->catatan_user, 'surve') !== false));
            
            if ($isSurvey) {
                // Survei dipetakan ke tanggal_mulai
                $eventDate = $r->tanggal_mulai ? $r->tanggal_mulai->toDateString() : null;
                if ($eventDate && $eventDate >= $startDate->toDateString() && $eventDate <= $endDate->toDateString()) {
                    $kamarNo = $r->kamar ? $r->kamar->nomor_kamar : '-';
                    $userName = $r->user ? $r->user->nama : 'Tamu';
                    $events[] = [
                        'id' => "reservasi-{$r->id}-survey",
                        'title' => "📍 Survei Kamar {$kamarNo} - {$userName}",
                        'date' => $eventDate,
                        'type' => 'survey',
                        'color' => \App\Services\CalendarStyleHelper::getColorClasses('survey'),
                        'status' => 'Pending / Rencana Survei',
                        'description' => "Calon penyewa {$userName} menjadwalkan survei untuk Kamar {$kamarNo}. Catatan: " . ($r->catatan_user ?? '-'),
                        'url' => route('admin.reservasi.show', $r->id),
                        'metadata' => [
                            'id' => $r->id,
                            'penyewa_nama' => $userName,
                            'kamar_nomor' => $kamarNo,
                            'status_reservasi' => $r->status,
                            'url' => route('admin.reservasi.show', $r->id),
                        ]
                    ];
                }
            } else {
                // Check-in (tanggal_mulai)
                $checkInDate = $r->tanggal_mulai ? $r->tanggal_mulai->toDateString() : null;
                if ($checkInDate && $checkInDate >= $startDate->toDateString() && $checkInDate <= $endDate->toDateString()) {
                    $kamarNo = $r->kamar ? $r->kamar->nomor_kamar : '-';
                    $userName = $r->user ? $r->user->nama : 'Penyewa';
                    $events[] = [
                        'id' => "reservasi-{$r->id}-checkin",
                        'title' => "🔑 Check-in Kamar {$kamarNo} - {$userName}",
                        'date' => $checkInDate,
                        'type' => 'check-in',
                        'color' => \App\Services\CalendarStyleHelper::getColorClasses('check-in'),
                        'status' => ucfirst($r->status),
                        'description' => "Jadwal masuk penyewa {$userName} untuk Kamar {$kamarNo}. Status pembayaran: " . ucfirst($r->status),
                        'url' => route('admin.reservasi.show', $r->id),
                        'metadata' => [
                            'id' => $r->id,
                            'penyewa_nama' => $userName,
                            'kamar_nomor' => $kamarNo,
                            'status_reservasi' => $r->status,
                            'url' => route('admin.reservasi.show', $r->id),
                        ]
                    ];
                }

                // Check-out (tanggal_selesai)
                $checkOutDate = $r->tanggal_selesai ? $r->tanggal_selesai->toDateString() : null;
                if ($checkOutDate && $checkOutDate >= $startDate->toDateString() && $checkOutDate <= $endDate->toDateString()) {
                    $kamarNo = $r->kamar ? $r->kamar->nomor_kamar : '-';
                    $userName = $r->user ? $r->user->nama : 'Penyewa';
                    $events[] = [
                        'id' => "reservasi-{$r->id}-checkout",
                        'title' => "🚪 Check-out Kamar {$kamarNo} - {$userName}",
                        'date' => $checkOutDate,
                        'type' => 'check-out',
                        'color' => \App\Services\CalendarStyleHelper::getColorClasses('check-out'),
                        'status' => ucfirst($r->status),
                        'description' => "Jadwal keluar penyewa {$userName} dari Kamar {$kamarNo}.",
                        'url' => route('admin.reservasi.show', $r->id),
                        'metadata' => [
                            'id' => $r->id,
                            'penyewa_nama' => $userName,
                            'kamar_nomor' => $kamarNo,
                            'status_reservasi' => $r->status,
                            'url' => route('admin.reservasi.show', $r->id),
                        ]
                    ];
                }
            }
        }

        // 2. Ambil Tagihan (Jatuh Tempo Pembayaran & Denda Keterlambatan)
        $tagihans = Tagihan::with(['penyewa.user', 'penyewa.kamar'])
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('tanggal_jatuh_tempo', [$startDate->toDateString(), $endDate->toDateString()])
                    ->orWhereBetween('tanggal_tagihan', [$startDate->toDateString(), $endDate->toDateString()]);
            })
            ->get();

        foreach ($tagihans as $t) {
            $penyewa = $t->penyewa;
            $userName = ($penyewa && $penyewa->user) ? $penyewa->user->nama : 'Penyewa';
            $kamarNo = ($penyewa && $penyewa->kamar) ? $penyewa->kamar->nomor_kamar : '-';
            
            $dueDate = $t->tanggal_jatuh_tempo ? $t->tanggal_jatuh_tempo->toDateString() : null;
            if ($dueDate && $dueDate >= $startDate->toDateString() && $dueDate <= $endDate->toDateString()) {
                $status = $t->status;
                $lateMonths = $t->bulan_keterlambatan;
                $color = \App\Services\CalendarStyleHelper::getColorClasses('tagihan', $status, $lateMonths);

                $statusText = 'Pending';
                if ($status === 'lunas') {
                    $statusText = 'Lunas';
                } elseif ($status === 'terlambat' || ($lateMonths > 0 && $t->tanggal_jatuh_tempo->isPast())) {
                    if ($lateMonths == 1) {
                        $statusText = 'Terlambat 1 Bulan - Reminder Aktif';
                    } elseif ($lateMonths == 2) {
                        $statusText = 'Terlambat 2 Bulan - Notifikasi Wali via Fonnte';
                    } elseif ($lateMonths >= 3) {
                        $statusText = "Terlambat {$lateMonths} Bulan - Denda 5% Pokok Berjalan";
                    }
                }

                $events[] = [
                    'id' => "tagihan-{$t->id}-tempo",
                    'title' => "💸 Jatuh Tempo: Rp " . number_format($t->nominal_total, 0, ',', '.') . " - {$userName}",
                    'date' => $dueDate,
                    'type' => 'tagihan',
                    'color' => $color,
                    'status' => $statusText,
                    'description' => "Tagihan Periode {$t->periode_bulan}/{$t->periode_tahun} untuk Kamar {$kamarNo} atas nama {$userName}. Jatuh tempo pada {$dueDate}. Pokok: Rp " . number_format($t->nominal_pokok, 0, ',', '.') . ", Denda: Rp " . number_format($t->nominal_denda, 0, ',', '.'),
                    'url' => route('admin.tagihan.show', $t->id),
                    'metadata' => [
                        'id' => $t->id,
                        'penyewa_nama' => $userName,
                        'kamar_nomor' => $kamarNo,
                        'nominal_total' => $t->nominal_total,
                        'nominal_denda' => $t->nominal_denda,
                        'periode' => "{$t->periode_bulan}/{$t->periode_tahun}",
                        'status_tagihan' => $status,
                        'bulan_keterlambatan' => $t->bulan_keterlambatan,
                        'url' => route('admin.tagihan.show', $t->id),
                    ]
                ];
            }
        }

        // Sort events based on business rule priorities:
        // Priority 1 (Critical): Tagihan Overdue
        // Priority 2 (Medium): Check-In & Survei Lokasi
        // Priority 3 (Low): Check-Out
        // Priority 4: Normal Invoices
        // Default: 99
        usort($events, function($a, $b) {
            $dateCompare = strcmp($a['date'], $b['date']);
            if ($dateCompare !== 0) {
                return $dateCompare;
            }

            $weightA = 99;
            if ($a['type'] === 'tagihan') {
                if (isset($a['metadata']['status_tagihan']) && 
                    ($a['metadata']['status_tagihan'] === 'terlambat' || ($a['metadata']['bulan_keterlambatan'] ?? 0) > 0)) {
                    $weightA = 1;
                } else {
                    $weightA = 4;
                }
            } elseif ($a['type'] === 'check-in' || $a['type'] === 'survey') {
                $weightA = 2;
            } elseif ($a['type'] === 'check-out') {
                $weightA = 3;
            }

            $weightB = 99;
            if ($b['type'] === 'tagihan') {
                if (isset($b['metadata']['status_tagihan']) && 
                    ($b['metadata']['status_tagihan'] === 'terlambat' || ($b['metadata']['bulan_keterlambatan'] ?? 0) > 0)) {
                    $weightB = 1;
                } else {
                    $weightB = 4;
                }
            } elseif ($b['type'] === 'check-in' || $b['type'] === 'survey') {
                $weightB = 2;
            } elseif ($b['type'] === 'check-out') {
                $weightB = 3;
            }

            return $weightA <=> $weightB;
        });

        return response()->json($events);
    }
}
