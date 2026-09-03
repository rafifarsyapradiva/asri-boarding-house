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
        $dbName = DB::connection()->getDatabaseName();
        $driver = Schema::getConnection()->getDriverName();

        // 1. Drop foreign key if it exists (MySQL only)
        if ($driver === 'mysql') {
            $foreigns = DB::select("SELECT CONSTRAINT_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_NAME = 'penyewa' AND CONSTRAINT_SCHEMA = '$dbName' AND CONSTRAINT_NAME = 'penyewa_user_id_foreign'");
            if (!empty($foreigns)) {
                Schema::table('penyewa', function (Blueprint $table) {
                    $table->dropForeign('penyewa_user_id_foreign');
                });
            }
        }

        // 2. Drop unique constraints if they exist
        Schema::table('penyewa', function (Blueprint $table) {
            $indexes = Schema::getIndexes('penyewa');
            $indexNames = array_column($indexes, 'name');

            if (in_array('penyewa_user_id_unique', $indexNames)) {
                $table->dropUnique('penyewa_user_id_unique');
            }

            if (in_array('uq_penyewa_active_nik', $indexNames)) {
                $table->dropUnique('uq_penyewa_active_nik');
            }
        });

        // 3. Add normal indexes if they do not exist
        Schema::table('penyewa', function (Blueprint $table) {
            $indexes = Schema::getIndexes('penyewa');
            $user_id_indexed = false;
            $nik_indexed = false;
            foreach ($indexes as $index) {
                if ($index['columns'] === ['user_id']) {
                    $user_id_indexed = true;
                }
                if ($index['columns'] === ['nik']) {
                    $nik_indexed = true;
                }
            }

            if (!$user_id_indexed) {
                $table->index('user_id');
            }
            if (!$nik_indexed) {
                $table->index('nik');
            }
        });

        // 4. Re-create foreign key if it does not exist (MySQL only)
        if ($driver === 'mysql') {
            $foreignsAfter = DB::select("SELECT CONSTRAINT_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_NAME = 'penyewa' AND CONSTRAINT_SCHEMA = '$dbName' AND CONSTRAINT_NAME = 'penyewa_user_id_foreign'");
            if (empty($foreignsAfter)) {
                Schema::table('penyewa', function (Blueprint $table) {
                    $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
                });
            }
        } else {
            // For SQLite, re-create foreign key simply
            Schema::table('penyewa', function (Blueprint $table) {
                try {
                    $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
                } catch (\Exception $e) {
                    // SQLite handles it gracefully
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $dbName = DB::connection()->getDatabaseName();
        $driver = Schema::getConnection()->getDriverName();

        // 1. Drop foreign key if it exists (MySQL only)
        if ($driver === 'mysql') {
            $foreigns = DB::select("SELECT CONSTRAINT_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_NAME = 'penyewa' AND CONSTRAINT_SCHEMA = '$dbName' AND CONSTRAINT_NAME = 'penyewa_user_id_foreign'");
            if (!empty($foreigns)) {
                Schema::table('penyewa', function (Blueprint $table) {
                    $table->dropForeign('penyewa_user_id_foreign');
                });
            }
        }

        // 2. Drop normal indexes if they exist
        Schema::table('penyewa', function (Blueprint $table) {
            $indexes = Schema::getIndexes('penyewa');
            foreach ($indexes as $index) {
                if ($index['columns'] === ['user_id'] && !$index['unique']) {
                    $table->dropIndex($index['name']);
                }
                if ($index['columns'] === ['nik'] && !$index['unique']) {
                    $table->dropIndex($index['name']);
                }
            }
        });

        // 3. Restore unique indexes if they do not exist
        Schema::table('penyewa', function (Blueprint $table) {
            $indexes = Schema::getIndexes('penyewa');
            $indexNames = array_column($indexes, 'name');

            if (!in_array('penyewa_user_id_unique', $indexNames)) {
                $table->unique('user_id', 'penyewa_user_id_unique');
            }
            if (!in_array('uq_penyewa_active_nik', $indexNames)) {
                $table->unique('active_nik', 'uq_penyewa_active_nik');
            }
        });

        // 4. Restore foreign key if it does not exist (MySQL only)
        if ($driver === 'mysql') {
            $foreignsAfter = DB::select("SELECT CONSTRAINT_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_NAME = 'penyewa' AND CONSTRAINT_SCHEMA = '$dbName' AND CONSTRAINT_NAME = 'penyewa_user_id_foreign'");
            if (empty($foreignsAfter)) {
                Schema::table('penyewa', function (Blueprint $table) {
                    $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
                });
            }
        } else {
            Schema::table('penyewa', function (Blueprint $table) {
                try {
                    $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
                } catch (\Exception $e) {
                    // SQLite handles it gracefully
                }
            });
        }
    }
};
