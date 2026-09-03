<?php

namespace App\Http\Requests\Admin;

use App\Traits\SanitizesCurrency;
use Illuminate\Foundation\Http\FormRequest;

class KonfirmasiReservasiRequest extends FormRequest
{
    use SanitizesCurrency;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin';
    }

    /**
     * Prepare data for validation by sanitizing currency input.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('deposit') && $this->input('deposit') !== null) {
            $this->merge([
                'deposit' => $this->sanitizeCurrency($this->input('deposit')),
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nik' => ['required', 'numeric', 'digits:16', 'unique:penyewa,nik'],
            'nama_wali' => ['required', 'string', 'max:100'],
            'no_wali' => ['required', 'string', 'regex:/^(08|628|\+628)[0-9]{8,13}$/'],
            'deposit' => ['nullable', 'numeric', 'min:0'],
            'catatan_admin' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'nik.required' => 'NIK wajib diisi.',
            'nik.numeric' => 'NIK harus berupa angka.',
            'nik.digits' => 'NIK harus tepat 16 digit.',
            'nik.unique' => 'NIK sudah terdaftar pada penyewa lain.',
            'nama_wali.required' => 'Nama wali wajib diisi.',
            'no_wali.required' => 'Nomor HP wali wajib diisi.',
            'no_wali.regex' => 'Format nomor HP wali tidak valid.',
            'deposit.numeric' => 'Deposit harus berupa angka.',
            'deposit.min' => 'Deposit tidak boleh kurang dari 0.',
            'catatan_admin.max' => 'Catatan admin maksimal 500 karakter.',
        ];
    }
}
