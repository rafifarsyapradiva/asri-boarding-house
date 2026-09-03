<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminProfileTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $penyewa;
    private User $otherUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'nama' => 'Admin Asep',
            'email' => 'asri-asep@gmail.com',
            'password' => bcrypt('password123'),
            'no_hp' => '6281234567890',
            'role' => 'admin',
            'is_active' => 1,
            'require_password_change' => 0,
        ]);

        $this->penyewa = User::create([
            'nama' => 'Penyewa Kost',
            'email' => 'penyewa@gmail.com',
            'password' => bcrypt('password123'),
            'no_hp' => '6281234567891',
            'role' => 'penyewa',
            'is_active' => 1,
            'require_password_change' => 0,
        ]);

        $this->otherUser = User::create([
            'nama' => 'User Lain',
            'email' => 'userlain@gmail.com',
            'password' => bcrypt('password123'),
            'no_hp' => '6281234567892',
            'role' => 'penyewa',
            'is_active' => 1,
            'require_password_change' => 0,
        ]);
    }

    public function test_admin_can_access_profile_edit_page(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.profile.edit'));

        $response->assertStatus(200);
        $response->assertSee('Pengaturan Akun');
        $response->assertSee($this->admin->email);
    }

    public function test_guest_and_tenant_cannot_access_profile_edit_page(): void
    {
        // Guest redirect
        $response = $this->get(route('admin.profile.edit'));
        $response->assertRedirect(route('admin.login'));

        // Tenant forbidden
        $response = $this->actingAs($this->penyewa)
            ->get(route('admin.profile.edit'));
        $response->assertStatus(403);
    }

    public function test_admin_can_update_profile_info_without_password(): void
    {
        $payload = [
            'nama' => 'Asep Update',
            'email' => 'asep-baru@gmail.com',
            'no_hp' => '081298765432',
        ];

        $response = $this->actingAs($this->admin)
            ->from(route('admin.profile.edit'))
            ->patch(route('admin.profile.update'), $payload);

        $response->assertRedirect(route('admin.profile.edit'));
        $response->assertSessionHas('success', 'Profil admin berhasil diperbarui!');

        $this->admin->refresh();
        $this->assertEquals('Asep Update', $this->admin->nama);
        $this->assertEquals('asep-baru@gmail.com', $this->admin->email);
        $this->assertEquals('6281298765432', $this->admin->no_hp);
        $this->assertTrue(Hash::check('password123', $this->admin->password));
    }

    public function test_admin_can_update_profile_info_with_password(): void
    {
        $payload = [
            'nama' => 'Asep Update',
            'email' => 'asep-baru@gmail.com',
            'no_hp' => '081298765432',
            'password' => 'newPassword123',
            'password_confirmation' => 'newPassword123',
        ];

        $response = $this->actingAs($this->admin)
            ->from(route('admin.profile.edit'))
            ->patch(route('admin.profile.update'), $payload);

        $response->assertRedirect(route('admin.profile.edit'));
        $response->assertSessionHas('success', 'Profil admin berhasil diperbarui!');

        $this->admin->refresh();
        $this->assertEquals('Asep Update', $this->admin->nama);
        $this->assertEquals('asep-baru@gmail.com', $this->admin->email);
        $this->assertEquals('6281298765432', $this->admin->no_hp);
        $this->assertTrue(Hash::check('newPassword123', $this->admin->password));
    }

    public function test_admin_profile_update_validation_email_must_be_gmail(): void
    {
        $payload = [
            'nama' => 'Asep Update',
            'email' => 'asep@yahoo.com', // Not a gmail address
            'no_hp' => '081298765432',
        ];

        $response = $this->actingAs($this->admin)
            ->from(route('admin.profile.edit'))
            ->patch(route('admin.profile.update'), $payload);

        $response->assertRedirect(route('admin.profile.edit'));
        $response->assertSessionHasErrors('email');

        $this->admin->refresh();
        $this->assertNotEquals('asep@yahoo.com', $this->admin->email);
    }

    public function test_admin_profile_update_validation_no_hp_format(): void
    {
        $payload = [
            'nama' => 'Asep Update',
            'email' => 'asep@gmail.com',
            'no_hp' => '12345', // Invalid Indonesian phone format
        ];

        $response = $this->actingAs($this->admin)
            ->from(route('admin.profile.edit'))
            ->patch(route('admin.profile.update'), $payload);

        $response->assertRedirect(route('admin.profile.edit'));
        $response->assertSessionHasErrors('no_hp');
    }

    public function test_admin_profile_update_validation_unique_email_and_no_hp(): void
    {
        // Email match another user
        $payload = [
            'nama' => 'Asep Update',
            'email' => $this->otherUser->email,
            'no_hp' => '081298765432',
        ];

        $response = $this->actingAs($this->admin)
            ->from(route('admin.profile.edit'))
            ->patch(route('admin.profile.update'), $payload);

        $response->assertRedirect(route('admin.profile.edit'));
        $response->assertSessionHasErrors('email');

        // Phone number match another user
        $payload = [
            'nama' => 'Asep Update',
            'email' => 'asep@gmail.com',
            'no_hp' => $this->otherUser->no_hp,
        ];

        $response = $this->actingAs($this->admin)
            ->from(route('admin.profile.edit'))
            ->patch(route('admin.profile.update'), $payload);

        $response->assertRedirect(route('admin.profile.edit'));
        $response->assertSessionHasErrors('no_hp');
    }

    public function test_admin_profile_page_renders_safely_when_updated_at_is_null(): void
    {
        // Arrange
        $this->admin->updated_at = null;
        $this->admin->saveQuietly();

        // Act
        $response = $this->actingAs($this->admin)
            ->get(route('admin.profile.edit'));

        // Assert
        $response->assertStatus(200);
        $response->assertSee('Belum pernah');
    }

    public function test_admin_profile_update_fails_when_password_confirmation_mismatches(): void
    {
        // Arrange
        $payload = [
            'nama' => 'Admin Test',
            'email' => 'admin-test@gmail.com',
            'no_hp' => '081234567890',
            'password' => 'Password123',
            'password_confirmation' => 'DifferentPassword123',
        ];

        // Act
        $response = $this->actingAs($this->admin)
            ->from(route('admin.profile.edit'))
            ->patch(route('admin.profile.update'), $payload);

        // Assert
        $response->assertRedirect(route('admin.profile.edit'));
        $response->assertSessionHasErrors('password');
    }
}

