<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class FaqController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = Faq::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('pertanyaan', 'like', "%{$search}%")
                  ->orWhere('jawaban', 'like', "%{$search}%");
            });
        }

        // Ambil data FAQ diurutkan berdasarkan kolom 'urutan' secara ascending
        $faqs = $query->orderBy('urutan', 'asc')->paginate(10)->withQueryString();
        return view('admin.faq.index', compact('faqs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin.faq.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'pertanyaan' => ['required', 'string'],
            'jawaban' => ['required', 'string'],
            'urutan' => ['required', 'integer'],
            'is_active' => ['nullable'],
        ], [
            'pertanyaan.required' => 'Pertanyaan wajib diisi.',
            'jawaban.required' => 'Jawaban wajib diisi.',
            'urutan.required' => 'Urutan wajib diisi.',
            'urutan.integer' => 'Urutan harus berupa angka.',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        Faq::create($validated);

        return redirect()->route('admin.faq.index')->with('success', 'FAQ berhasil ditambahkan!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Faq $faq): View
    {
        return view('admin.faq.edit', compact('faq'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Faq $faq): RedirectResponse
    {
        $validated = $request->validate([
            'pertanyaan' => ['required', 'string'],
            'jawaban' => ['required', 'string'],
            'urutan' => ['required', 'integer'],
            'is_active' => ['nullable'],
        ], [
            'pertanyaan.required' => 'Pertanyaan wajib diisi.',
            'jawaban.required' => 'Jawaban wajib diisi.',
            'urutan.required' => 'Urutan wajib diisi.',
            'urutan.integer' => 'Urutan harus berupa angka.',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $faq->update($validated);

        return redirect()->route('admin.faq.index')->with('success', 'FAQ berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Faq $faq): RedirectResponse
    {
        $faq->delete();

        return redirect()->route('admin.faq.index')->with('success', 'FAQ berhasil dihapus!');
    }
}
