<?php

namespace Tests\Unit\Requests;

use App\Http\Requests\UpdatePenyewaRequest;
use Tests\TestCase;

class UpdatePenyewaRequestTest extends TestCase
{
    /**
     * Test sanitization for phone numbers and currencies.
     */
    public function test_request_sanitization(): void
    {
        $request = new UpdatePenyewaRequest();

        $reflection = new \ReflectionClass(UpdatePenyewaRequest::class);

        $sanitizeCurrency = $reflection->getMethod('sanitizeCurrency');
        $sanitizeCurrency->setAccessible(true);

        $bersihkanNomorHp = $reflection->getMethod('bersihkanNomorHp');
        $bersihkanNomorHp->setAccessible(true);

        // Test currency sanitization
        $this->assertEquals(1500000.0, $sanitizeCurrency->invoke($request, '1.500.000'));
        $this->assertEquals(1500000.5, $sanitizeCurrency->invoke($request, '1.500.000,50'));
        $this->assertEquals(1250000.0, $sanitizeCurrency->invoke($request, 1250000));
        $this->assertEquals(1250000.0, $sanitizeCurrency->invoke($request, '1250000.00'));

        // Test phone number sanitization
        $this->assertEquals('628123456789', $bersihkanNomorHp->invoke($request, '0812-3456-789'));
        $this->assertEquals('628123456789', $bersihkanNomorHp->invoke($request, '+628123456789'));
        $this->assertEquals('628123456789', $bersihkanNomorHp->invoke($request, '628123456789'));
        $this->assertEquals('628123456789', $bersihkanNomorHp->invoke($request, '6208123456789'));
    }
}
