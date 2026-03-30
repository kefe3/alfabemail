<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ogrenci_veli', function (Blueprint $table) {
            $table->foreignId('ogrenci_id')->constrained('ogrenciler')->cascadeOnDelete();
            $table->foreignId('veli_id')->constrained('veliler')->cascadeOnDelete();
            $table->primary(['ogrenci_id', 'veli_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ogrenci_veli');
    }
};
