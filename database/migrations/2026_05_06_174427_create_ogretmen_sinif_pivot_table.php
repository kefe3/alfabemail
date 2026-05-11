<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ogretmen_sinif', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sinif_id')->constrained('siniflar')->onDelete('cascade');
            $table->foreignId('ogretmen_user_id')->constrained('users')->onDelete('cascade');
            $table->string('brans')->nullable(); // Ders branşı
            $table->timestamps();
            
            $table->unique(['sinif_id', 'ogretmen_user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ogretmen_sinif');
    }
};
