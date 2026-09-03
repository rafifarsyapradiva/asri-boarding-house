<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreReservasiRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $maxDurasi = config('reservasi.max_durasi.' . $this->input('tipe_sewa'), 12);

        return [
            'kamar_id'      => ['required', 'integer', Rule::exists('kamar', 'id')],
            'tipe_sewa'     => ['required', 'string', Rule::in(['harian', 'mingguan', 'bulanan'])],
            'tanggal_mulai' => ['required', 'date_format:Y-m-d', 'after_or_equal:today'],
            'durasi'        => ['required', 'integer', 'min:1', 'max:' . $maxDurasi],
            'is_dp'         => ['nullable', 'boolean'],
        ];
    }

    /**
     * Get custom error messages for validator failures.
     */
    public function messages(): array
    {
        return [
            'kamar_id.required'          => 'Kamar harus dipilih.',
            'kamar_id.integer'           => 'ID Kamar harus berupa angka.',
            'kamar_id.exists'            => 'Kamar yang dipilih tidak valid.',
            'tipe_sewa.required'         => 'Tipe sewa wajib dipilih.',
            'tipe_sewa.in'               => 'Tipe sewa harus berupa harian, mingguan, atau bulanan.',
            'tanggal_mulai.required'     => 'Tanggal mulai sewa wajib diisi.',
            'tanggal_mulai.date_format'  => 'Format tanggal mulai sewa harus YYYY-MM-DD.',
            'tanggal_mulai.after_or_equal' => 'Tanggal mulai sewa tidak boleh sebelum hari ini.',
            'durasi.required'            => 'Durasi sewa wajib diisi.',
            'durasi.integer'             => 'Durasi sewa harus berupa angka.',
            'durasi.min'                 => 'Durasi sewa minimal adalah :min.',
            'durasi.max'                 => 'Durasi sewa melebihi batas maksimal untuk tipe sewa yang dipilih (maksimal :max).',
            'is_dp.boolean'              => 'Pilihan pembayaran DP harus valid.',
        ];
    }
}
