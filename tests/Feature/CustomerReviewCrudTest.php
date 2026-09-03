<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\CustomerReview;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CustomerReviewCrudTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'nama' => 'Admin Kost',
            'email' => 'admin.reviews@example.com',
            'password' => bcrypt('password'),
            'no_hp' => '081234567891',
            'role' => 'admin',
            'is_active' => 1,
        ]);

        $this->user = User::create([
            'nama' => 'Penyewa Biasa',
            'email' => 'penyewa@example.com',
            'password' => bcrypt('password'),
            'no_hp' => '081234567892',
            'role' => 'penyewa',
            'is_active' => 1,
        ]);
    }

    public function test_admin_bisa_mengakses_halaman_manajemen_review(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.reviews.index'));

        $response->assertStatus(200);
        $response->assertSee('Daftar Review Pelanggan');
        $response->assertSee('Tambah Review Baru');
    }

    public function test_non_admin_tidak_bisa_mengakses_halaman_manajemen_review(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('admin.reviews.index'));

        $response->assertStatus(403);
    }

    public function test_admin_bisa_menambah_review_tanpa_foto(): void
    {
        $payload = [
            'nama' => 'Ahmad Yusuf',
            'pekerjaan' => 'Mahasiswa Rantau UNDIP',
            'bintang' => 5,
            'ulasan' => 'Kost yang sangat recommended. Nyaman dan dekat kampus.',
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('admin.reviews.store'), $payload);

        $response->assertRedirect(route('admin.reviews.index'));
        $response->assertSessionHas('success', 'Review berhasil ditambahkan!');

        $this->assertDatabaseHas('customer_reviews', [
            'nama' => 'Ahmad Yusuf',
            'ulasan' => 'Kost yang sangat recommended. Nyaman dan dekat kampus.',
            'bintang' => 5,
        ]);

        // Verifikasi tampil di landing page
        $landingResponse = $this->get(route('landing.index'));
        $landingResponse->assertStatus(200);
        $landingResponse->assertSee('Ahmad Yusuf');
        $landingResponse->assertSee('Mahasiswa Rantau UNDIP');
        $landingResponse->assertSee('Kost yang sangat recommended. Nyaman dan dekat kampus.');
    }

    public function test_admin_bisa_menambah_review_dengan_foto(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->create('ahmad.jpg', 100, 'image/jpeg');

        $payload = [
            'nama' => 'Ahmad Yusuf',
            'pekerjaan' => 'Mahasiswa Rantau UNDIP',
            'bintang' => 5,
            'ulasan' => 'Kost yang sangat recommended. Nyaman dan dekat kampus.',
            'foto' => $file,
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('admin.reviews.store'), $payload);

        $response->assertRedirect(route('admin.reviews.index'));
        $response->assertSessionHas('success', 'Review berhasil ditambahkan!');

        $review = CustomerReview::first();
        $this->assertNotNull($review->foto);
        Storage::disk('public')->assertExists($review->foto);
    }

    public function test_validation_saat_tambah_review_kolom_wajib_kosong(): void
    {
        $payload = [
            'nama' => '', // Kosong
            'pekerjaan' => 'Mahasiswa Rantau',
            'bintang' => '', // Kosong
            'ulasan' => '', // Kosong
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('admin.reviews.store'), $payload);

        $response->assertSessionHasErrors(['nama', 'bintang', 'ulasan']);
    }

    public function test_validation_saat_tambah_review_format_foto_salah(): void
    {
        $file = UploadedFile::fake()->create('document.pdf', 500, 'application/pdf');

        $payload = [
            'nama' => 'Ahmad Yusuf',
            'pekerjaan' => 'Mahasiswa Rantau',
            'bintang' => 5,
            'ulasan' => 'Kost yang sangat recommended.',
            'foto' => $file, // File PDF
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('admin.reviews.store'), $payload);

        $response->assertSessionHasErrors(['foto']);
    }

    public function test_admin_bisa_mengubah_review(): void
    {
        Storage::fake('public');

        $review = CustomerReview::create([
            'nama' => 'Lama',
            'pekerjaan' => 'Pekerja Rantau',
            'bintang' => 4,
            'ulasan' => 'Ulasan lama.',
            'foto' => null,
        ]);

        $file = UploadedFile::fake()->create('baru.png', 100, 'image/png');

        $payload = [
            'nama' => 'Baru',
            'pekerjaan' => 'Pekerja Rantau Terupdate',
            'bintang' => 5,
            'ulasan' => 'Ulasan yang sudah diperbarui.',
            'foto' => $file,
        ];

        $response = $this->actingAs($this->admin)
            ->put(route('admin.reviews.update', $review->id), $payload);

        $response->assertRedirect(route('admin.reviews.index'));
        $response->assertSessionHas('success', 'Review berhasil diperbarui!');

        $review->refresh();
        $this->assertEquals('Baru', $review->nama);
        $this->assertEquals('Ulasan yang sudah diperbarui.', $review->ulasan);
        $this->assertEquals(5, $review->bintang);
        $this->assertNotNull($review->foto);
        Storage::disk('public')->assertExists($review->foto);
    }

    public function test_admin_bisa_menghapus_review(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->create('review.jpg', 100, 'image/jpeg');
        $path = $file->store('reviews', 'public');

        $review = CustomerReview::create([
            'nama' => 'Ahmad Yusuf',
            'pekerjaan' => 'Mahasiswa',
            'bintang' => 5,
            'ulasan' => 'Kost yang sangat recommended.',
            'foto' => $path,
        ]);

        Storage::disk('public')->assertExists($path);

        $response = $this->actingAs($this->admin)
            ->delete(route('admin.reviews.destroy', $review->id));

        $response->assertRedirect(route('admin.reviews.index'));
        $response->assertSessionHas('success', 'Review berhasil dihapus!');

        $this->assertDatabaseMissing('customer_reviews', [
            'id' => $review->id,
        ]);
        Storage::disk('public')->assertMissing($path);
    }

    public function test_admin_bisa_mengubah_review_tanpa_mengganti_foto(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->create('lama.jpg', 100, 'image/jpeg');
        $path = $file->store('reviews', 'public');

        $review = CustomerReview::create([
            'nama' => 'Lama',
            'pekerjaan' => 'Pekerja Rantau',
            'bintang' => 4,
            'ulasan' => 'Ulasan lama.',
            'foto' => $path,
        ]);

        $payload = [
            'nama' => 'Baru',
            'pekerjaan' => 'Pekerja Rantau Terupdate',
            'bintang' => 5,
            'ulasan' => 'Ulasan yang sudah diperbarui.',
            'foto' => null, // Tidak mengganti foto
        ];

        $response = $this->actingAs($this->admin)
            ->put(route('admin.reviews.update', $review->id), $payload);

        $response->assertRedirect(route('admin.reviews.index'));
        $response->assertSessionHas('success', 'Review berhasil diperbarui!');

        $review->refresh();
        $this->assertEquals('Baru', $review->nama);
        $this->assertEquals('Ulasan yang sudah diperbarui.', $review->ulasan);
        $this->assertEquals(5, $review->bintang);
        $this->assertEquals($path, $review->foto); // Foto lama tetap dipertahankan
        Storage::disk('public')->assertExists($path);
    }

    public function test_halaman_testimoni_public_bisa_diakses_dan_menampilkan_data(): void
    {
        // Buat review dummy
        CustomerReview::create([
            'nama' => 'Budi Santoso',
            'pekerjaan' => 'Pekerja Kantoran',
            'bintang' => 4,
            'ulasan' => 'Tempatnya bersih dan tenang.',
            'foto' => null,
        ]);

        $response = $this->get(route('landing.testimoni'));

        $response->assertStatus(200);
        $response->assertSee('TESTIMONI PENYEWA KOST 💬');
        $response->assertSee('Budi Santoso');
        $response->assertSee('Pekerja Kantoran');
        $response->assertSee('Tempatnya bersih dan tenang.');
    }
}
