<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('penyewa', function (Blueprint $table) {
            $table->date('tanggal_keluar_seharusnya')->nullable()->after('tanggal_keluar');
        });

        // Data Catch-up untuk penyewa yang sedang aktif saat ini
        $penyewaAktif = DB::table('penyewa')->where('status', 'aktif')->get();
        foreach ($penyewaAktif as $p) {
            $tanggalMasuk = Carbon::parse($p->tanggal_masuk);
            
            if ($p->tipe_sewa === 'harian') {
                $tanggalKeluarSeharusnya = $tanggalMasuk->copy()->addDays($p->durasi)->toDateString();
            } elseif ($p->tipe_sewa === 'mingguan') {
                $tanggalKeluarSeharusnya = $tanggalMasuk->copy()->addWeeks($p->durasi)->toDateString();
            } else {
                // bulanan
                $tanggalKeluarSeharusnya = $tanggalMasuk->copy()->addMonths($p->durasi)->toDateString();
            }

            DB::table('penyewa')->where('id', $p->id)->update([
                'tanggal_keluar_seharusnya' => $tanggalKeluarSeharusnya
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penyewa', function (Blueprint $table) {
            $table->dropColumn('tanggal_keluar_seharusnya');
        });
    }
};
