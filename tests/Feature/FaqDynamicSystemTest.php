<?php

namespace Tests\Feature;

use App\Models\Faq;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FaqDynamicSystemTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $penyewa;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'nama' => 'Admin Kost',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'no_hp' => '081234567891',
            'role' => 'admin',
            'is_active' => 1,
            'require_password_change' => 0, // Bypass force password change middleware
        ]);

        $this->penyewa = User::create([
            'nama' => 'Penyewa Kost',
            'email' => 'penyewa@example.com',
            'password' => bcrypt('password'),
            'no_hp' => '081234567892',
            'role' => 'penyewa',
            'is_active' => 1,
            'require_password_change' => 0,
        ]);
    }

    public function test_faq_public_page_displays_active_faqs(): void
    {
        Faq::create([
            'pertanyaan' => 'Pertanyaan Aktif 1',
            'jawaban' => 'Jawaban Aktif 1',
            'urutan' => 1,
            'is_active' => 1,
        ]);

        Faq::create([
            'pertanyaan' => 'Pertanyaan Non-Aktif 2',
            'jawaban' => 'Jawaban Non-Aktif 2',
            'urutan' => 2,
            'is_active' => 0,
        ]);

        $response = $this->get(route('landing.faq'));

        $response->assertStatus(200);
        $response->assertSee('Pertanyaan Aktif 1');
        $response->assertDontSee('Pertanyaan Non-Aktif 2');
    }

    public function test_landing_page_displays_up_to_five_active_faqs(): void
    {
        // Create 6 active FAQs
        for ($i = 1; $i <= 6; $i++) {
            Faq::create([
                'pertanyaan' => "Pertanyaan Aktif Ke-{$i}",
                'jawaban' => "Jawaban Ke-{$i}",
                'urutan' => $i,
                'is_active' => 1,
            ]);
        }

        $response = $this->get(route('landing.index'));

        $response->assertStatus(200);
        $response->assertSee('Pertanyaan Aktif Ke-1');
        $response->assertSee('Pertanyaan Aktif Ke-5');
        // The 6th FAQ should not be displayed on the landing page (take(5))
        $response->assertDontSee('Pertanyaan Ke-6');
    }

    public function test_admin_can_manage_faq_crud(): void
    {
        // 1. View Index
        $response = $this->actingAs($this->admin)
            ->get(route('admin.faq.index'));
        $response->assertStatus(200);

        // 2. Create/Store
        $response = $this->actingAs($this->admin)
            ->post(route('admin.faq.store'), [
                'pertanyaan' => 'Pertanyaan Baru',
                'jawaban' => 'Jawaban Baru',
                'urutan' => 10,
                'is_active' => 1,
            ]);

        $response->assertRedirect(route('admin.faq.index'));
        $response->assertSessionHas('success', 'FAQ berhasil ditambahkan!');
        
        $this->assertDatabaseHas('faqs', [
            'pertanyaan' => 'Pertanyaan Baru',
            'jawaban' => 'Jawaban Baru',
            'urutan' => 10,
            'is_active' => 1,
        ]);

        $faq = Faq::first();

        // 3. Edit/Update
        $response = $this->actingAs($this->admin)
            ->put(route('admin.faq.update', $faq->id), [
                'pertanyaan' => 'Pertanyaan Diubah',
                'jawaban' => 'Jawaban Diubah',
                'urutan' => 5,
                'is_active' => 0,
            ]);

        $response->assertRedirect(route('admin.faq.index'));
        $response->assertSessionHas('success', 'FAQ berhasil diperbarui!');

        $this->assertDatabaseHas('faqs', [
            'id' => $faq->id,
            'pertanyaan' => 'Pertanyaan Diubah',
            'is_active' => 0,
        ]);

        // 4. Delete
        $response = $this->actingAs($this->admin)
            ->delete(route('admin.faq.destroy', $faq->id));

        $response->assertRedirect(route('admin.faq.index'));
        $response->assertSessionHas('success', 'FAQ berhasil dihapus!');

        $this->assertDatabaseMissing('faqs', [
            'id' => $faq->id,
        ]);
    }

    public function test_guest_and_tenant_cannot_access_faq_admin_crud(): void
    {
        // Guest index
        $response = $this->get(route('admin.faq.index'));
        $response->assertRedirect(route('admin.login'));

        // Tenant index
        $response = $this->actingAs($this->penyewa)
            ->get(route('admin.faq.index'));
        $response->assertStatus(403);
    }
}
