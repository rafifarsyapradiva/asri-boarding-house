<?php

namespace Tests\Unit;

use App\Traits\SanitizesCurrency;
use Illuminate\Http\Request;
use Tests\TestCase;

class SanitizesCurrencyTest extends TestCase
{
    private object $dummy;

    protected function setUp(): void
    {
        parent::setUp();

        $this->dummy = new class {
            use SanitizesCurrency;

            public function testSanitize(mixed $value): mixed
            {
                return $this->sanitizeCurrency($value);
            }
        };
    }

    /**
     * Test sanitasi string format Rupiah ke decimal/numeric.
     */
    public function test_sanitizes_formatted_rupiah_string(): void
    {
        $this->assertEquals(1500000, $this->dummy->testSanitize('Rp 1.500.000'));
        $this->assertEquals(250000, $this->dummy->testSanitize('Rp. 250.000'));
        $this->assertEquals(75000, $this->dummy->testSanitize('75.000'));
    }

    /**
     * Test penanganan input null dan string kosong.
     */
    public function test_handles_null_and_empty_inputs(): void
    {
        $this->assertNull($this->dummy->testSanitize(null));
        $this->assertEquals('', $this->dummy->testSanitize(''));
    }

    /**
     * Test sanitasi input numerik (int dan float).
     */
    public function test_sanitizes_numeric_inputs(): void
    {
        $this->assertEquals(500000, $this->dummy->testSanitize(500000));
        $this->assertEquals(125000.75, $this->dummy->testSanitize(125000.75));
    }

    /**
     * Test batch helper sanitizeCurrencyFields pada Request.
     */
    public function test_sanitizes_multiple_currency_fields_on_request(): void
    {
        $formRequest = new class extends \Illuminate\Foundation\Http\FormRequest {
            use SanitizesCurrency;

            public function runSanitizeFields(array $fields): void
            {
                $this->sanitizeCurrencyFields($fields);
            }
        };

        $formRequest->initialize(['nominal' => 'Rp 500.000', 'biaya_tambahan' => 'Rp 50.000']);
        $formRequest->runSanitizeFields(['nominal', 'biaya_tambahan']);

        $this->assertEquals(500000, $formRequest->input('nominal'));
        $this->assertEquals(50000, $formRequest->input('biaya_tambahan'));
    }
}
