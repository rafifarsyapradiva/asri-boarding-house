<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Keluhan;
use App\Events\KeluhanDitanggapi;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Carbon\Carbon;

class KeluhanController extends Controller
{
    /**
     * Display a listing of all complaints with status filter.
     */
    public function index(Request $request): View
    {
        $status = $request->query('status');
        $search = $request->query('search');

        $query = Keluhan::with(['penyewa.user', 'penyewa.kamar'])
            ->orderBy('created_at', 'desc');

        if ($status && in_array($status, ['pending', 'diproses', 'selesai'])) {
            $query->where('status', $status);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhereHas('penyewa.user', function ($qu) use ($search) {
                      $qu->where('nama', 'like', "%{$search}%");
                  })
                  ->orWhereHas('penyewa.kamar', function ($qk) use ($search) {
                      $qk->where('nomor_kamar', 'like', "%{$search}%");
                  });
            });
        }

        $keluhan = $query->paginate(10)->withQueryString();

        return view('admin.keluhan.index', compact('keluhan', 'status'));
    }

    /**
     * Display the specified complaint details.
     */
    public function show(Keluhan $keluhan): View
    {
        $keluhan->load(['penyewa.user', 'penyewa.kamar']);
        return view('admin.keluhan.show', compact('keluhan'));
    }

    /**
     * Update the specified complaint's status and response.
     */
    public function update(Request $request, Keluhan $keluhan): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:diproses,selesai'],
            'tanggapan_admin' => ['required', 'string'],
        ], [
            'status.required' => 'Status wajib dipilih.',
            'status.in' => 'Status tidak valid.',
            'tanggapan_admin.required' => 'Tanggapan wajib diisi.',
        ]);

        $data = [
            'status' => $validated['status'],
            'tanggapan_admin' => $validated['tanggapan_admin'],
        ];

        // Automatically set tanggal_selesai if status is updated to selesai, otherwise nullify it
        if ($validated['status'] === 'selesai') {
            $data['tanggal_selesai'] = Carbon::now();
        } else {
            $data['tanggal_selesai'] = null;
        }

        $keluhan->update($data);

        // Trigger event to notify the tenant via WhatsApp
        event(new KeluhanDitanggapi($keluhan));

        return redirect()->route('admin.keluhan.show', $keluhan)
            ->with('success', 'Tanggapan keluhan berhasil diperbarui.');
    }
}
