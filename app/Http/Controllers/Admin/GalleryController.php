<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;

class GalleryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $galleries = Gallery::when($request->filled('search'), function ($query) use ($request) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        })
        ->when($request->has('status') && $request->input('status') !== null && $request->input('status') !== '', function ($query) use ($request) {
            $status = $request->input('status');
            $query->where('is_active', $status === 'active');
        })
        ->orderBy('urutan', 'asc')
        ->paginate(10)
        ->withQueryString();

        return view('admin.gallery.index', compact('galleries'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin.gallery.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $rules = [
            'judul' => ['required', 'string', 'max:100'],
            'deskripsi' => ['nullable', 'string'],
            'foto' => ['required_without:foto_url', 'nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'foto_url' => ['nullable', 'url'],
            'urutan' => ['required', 'integer'],
            'is_active' => ['nullable'],
        ];

        $messages = [
            'judul.required' => 'Judul galeri wajib diisi.',
            'judul.max' => 'Judul galeri maksimal 100 karakter.',
            'foto.required_without' => 'File foto atau URL foto wajib disediakan.',
            'foto.image' => 'File harus berupa gambar.',
            'foto.mimes' => 'Format file harus JPG/PNG/WEBP.',
            'foto.max' => 'Ukuran file foto maksimal 2MB.',
            'foto_url.url' => 'Format URL tidak valid.',
            'urutan.required' => 'Urutan wajib diisi.',
            'urutan.integer' => 'Urutan harus berupa angka.',
        ];

        $validated = $request->validate($rules, $messages);

        $validated['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('galleries', 'public');
            $validated['foto'] = $path;
        } elseif ($request->filled('foto_url')) {
            $validated['foto'] = $request->input('foto_url');
        }

        unset($validated['foto_url']);

        Gallery::create($validated);

        return redirect()->route('admin.gallery.index')->with('success', 'Galeri berhasil ditambahkan!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Gallery $gallery): View
    {
        return view('admin.gallery.edit', compact('gallery'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Gallery $gallery): RedirectResponse
    {
        $rules = [
            'judul' => ['required', 'string', 'max:100'],
            'deskripsi' => ['nullable', 'string'],
            'foto' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'foto_url' => ['nullable', 'url'],
            'urutan' => ['required', 'integer'],
            'is_active' => ['nullable'],
        ];

        $messages = [
            'judul.required' => 'Judul galeri wajib diisi.',
            'judul.max' => 'Judul galeri maksimal 100 karakter.',
            'foto.image' => 'File harus berupa gambar.',
            'foto.mimes' => 'Format file harus JPG/PNG/WEBP.',
            'foto.max' => 'Ukuran file foto maksimal 2MB.',
            'foto_url.url' => 'Format URL tidak valid.',
            'urutan.required' => 'Urutan wajib diisi.',
            'urutan.integer' => 'Urutan harus berupa angka.',
        ];

        $validated = $request->validate($rules, $messages);

        $validated['is_active'] = $request->boolean('is_active');

        // Validation: If current photo is a URL, and user clears it without uploading a file
        if (!$request->hasFile('foto') && $gallery->foto && str_starts_with($gallery->foto, 'http') && $request->has('foto_url') && !$request->filled('foto_url')) {
            return back()->withErrors(['foto_url' => 'Foto URL atau file foto wajib diisi untuk menggantikan foto saat ini.'])->withInput();
        }

        if ($request->hasFile('foto')) {
            if ($gallery->foto && !str_starts_with($gallery->foto, 'http') && Storage::disk('public')->exists($gallery->foto)) {
                Storage::disk('public')->delete($gallery->foto);
            }
            $path = $request->file('foto')->store('galleries', 'public');
            $validated['foto'] = $path;
        } elseif ($request->filled('foto_url')) {
            if ($gallery->foto && !str_starts_with($gallery->foto, 'http') && Storage::disk('public')->exists($gallery->foto)) {
                Storage::disk('public')->delete($gallery->foto);
            }
            $validated['foto'] = $request->input('foto_url');
        } else {
            unset($validated['foto']);
        }

        unset($validated['foto_url']);

        $gallery->update($validated);

        return redirect()->route('admin.gallery.index')->with('success', 'Galeri berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Gallery $gallery): RedirectResponse
    {
        if ($gallery->foto && !str_starts_with($gallery->foto, 'http') && Storage::disk('public')->exists($gallery->foto)) {
            Storage::disk('public')->delete($gallery->foto);
        }

        $gallery->delete();

        return redirect()->route('admin.gallery.index')->with('success', 'Galeri berhasil dihapus!');
    }
}
