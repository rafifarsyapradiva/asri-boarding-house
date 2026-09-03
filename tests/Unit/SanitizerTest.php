<?php

namespace Tests\Unit;

use App\Support\Sanitizer;
use PHPUnit\Framework\TestCase;

class SanitizerTest extends TestCase
{
    /**
     * Test currency parsing with integer and float inputs.
     */
    public function test_currency_passes_numeric_types_directly(): void
    {
        $this->assertEquals(1500000, Sanitizer::currency(1500000));
        $this->assertEquals(1250.75, Sanitizer::currency(1250.75));
        $this->assertNull(Sanitizer::currency(null));
    }

    /**
     * Test currency parsing with standard database decimal string format.
     */
    public function test_currency_parses_database_decimal_format(): void
    {
        $this->assertEquals(1400000.0, Sanitizer::currency('1400000.00'));
        $this->assertEquals(750000.5, Sanitizer::currency('750000.5'));
    }

    /**
     * Test currency parsing with Indonesian thousand separator and decimals.
     */
    public function test_currency_parses_indonesian_formats(): void
    {
        $this->assertEquals(1500000.0, Sanitizer::currency('1.500.000'));
        $this->assertEquals(1500000.5, Sanitizer::currency('1.500.000,50'));
        $this->assertEquals(1500000.5, Sanitizer::currency('1500000,50'));
    }

    /**
     * Test currency parsing with prefix and non-numeric symbols.
     */
    public function test_currency_strips_symbols_and_spaces(): void
    {
        $this->assertEquals(1500000.0, Sanitizer::currency('Rp 1.500.000'));
        $this->assertEquals(1500000.5, Sanitizer::currency('Rp. 1.500.000,50'));
        // In Indonesian locale, comma is decimal, so '1,000' is 1.0 (one)
        $this->assertEquals(1.0, Sanitizer::currency('1,000'));
    }

    /**
     * Test currency parsing does not corrupt standard decimals with 3+ decimal places.
     */
    public function test_currency_does_not_corrupt_standard_precise_decimals(): void
    {
        // "12.3456" should remain 12.3456 (4 decimal places), while "12.345" is 12345 (thousands separator)
        $this->assertEquals(12.3456, Sanitizer::currency('12.3456'));
    }

    /**
     * Test phone number normalization with empty inputs.
     */
    public function test_phone_number_returns_empty_on_empty_input(): void
    {
        $this->assertEquals('', Sanitizer::phoneNumber(null));
        $this->assertEquals('', Sanitizer::phoneNumber(''));
        $this->assertEquals('', Sanitizer::phoneNumber('   '));
        $this->assertEquals('', Sanitizer::phoneNumber('abc'));
    }

    /**
     * Test phone number normalization formats.
     */
    public function test_phone_number_normalizes_prefixes(): void
    {
        // 08... -> 628...
        $this->assertEquals('628123456789', Sanitizer::phoneNumber('08123456789'));
        
        // 6208... -> 628...
        $this->assertEquals('628123456789', Sanitizer::phoneNumber('6208123456789'));
        
        // 812... -> 62812...
        $this->assertEquals('628123456789', Sanitizer::phoneNumber('8123456789'));
        
        // 628... -> 628...
        $this->assertEquals('628123456789', Sanitizer::phoneNumber('628123456789'));
    }

    /**
     * Test phone number sanitizes non-numeric formatting characters.
     */
    public function test_phone_number_strips_formatting_characters(): void
    {
        $this->assertEquals('6281234567890', Sanitizer::phoneNumber('+62 812-3456-7890'));
        $this->assertEquals('6281234567890', Sanitizer::phoneNumber('(0812) 3456-7890'));
    }

    /**
     * Test phone number keeps international prefixes intact if not Indonesian.
     */
    public function test_phone_number_preserves_other_international_prefixes(): void
    {
        // US number +1 555-0100 -> should clean to 15550100 and NOT prepend 62
        $this->assertEquals('15550100', Sanitizer::phoneNumber('+1 555-0100'));
    }

    /**
     * Test currency parsing with English/International formats.
     */
    public function test_currency_parses_english_and_international_formats(): void
    {
        // English formats with multiple commas
        $this->assertEquals(1500000.0, Sanitizer::currency('1,500,000'));
        $this->assertEquals(1500000.5, Sanitizer::currency('1,500,000.50'));
        $this->assertEquals(1500.5, Sanitizer::currency('1,500.50'));
        
        // Symbols with English formats
        $this->assertEquals(1500.5, Sanitizer::currency('$ 1,500.50'));
    }

    /**
     * Test currency parsing with negative formatting styles.
     */
    public function test_currency_parses_negative_formats(): void
    {
        // Negative sign prefixes
        $this->assertEquals(-1500000.0, Sanitizer::currency('-1.500.000'));
        $this->assertEquals(-1500000.5, Sanitizer::currency('-Rp 1.500.000,50'));
        $this->assertEquals(-1500.5, Sanitizer::currency('- $1,500.50'));

        // Parentheses formatting (accounting)
        $this->assertEquals(-1500000.0, Sanitizer::currency('(1.500.000)'));
        $this->assertEquals(-1500000.5, Sanitizer::currency('Rp (1.500.000,50)'));
        $this->assertEquals(-1500.5, Sanitizer::currency('($1,500.50)'));
    }

    /**
     * Test currency parsing handles non-breaking spaces and multi-byte whitespace.
     */
    public function test_currency_handles_unicode_whitespace(): void
    {
        // Non-breaking space \u{00A0}
        $this->assertEquals(1500000.0, Sanitizer::currency("Rp\u{00A0}1.500.000"));
    }

    /**
     * Test email sanitization trims and lowercases input.
     */
    public function test_email_sanitization(): void
    {
        $this->assertEquals('user@example.com', Sanitizer::email('  USER@EXAMPLE.COM  '));
        $this->assertEquals('', Sanitizer::email(null));
        $this->assertEquals('', Sanitizer::email('   '));
    }
}


