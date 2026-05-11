<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('veli_mesajlari', function (Blueprint $table) {
            $table->id();
            $table->foreignId('veli_id')->constrained('veliler')->cascadeOnDelete();
            $table->foreignId('ogretmen_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('ogrenci_id')->constrained('ogrenciler')->cascadeOnDelete();
            $table->string('konu');
            $table->text('mesaj');
            $table->boolean('okundu_mu')->default(false);
            $table->timestamp('okundu_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('veli_mesajlari');
    }
};
