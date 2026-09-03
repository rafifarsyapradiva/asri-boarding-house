<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CekKetersediaanRequest extends FormRequest
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
            'tanggal_masuk' => ['required', 'date_format:Y-m-d', 'after_or_equal:today'],
            'durasi'        => ['required', 'integer', 'min:1'],
            'tipe_sewa'     => ['sometimes', 'nullable', 'string', 'in:harian,mingguan,bulanan'],
        ];
    }

    /**
     * Get custom error messages for validator failures.
     */
    public function messages(): array
    {
        return [
            'tanggal_masuk.required'       => 'Tanggal masuk wajib diisi.',
            'tanggal_masuk.date_format'    => 'Format tanggal masuk harus YYYY-MM-DD.',
            'tanggal_masuk.after_or_equal' => 'Tanggal masuk tidak boleh sebelum hari ini.',
            'durasi.min'                   => 'Durasi minimal adalah 1.',
            'tipe_sewa.in'                 => 'Tipe sewa harus berupa harian, mingguan, atau bulanan.',
        ];
    }

    /**
     * Handle a failed validation attempt for JSON requests.
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        if ($this->expectsJson()) {
            $response = response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors'  => $validator->errors()->toArray(),
            ], 422);

            throw new \Illuminate\Validation\ValidationException($validator, $response);
        }

        parent::failedValidation($validator);
    }
}
