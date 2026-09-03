<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $driver = DB::getDriverName();

        // 1. users table: Drop redundant indexes and apply virtual column unique indexes
        Schema::table('users', function (Blueprint $table) use ($driver) {
            $table->dropUnique('users_email_unique');
            $table->dropUnique('users_no_hp_unique');
            $table->dropUnique('users_nik_unique');
            if ($driver === 'mysql') {
                $table->dropIndex('idx_role');
            }

            $table->string('active_email', 150)->nullable()->virtualAs("CASE WHEN deleted_at IS NULL THEN email ELSE NULL END")->after('email');
            $table->string('active_no_hp', 20)->nullable()->virtualAs("CASE WHEN deleted_at IS NULL THEN no_hp ELSE NULL END")->after('no_hp');
            $table->string('active_nik', 20)->nullable()->virtualAs("CASE WHEN deleted_at IS NULL THEN nik ELSE NULL END")->after('nik');

            $table->unique('active_email', 'uq_users_active_email');
            $table->unique('active_no_hp', 'uq_users_active_no_hp');
            $table->unique('active_nik', 'uq_users_active_nik');
        });

        // 2. kamar table: Drop redundant indexes and apply virtual column unique index
        Schema::table('kamar', function (Blueprint $table) use ($driver) {
            $table->dropUnique('kamar_nomor_kamar_unique');
            if ($driver === 'mysql') {
                $table->dropIndex('idx_status');
            }

            $table->string('active_nomor_kamar', 50)->nullable()->virtualAs("CASE WHEN deleted_at IS NULL THEN nomor_kamar ELSE NULL END")->after('nomor_kamar');
            $table->unique('active_nomor_kamar', 'uq_kamar_active_nomor');
        });

        // 3. penyewa table: Drop redundant indexes and apply virtual column unique index
        Schema::table('penyewa', function (Blueprint $table) use ($driver) {
            $table->dropUnique('penyewa_nik_unique');
            if ($driver === 'mysql') {
                $table->dropIndex('idx_penyewa_user_deleted_at');
            }

            $table->string('active_nik', 20)->nullable()->virtualAs("CASE WHEN deleted_at IS NULL THEN nik ELSE NULL END")->after('nik');
            $table->unique('active_nik', 'uq_penyewa_active_nik');
        });

        // 4. reservasi table: Drop redundant indexes and apply virtual column unique index
        Schema::table('reservasi', function (Blueprint $table) use ($driver) {
            $table->dropUnique('reservasi_order_id_unique');
            if ($driver === 'mysql') {
                $table->dropIndex('reservasi_status_index');
                $table->dropIndex('reservasi_kamar_id_index');
            }

            $table->string('active_order_id', 100)->nullable()->virtualAs("CASE WHEN deleted_at IS NULL THEN order_id ELSE NULL END")->after('order_id');
            $table->unique('active_order_id', 'uq_reservasi_active_order_id');
        });

        // 5. guest_chat_messages table: Add composite index
        Schema::table('guest_chat_messages', function (Blueprint $table) {
            $table->index(['guest_chat_thread_id', 'id'], 'idx_guest_chat_messages_thread_id_id');
        });

        // 6. notifikasi_khusus table: Add composite index
        Schema::table('notifikasi_khusus', function (Blueprint $table) {
            $table->index(['user_id', 'created_at'], 'idx_notifikasi_khusus_user_created');
        });

        // 7. pengeluaran table: Add date index
        Schema::table('pengeluaran', function (Blueprint $table) {
            $table->index('tanggal_pengeluaran', 'idx_pengeluaran_tanggal');
        });

        // 8. pembayaran table: Add date & status index
        Schema::table('pembayaran', function (Blueprint $table) {
            $table->index(['status_midtrans', 'tanggal_bayar'], 'idx_pembayaran_tanggal_status');
        });

        // 9. whatsapp_clicks table: Add date index
        Schema::table('whatsapp_clicks', function (Blueprint $table) {
            $table->index('created_at', 'idx_whatsapp_clicks_created');
        });

        // 10. log_notifikasi table: Fix foreign key RESTRICT delete and add tagihan_id index/FK
        Schema::table('log_notifikasi', function (Blueprint $table) use ($driver) {
            if ($driver === 'mysql') {
                $table->dropForeign('log_notifikasi_penyewa_id_foreign');
                $table->foreign('penyewa_id')
                      ->references('id')
                      ->on('penyewa')
                      ->onDelete('cascade')
                      ->onUpdate('cascade');
            }

            $table->unsignedInteger('tagihan_id')->nullable()->change();
            $table->index('tagihan_id', 'idx_log_notifikasi_tagihan_id');
            
            if ($driver === 'mysql') {
                $table->foreign('tagihan_id')
                      ->references('id')
                      ->on('tagihan')
                      ->onDelete('set null');
            }
        });

        // 11. sessions table: Modify payload to mediumText to prevent fragmentation
        Schema::table('sessions', function (Blueprint $table) {
            $table->mediumText('payload')->change();
        });

        // 12. MySQL Specific Check Constraints & Events
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE `tagihan` ADD CONSTRAINT `chk_tagihan_nominal_pokok` CHECK (`nominal_pokok` >= 0)");
            DB::statement("ALTER TABLE `tagihan` ADD CONSTRAINT `chk_tagihan_nominal_denda` CHECK (`nominal_denda` >= 0)");
            DB::statement("ALTER TABLE `tagihan` ADD CONSTRAINT `chk_tagihan_nominal_total` CHECK (`nominal_total` >= 0)");

            DB::statement("ALTER TABLE `reservasi` ADD CONSTRAINT `chk_reservasi_total_harga` CHECK (`total_harga` >= 0)");
            DB::statement("ALTER TABLE `reservasi` ADD CONSTRAINT `chk_reservasi_nominal_dp` CHECK (`nominal_dp` >= 0)");
            DB::statement("ALTER TABLE `reservasi` ADD CONSTRAINT `chk_reservasi_nominal_sisa` CHECK (`nominal_sisa` >= 0)");
            DB::statement("ALTER TABLE `reservasi` ADD CONSTRAINT `chk_reservasi_tanggal_logic` CHECK (`tanggal_selesai` >= `tanggal_mulai`)");

            DB::statement("ALTER TABLE `pembayaran` ADD CONSTRAINT `chk_pembayaran_nominal` CHECK (`nominal` >= 0)");
            DB::statement("ALTER TABLE `pengeluaran` ADD CONSTRAINT `chk_pengeluaran_nominal` CHECK (`nominal` >= 0)");

            DB::statement("ALTER TABLE `penyewa` ADD CONSTRAINT `chk_penyewa_harga_sewa` CHECK (`harga_sewa` >= 0)");
            DB::statement("ALTER TABLE `penyewa` ADD CONSTRAINT `chk_penyewa_deposit` CHECK (`deposit` >= 0)");
            DB::statement("ALTER TABLE `penyewa` ADD CONSTRAINT `chk_penyewa_tanggal_logic` CHECK (`tanggal_keluar_seharusnya` IS NULL OR `tanggal_keluar_seharusnya` >= `tanggal_masuk`)");

            // Event Scheduler setup
            DB::statement("SET GLOBAL event_scheduler = ON");

            DB::unprepared("
                CREATE EVENT IF NOT EXISTS `ev_purge_expired_sessions`
                ON SCHEDULE EVERY 1 DAY
                STARTS CURRENT_TIMESTAMP
                DO
                BEGIN
                  DELETE FROM `sessions` WHERE `last_activity` < UNIX_TIMESTAMP(DATE_SUB(NOW(), INTERVAL 7 DAY));
                END
            ");

            DB::unprepared("
                CREATE EVENT IF NOT EXISTS `ev_purge_old_notification_logs`
                ON SCHEDULE EVERY 1 DAY
                STARTS CURRENT_TIMESTAMP
                DO
                BEGIN
                  DELETE FROM `log_notifikasi` WHERE `created_at` < DATE_SUB(NOW(), INTERVAL 90 DAY);
                END
            ");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::getDriverName();

        // 12. Drop CHECK Constraints & Events in MySQL
        if ($driver === 'mysql') {
            try {
                DB::statement("ALTER TABLE `tagihan` DROP CONSTRAINT `chk_tagihan_nominal_pokok`, DROP CONSTRAINT `chk_tagihan_nominal_denda`, DROP CONSTRAINT `chk_tagihan_nominal_total`");
            } catch (\Exception $e) {}
            try {
                DB::statement("ALTER TABLE `reservasi` DROP CONSTRAINT `chk_reservasi_total_harga`, DROP CONSTRAINT `chk_reservasi_nominal_dp`, DROP CONSTRAINT `chk_reservasi_nominal_sisa`, DROP CONSTRAINT `chk_reservasi_tanggal_logic`");
            } catch (\Exception $e) {}
            try {
                DB::statement("ALTER TABLE `pembayaran` DROP CONSTRAINT `chk_pembayaran_nominal`");
            } catch (\Exception $e) {}
            try {
                DB::statement("ALTER TABLE `pengeluaran` DROP CONSTRAINT `chk_pengeluaran_nominal`");
            } catch (\Exception $e) {}
            try {
                DB::statement("ALTER TABLE `penyewa` DROP CONSTRAINT `chk_penyewa_harga_sewa`, DROP CONSTRAINT `chk_penyewa_deposit`, DROP CONSTRAINT `chk_penyewa_tanggal_logic`");
            } catch (\Exception $e) {}

            try {
                DB::statement("DROP EVENT IF EXISTS `ev_purge_expired_sessions`");
                DB::statement("DROP EVENT IF EXISTS `ev_purge_old_notification_logs`");
            } catch (\Exception $e) {}
        }

        // Rest of downs
        try {
            Schema::table('sessions', function (Blueprint $table) {
                $table->longText('payload')->change();
            });
        } catch (\Exception $e) {}

        try {
            Schema::table('log_notifikasi', function (Blueprint $table) use ($driver) {
                if ($driver === 'mysql') {
                    try {
                        $table->dropForeign('log_notifikasi_tagihan_id_foreign');
                    } catch (\Exception $ex) {}
                }
                try {
                    $table->dropIndex('idx_log_notifikasi_tagihan_id');
                } catch (\Exception $ex) {}
                
                if ($driver === 'mysql') {
                    try {
                        $table->dropForeign('log_notifikasi_penyewa_id_foreign');
                    } catch (\Exception $ex) {}
                    try {
                        $table->foreign('penyewa_id')
                              ->references('id')
                              ->on('penyewa')
                              ->onUpdate('cascade');
                    } catch (\Exception $ex) {}
                }
            });
        } catch (\Exception $e) {}

        try {
            Schema::table('whatsapp_clicks', function (Blueprint $table) {
                $table->dropIndex('idx_whatsapp_clicks_created');
            });
        } catch (\Exception $e) {}

        try {
            Schema::table('pembayaran', function (Blueprint $table) {
                $table->dropIndex('idx_pembayaran_tanggal_status');
            });
        } catch (\Exception $e) {}

        try {
            Schema::table('pengeluaran', function (Blueprint $table) {
                $table->dropIndex('idx_pengeluaran_tanggal');
            });
        } catch (\Exception $e) {}

        try {
            Schema::table('notifikasi_khusus', function (Blueprint $table) use ($driver) {
                if ($driver === 'mysql') {
                    try {
                        // Re-create the dropped foreign key index to prevent Needed in a Foreign Key Constraint error
                        $table->index('user_id', 'notifikasi_khusus_user_id_foreign');
                    } catch (\Exception $ex) {}
                }
                try {
                    $table->dropIndex('idx_notifikasi_khusus_user_created');
                } catch (\Exception $ex) {}
            });
        } catch (\Exception $e) {}

        try {
            Schema::table('guest_chat_messages', function (Blueprint $table) use ($driver) {
                if ($driver === 'mysql') {
                    try {
                        // Re-create foreign key index
                        $table->index('guest_chat_thread_id', 'guest_chat_messages_guest_chat_thread_id_foreign');
                    } catch (\Exception $ex) {}
                }
                try {
                    $table->dropIndex('idx_guest_chat_messages_thread_id_id');
                } catch (\Exception $ex) {}
            });
        } catch (\Exception $e) {}

        try {
            Schema::table('reservasi', function (Blueprint $table) use ($driver) {
                try {
                    $table->dropUnique('uq_reservasi_active_order_id');
                } catch (\Exception $ex) {}
                try {
                    $table->dropColumn('active_order_id');
                } catch (\Exception $ex) {}
                try {
                    $table->unique('order_id', 'reservasi_order_id_unique');
                } catch (\Exception $ex) {}
                if ($driver === 'mysql') {
                    try {
                        $table->index('status', 'reservasi_status_index');
                    } catch (\Exception $ex) {}
                    try {
                        $table->index('kamar_id', 'reservasi_kamar_id_index');
                    } catch (\Exception $ex) {}
                }
            });
        } catch (\Exception $e) {}

        try {
            Schema::table('penyewa', function (Blueprint $table) use ($driver) {
                try {
                    $table->dropUnique('uq_penyewa_active_nik');
                } catch (\Exception $ex) {}
                try {
                    $table->dropColumn('active_nik');
                } catch (\Exception $ex) {}
                try {
                    $table->unique('nik', 'penyewa_nik_unique');
                } catch (\Exception $ex) {}
                if ($driver === 'mysql') {
                    try {
                        $table->index(['user_id', 'deleted_at'], 'idx_penyewa_user_deleted_at');
                    } catch (\Exception $ex) {}
                }
            });
        } catch (\Exception $e) {}

        try {
            Schema::table('kamar', function (Blueprint $table) use ($driver) {
                try {
                    $table->dropUnique('uq_kamar_active_nomor');
                } catch (\Exception $ex) {}
                try {
                    $table->dropColumn('active_nomor_kamar');
                } catch (\Exception $ex) {}
                try {
                    $table->unique('nomor_kamar', 'kamar_nomor_kamar_unique');
                } catch (\Exception $ex) {}
                if ($driver === 'mysql') {
                    try {
                        $table->index('status', 'idx_status');
                    } catch (\Exception $ex) {}
                }
            });
        } catch (\Exception $e) {}

        try {
            Schema::table('users', function (Blueprint $table) use ($driver) {
                try {
                    $table->dropUnique('uq_users_active_email');
                } catch (\Exception $ex) {}
                try {
                    $table->dropUnique('uq_users_active_no_hp');
                } catch (\Exception $ex) {}
                try {
                    $table->dropUnique('uq_users_active_nik');
                } catch (\Exception $ex) {}
                try {
                    $table->dropColumn(['active_email', 'active_no_hp', 'active_nik']);
                } catch (\Exception $ex) {}

                try {
                    $table->unique('email', 'users_email_unique');
                } catch (\Exception $ex) {}
                try {
                    $table->unique('no_hp', 'users_no_hp_unique');
                } catch (\Exception $ex) {}
                try {
                    $table->unique('nik', 'users_nik_unique');
                } catch (\Exception $ex) {}
                if ($driver === 'mysql') {
                    try {
                        $table->index('role', 'idx_role');
                    } catch (\Exception $ex) {}
                }
            });
        } catch (\Exception $e) {}
    }
};
