<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NotifikasiKhusus;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class NotifikasiKhususController extends Controller
{
    /**
     * Tampilkan daftar notifikasi khusus dengan filter pencarian dan sumber
     */
    public function index(Request $request): View
    {
        $notifikasi = NotifikasiKhusus::with('user')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->input('search');
                $query->where(function ($q) use ($search) {
                    $q->where('deskripsi', 'like', "%{$search}%")
                      ->orWhere('tipe_aktivitas', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('sumber') && in_array($request->input('sumber'), ['reservasi', 'tagihan', 'admin']), function ($query) use ($request) {
                $query->where('sumber', $request->input('sumber'));
            })
            ->when($request->filled('tanggal_mulai'), function ($query) use ($request) {
                $query->whereDate('created_at', '>=', $request->input('tanggal_mulai'));
            })
            ->when($request->filled('tanggal_selesai'), function ($query) use ($request) {
                $query->whereDate('created_at', '<=', $request->input('tanggal_selesai'));
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $counts = [
            'semua' => NotifikasiKhusus::count(),
            'reservasi' => NotifikasiKhusus::where('sumber', 'reservasi')->count(),
            'tagihan' => NotifikasiKhusus::where('sumber', 'tagihan')->count(),
            'admin' => NotifikasiKhusus::where('sumber', 'admin')->count(),
        ];

        return view('admin.notifikasi-khusus.index', compact('notifikasi', 'counts'));
    }

    /**
     * Hapus satu entri notifikasi khusus
     */
    public function destroy(NotifikasiKhusus $notifikasi): RedirectResponse
    {
        $notifikasi->delete();

        return redirect()
            ->route('admin.notifikasi-khusus.index')
            ->with('success', 'Entri log notifikasi khusus berhasil dihapus.');
    }

    /**
     * Hapus semua log notifikasi khusus
     */
    public function clearAll(): RedirectResponse
    {
        NotifikasiKhusus::truncate();

        return redirect()
            ->route('admin.notifikasi-khusus.index')
            ->with('success', 'Seluruh log notifikasi khusus berhasil dibersihkan.');
    }
}
