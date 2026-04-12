<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── password_reset_tokens ────────────────────────────────
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email', 180)->primary();
            $table->string('token', 255);
            $table->timestamp('created_at')->useCurrent();

            $table->index('token');
        });

        // ── sessions (pour l'authentification) ───────────────────
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
