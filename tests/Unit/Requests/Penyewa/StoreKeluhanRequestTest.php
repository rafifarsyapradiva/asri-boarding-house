<?php

namespace Tests\Unit\Requests\Penyewa;

use App\Http\Requests\Penyewa\StoreKeluhanRequest;
use App\Models\Kamar;
use App\Models\Penyewa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class StoreKeluhanRequestTest extends TestCase
{
    use RefreshDatabase;

    private StoreKeluhanRequest $request;

    protected function setUp(): void
    {
        parent::setUp();
        $this->request = new StoreKeluhanRequest();
    }

    private function createKamar(): Kamar
    {
        return Kamar::create([
            'nomor_kamar' => '101',
            'lantai' => 1,
            'tipe' => 'standar',
            'luas_m2' => 12,
            'harga_bulan' => 1000000,
            'status' => 'terisi',
        ]);
    }

    private function createPenyewa(User $user, string $status = 'aktif'): Penyewa
    {
        $kamar = $this->createKamar();

        return Penyewa::create([
            'user_id' => $user->id,
            'kamar_id' => $kamar->id,
            'nik' => '320123456789' . rand(1000, 9999),
            'tanggal_masuk' => now(),
            'status' => $status,
            'tanggal_billing' => 1,
            'deposit' => 300000,
            'no_wali' => '081234567890',
            'nama_wali' => 'Nama Wali Test',
        ]);
    }

    #[Test]
    public function authorize_fails_for_null_user()
    {
        $this->request->setUserResolver(fn () => null);

        $this->assertFalse($this->request->authorize());
    }

    #[Test]
    public function authorize_fails_for_user_without_tenant_profile()
    {
        $user = User::factory()->create();

        $this->request->setUserResolver(fn () => $user);

        $this->assertFalse($this->request->authorize());
    }

    #[Test]
    public function authorize_fails_for_user_with_inactive_tenant_profile()
    {
        $user = User::factory()->create();
        $this->createPenyewa($user, 'nonaktif');

        $this->request->setUserResolver(fn () => $user);

        $this->assertFalse($this->request->authorize());
    }

    #[Test]
    public function authorize_passes_for_active_tenant()
    {
        $user = User::factory()->create();
        $this->createPenyewa($user, 'aktif');

        $this->request->setUserResolver(fn () => $user);

        $this->assertTrue($this->request->authorize());
    }

    #[Test]
    public function validation_passes_with_valid_data()
    {
        $data = [
            'judul' => 'Kamar Mandi Bocor',
            'kategori' => 'kamar',
            'deskripsi' => 'Kran air di kamar mandi terus menetes dan tidak bisa dimatikan.',
            'foto_bukti' => UploadedFile::fake()->create('bukti.jpg', 500, 'image/jpeg'),
        ];

        $validator = Validator::make($data, $this->request->rules(), $this->request->messages());

        $this->assertTrue($validator->passes());
    }

    #[Test]
    public function validation_fails_when_judul_is_too_short()
    {
        $data = [
            'judul' => 'AC',
            'kategori' => 'kamar',
            'deskripsi' => 'Deskripsi keluhan yang valid dan panjang.',
        ];

        $validator = Validator::make($data, $this->request->rules(), $this->request->messages());

        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('judul', $validator->errors()->toArray());
        $this->assertEquals('Judul keluhan minimal 5 karakter.', $validator->errors()->first('judul'));
    }

    #[Test]
    public function validation_fails_when_kategori_is_invalid()
    {
        $data = [
            'judul' => 'Keluhan Fasilitas',
            'kategori' => 'kategori_sembarangan',
            'deskripsi' => 'Deskripsi keluhan yang valid dan panjang.',
        ];

        $validator = Validator::make($data, $this->request->rules(), $this->request->messages());

        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('kategori', $validator->errors()->toArray());
        $this->assertEquals('Kategori keluhan tidak valid.', $validator->errors()->first('kategori'));
    }

    #[Test]
    public function validation_fails_when_deskripsi_is_too_short()
    {
        $data = [
            'judul' => 'Keluhan Pendek',
            'kategori' => 'kebersihan',
            'deskripsi' => 'Pendek',
        ];

        $validator = Validator::make($data, $this->request->rules(), $this->request->messages());

        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('deskripsi', $validator->errors()->toArray());
        $this->assertEquals('Deskripsi keluhan minimal 10 karakter.', $validator->errors()->first('deskripsi'));
    }

    #[Test]
    public function validation_fails_when_foto_bukti_is_not_an_image()
    {
        $data = [
            'judul' => 'Lampu Rusak Halaman',
            'kategori' => 'fasilitas_bersama',
            'deskripsi' => 'Lampu taman belakang mati total.',
            'foto_bukti' => UploadedFile::fake()->create('dokumen.pdf', 500, 'application/pdf'),
        ];

        $validator = Validator::make($data, $this->request->rules(), $this->request->messages());

        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('foto_bukti', $validator->errors()->toArray());
    }

    #[Test]
    public function prepare_for_validation_sanitizes_html_and_trims_whitespace()
    {
        $request = StoreKeluhanRequest::create('/penyewa/keluhan', 'POST', [
            'judul' => '  <b>AC Kamar Rusak</b>  ',
            'kategori' => 'kamar',
            'deskripsi' => '  <script>alert(1)</script>Pendingin ruangan tidak menyala sejak sore.  ',
        ]);

        $method = new \ReflectionMethod(StoreKeluhanRequest::class, 'prepareForValidation');
        $method->setAccessible(true);
        $method->invoke($request);

        $this->assertEquals('AC Kamar Rusak', $request->input('judul'));
        $this->assertEquals('alert(1)Pendingin ruangan tidak menyala sejak sore.', $request->input('deskripsi'));
    }
}
