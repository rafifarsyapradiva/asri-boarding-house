<?php

namespace App\Http\Requests\Admin;

use App\Traits\SanitizesCurrency;
use Illuminate\Foundation\Http\FormRequest;

class StorePengeluaranRequest extends FormRequest
{
    use SanitizesCurrency;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin';
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('nominal') && $this->input('nominal') !== null) {
            $this->merge([
                'nominal' => $this->sanitizeCurrency($this->input('nominal')),
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'nama_pengeluaran' => ['required', 'string', 'max:150'],
            'kategori' => ['required', 'in:maintenance,utilitas,operasional,lainnya'],
            'nominal' => ['required', 'numeric', 'min:0'],
            'tanggal_pengeluaran' => ['required', 'date'],
            'bukti_nota' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'keterangan' => ['nullable', 'string'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'nama_pengeluaran.required' => 'Nama pengeluaran wajib diisi.',
            'nama_pengeluaran.max' => 'Nama pengeluaran maksimal 150 karakter.',
            'kategori.required' => 'Kategori wajib diisi.',
            'kategori.in' => 'Kategori tidak valid.',
            'nominal.required' => 'Nominal wajib diisi.',
            'nominal.numeric' => 'Nominal harus berupa angka.',
            'nominal.min' => 'Nominal tidak boleh kurang dari 0.',
            'tanggal_pengeluaran.required' => 'Tanggal pengeluaran wajib diisi.',
            'tanggal_pengeluaran.date' => 'Format tanggal pengeluaran tidak valid.',
            'bukti_nota.image' => 'Bukti nota harus berupa gambar.',
            'bukti_nota.mimes' => 'Format bukti nota harus jpg, jpeg, atau png.',
            'bukti_nota.max' => 'Ukuran bukti nota maksimal 2MB.',
        ];
    }
}

