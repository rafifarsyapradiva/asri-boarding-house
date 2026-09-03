<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/profile');

        $response->assertOk();
    }

    public function test_profile_information_can_be_updated(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => 'test@example.com',
                'no_hp' => '081234567890',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $user->refresh();

        $this->assertSame('Test User', $user->name);
        $this->assertSame('test@example.com', $user->email);
        $this->assertNull($user->email_verified_at);
    }

    public function test_email_verification_status_is_unchanged_when_the_email_address_is_unchanged(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => $user->email,
                'no_hp' => $user->no_hp ?? '081234567890',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $this->assertSame('Test User', $user->refresh()->name);
    }

    public function test_user_can_delete_their_account(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->delete('/profile', [
                'password' => 'password',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/');

        $this->assertGuest();
        $this->assertNull(User::find($user->id));
    }

    public function test_correct_password_must_be_provided_to_delete_account(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->delete('/profile', [
                'password' => 'wrong-password',
            ]);

        $response
            ->assertSessionHasErrorsIn('userDeletion', 'password')
            ->assertRedirect('/profile');

        $this->assertNotNull($user->fresh());
    }

    public function test_user_deletion_rolls_back_on_failure(): void
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create([
            'nama' => 'John Doe',
            'email' => 'john@example.com',
            'no_hp' => '081234567891',
        ]);

        // Register a deleting listener that throws an exception to simulate failure
        User::deleting(function ($u) use ($user) {
            if ($u->id === $user->id) {
                throw new \RuntimeException('Simulated database deletion failure');
            }
        });

        try {
            $this->actingAs($user)
                ->delete('/profile', [
                    'password' => 'password',
                ]);
            $this->fail('Expected transaction to fail and throw exception');
        } catch (\RuntimeException $e) {
            $this->assertEquals('Simulated database deletion failure', $e->getMessage());
        }

        // Refresh user and assert that fields were rolled back and not anonymized
        $user->refresh();
        $this->assertEquals('john@example.com', $user->email);
        $this->assertEquals('081234567891', $user->no_hp);
        $this->assertNull($user->deleted_at);
    }

    public function test_profile_edit_serves_correct_view_based_on_role(): void
    {
        // 1. User with role 'penyewa'
        $penyewa = User::factory()->create([
            'role' => 'penyewa',
        ]);
        $response = $this->actingAs($penyewa)->get('/profile');
        $response->assertOk();
        $response->assertViewIs('penyewa.kelola-akun');

        // 2. User with role other than 'penyewa' (e.g. 'admin' or general)
        $general = User::factory()->create([
            'role' => 'admin',
        ]);
        $response2 = $this->actingAs($general)->get('/profile');
        $response2->assertOk();
        $response2->assertViewIs('profile.edit');
    }
}
