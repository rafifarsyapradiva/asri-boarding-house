<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fasilitas', function (Blueprint $table) {
            $table->text('deskripsi')->nullable()->after('ikon');
            $table->boolean('is_active')->default(true)->after('deskripsi')->index();
            $table->timestamp('updated_at')->nullable()->useCurrentOnUpdate()->after('created_at');
        });
    }

    public function down(): void
    {
        Schema::table('fasilitas', function (Blueprint $table) {
            $table->dropIndex(['is_active']);
            $table->dropColumn(['deskripsi', 'is_active', 'updated_at']);
        });
    }
};
