<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->uuid('groupe_uuid')->nullable()->after('employe_id');
            $table->index('groupe_uuid');
        });
    }

    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropIndex(['groupe_uuid']);
            $table->dropColumn('groupe_uuid');
        });
    }
};
