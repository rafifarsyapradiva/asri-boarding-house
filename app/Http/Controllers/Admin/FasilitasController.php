<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Fasilitas;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class FasilitasController extends Controller
{
    /**
     * Display a listing of the resource (JSON).
     */
    public function index(Request $request)
    {
        if ($request->wantsJson()) {
            $fasilitas = Fasilitas::all();
            return response()->json($fasilitas);
        }
        
        $fasilitas = Fasilitas::paginate(10)->withQueryString();
        return view('admin.fasilitas.index', compact('fasilitas'));
    }

    /**
     * Store a newly created resource in storage (AJAX / Form).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:100', 'unique:fasilitas,nama'],
            'ikon' => ['required', 'string', 'in:wifi,snowflake,bolt,bath,shower,door-closed,desktop,bed'],
            'deskripsi' => ['required', 'string'],
        ], [
            'nama.required' => 'Nama fasilitas wajib diisi.',
            'nama.unique' => 'Nama fasilitas sudah terdaftar.',
            'nama.max' => 'Nama fasilitas maksimal 100 karakter.',
            'ikon.required' => 'Ikon wajib dipilih.',
            'ikon.in' => 'Ikon yang dipilih tidak valid.',
            'deskripsi.required' => 'Deskripsi wajib diisi.',
        ]);

        $validated['is_active'] = $request->has('is_active') ? $request->boolean('is_active') : false;

        $fasilitas = Fasilitas::create($validated);

        // Invalidate cache
        Cache::forget('fasilitas_all');
        Cache::forget('fasilitas_aktif_landing');

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Fasilitas berhasil ditambahkan!',
                'data' => $fasilitas
            ]);
        }

        return redirect()->back()->with('success', 'Fasilitas berhasil ditambahkan!');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Resolve model instance safely handling both plural/singular parameter bindings
        $fasilitas = Fasilitas::find($id);
        if (!$fasilitas) {
            $routeParam = $request->route('fasilita') ?? $request->route('fasilitas');
            $fasilitas = $routeParam instanceof Fasilitas ? $routeParam : Fasilitas::findOrFail($routeParam);
        }
        
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:100', 'unique:fasilitas,nama,' . $fasilitas->id],
            'ikon' => ['required', 'string', 'in:wifi,snowflake,bolt,bath,shower,door-closed,desktop,bed'],
            'deskripsi' => ['required', 'string'],
        ], [
            'nama.required' => 'Nama fasilitas wajib diisi.',
            'nama.unique' => 'Nama fasilitas sudah terdaftar.',
            'nama.max' => 'Nama fasilitas maksimal 100 karakter.',
            'ikon.required' => 'Ikon wajib dipilih.',
            'ikon.in' => 'Ikon yang dipilih tidak valid.',
            'deskripsi.required' => 'Deskripsi wajib diisi.',
        ]);

        $validated['is_active'] = $request->has('is_active') ? $request->boolean('is_active') : false;

        $fasilitas->update($validated);

        // Invalidate cache
        Cache::forget('fasilitas_all');
        Cache::forget('fasilitas_aktif_landing');

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Fasilitas berhasil diperbarui!',
                'data' => $fasilitas
            ]);
        }

        return redirect()->back()->with('success', 'Fasilitas berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        $fasilitas = Fasilitas::find($id);
        if (!$fasilitas) {
            $routeParam = $request->route('fasilita') ?? $request->route('fasilitas');
            $fasilitas = $routeParam instanceof Fasilitas ? $routeParam : Fasilitas::findOrFail($routeParam);
        }

        // Safety constraint: Check if this facility is attached to any room
        if ($fasilitas->kamar()->exists()) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Fasilitas tidak dapat dihapus karena sedang terpasang pada satu atau lebih kamar.'
                ], 422);
            }
            return redirect()->back()->with('error', 'Fasilitas tidak dapat dihapus karena sedang terpasang pada satu atau lebih kamar.');
        }

        $fasilitas->delete();

        // Invalidate cache
        Cache::forget('fasilitas_all');
        Cache::forget('fasilitas_aktif_landing');

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Fasilitas berhasil dihapus!'
            ]);
        }

        return redirect()->back()->with('success', 'Fasilitas berhasil dihapus!');
    }

    /**
     * Return the rendered facilities checkboxes HTML partial.
     */
    public function listHtml(Request $request)
    {
        $fasilitas = Fasilitas::all();
        
        $kamar = null;
        if ($request->has('kamar_id')) {
            $kamar = \App\Models\Kamar::find($request->input('kamar_id'));
        }

        return view('admin.kamar.partials.facilities-checkboxes', compact('fasilitas', 'kamar'));
    }
}
