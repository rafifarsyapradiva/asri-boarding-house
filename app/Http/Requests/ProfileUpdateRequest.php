<?php

namespace App\Http\Requests;

use App\Models\User;
use App\Http\Requests\Traits\HasSanitizedInput;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    use HasSanitizedInput;

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('name') && !$this->has('nama')) {
            $this->merge(['nama' => $this->input('name')]);
        }
        if ($this->has('nama') && !$this->has('name')) {
            $this->merge(['name' => $this->input('nama')]);
        }

        $this->sanitizeInputs([
            'email' => 'email',
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userId = $this->user()?->id;

        return [
            'nama' => ['required_without:name', 'nullable', 'string', 'max:255'],
            'name' => ['required_without:nama', 'nullable', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($userId),
            ],
            'no_hp' => [
                'required',
                'string',
                'regex:/^(08|628|\+628)[0-9]{8,13}$/',
                Rule::unique(User::class)->ignore($userId),
            ],
        ];
    }

    /**
     * Get the validation error messages.
     */
    public function messages(): array
    {
        return [
            'nama.required'         => 'Nama lengkap wajib diisi.',
            'nama.required_without' => 'Nama lengkap wajib diisi.',
            'name.required'         => 'Nama lengkap wajib diisi.',
            'name.required_without' => 'Nama lengkap wajib diisi.',
            'email.required'        => 'Alamat email wajib diisi.',
            'email.unique'          => 'Alamat email sudah terdaftar pada akun lain.',
            'no_hp.required'        => 'Nomor WhatsApp wajib diisi.',
            'no_hp.regex'           => 'Format nomor WhatsApp tidak valid (gunakan format Indonesia seperti 0812xxx atau 62812xxx).',
            'no_hp.unique'          => 'Nomor WhatsApp sudah terdaftar pada akun lain.',
        ];
    }
}
