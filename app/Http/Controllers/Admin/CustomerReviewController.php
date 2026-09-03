<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomerReview;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;

class CustomerReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = CustomerReview::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('pekerjaan', 'like', "%{$search}%")
                  ->orWhere('ulasan', 'like', "%{$search}%");
            });
        }

        $reviews = $query->latest()->paginate(10)->withQueryString();
        return view('admin.reviews.index', compact('reviews'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin.reviews.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $rules = [
            'nama' => ['required', 'string', 'max:100'],
            'pekerjaan' => ['nullable', 'string', 'max:100'],
            'bintang' => ['required', 'integer', 'min:1', 'max:5'],
            'ulasan' => ['required', 'string', 'max:1000'],
            'foto' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ];

        $messages = [
            'nama.required' => 'Nama pelanggan wajib diisi.',
            'bintang.required' => 'Rating wajib diisi.',
            'ulasan.required' => 'Isi ulasan wajib diisi.',
            'foto.image' => 'Format file harus berupa JPG/PNG.',
            'foto.mimes' => 'Format file harus berupa JPG/PNG.',
            'foto.max' => 'Ukuran file foto maksimal 2MB.',
        ];

        $validated = $request->validate($rules, $messages);

        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('reviews', 'public');
            $validated['foto'] = $path;
        }

        CustomerReview::create($validated);

        return redirect()->route('admin.reviews.index')->with('success', 'Review berhasil ditambahkan!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CustomerReview $review): View
    {
        return view('admin.reviews.edit', compact('review'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CustomerReview $review): RedirectResponse
    {
        $rules = [
            'nama' => ['required', 'string', 'max:100'],
            'pekerjaan' => ['nullable', 'string', 'max:100'],
            'bintang' => ['required', 'integer', 'min:1', 'max:5'],
            'ulasan' => ['required', 'string', 'max:1000'],
            'foto' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ];

        $messages = [
            'nama.required' => 'Nama pelanggan wajib diisi.',
            'bintang.required' => 'Rating wajib diisi.',
            'ulasan.required' => 'Isi ulasan wajib diisi.',
            'foto.image' => 'Format file harus berupa JPG/PNG.',
            'foto.mimes' => 'Format file harus berupa JPG/PNG.',
            'foto.max' => 'Ukuran file foto maksimal 2MB.',
        ];

        $validated = $request->validate($rules, $messages);

        if ($request->hasFile('foto')) {
            // Delete old photo if exists
            if ($review->foto && Storage::disk('public')->exists($review->foto)) {
                Storage::disk('public')->delete($review->foto);
            }
            $path = $request->file('foto')->store('reviews', 'public');
            $validated['foto'] = $path;
        } else {
            // Jangan overwrite foto lama dengan null jika tidak ada upload foto baru
            unset($validated['foto']);
        }

        $review->update($validated);

        return redirect()->route('admin.reviews.index')->with('success', 'Review berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CustomerReview $review): RedirectResponse
    {
        if ($review->foto && Storage::disk('public')->exists($review->foto)) {
            Storage::disk('public')->delete($review->foto);
        }

        $review->delete();

        return redirect()->route('admin.reviews.index')->with('success', 'Review berhasil dihapus!');
    }
}
