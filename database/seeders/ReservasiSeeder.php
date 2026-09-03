<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Kamar;
use App\Models\Reservasi;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class ReservasiSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create a dummy tenant user
        $user = User::updateOrCreate(
            ['email' => 'rudi@example.com'],
            [
                'nama' => 'Rudi Gunawan',
                'password' => Hash::make('password123'),
                'no_hp' => '081234567895',
                'role' => 'penyewa',
                'is_active' => 1,
                'require_password_change' => false,
            ]
        );

        $kamar101 = Kamar::where('nomor_kamar', '101')->first();
        $kamar102 = Kamar::where('nomor_kamar', '102')->first();
        $kamar103 = Kamar::where('nomor_kamar', '103')->first();
        $kamar104 = Kamar::where('nomor_kamar', '104')->first();

        $now = Carbon::now();

        // 2. Pending Reservation
        Reservasi::updateOrCreate(
            ['order_id' => 'RSV-PENDING-RUDI'],
            [
                'user_id' => $user->id,
                'kamar_id' => $kamar101->id,
                'tipe_sewa' => 'bulanan',
                'tanggal_mulai' => $now->toDateString(),
                'tanggal_selesai' => $now->copy()->addMonth()->toDateString(),
                'durasi' => 1,
                'total_harga' => 1400000,
                'status' => 'pending',
                'metode_pembayaran' => 'midtrans',
                'is_dp' => true,
                'nominal_dp' => 420000,
                'nominal_sisa' => 980000,
                'catatan_user' => 'Mohon infokan jika kamar sudah siap ditempati.',
            ]
        );

        // 3. DP Paid Reservation
        Reservasi::updateOrCreate(
            ['order_id' => 'RSV-DP-RUDI'],
            [
                'user_id' => $user->id,
                'kamar_id' => $kamar102->id,
                'tipe_sewa' => 'bulanan',
                'tanggal_mulai' => $now->toDateString(),
                'tanggal_selesai' => $now->copy()->addMonth()->toDateString(),
                'durasi' => 1,
                'total_harga' => 1400000,
                'status' => 'dp',
                'metode_pembayaran' => 'midtrans',
                'is_dp' => true,
                'nominal_dp' => 420000,
                'nominal_sisa' => 980000,
                'catatan_user' => 'Saya sudah membayar DP lewat Midtrans, terima kasih.',
            ]
        );

        // 4. Lunas (Fully Paid) Reservation
        Reservasi::updateOrCreate(
            ['order_id' => 'RSV-LUNAS-RUDI'],
            [
                'user_id' => $user->id,
                'kamar_id' => $kamar103->id,
                'tipe_sewa' => 'bulanan',
                'tanggal_mulai' => $now->toDateString(),
                'tanggal_selesai' => $now->copy()->addMonth()->toDateString(),
                'durasi' => 1,
                'total_harga' => 1400000,
                'status' => 'lunas',
                'metode_pembayaran' => 'midtrans',
                'is_dp' => false,
                'nominal_dp' => 0,
                'nominal_sisa' => 0,
                'catatan_user' => 'Saya membayar lunas langsung lunas penuh.',
            ]
        );

        // 5. Batal (Cancelled) Reservation
        Reservasi::updateOrCreate(
            ['order_id' => 'RSV-BATAL-RUDI'],
            [
                'user_id' => $user->id,
                'kamar_id' => $kamar104->id,
                'tipe_sewa' => 'bulanan',
                'tanggal_mulai' => $now->toDateString(),
                'tanggal_selesai' => $now->copy()->addMonth()->toDateString(),
                'durasi' => 1,
                'total_harga' => 950000,
                'status' => 'batal',
                'metode_pembayaran' => 'cash',
                'is_dp' => false,
                'nominal_dp' => 0,
                'nominal_sisa' => 0,
                'catatan_user' => 'Saya ingin membatalkan sewa karena ada dinas luar kota.',
            ]
        );
    }
}
