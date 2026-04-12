<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('villes', function (Blueprint $table) {
            $table->id();
            $table->string('nom_ville', 100)->unique();
            $table->string('code_postal', 10);
            $table->string('region', 100);
            $table->string('pays', 60)->default('Maroc');
            $table->boolean('actif')->default(true);
            $table->timestamp('cree_le')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('villes');
    }
};
