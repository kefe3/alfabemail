<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('odevler', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ogretmen_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('sinif_id')->nullable()->constrained('siniflar')->cascadeOnDelete();
            $table->string('baslik');
            $table->text('aciklama')->nullable();
            $table->date('teslim_tarihi')->nullable();
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });

        Schema::create('odev_ogrenci', function (Blueprint $table) {
            $table->id();
            $table->foreignId('odev_id')->constrained('odevler')->cascadeOnDelete();
            $table->foreignId('ogrenci_id')->constrained('ogrenciler')->cascadeOnDelete();
            $table->boolean('tamamlandi')->default(false);
            $table->timestamp('tamamlanma_tarihi')->nullable();
            $table->timestamps();

            $table->unique(['odev_id', 'ogrenci_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('odev_ogrenci');
        Schema::dropIfExists('odevler');
    }
};
