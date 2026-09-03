<?php

namespace Tests\Feature;

use App\Models\Gallery;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminGalleryTest extends TestCase
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

    public function test_gallery_public_section_displays_active_galleries(): void
    {
        Gallery::create([
            'judul' => 'Kamar VIP Baru',
            'deskripsi' => 'Deskripsi Kamar VIP Baru',
            'foto' => 'https://images.unsplash.com/photo-1618773928121-c32242e63f39?auto=format&fit=crop&w=800&q=80',
            'urutan' => 1,
            'is_active' => true,
        ]);

        Gallery::create([
            'judul' => 'Kamar Standar Rahasia',
            'deskripsi' => 'Deskripsi Kamar Standar Rahasia',
            'foto' => 'https://images.unsplash.com/photo-1598928506311-c55ded91a20c?auto=format&fit=crop&w=800&q=80',
            'urutan' => 2,
            'is_active' => false,
        ]);

        $response = $this->get(route('landing.index'));

        $response->assertStatus(200);
        $response->assertSee('Kamar VIP Baru');
        $response->assertDontSee('Kamar Standar Rahasia');
    }

    public function test_admin_can_manage_gallery_crud(): void
    {
        Storage::fake('public');

        // 1. View Index
        $response = $this->actingAs($this->admin)
            ->get(route('admin.gallery.index'));
        $response->assertStatus(200);

        // 2. View Create Form
        $response = $this->actingAs($this->admin)
            ->get(route('admin.gallery.create'));
        $response->assertStatus(200);

        // 3. Store new gallery item (with file upload)
        $file = UploadedFile::fake()->create('room1.jpg', 100, 'image/jpeg');
        $response = $this->actingAs($this->admin)
            ->post(route('admin.gallery.store'), [
                'judul' => 'Foto Kamar Keren',
                'deskripsi' => 'Kamar dengan pencahayaan yang sangat bagus.',
                'foto' => $file,
                'urutan' => 1,
                'is_active' => 1,
            ]);

        $response->assertRedirect(route('admin.gallery.index'));
        $response->assertSessionHas('success', 'Galeri berhasil ditambahkan!');

        $gallery = Gallery::first();
        $this->assertNotNull($gallery);
        $this->assertEquals('Foto Kamar Keren', $gallery->judul);
        $this->assertStringContainsString('galleries/', $gallery->foto);

        Storage::disk('public')->assertExists($gallery->foto);

        // 4. View Edit Form
        $response = $this->actingAs($this->admin)
            ->get(route('admin.gallery.edit', $gallery->id));
        $response->assertStatus(200);

        // 5. Update gallery item (with external URL)
        $response = $this->actingAs($this->admin)
            ->put(route('admin.gallery.update', $gallery->id), [
                'judul' => 'Foto Kamar Keren Diperbarui',
                'deskripsi' => 'Kamar dengan sirkulasi udara luar biasa.',
                'foto_url' => 'https://images.unsplash.com/photo-1590490360182-c33d57733427?auto=format&fit=crop&w=800&q=80',
                'urutan' => 3,
                'is_active' => 1,
            ]);

        $response->assertRedirect(route('admin.gallery.index'));
        $response->assertSessionHas('success', 'Galeri berhasil diperbarui!');

        $gallery->refresh();
        $this->assertEquals('Foto Kamar Keren Diperbarui', $gallery->judul);
        $this->assertEquals('https://images.unsplash.com/photo-1590490360182-c33d57733427?auto=format&fit=crop&w=800&q=80', $gallery->foto);

        // 5.5. Update title/description only while keeping URL (submitting the existing URL)
        $response = $this->actingAs($this->admin)
            ->put(route('admin.gallery.update', $gallery->id), [
                'judul' => 'Foto Kamar Keren Diperbarui Sekali Lagi',
                'deskripsi' => 'Kamar dengan sirkulasi udara luar biasa dan bersih.',
                'foto' => null,
                'foto_url' => 'https://images.unsplash.com/photo-1590490360182-c33d57733427?auto=format&fit=crop&w=800&q=80',
                'urutan' => 3,
                'is_active' => 1,
            ]);

        $response->assertRedirect(route('admin.gallery.index'));
        $response->assertSessionHas('success', 'Galeri berhasil diperbarui!');

        $gallery->refresh();
        $this->assertEquals('Foto Kamar Keren Diperbarui Sekali Lagi', $gallery->judul);
        $this->assertEquals('https://images.unsplash.com/photo-1590490360182-c33d57733427?auto=format&fit=crop&w=800&q=80', $gallery->foto);

        // 5.6. Change photo back to a file
        $file2 = UploadedFile::fake()->create('room2.jpg', 100, 'image/jpeg');
        $response = $this->actingAs($this->admin)
            ->put(route('admin.gallery.update', $gallery->id), [
                'judul' => 'Foto Kamar Keren File Baru',
                'foto' => $file2,
                'foto_url' => '',
                'urutan' => 3,
                'is_active' => 1,
            ]);
        $response->assertRedirect(route('admin.gallery.index'));
        $gallery->refresh();
        $this->assertStringContainsString('galleries/', $gallery->foto);

        // 5.7. Update text-only while keeping the local file (both foto and foto_url are empty)
        $response = $this->actingAs($this->admin)
            ->put(route('admin.gallery.update', $gallery->id), [
                'judul' => 'Foto Kamar Keren File Baru Diperbarui',
                'foto' => null,
                'foto_url' => '',
                'urutan' => 3,
                'is_active' => 1,
            ]);
        $response->assertRedirect(route('admin.gallery.index'));
        $gallery->refresh();
        $this->assertStringContainsString('galleries/', $gallery->foto);

        // 6. Delete gallery item
        $response = $this->actingAs($this->admin)
            ->delete(route('admin.gallery.destroy', $gallery->id));

        $response->assertRedirect(route('admin.gallery.index'));
        $response->assertSessionHas('success', 'Galeri berhasil dihapus!');

        $this->assertDatabaseMissing('galleries', [
            'id' => $gallery->id,
        ]);
    }

    public function test_admin_cannot_clear_photo_url_without_replacement(): void
    {
        $gallery = Gallery::create([
            'judul' => 'Kamar URL Awal',
            'foto' => 'https://images.unsplash.com/photo-1590490360182-c33d57733427?auto=format&fit=crop&w=800&q=80',
            'urutan' => 1,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)
            ->from(route('admin.gallery.edit', $gallery->id))
            ->put(route('admin.gallery.update', $gallery->id), [
                'judul' => 'Kamar URL Awal Diperbarui',
                'foto' => null,
                'foto_url' => '', // Clear the URL
                'urutan' => 1,
                'is_active' => 1,
            ]);

        $response->assertRedirect(route('admin.gallery.edit', $gallery->id));
        $response->assertSessionHasErrors('foto_url');

        $gallery->refresh();
        $this->assertEquals('https://images.unsplash.com/photo-1590490360182-c33d57733427?auto=format&fit=crop&w=800&q=80', $gallery->foto);
    }

    public function test_guest_and_tenant_cannot_access_gallery_admin_crud(): void
    {
        // Create a test gallery item
        $gallery = Gallery::create([
            'judul' => 'Kamar Uji',
            'foto' => 'https://example.com/test.jpg',
            'urutan' => 1,
            'is_active' => true,
        ]);

        // Guest index redirect
        $response = $this->get(route('admin.gallery.index'));
        $response->assertRedirect(route('admin.login'));

        // Tenant index forbidden (403)
        $response = $this->actingAs($this->penyewa)
            ->get(route('admin.gallery.index'));
        $response->assertStatus(403);

        // Tenant store forbidden (403)
        $response = $this->actingAs($this->penyewa)
            ->post(route('admin.gallery.store'), [
                'judul' => 'Kamar Coba',
                'foto_url' => 'https://example.com/test2.jpg',
                'urutan' => 2,
            ]);
        $response->assertStatus(403);
    }
}
