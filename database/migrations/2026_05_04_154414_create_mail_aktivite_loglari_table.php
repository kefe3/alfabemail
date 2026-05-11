<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('mail_aktivite_loglari', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ogrenci_id');
            $table->string('tip', 50);
            $table->string('konu', 255)->nullable();
            $table->string('kime', 255)->nullable();
            $table->string('kimden', 255)->nullable();
            $table->dateTime('tarih')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mail_aktivite_loglari');
    }
};
