<?php

namespace App\Http\Requests;

use App\Http\Requests\Traits\HasSanitizedInput;
use App\Rules\KamarTersediaRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePenyewaRequest extends FormRequest
{
    use HasSanitizedInput;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->sanitizeInputs([
            'email'      => 'email',
            'no_hp'      => 'phone',
            'no_wali'    => 'phone',
            'deposit'    => 'currency',
            'harga_sewa' => 'currency',
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $maxDurasi = config('reservasi.max_durasi.' . $this->input('tipe_sewa'), 12);

        return [
            // Field untuk tabel users
            'nama'          => ['required', 'string', 'max:100'],
            'email'         => ['required', 'email', 'max:150', Rule::unique('users', 'email')],
            'no_hp'         => ['required', 'string', 'regex:/^(08|628|\+628)[0-9]{8,13}$/', Rule::unique('users', 'no_hp')],

            // Field untuk tabel penyewa
            'nik'           => ['required', 'numeric', 'digits:16', Rule::unique('penyewa', 'nik')],
            'kamar_id'      => [
                'required',
                new KamarTersediaRule(),
            ],
            'tanggal_masuk' => ['required', 'date'],
            'deposit'       => ['nullable', 'numeric', 'min:0'],
            'harga_sewa'    => ['nullable', 'numeric', 'min:0'],
            'nama_wali'     => ['required', 'string', 'max:100'],
            'no_wali'       => ['required', 'string', 'regex:/^(08|628|\+628)[0-9]{8,13}$/'],
            'catatan'       => ['nullable', 'string'],
            'tipe_sewa'     => ['required', 'string', Rule::in(['harian', 'mingguan', 'bulanan'])],
            'durasi'        => ['required', 'integer', 'min:1', 'max:' . $maxDurasi],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'kamar_id.required' => 'Kamar harus dipilih.',
            'no_hp.regex'       => 'Format nomor WhatsApp tidak valid (gunakan format Indonesia seperti 0812xxx atau 62812xxx).',
            'no_hp.unique'      => 'Nomor WhatsApp sudah terdaftar pada akun lain.',
            'no_wali.regex'     => 'Format nomor HP wali tidak valid.',
            'nik.digits'        => 'NIK harus berupa bilangan tepat 16 digit.',
            'nik.numeric'       => 'NIK hanya boleh diisi oleh karakter angka.',
            'nik.unique'        => 'NIK ini sudah terdaftar untuk penyewa lain.',
            'durasi.max'        => 'Durasi sewa ' . ($this->input('tipe_sewa') ?: 'bulanan') . ' maksimal :max bulan.',
        ];
    }
}
