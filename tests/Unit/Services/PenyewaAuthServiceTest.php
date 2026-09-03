<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\PenyewaAuthService;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PenyewaAuthServiceTest extends TestCase
{
    use RefreshDatabase;

    protected PenyewaAuthService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new PenyewaAuthService();
    }

    public function test_get_phone_variations_handles_08_and_62(): void
    {
        $variations1 = $this->service->getAllPhoneVariations('08123456789');
        $this->assertContains('08123456789', $variations1);
        $this->assertContains('628123456789', $variations1);

        $variations2 = $this->service->getAllPhoneVariations('628123456789');
        $this->assertContains('628123456789', $variations2);
        $this->assertContains('08123456789', $variations2);
    }

    public function test_attempt_initial_password_fallback_matches_alternative_phone_format(): void
    {
        $user = User::factory()->create([
            'no_hp'                   => '081234567890',
            'password'                => bcrypt('081234567890'),
            'require_password_change' => true,
        ]);

        // User enters '6281234567890' as password
        $result = $this->service->attemptInitialPasswordFallback($user, '6281234567890');

        $this->assertTrue($result);
    }

    public function test_attempt_initial_password_fallback_returns_false_for_invalid_password(): void
    {
        $user = User::factory()->create([
            'no_hp'                   => '081234567890',
            'password'                => bcrypt('081234567890'),
            'require_password_change' => true,
        ]);

        $result = $this->service->attemptInitialPasswordFallback($user, 'wrongpassword');

        $this->assertFalse($result);
    }
}
