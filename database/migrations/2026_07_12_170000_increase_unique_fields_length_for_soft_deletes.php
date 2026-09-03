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
            $table->string('no_hp', 50)->nullable()->change();
            $table->string('nik', 50)->nullable()->change();
        });

        Schema::table('penyewa', function (Blueprint $table) {
            $table->string('nik', 50)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('no_hp', 20)->nullable()->change();
            $table->string('nik', 20)->nullable()->change();
        });

        Schema::table('penyewa', function (Blueprint $table) {
            $table->string('nik', 20)->change();
        });
    }
};
