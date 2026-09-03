<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Pengeluaran;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminPengeluaranTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'nama' => 'Admin Kost',
            'email' => 'admin.pengeluaran.test@example.com',
            'password' => bcrypt('password'),
            'no_hp' => '081234567891',
            'role' => 'admin',
            'is_active' => 1,
        ]);

        $this->user = User::create([
            'nama' => 'Penyewa Biasa',
            'email' => 'penyewa.pengeluaran.test@example.com',
            'password' => bcrypt('password'),
            'no_hp' => '081234567892',
            'role' => 'penyewa',
            'is_active' => 1,
        ]);
    }

    public function test_admin_bisa_mengakses_halaman_pengeluaran(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.pengeluaran.index'));

        $response->assertStatus(200);
        $response->assertSee('Daftar Pengeluaran Operasional Kost');
        $response->assertSee('Tambah Pengeluaran');
    }

    public function test_non_admin_tidak_bisa_mengakses_halaman_pengeluaran(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('admin.pengeluaran.index'));

        $response->assertStatus(403);
    }

    public function test_admin_bisa_menambah_pengeluaran_tanpa_nota(): void
    {
        $payload = [
            'nama_pengeluaran' => 'Beli Sapu dan Pel',
            'kategori' => 'operasional',
            'nominal' => 75000,
            'tanggal_pengeluaran' => date('Y-m-d'),
            'keterangan' => 'Untuk kebersihan area kost umum.',
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('admin.pengeluaran.store'), $payload);

        $response->assertRedirect(route('admin.pengeluaran.index'));
        $response->assertSessionHas('success', 'Pencatatan pengeluaran berhasil ditambahkan.');

        $this->assertDatabaseHas('pengeluaran', [
            'nama_pengeluaran' => 'Beli Sapu dan Pel',
            'kategori' => 'operasional',
            'nominal' => 75000.00,
        ]);

        $pengeluaran = Pengeluaran::where('nama_pengeluaran', 'Beli Sapu dan Pel')->first();
        $this->assertNotNull($pengeluaran);
        $this->assertEquals(date('Y-m-d'), $pengeluaran->tanggal_pengeluaran->format('Y-m-d'));
    }

    public function test_admin_bisa_menambah_pengeluaran_dengan_nota(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->create('nota_beli.jpg', 150, 'image/jpeg');

        $payload = [
            'nama_pengeluaran' => 'Service AC Kamar 104',
            'kategori' => 'maintenance',
            'nominal' => 120000,
            'tanggal_pengeluaran' => date('Y-m-d'),
            'bukti_nota' => $file,
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('admin.pengeluaran.store'), $payload);

        $response->assertRedirect(route('admin.pengeluaran.index'));
        $response->assertSessionHas('success', 'Pencatatan pengeluaran berhasil ditambahkan.');

        $pengeluaran = Pengeluaran::first();
        $this->assertNotNull($pengeluaran->bukti_nota);
        Storage::disk('public')->assertExists($pengeluaran->bukti_nota);
    }

    public function test_validation_saat_tambah_pengeluaran_kolom_wajib_kosong(): void
    {
        $payload = [
            'nama_pengeluaran' => '',
            'kategori' => '',
            'nominal' => '',
            'tanggal_pengeluaran' => '',
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('admin.pengeluaran.store'), $payload);

        $response->assertSessionHasErrors(['nama_pengeluaran', 'kategori', 'nominal', 'tanggal_pengeluaran']);
    }

    public function test_validation_saat_tambah_pengeluaran_format_nota_salah(): void
    {
        $file = UploadedFile::fake()->create('document.pdf', 300, 'application/pdf');

        $payload = [
            'nama_pengeluaran' => 'Pembayaran Wifi',
            'kategori' => 'utilitas',
            'nominal' => 350000,
            'tanggal_pengeluaran' => date('Y-m-d'),
            'bukti_nota' => $file,
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('admin.pengeluaran.store'), $payload);

        $response->assertSessionHasErrors(['bukti_nota']);
    }

    public function test_admin_bisa_mengubah_pengeluaran(): void
    {
        Storage::fake('public');

        $fileLama = UploadedFile::fake()->create('lama.png', 50, 'image/png');
        $pathLama = $fileLama->store('nota_pengeluaran', 'public');

        $pengeluaran = Pengeluaran::create([
            'nama_pengeluaran' => 'Beli Lampu Cadangan',
            'kategori' => 'lainnya',
            'nominal' => 45000,
            'tanggal_pengeluaran' => date('Y-m-d'),
            'bukti_nota' => $pathLama,
        ]);

        Storage::disk('public')->assertExists($pathLama);

        $fileBaru = UploadedFile::fake()->create('baru.jpg', 100, 'image/jpeg');

        $payload = [
            'nama_pengeluaran' => 'Beli Lampu Cadangan Update',
            'kategori' => 'utilitas',
            'nominal' => 50000,
            'tanggal_pengeluaran' => date('Y-m-d'),
            'bukti_nota' => $fileBaru,
        ];

        $response = $this->actingAs($this->admin)
            ->put(route('admin.pengeluaran.update', $pengeluaran->id), $payload);

        $response->assertRedirect(route('admin.pengeluaran.index'));
        $response->assertSessionHas('success', 'Pencatatan pengeluaran berhasil diperbarui.');

        $pengeluaran->refresh();
        $this->assertEquals('Beli Lampu Cadangan Update', $pengeluaran->nama_pengeluaran);
        $this->assertEquals(50000, $pengeluaran->nominal);
        $this->assertEquals('utilitas', $pengeluaran->kategori);
        
        // Assert old file deleted and new file uploaded
        Storage::disk('public')->assertMissing($pathLama);
        Storage::disk('public')->assertExists($pengeluaran->bukti_nota);
    }

    public function test_admin_bisa_menghapus_pengeluaran(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->create('nota.jpg', 100, 'image/jpeg');
        $path = $file->store('nota_pengeluaran', 'public');

        $pengeluaran = Pengeluaran::create([
            'nama_pengeluaran' => 'Service Mesin Air',
            'kategori' => 'maintenance',
            'nominal' => 250000,
            'tanggal_pengeluaran' => date('Y-m-d'),
            'bukti_nota' => $path,
        ]);

        Storage::disk('public')->assertExists($path);

        $response = $this->actingAs($this->admin)
            ->delete(route('admin.pengeluaran.destroy', $pengeluaran->id));

        $response->assertRedirect(route('admin.pengeluaran.index'));
        $response->assertSessionHas('success', 'Pencatatan pengeluaran berhasil dihapus.');

        $this->assertDatabaseMissing('pengeluaran', [
            'id' => $pengeluaran->id,
        ]);
        Storage::disk('public')->assertMissing($path);
    }

    public function test_admin_bisa_melihat_detail_pengeluaran(): void
    {
        $pengeluaran = Pengeluaran::create([
            'nama_pengeluaran' => 'Service Pompa Air',
            'kategori' => 'maintenance',
            'nominal' => 150000,
            'tanggal_pengeluaran' => date('Y-m-d'),
            'keterangan' => 'Pompa air macet di area parkir.',
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.pengeluaran.show', $pengeluaran->id));

        $response->assertStatus(200);
        $response->assertSee('Detail Pengeluaran Operasional');
        $response->assertSee('Service Pompa Air');
        $response->assertSee('Rp 150.000');
    }

    public function test_non_admin_tidak_bisa_melihat_detail_pengeluaran(): void
    {
        $pengeluaran = Pengeluaran::create([
            'nama_pengeluaran' => 'Service Pompa Air',
            'kategori' => 'maintenance',
            'nominal' => 150000,
            'tanggal_pengeluaran' => date('Y-m-d'),
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('admin.pengeluaran.show', $pengeluaran->id));

        $response->assertStatus(403);
    }

    public function test_grouping_query_search_dan_kategori(): void
    {
        // Create matching search but different category
        $p1 = Pengeluaran::create([
            'nama_pengeluaran' => 'Pembayaran Wifi',
            'kategori' => 'utilitas',
            'nominal' => 350000,
            'tanggal_pengeluaran' => date('Y-m-d'),
        ]);

        // Create matching category but different search term
        $p2 = Pengeluaran::create([
            'nama_pengeluaran' => 'Service AC Kamar',
            'kategori' => 'maintenance',
            'nominal' => 150000,
            'tanggal_pengeluaran' => date('Y-m-d'),
        ]);

        // Search for 'Wifi' with category 'maintenance' should yield NO results because of grouping logic (AND)
        $response = $this->actingAs($this->admin)
            ->get(route('admin.pengeluaran.index', [
                'search' => 'Wifi',
                'kategori' => 'maintenance',
            ]));

        $response->assertStatus(200);
        $response->assertDontSee('Pembayaran Wifi');
        $response->assertDontSee('Service AC Kamar');

        // Search for 'Wifi' with category 'utilitas' should yield Pembayaran Wifi
        $response = $this->actingAs($this->admin)
            ->get(route('admin.pengeluaran.index', [
                'search' => 'Wifi',
                'kategori' => 'utilitas',
            ]));

        $response->assertStatus(200);
        $response->assertSee('Pembayaran Wifi');
    }

    public function test_update_pengeluaran_tanpa_unggah_nota_baru_mempertahankan_nota_lama(): void
    {
        Storage::fake('public');

        $fileLama = UploadedFile::fake()->create('nota_lama.jpg', 100, 'image/jpeg');
        $pathLama = $fileLama->store('nota_pengeluaran', 'public');

        $pengeluaran = Pengeluaran::create([
            'nama_pengeluaran' => 'Pembayaran Listrik',
            'kategori' => 'utilitas',
            'nominal' => 500000,
            'tanggal_pengeluaran' => date('Y-m-d'),
            'bukti_nota' => $pathLama,
        ]);

        Storage::disk('public')->assertExists($pathLama);

        $payload = [
            'nama_pengeluaran' => 'Pembayaran Listrik Mei',
            'kategori' => 'utilitas',
            'nominal' => 550000,
            'tanggal_pengeluaran' => date('Y-m-d'),
            // 'bukti_nota' is not provided in payload
        ];

        $response = $this->actingAs($this->admin)
            ->put(route('admin.pengeluaran.update', $pengeluaran->id), $payload);

        $response->assertRedirect(route('admin.pengeluaran.index'));

        $pengeluaran->refresh();
        $this->assertEquals('Pembayaran Listrik Mei', $pengeluaran->nama_pengeluaran);
        $this->assertEquals(550000, $pengeluaran->nominal);
        // Assert the old file is NOT deleted and is still referenced
        $this->assertEquals($pathLama, $pengeluaran->bukti_nota);
        Storage::disk('public')->assertExists($pathLama);
    }

    public function test_admin_bisa_ekspor_pdf_pengeluaran(): void
    {
        Pengeluaran::create([
            'nama_pengeluaran' => 'Service Pompa',
            'kategori' => 'maintenance',
            'nominal' => 150000,
            'tanggal_pengeluaran' => date('Y-m-d'),
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.pengeluaran.exportPdf', ['kategori' => 'maintenance']));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
        $this->assertNotEmpty($response->streamedContent());
    }

    public function test_admin_bisa_ekspor_excel_pengeluaran(): void
    {
        Pengeluaran::create([
            'nama_pengeluaran' => 'Pembayaran Wifi',
            'kategori' => 'utilitas',
            'nominal' => 350000,
            'tanggal_pengeluaran' => date('Y-m-d'),
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.pengeluaran.exportExcel', ['kategori' => 'utilitas']));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=utf-8');
        
        $content = $response->streamedContent();
        $this->assertStringContainsString('Pembayaran Wifi', $content);
        $this->assertStringContainsString('UTILITAS', $content);
    }

    public function test_non_admin_tidak_bisa_ekspor_pengeluaran(): void
    {
        $responsePdf = $this->actingAs($this->user)
            ->get(route('admin.pengeluaran.exportPdf'));
        $responsePdf->assertStatus(403);

        $responseExcel = $this->actingAs($this->user)
            ->get(route('admin.pengeluaran.exportExcel'));
        $responseExcel->assertStatus(403);
    }
}
