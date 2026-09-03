<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LogNotifikasi;
use App\Models\Penyewa;
use App\Models\Pengumuman;
use App\Models\Tagihan;
use App\Models\Pembayaran;
use App\Services\NotifikasiService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class NotifikasiController extends Controller
{
    public function __construct(protected NotifikasiService $notifikasiService)
    {
    }

    /**
     * Tampilkan halaman utama Manajemen Notifikasi & Pengumuman
     */
    public function index(Request $request): View
    {
        // 1. Ambil data log audit notifikasi otomatis & manual
        $logs = LogNotifikasi::with(['penyewa.user', 'penyewa.kamar'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->input('search');
                $query->whereHas('penyewa.user', function ($q) use ($search) {
                    $q->where('nama', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('channel'), function ($query) use ($request) {
                $query->where('channel', $request->input('channel'));
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->input('status'));
            })
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        // 2. Ambil data pengumuman aktif untuk portal penyewa
        $pengumumanList = Pengumuman::latest()->get();

        // 3. Data penyewa aktif untuk target pengiriman manual
        $penyewaAktif = Penyewa::where('status', 'aktif')->with(['user', 'kamar'])->get();

        return view('admin.notifikasi.index', compact('logs', 'pengumumanList', 'penyewaAktif'));
    }

    /**
     * Kirim broadcast pesan manual ke WA, Email, dan/atau posting ke Web
     */
    public function broadcast(Request $request): RedirectResponse
    {
        $request->validate([
            'judul' => ['required_if:post_to_web,1', 'nullable', 'string', 'max:150'],
            'target' => ['required', 'string'], // 'all' atau ID penyewa spesifik
            'pesan' => ['required', 'string', 'min:5'],
        ], [
            'judul.required_if' => 'Judul wajib diisi jika Anda memposting ke web portal.',
            'target.required' => 'Target penerima wajib dipilih.',
            'pesan.required' => 'Isi pesan pengumuman wajib diisi.',
            'pesan.min' => 'Pesan minimal terdiri dari 5 karakter.',
        ]);

        $pesan = $request->input('pesan');
        $target = $request->input('target');
        $judul = $request->input('judul');

        $sendWa = $request->boolean('send_wa');
        $sendEmail = $request->boolean('send_email');
        $postToWeb = $request->boolean('post_to_web');

        if (!$sendWa && !$sendEmail && !$postToWeb) {
            return redirect()->back()->with('error', 'Silakan pilih minimal satu saluran pengiriman (WhatsApp, Email, atau Posting Web).');
        }

        // Tentukan daftar penyewa tujuan
        if ($target === 'all') {
            $penyewas = Penyewa::where('status', 'aktif')->get();
        } else {
            $penyewas = Penyewa::where('id', $target)->get();
        }

        // 1. Posting ke Web (Notifikasi Sistem internal portal penyewa)
        if ($postToWeb) {
            Pengumuman::create([
                'judul' => $judul ?? 'Pengumuman Pengelola',
                'isi' => $pesan,
                'is_active' => true,
            ]);
        }

        // 2. Pengiriman Broadcast luar (WA / Email)
        $totalSendCount = 0;
        $delay = 0;

        if (($sendWa || $sendEmail) && !$penyewas->isEmpty()) {
            foreach ($penyewas as $penyewa) {
                if ($sendWa) {
                    $totalSendCount++;
                    \App\Jobs\KirimNotifikasiKustomJob::dispatch($penyewa, 'whatsapp', $pesan)
                        ->delay(now()->addSeconds($delay));
                    $delay += 2; // jeda 2 detik per pengiriman WhatsApp
                }
                if ($sendEmail) {
                    $totalSendCount++;
                    \App\Jobs\KirimNotifikasiKustomJob::dispatch($penyewa, 'email', $pesan, $judul ?? 'Pengumuman Asri Boarding House')
                        ->delay(now()->addSeconds($delay));
                    $delay += 2; // jeda 2 detik per pengiriman Email
                }
            }
        }

        $msg = "Aksi broadcast berhasil diproses!";
        if ($postToWeb) {
            $msg .= " Pengumuman telah dipublikasikan di dashboard penyewa.";
        }
        if ($totalSendCount > 0) {
            $msg .= " Sebanyak {$totalSendCount} notifikasi WA/Email telah dijadwalkan ke dalam antrean background system (proses pengiriman bertahap dengan jeda aman).";
        }

        return redirect()->route('admin.notifikasi.index')->with('success', $msg);
    }

    /**
     * Kirim ulang notifikasi yang gagal
     */
    public function retry(LogNotifikasi $log): RedirectResponse
    {
        $penyewa = $log->penyewa;
        if (!$penyewa) {
            return redirect()->back()->with('error', 'Data penyewa tidak ditemukan untuk pengiriman ulang.');
        }

        $pesan = $log->pesan ?? '';

        // Jika pesan kosong (biasanya untuk notifikasi otomatis yang templatenya dinamis), coba generate ulang sesuai jenis eventnya
        if (empty($pesan)) {
            if ($log->tagihan_id) {
                $tagihan = Tagihan::find($log->tagihan_id);
                if ($tagihan) {
                    if ($log->event === 'tagihan_baru') {
                        $pesan = $this->notifikasiService->templateTagihanBaru($tagihan, $penyewa);
                    } elseif ($log->event === 'denda_dikenakan') {
                        $pesan = $this->notifikasiService->templateDenda($tagihan, $penyewa);
                    } elseif ($log->event === 'reminder_penyewa') {
                        $pesan = $this->notifikasiService->templateReminderJatuhTempo($tagihan, $penyewa);
                    } elseif ($log->event === 'notifikasi_wali') {
                        $pesan = $this->notifikasiService->templateNotifikasiWali($tagihan, $penyewa);
                    } elseif ($log->event === 'pembayaran_sukses') {
                        $pembayaran = Pembayaran::where('tagihan_id', $tagihan->id)->latest()->first();
                        if ($pembayaran) {
                            $pesan = $this->notifikasiService->templatePembayaranBerhasil($pembayaran);
                        }
                    }
                }
            } elseif ($log->event === 'welcome_penyewa') {
                $pesan = $this->notifikasiService->templateWelcomePenyewa($penyewa);
            }
        }

        if (empty($pesan)) {
            return redirect()->back()->with('error', 'Template pesan gagal digenerate otomatis.');
        }

        $res = $this->notifikasiService->kirimUlangNotifikasi($log, $pesan);

        if ($res) {
            return redirect()->route('admin.notifikasi.index')->with('success', 'Pesan berhasil dikirim ulang!');
        }

        return redirect()->route('admin.notifikasi.index')->with('error', 'Pengiriman ulang masih gagal. Silakan periksa log.');
    }

    /**
     * Hapus postingan pengumuman portal penyewa
     */
    public function destroyPengumuman(Pengumuman $pengumuman): RedirectResponse
    {
        $pengumuman->delete();
        return redirect()->route('admin.notifikasi.index')->with('success', 'Pengumuman web berhasil dihapus.');
    }
}
