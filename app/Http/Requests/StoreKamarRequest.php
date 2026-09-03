<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreKamarRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'nomor_kamar' => ['required', 'string', 'max:10', Rule::unique('kamar', 'nomor_kamar')],
            'lantai'      => ['required', 'integer', 'min:1'],
            'tipe'        => ['required', Rule::in(['standar', 'deluxe', 'vip'])],
            'luas_m2'     => ['required', 'numeric', 'min:0'],
            'harga_bulan' => ['required', 'numeric', 'min:0'],
            'deskripsi'   => ['nullable', 'string'],
            'foto'        => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'status'      => ['nullable', Rule::in(['tersedia', 'terisi', 'maintenance'])],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'nomor_kamar.required' => 'Nomor kamar wajib diisi.',
            'nomor_kamar.unique'   => 'Nomor kamar sudah terdaftar.',
            'lantai.required'      => 'Posisi lantai wajib diisi.',
            'tipe.required'        => 'Tipe kamar wajib dipilih.',
            'harga_bulan.required' => 'Harga sewa per bulan wajib diisi.',
            'foto.image'           => 'Berkas foto harus berupa gambar.',
            'foto.max'             => 'Ukuran foto maksimal 2MB.',
        ];
    }
}
