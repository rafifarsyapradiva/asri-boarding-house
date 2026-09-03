<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\KonfirmasiReservasiRequest;
use App\Models\Reservasi;
use App\Services\TransisiPenyewaService;
use App\Events\ReservasiDikonfirmasi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ReservasiController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(protected TransisiPenyewaService $transisiPenyewaService)
    {
    }

    /**
     * Display a listing of reservations.
     */
    public function index(Request $request): View
    {
        $query = Reservasi::withTrashed()
            ->with(['user', 'kamar'])
            ->filterStatus($request->input('status'));

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('order_id', 'like', '%' . $search . '%')
                  ->orWhereHas('user', function ($qu) use ($search) {
                      $qu->withTrashed()->where('nama', 'like', '%' . $search . '%');
                  })
                  ->orWhereHas('kamar', function ($qk) use ($search) {
                      $qk->withTrashed()->where('nomor_kamar', 'like', '%' . $search . '%');
                  });
            });
        }

        $reservasi = $query->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.reservasi.index', compact('reservasi'));
    }

    /**
     * Display the specified reservation.
     */
    public function show(Reservasi $reservasi): View
    {
        // Load relationships including chat messages
        $reservasi->load(['user', 'kamar', 'chatMessages.sender']);

        return view('admin.reservasi.show', compact('reservasi'));
    }

    /**
     * Confirm the specified reservation and transition the user to a tenant.
     */
    public function konfirmasi(KonfirmasiReservasiRequest $request, Reservasi $reservasi): RedirectResponse
    {
        // Pengecekan Status Transaksi
        if (!in_array($reservasi->status, ['dp', 'lunas'])) {
            return redirect()->back()->with('error', 'Status reservasi tidak valid untuk dikonfirmasi.');
        }

        // Bungkus proses ke dalam DB::transaction()
        DB::transaction(function () use ($request, $reservasi) {
            // [ATURAN KAKU LOCK ORDER]: Kunci baris Kamar terlebih dahulu untuk menghindari kebuntuan (circular wait)
            // Hal ini dikarenakan ReservasiService::buatReservasi mengunci kamars dulu baru kemudian reservasis.
            \App\Models\Kamar::where('id', $reservasi->kamar_id)
                ->lockForUpdate()
                ->first();

            // Update catatan_admin
            $reservasi->update([
                'catatan_admin' => $request->catatan_admin,
            ]);

            // Panggil fungsi layanan inti
            $this->transisiPenyewaService->transisi($reservasi, Auth::id(), $request->validated());
        });

        return redirect()->route('admin.reservasi.index')->with('success', 'Reservasi berhasil dikonfirmasi dan penyewa telah aktif.');
    }

    /**
     * Cancel the specified reservation by admin.
     */
    public function batal(Reservasi $reservasi): RedirectResponse
    {
        if (!in_array($reservasi->status, ['pending', 'dp', 'lunas'])) {
            return redirect()->back()->with('error', 'Status reservasi tidak valid untuk dibatalkan.');
        }

        $reservasi->update(['status' => 'batal']);

        return redirect()->route('admin.reservasi.index')->with('success', 'Reservasi berhasil dibatalkan.');
    }

    /**
     * Remove the specified reservation from storage (soft delete).
     */
    public function destroy(Reservasi $reservasi): RedirectResponse
    {
        \Illuminate\Support\Facades\DB::transaction(function () use ($reservasi) {
            if ($reservasi->status === 'batal') {
                // Hapus chat messages secara cascade
                $reservasi->chatMessages()->delete();
                // Hapus permanen dari database
                $reservasi->forceDelete();
            } else {
                if ($reservasi->order_id) {
                    $reservasi->update([
                        'order_id' => $reservasi->order_id . '_deleted_' . time(),
                    ]);
                }
                $reservasi->delete();
            }
        });

        return redirect()->route('admin.reservasi.index')->with('success', 'Reservasi berhasil dihapus.');
    }

    /**
     * Restore the specified resource from storage.
     */
    public function restore(int $id): RedirectResponse
    {
        $reservasi = Reservasi::onlyTrashed()->findOrFail($id);

        $orderId = $reservasi->order_id;
        $originalOrderId = $orderId;
        if ($orderId && str_contains($orderId, '_deleted_')) {
            $parts = explode('_deleted_', $orderId);
            $originalOrderId = $parts[0];
        }
        
        // Cek tabrakan unik key
        if ($originalOrderId && Reservasi::where('order_id', $originalOrderId)->exists()) {
            return back()->with('error', 'Gagal memulihkan reservasi. Order ID ' . $originalOrderId . ' sudah digunakan.');
        }
        $reservasi->order_id = $originalOrderId;

        $reservasi->restore();
        return redirect()->route('admin.reservasi.index')->with('success', 'Reservasi berhasil dipulihkan.');
    }
}
