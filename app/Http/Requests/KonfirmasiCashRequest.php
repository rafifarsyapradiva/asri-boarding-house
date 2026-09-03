<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class KonfirmasiCashRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin';
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'catatan' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'catatan.max' => 'Catatan konfirmasi maksimal 500 karakter.',
        ];
    }
}
