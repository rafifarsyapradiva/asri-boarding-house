<?php

namespace App\Http\Requests;

use App\Http\Requests\Traits\HasSanitizedInput;
use App\Models\Penyewa;
use App\Rules\KamarTersediaRule;
use App\Rules\TanpaPenyewaAktifLainRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePenyewaRequest extends FormRequest
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
        $penyewa = $this->route('penyewa');
        $penyewaModel = $penyewa instanceof Penyewa ? $penyewa : ($penyewa ? Penyewa::find($penyewa) : null);

        $userId = $penyewaModel ? $penyewaModel->user_id : null;
        $penyewaId = $penyewaModel ? $penyewaModel->id : null;

        $maxDurasi = config('reservasi.max_durasi.' . $this->input('tipe_sewa'), 12);

        return [
            'nama'          => ['required', 'string', 'max:100'],
            'email'         => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($userId)],
            'no_hp'         => ['required', 'string', 'regex:/^(08|628|\+628)[0-9]{8,13}$/', Rule::unique('users', 'no_hp')->ignore($userId)],
            'nik'           => ['required', 'numeric', 'digits:16', Rule::unique('penyewa', 'nik')->ignore($penyewaId)],
            'kamar_id'      => [
                'required',
                new KamarTersediaRule($penyewaModel, $this->input('status')),
            ],
            'tanggal_masuk' => ['required', 'date'],
            'nama_wali'     => ['required', 'string', 'max:100'],
            'no_wali'       => ['required', 'string', 'regex:/^(08|628|\+628)[0-9]{8,13}$/'],
            'catatan'       => ['nullable', 'string'],
            'tipe_sewa'     => ['required', 'string', Rule::in(['harian', 'mingguan', 'bulanan'])],
            'durasi'        => ['required', 'integer', 'min:1', 'max:' . $maxDurasi],
            'deposit'       => ['required', 'numeric', 'min:0'],
            'harga_sewa'    => ['required', 'numeric', 'min:0'],
            'status'        => [
                'required',
                'string',
                Rule::in(['aktif', 'nonaktif']),
                new TanpaPenyewaAktifLainRule($this->input('kamar_id'), $penyewaId),
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'no_hp.regex'   => 'Format nomor WhatsApp tidak valid (gunakan format Indonesia seperti 0812xxx atau 62812xxx).',
            'no_hp.unique'  => 'Nomor WhatsApp sudah terdaftar pada akun lain.',
            'no_wali.regex' => 'Format nomor HP wali tidak valid.',
            'nik.digits'    => 'NIK harus berupa bilangan tepat 16 digit.',
            'nik.numeric'   => 'NIK hanya boleh diisi oleh karakter angka.',
            'nik.unique'    => 'NIK ini sudah terdaftar untuk penyewa lain.',
            'durasi.max'    => 'Durasi sewa melebihi batas maksimal untuk tipe sewa yang dipilih (maksimal :max).',
        ];
    }
}
