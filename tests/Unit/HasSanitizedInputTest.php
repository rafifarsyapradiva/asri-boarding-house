<?php

namespace Tests\Unit;


use PHPUnit\Framework\Attributes\Test;
use App\Http\Requests\Traits\HasSanitizedInput;
use Illuminate\Foundation\Http\FormRequest;
use Tests\TestCase;

class HasSanitizedInputTest extends TestCase
{
    private object $dummyRequest;

    protected function setUp(): void
    {
        parent::setUp();

        $this->dummyRequest = new class extends FormRequest {
            use HasSanitizedInput;

            public function testSanitizeInputs(array $inputData, array $rules): array
            {
                $this->replace($inputData);
                $this->sanitizeInputs($rules);
                return $this->all();
            }

            public function testSingleCurrency(mixed $val): mixed
            {
                return $this->sanitizeCurrency($val);
            }

            public function testSinglePhone(?string $val): string
            {
                return $this->sanitizePhoneNumber($val);
            }

            public function testSingleEmail(?string $val): string
            {
                return $this->sanitizeEmail($val);
            }

            public function testDeprecatedPhone(?string $val): string
            {
                return $this->bersihkanNomorHp($val);
            }
        };
    }

    /**
     * Test single currency sanitization.
     */
    public function test_sanitizes_currency_correctly(): void
    {
        $this->assertEquals(1500000.0, $this->dummyRequest->testSingleCurrency('Rp 1.500.000'));
        $this->assertEquals(750000.5, $this->dummyRequest->testSingleCurrency('750000,50'));
    }

    /**
     * Test single phone number sanitization does not mutate short input to empty string.
     */
    public function test_sanitizes_phone_number_without_truncating_short_inputs(): void
    {
        $this->assertEquals('628123456789', $this->dummyRequest->testSinglePhone('08123456789'));
        // Short phone input should normalize to 628123 without truncating to empty string
        $this->assertEquals('628123', $this->dummyRequest->testSinglePhone('08123'));
    }

    /**
     * Test single email sanitization.
     */
    public function test_sanitizes_email_correctly(): void
    {
        $this->assertEquals('user@example.com', $this->dummyRequest->testSingleEmail('  USER@EXAMPLE.COM  '));
        $this->assertEquals('', $this->dummyRequest->testSingleEmail(null));
    }

    /**
     * Test deprecated bersihkanNomorHp method proxies to sanitizePhoneNumber.
     */
    public function test_deprecated_phone_method_proxies_properly(): void
    {
        $this->assertEquals('628123456789', $this->dummyRequest->testDeprecatedPhone('08123456789'));
    }

    /**
     * Test batch input sanitization via sanitizeInputs().
     */
    public function test_performs_batch_sanitization_declaratively(): void
    {
        $input = [
            'email'      => ' USER@EXAMPLE.COM ',
            'no_hp'      => '0812-3456-7890',
            'harga_sewa' => 'Rp 2.500.000',
            'catatan'    => '  bebas  ',
            'custom'     => 'hello world',
        ];

        $rules = [
            'email'      => 'email',
            'no_hp'      => 'phone',
            'harga_sewa' => 'currency',
            'catatan'    => 'trim',
            'custom'     => fn ($val) => strtoupper($val),
        ];

        $result = $this->dummyRequest->testSanitizeInputs($input, $rules);

        $this->assertEquals('user@example.com', $result['email']);
        $this->assertEquals('6281234567890', $result['no_hp']);
        $this->assertEquals(2500000.0, $result['harga_sewa']);
        $this->assertEquals('bebas', $result['catatan']);
        $this->assertEquals('HELLO WORLD', $result['custom']);
    }

    /**
     * Test sanitizeInputs ignores fields not present in input.
     */
    public function test_sanitize_inputs_ignores_missing_fields(): void
    {
        $input = [
            'email' => 'TEST@TEST.COM',
        ];

        $rules = [
            'email' => 'email',
            'no_hp' => 'phone',
        ];

        $result = $this->dummyRequest->testSanitizeInputs($input, $rules);

        $this->assertEquals('test@test.com', $result['email']);
        $this->assertArrayNotHasKey('no_hp', $result);
    }
}
