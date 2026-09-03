<?php

namespace App\Http\Requests;

use App\Http\Requests\Traits\HasSanitizedInput;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileCompletionRequest extends FormRequest
{
    use HasSanitizedInput;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $user = $this->user();
        if ($user && $penyewa = $user->penyewa) {
            $this->merge([
                'nik'       => $this->input('nik') ?: $penyewa->nik,
                'nama_wali' => $this->input('nama_wali') ?: $penyewa->nama_wali,
                'no_wali'   => $this->input('no_wali') ?: $penyewa->no_wali,
            ]);
        }

        // Sanitasi nomor HP/Wali secara konsisten menggunakan Trait
        $this->sanitizeInputs([
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $user = $this->user();
        $userId = $user?->id;
        $penyewaId = $user?->penyewa?->id;

        return [
            'no_hp'     => ['required', 'string', 'regex:/^(08|628|\+628)[0-9]{8,13}$/', Rule::unique('users', 'no_hp')->ignore($userId)],
            'nik'       => ['required', 'numeric', 'digits:16', Rule::unique('penyewa', 'nik')->ignore($penyewaId)],
            'nama_wali' => ['required', 'string', 'max:100'],
            'no_wali'   => ['required', 'string', 'regex:/^(08|628|\+628)[0-9]{8,13}$/'],
        ];
    }

    /**
     * Get the custom validation messages.
     */
    public function messages(): array
    {
        return [
            'no_hp.required'     => 'Nomor WhatsApp wajib diisi.',
            'no_hp.regex'        => 'Format nomor WhatsApp tidak valid (gunakan format Indonesia seperti 0812xxx atau 62812xxx).',
            'no_hp.unique'       => 'Nomor WhatsApp sudah terdaftar pada akun lain.',
            'nik.required'       => 'NIK wajib diisi.',
            'nik.numeric'        => 'NIK harus berupa angka.',
            'nik.digits'         => 'NIK harus tepat 16 digit.',
            'nik.unique'         => 'NIK sudah terdaftar pada penyewa lain.',
            'nama_wali.required' => 'Nama wali wajib diisi.',
            'no_wali.required'   => 'Nomor HP wali wajib diisi.',
            'no_wali.regex'      => 'Format nomor HP wali tidak valid (gunakan format Indonesia seperti 0812xxx atau 62812xxx).',
        ];
    }
}
