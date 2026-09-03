<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('nik', 20)->nullable()->unique()->after('no_hp');
            $table->string('nama_wali', 100)->nullable()->after('nik');
            $table->string('no_wali', 20)->nullable()->after('nama_wali');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['nik', 'nama_wali', 'no_wali']);
        });
    }
};
