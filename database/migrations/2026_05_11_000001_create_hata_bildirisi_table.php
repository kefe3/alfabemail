<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hata_bildirisi', function (Blueprint $table) {
            $table->id();
            $table->string('ad_soyad');
            $table->string('email');
            $table->string('konu');
            $table->text('aciklama');
            $table->string('ekran_goruntusu')->nullable();
            $table->string('sayfa')->nullable();
            $table->string('tarayici')->nullable();
            $table->boolean('cozuldu_mu')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hata_bildirisi');
    }
};
