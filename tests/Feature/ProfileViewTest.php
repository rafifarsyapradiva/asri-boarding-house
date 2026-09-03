<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileViewTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_profile_edit_passes_context_variables_and_renders_accessible_alerts(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
        ]);

        $response = $this->actingAs($admin)->get(route('profile.edit'));

        $response->assertOk()
            ->assertViewIs('profile.edit')
            ->assertViewHas('user', $admin)
            ->assertViewHas('isDeletable', true)
            ->assertViewHas('updateRoute', route('profile.update'))
            ->assertViewHas('deleteRoute', route('profile.destroy'));
    }

    public function test_penyewa_kelola_akun_passes_context_variables(): void
    {
        $penyewa = User::factory()->create([
            'role' => User::ROLE_PENYEWA,
        ]);

        $response = $this->actingAs($penyewa)->get(route('penyewa.profile.edit'));

        $response->assertOk()
            ->assertViewIs('penyewa.kelola-akun')
            ->assertViewHas('user', $penyewa)
            ->assertViewHas('updateRoute', route('penyewa.profile.update'))
            ->assertViewHas('deleteRoute', route('penyewa.profile.destroy'));
    }

    public function test_profile_edit_displays_accessible_status_alert_for_profile_update(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
        ]);

        $response = $this->actingAs($admin)
            ->withSession(['status' => 'profile-updated'])
            ->get(route('profile.edit'));

        $response->assertOk()
            ->assertSee('role="alert"', false)
            ->assertSee('aria-live="polite"', false)
            ->assertSee('Informasi profil berhasil diperbarui!');
    }

    public function test_profile_edit_displays_accessible_status_alert_for_password_update(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
        ]);

        $response = $this->actingAs($admin)
            ->withSession(['status' => 'password-updated'])
            ->get(route('profile.edit'));

        $response->assertOk()
            ->assertSee('role="alert"', false)
            ->assertSee('aria-live="polite"', false)
            ->assertSee('Kata sandi akun berhasil diperbarui!');
    }
}
