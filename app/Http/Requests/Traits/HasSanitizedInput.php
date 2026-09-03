<?php

namespace App\Http\Requests\Traits;

use App\Support\Sanitizer;

/**
 * Trait HasSanitizedInput
 * 
 * Menyediakan helper sanitasi input yang bersih, deklaratif, dan aman untuk Laravel FormRequest.
 */
trait HasSanitizedInput
{
    /**
     * Sanitasi batch untuk daftar field pada FormRequest.
     * 
     * Contoh penggunaan di prepareForValidation():
     * 
     * $this->sanitizeInputs([
     *     'deposit'    => 'currency',
     *     'harga_sewa' => 'currency',
     *     'no_hp'      => 'phone',
     *     'email'      => 'email',
     *     'catatan'    => 'trim',
     * ]);
     * 
     * @param array<string, string|callable> $sanitizers
     */
    protected function sanitizeInputs(array $sanitizers): void
    {
        $merged = [];

        foreach ($sanitizers as $field => $type) {
            if (!$this->has($field)) {
                continue;
            }

            $value = $this->input($field);

            $merged[$field] = match ($type) {
                'currency' => $this->sanitizeCurrency($value),
                'phone'    => $this->sanitizePhoneNumber(is_string($value) ? $value : null),
                'email'    => $this->sanitizeEmail(is_string($value) ? $value : null),
                'trim'     => is_string($value) ? trim($value) : $value,
                default    => is_callable($type) ? $type($value) : $value,
            };
        }

        if (!empty($merged)) {
            $this->merge($merged);
        }
    }

    /**
     * Memformat string rupiah visual frontend menjadi nilai decimal database standar.
     */
    protected function sanitizeCurrency(mixed $value): mixed
    {
        return Sanitizer::currency($value);
    }

    /**
     * Membersihkan dan menormalisasi nomor HP agar siap dikirim via Fonnte (Format: 628xxx).
     */
    protected function sanitizePhoneNumber(?string $nomor): string
    {
        return Sanitizer::phoneNumber($nomor);
    }

    /**
     * Membersihkan string email (trim dan lowercasing).
     */
    protected function sanitizeEmail(?string $email): string
    {
        return Sanitizer::email($email);
    }

    /**
     * Membersihkan dan menormalisasi nomor HP agar siap dikirim via Fonnte (Format: 628xxx).
     * 
     * @deprecated Gunakan sanitizePhoneNumber() atau $this->sanitizeInputs(['no_hp' => 'phone'])
     */
    protected function bersihkanNomorHp(?string $nomor): string
    {
        return $this->sanitizePhoneNumber($nomor);
    }
}
