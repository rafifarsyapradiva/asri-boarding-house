<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kamar;
use App\Models\Fasilitas;
use App\Http\Requests\StoreKamarRequest;
use App\Http\Requests\UpdateKamarRequest;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;

class KamarController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = Kamar::withTrashed()
            ->with(['fasilitas', 'penyewaAktif.user'])
            ->filterStatus($request->input('status'));

        if ($request->filled('tipe')) {
            $query->where('tipe', $request->input('tipe'));
        }

        if ($request->filled('lantai')) {
            $query->where('lantai', $request->input('lantai'));
        }

        $kamar = $query->paginate(12)->withQueryString();
        $allFloors = Kamar::withoutTrashed()->pluck('lantai')->unique()->sort();

        // Optimized single-pass aggregated query for stats header
        $statsData = Kamar::withTrashed()
            ->selectRaw("
                COUNT(CASE WHEN deleted_at IS NULL THEN 1 END) as total,
                COUNT(CASE WHEN deleted_at IS NULL AND status = 'tersedia' THEN 1 END) as tersedia,
                COUNT(CASE WHEN deleted_at IS NULL AND status = 'terisi' THEN 1 END) as terisi,
                COUNT(CASE WHEN deleted_at IS NULL AND status = 'maintenance' THEN 1 END) as maintenance,
                COUNT(CASE WHEN deleted_at IS NOT NULL THEN 1 END) as deleted
            ")
            ->first();

        $stats = [
            'total'       => (int) ($statsData->total ?? 0),
            'tersedia'    => (int) ($statsData->tersedia ?? 0),
            'terisi'      => (int) ($statsData->terisi ?? 0),
            'maintenance' => (int) ($statsData->maintenance ?? 0),
            'deleted'     => (int) ($statsData->deleted ?? 0),
        ];

        $occupancyRate = $stats['total'] > 0 ? round(($stats['terisi'] / $stats['total']) * 100, 1) : 0;

        $gridQuery = Kamar::withTrashed()
            ->with(['penyewaAktif.user'])
            ->filterStatus($request->input('status'));

        if ($request->filled('tipe')) {
            $gridQuery->where('tipe', $request->input('tipe'));
        }

        if ($request->filled('lantai')) {
            $gridQuery->where('lantai', $request->input('lantai'));
        }

        $allKamarGrouped = $gridQuery
            ->orderBy('lantai')
            ->orderBy('nomor_kamar')
            ->get()
            ->groupBy('lantai');

        return view('admin.kamar.index', compact('kamar', 'allFloors', 'stats', 'occupancyRate', 'allKamarGrouped'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $fasilitas = Fasilitas::all();
        return view('admin.kamar.create', compact('fasilitas'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreKamarRequest $request): RedirectResponse
    {
        $data = $request->validated();
        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('kamar', 'public');
        }

        DB::transaction(function () use ($data, $request) {
            $k = Kamar::create($data);
            if ($request->has('fasilitas')) {
                $k->fasilitas()->sync($request->input('fasilitas'));
            }
        });

        Cache::forget('fasilitas_all');

        return redirect()->route('admin.kamar.index')->with('success', 'Kamar berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Kamar $kamar): View
    {
        return view('admin.kamar.show', compact('kamar'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Kamar $kamar): View
    {
        $fasilitas = Fasilitas::all();
        return view('admin.kamar.edit', compact('kamar', 'fasilitas'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateKamarRequest $request, Kamar $kamar): RedirectResponse
    {
        $data = $request->validated();
        if ($request->hasFile('foto')) {
            if ($kamar->foto && Storage::disk('public')->exists($kamar->foto)) {
                Storage::disk('public')->delete($kamar->foto);
            }
            $data['foto'] = $request->file('foto')->store('kamar', 'public');
        }

        DB::transaction(function () use ($kamar, $data, $request) {
            $kamar->update($data);
            if ($request->has('fasilitas')) {
                $kamar->fasilitas()->sync($request->input('fasilitas'));
            } else {
                $kamar->fasilitas()->detach();
            }
        });

        Cache::forget('fasilitas_all');

        return redirect()->route('admin.kamar.index')->with('success', 'Kamar berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Kamar $kamar): RedirectResponse
    {
        if ($kamar->penyewaAktif()->exists() || $kamar->reservasiAktif()->exists()) {
            return back()->with('error', 'Kamar tidak dapat dihapus karena memiliki riwayat reservasi atau data penyewa.');
        }

        if ($kamar->foto && Storage::disk('public')->exists($kamar->foto)) {
            Storage::disk('public')->delete($kamar->foto);
        }

        DB::transaction(function () use ($kamar) {
            $kamar->fasilitas()->detach();
            $kamar->update([
                'nomor_kamar' => $kamar->nomor_kamar . '_deleted_' . time(),
            ]);
            $kamar->delete();
        });

        Cache::forget('fasilitas_all');

        return redirect()->route('admin.kamar.index')->with('success', 'Kamar berhasil dihapus.');
    }

    /**
     * Restore the specified resource from storage.
     */
    public function restore(int $id): RedirectResponse
    {
        $kamar = Kamar::onlyTrashed()->findOrFail($id);

        $nomor = $kamar->nomor_kamar;
        $originalNomor = $nomor;
        if (str_contains($nomor, '_deleted_')) {
            $parts = explode('_deleted_', $nomor);
            $originalNomor = $parts[0];
        }

        if (Kamar::where('nomor_kamar', $originalNomor)->exists()) {
            return back()->with('error', 'Gagal memulihkan kamar. Nomor kamar ' . $originalNomor . ' sudah digunakan oleh kamar aktif lain.');
        }

        $kamar->nomor_kamar = $originalNomor;
        $kamar->restore();

        Cache::forget('fasilitas_all');

        return redirect()->route('admin.kamar.index')->with('success', 'Kamar berhasil dipulihkan.');
    }

    /**
     * Update status kamar secara manual oleh admin.
     */
    public function updateStatus(Request $request, Kamar $kamar): RedirectResponse
    {
        $request->validate([
            'status' => ['required', 'in:tersedia,terisi,maintenance'],
        ]);

        $newStatus = $request->input('status');
        if (in_array($newStatus, ['tersedia', 'maintenance'])) {
            if ($kamar->penyewaAktif()->exists()) {
                return back()->with('error', 'Gagal memperbarui status kamar. Kamar ini masih dihuni oleh penyewa aktif.');
            }
        }

        $kamar->update([
            'status' => $request->status,
        ]);

        return redirect()->route('admin.kamar.index')->with('success', 'Status kamar berhasil diperbarui.');
    }
}
