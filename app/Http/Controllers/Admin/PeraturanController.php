<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Peraturan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Validation\Rule;

class PeraturanController extends Controller
{
    /**
     * Display a listing of the rules.
     */
    public function index(Request $request): View
    {
        $peraturan = Peraturan::when($request->filled('search'), function ($query) use ($request) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        })
        ->orderBy('urutan', 'asc')
        ->paginate(10)
        ->withQueryString();

        return view('admin.peraturan.index', compact('peraturan'));
    }

    /**
     * Show the form for creating a new rule.
     */
    public function create(): View
    {
        $icons = $this->getAvailableIcons();
        return view('admin.peraturan.create', compact('icons'));
    }

    /**
     * Store a newly created rule in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:100'],
            'deskripsi' => ['required', 'string'],
            'ikon' => ['required', 'string', Rule::in(array_keys($this->getAvailableIcons()))],
            'urutan' => ['required', 'integer', 'min:1'],
        ], [
            'judul.required' => 'Judul peraturan wajib diisi.',
            'deskripsi.required' => 'Deskripsi peraturan wajib diisi.',
            'ikon.required' => 'Ikon wajib dipilih.',
            'urutan.required' => 'Nomor urutan wajib diisi.',
            'urutan.integer' => 'Nomor urutan harus berupa angka.',
            'urutan.min' => 'Nomor urutan minimal bernilai 1.',
        ]);

        Peraturan::create($validated);

        return redirect()->route('admin.peraturan.index')
            ->with('success', 'Peraturan baru berhasil ditambahkan!');
    }

    /**
     * Show the form for editing the specified rule.
     */
    public function edit(Peraturan $peraturan): View
    {
        $icons = $this->getAvailableIcons();
        return view('admin.peraturan.edit', compact('peraturan', 'icons'));
    }

    /**
     * Update the specified rule in storage.
     */
    public function update(Request $request, Peraturan $peraturan): RedirectResponse
    {
        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:100'],
            'deskripsi' => ['required', 'string'],
            'ikon' => ['required', 'string', Rule::in(array_keys($this->getAvailableIcons()))],
            'urutan' => ['required', 'integer', 'min:1'],
        ], [
            'judul.required' => 'Judul peraturan wajib diisi.',
            'deskripsi.required' => 'Deskripsi peraturan wajib diisi.',
            'ikon.required' => 'Ikon wajib dipilih.',
            'urutan.required' => 'Nomor urutan wajib diisi.',
            'urutan.integer' => 'Nomor urutan harus berupa angka.',
            'urutan.min' => 'Nomor urutan minimal bernilai 1.',
        ]);

        $peraturan->update($validated);

        return redirect()->route('admin.peraturan.index')
            ->with('success', 'Peraturan berhasil diperbarui!');
    }

    /**
     * Remove the specified rule from storage.
     */
    public function destroy(Peraturan $peraturan): RedirectResponse
    {
        $peraturan->delete();

        return redirect()->route('admin.peraturan.index')
            ->with('success', 'Peraturan berhasil dihapus!');
    }

    /**
     * Get the list of available icons for the select field.
     */
    private function getAvailableIcons(): array
    {
        return [
            'sparkles' => 'Sparkles (Kebersihan / Kerapian)',
            'bolt' => 'Bolt (Listrik / Penghematan Energi)',
            'computer-desktop' => 'Computer Desktop (Alat Elektronik Tambahan)',
            'credit-card' => 'Credit Card (Pembayaran / Keuangan)',
            'exclamation-triangle' => 'Exclamation Triangle (Disiplin Kamar Mandi / Peringatan)',
            'user-group' => 'User Group (Tamu Menginap)',
            'shield-alert' => 'Shield Alert (Aturan Tamu Pria / Larangan)',
            'no-symbol' => 'No Symbol (Larangan Merokok / Area Bebas)',
            'moon' => 'Moon (Jam Malam / Gerbang)',
        ];
    }
}
