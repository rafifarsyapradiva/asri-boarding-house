<?php

namespace Tests\Unit\Requests\Admin;

use App\Http\Requests\Admin\KonfirmasiReservasiRequest;
use App\Http\Requests\Admin\StorePengeluaranRequest;
use App\Http\Requests\Admin\UpdatePengeluaranRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class AdminRequestsTest extends TestCase
{
    use RefreshDatabase;

    public function test_konfirmasi_reservasi_request_sanitizes_deposit_and_validates(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $payload = [
            'nik' => '3171012345678901',
            'nama_wali' => 'Budi Santoso',
            'no_wali' => '081234567890',
            'deposit' => 'Rp 500.000',
            'catatan_admin' => 'Konfirmasi via admin',
        ];

        $request = new KonfirmasiReservasiRequest();
        $request->setUserResolver(fn() => $admin);
        $request->merge($payload);

        $reflection = new \ReflectionClass(KonfirmasiReservasiRequest::class);
        $method = $reflection->getMethod('prepareForValidation');
        $method->setAccessible(true);
        $method->invoke($request);

        $validator = Validator::make($request->all(), $request->rules(), $request->messages());

        $this->assertTrue($request->authorize());
        $this->assertFalse($validator->fails());
        $this->assertEquals(500000, $request->input('deposit'));
    }

    public function test_konfirmasi_reservasi_request_denies_non_admin(): void
    {
        $tenant = User::factory()->create(['role' => 'penyewa']);

        $request = new KonfirmasiReservasiRequest();
        $request->setUserResolver(fn() => $tenant);

        $this->assertFalse($request->authorize());
    }

    public function test_store_pengeluaran_request_sanitizes_nominal_and_validates(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $payload = [
            'nama_pengeluaran' => 'Listrik Bulanan',
            'kategori' => 'utilitas',
            'nominal' => '1.250.000,00',
            'tanggal_pengeluaran' => '2026-07-30',
        ];

        $request = new StorePengeluaranRequest();
        $request->setUserResolver(fn() => $admin);
        $request->merge($payload);

        $reflection = new \ReflectionClass(StorePengeluaranRequest::class);
        $method = $reflection->getMethod('prepareForValidation');
        $method->setAccessible(true);
        $method->invoke($request);

        $validator = Validator::make($request->all(), $request->rules(), $request->messages());

        $this->assertTrue($request->authorize());
        $this->assertFalse($validator->fails());
        $this->assertEquals(1250000, $request->input('nominal'));
    }

    public function test_update_pengeluaran_request_inherits_store_request_validation(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $request = new UpdatePengeluaranRequest();
        $request->setUserResolver(fn() => $admin);

        $this->assertInstanceOf(StorePengeluaranRequest::class, $request);
        $this->assertTrue($request->authorize());

        $payload = [
            'nama_pengeluaran' => '',
            'kategori' => 'kategori_palsu',
            'nominal' => '-1000',
            'tanggal_pengeluaran' => 'bukan-tanggal',
        ];

        $validator = Validator::make($payload, $request->rules(), $request->messages());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('nama_pengeluaran', $validator->errors()->toArray());
        $this->assertArrayHasKey('kategori', $validator->errors()->toArray());
        $this->assertArrayHasKey('nominal', $validator->errors()->toArray());
        $this->assertArrayHasKey('tanggal_pengeluaran', $validator->errors()->toArray());
    }
}
