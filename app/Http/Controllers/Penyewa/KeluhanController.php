<?php

namespace App\Http\Controllers\Penyewa;

use App\Http\Controllers\Controller;
use App\Http\Requests\Penyewa\StoreKeluhanRequest;
use App\Models\Keluhan;
use App\Events\KeluhanDibuat;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class KeluhanController extends Controller
{
    /**
     * Display a listing of complaints for the current tenant.
     */
    public function index(Request $request): View
    {
        $status = $request->query('status');
        $search = $request->query('search');

        $penyewaId = Auth::user()->penyewa?->id;
        
        if ($penyewaId) {
            $query = Keluhan::where('penyewa_id', $penyewaId)->orderBy('created_at', 'desc');

            if ($status && in_array($status, ['pending', 'diproses', 'selesai'])) {
                $query->where('status', $status);
            }

            if ($request->filled('search')) {
                $query->where(function ($q) use ($search) {
                    $q->where('judul', 'like', "%{$search}%")
                      ->orWhere('deskripsi', 'like', "%{$search}%");
                });
            }

            $keluhan = $query->paginate(10)->withQueryString();
        } else {
            $keluhan = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10);
        }

        return view('penyewa.keluhan.index', compact('keluhan', 'status'));
    }

    public function create(): View|RedirectResponse
    {
        $penyewa = Auth::user()->penyewa;
        if (!$penyewa || $penyewa->status !== 'aktif') {
            return redirect()->route('penyewa.keluhan.index')
                ->with('error', 'Akses ditolak. Hanya penyewa aktif yang dapat membuat keluhan.');
        }

        return view('penyewa.keluhan.create');
    }

    /**
     * Store a newly created complaint in storage.
     */
    public function store(StoreKeluhanRequest $request): RedirectResponse
    {
        $penyewa = $request->user()->penyewa;
        $validated = $request->validated();

        $fotoPath = null;
        if ($request->hasFile('foto_bukti')) {
            $fotoPath = $request->file('foto_bukti')->store('keluhan', 'public');
        }

        try {
            $keluhan = DB::transaction(function () use ($penyewa, $validated, $fotoPath) {
                return Keluhan::create([
                    'penyewa_id' => $penyewa->id,
                    'judul' => $validated['judul'],
                    'kategori' => $validated['kategori'],
                    'deskripsi' => $validated['deskripsi'],
                    'foto_bukti' => $fotoPath,
                    'status' => 'pending',
                ]);
            });

            // Trigger event to send WhatsApp notification to admin
            event(new KeluhanDibuat($keluhan));

            return redirect()->route('penyewa.keluhan.index')
                ->with('success', 'Keluhan Anda berhasil dikirim.');
        } catch (\Throwable $e) {
            // Hapus file bukti jika insert database gagal untuk mencegah dangling file
            if ($fotoPath && Storage::disk('public')->exists($fotoPath)) {
                Storage::disk('public')->delete($fotoPath);
            }
            throw $e;
        }
    }

    /**
     * Display the specified complaint.
     */
    public function show(Keluhan $keluhan): View
    {
        // Refaktor Otorisasi: Menggunakan KeluhanPolicy
        $this->authorize('view', $keluhan);

        return view('penyewa.keluhan.show', compact('keluhan'));
    }
}
