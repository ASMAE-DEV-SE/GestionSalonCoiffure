<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('ville_id')->nullable()->after('telephone');
            $table->string('quartier', 100)->nullable()->after('ville_id');

            $table->foreign('ville_id')->references('id')->on('villes')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['ville_id']);
            $table->dropColumn(['ville_id', 'quartier']);
        });
    }
};
