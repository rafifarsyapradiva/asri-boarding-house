<?php

namespace App\Http\Requests\Penyewa;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreKeluhanRequest extends FormRequest
{
    /**
     * Daftar kategori keluhan yang valid.
     */
    public const KATEGORI_ALLOWED = [
        'kamar',
        'fasilitas_bersama',
        'kebersihan',
        'keamanan',
        'lainnya',
    ];

    /**
     * Determine if the user is authorized to make this request.
     * Memastikan user terautentikasi dan memiliki profil penyewa dengan status 'aktif'.
     */
    public function authorize(): bool
    {
        $user = $this->user();

        return $user !== null 
            && $user->penyewa !== null 
            && $user->penyewa->status === 'aktif';
    }

    /**
     * Handle a failed authorization attempt.
     */
    protected function failedAuthorization(): void
    {
        throw new \Illuminate\Http\Exceptions\HttpResponseException(
            redirect()->route('penyewa.keluhan.index')
                ->with('error', 'Akses ditolak. Hanya penyewa aktif yang dapat membuat keluhan.')
        );
    }

    /**
     * Sanitasi data sebelum masuk ke proses validasi.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'judul' => $this->judul ? trim(strip_tags($this->judul)) : null,
            'deskripsi' => $this->deskripsi ? trim(strip_tags($this->deskripsi)) : null,
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'judul' => ['required', 'string', 'min:5', 'max:150'],
            'kategori' => ['required', 'string', Rule::in(self::KATEGORI_ALLOWED)],
            'deskripsi' => ['required', 'string', 'min:10', 'max:5000'],
            'foto_bukti' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ];
    }

    /**
     * Get custom error messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'judul.required' => 'Judul keluhan wajib diisi.',
            'judul.min' => 'Judul keluhan minimal 5 karakter.',
            'judul.max' => 'Judul keluhan maksimal 150 karakter.',
            'kategori.required' => 'Kategori keluhan wajib dipilih.',
            'kategori.in' => 'Kategori keluhan tidak valid.',
            'deskripsi.required' => 'Deskripsi keluhan wajib diisi.',
            'deskripsi.min' => 'Deskripsi keluhan minimal 10 karakter.',
            'deskripsi.max' => 'Deskripsi keluhan maksimal 5000 karakter.',
            'foto_bukti.image' => 'File bukti harus berupa gambar.',
            'foto_bukti.mimes' => 'Format gambar harus berupa jpeg, png, jpg, atau webp.',
            'foto_bukti.max' => 'Ukuran gambar maksimal 2MB.',
        ];
    }
}

