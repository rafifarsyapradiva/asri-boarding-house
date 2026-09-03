<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guest_chat_threads', function (Blueprint $table) {
            $table->id();
            $table->string('session_token')->unique()->index();
            $table->string('name');
            $table->string('no_hp');
            $table->enum('status', ['active', 'closed'])->default('active');
            $table->timestamps();
        });

        Schema::create('guest_chat_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('guest_chat_thread_id');
            $table->enum('sender_type', ['guest', 'admin']);
            $table->unsignedInteger('sender_id')->nullable(); // Admin user id who replied
            $table->text('message');
            $table->boolean('is_read')->default(false);
            $table->timestamps();

            // Foreign keys
            $table->foreign('guest_chat_thread_id')
                ->references('id')
                ->on('guest_chat_threads')
                ->cascadeOnDelete();

            $table->foreign('sender_id')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guest_chat_messages');
        Schema::dropIfExists('guest_chat_threads');
    }
};
