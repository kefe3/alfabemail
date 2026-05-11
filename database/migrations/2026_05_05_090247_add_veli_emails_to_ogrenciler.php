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
        Schema::table('ogrenciler', function (Blueprint $table) {
            $table->string('anne_email')->nullable()->after('qr_svg');
            $table->string('baba_email')->nullable()->after('anne_email');
            $table->string('veli_email')->nullable()->after('baba_email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ogrenciler', function (Blueprint $table) {
            //
        });
    }
};
