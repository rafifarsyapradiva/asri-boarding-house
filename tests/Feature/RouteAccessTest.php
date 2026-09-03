<?php

namespace Tests\Feature;


use PHPUnit\Framework\Attributes\Test;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RouteAccessTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function guest_cannot_access_protected_admin_dashboard()
    {
        $response = $this->get(route('admin.dashboard'));
        $response->assertRedirect(route('admin.login'));
    }

    #[Test]
    public function tenant_cannot_access_admin_portal()
    {
        $tenant = User::factory()->create([
            'role' => 'penyewa',
            'no_hp' => '081234567890',
            'nik' => '1234567890123456',
            'nama_wali' => 'Wali Test',
            'no_wali' => '081234567891',
        ]);

        $response = $this->actingAs($tenant)->get(route('admin.dashboard'));
        $response->assertStatus(403);
    }

    #[Test]
    public function guest_chat_start_rate_limiter_blocks_excessive_requests()
    {
        for ($i = 0; $i < 5; $i++) {
            $this->postJson(route('guest-chat.start'), [
                'nama' => 'Guest Unit Test',
                'no_hp' => '08123456789',
            ]);
        }

        // Request ke-6 harus ditolak oleh Rate Limiter (HTTP 429)
        $response = $this->postJson(route('guest-chat.start'), [
            'nama' => 'Guest Unit Test',
            'no_hp' => '08123456789',
        ]);

        $response->assertStatus(429);
    }
}
