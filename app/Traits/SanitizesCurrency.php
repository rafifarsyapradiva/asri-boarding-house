<?php

namespace App\Traits;

use App\Support\Sanitizer;

trait SanitizesCurrency
{
    /**
     * Memformat string rupiah visual frontend menjadi nilai decimal/numeric database standar.
     * Menggunakan utilitas terpusat Sanitizer untuk memastikan konsistensi dan keandalan parser.
     *
     * @param mixed $value
     * @return mixed
     */
    protected function sanitizeCurrency(mixed $value): mixed
    {
        return Sanitizer::currency($value);
    }

    /**
     * Helper opsional untuk memproses multiple field currency pada FormRequest.
     *
     * @param array<int, string> $fields
     * @return void
     */
    protected function sanitizeCurrencyFields(array $fields): void
    {
        $sanitized = [];
        foreach ($fields as $field) {
            if ($this->has($field) && $this->input($field) !== null) {
                $sanitized[$field] = $this->sanitizeCurrency($this->input($field));
            }
        }

        if (!empty($sanitized)) {
            $this->merge($sanitized);
        }
    }
}

