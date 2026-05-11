<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('okullar', function (Blueprint $table) {
            $table->string('yonetici_ad_soyad')->nullable()->after('yonetici_user_id');
            $table->string('yonetici_email')->nullable()->after('yonetici_ad_soyad');
            $table->string('ulke')->nullable()->after('adres');
            $table->string('sehir')->nullable()->after('ulke');
            $table->string('ilce')->nullable()->after('sehir');
            $table->string('mahalle')->nullable()->after('ilce');
        });
    }

    public function down(): void
    {
        Schema::table('okullar', function (Blueprint $table) {
            $table->dropColumn(['yonetici_ad_soyad', 'yonetici_email', 'ulke', 'sehir', 'ilce', 'mahalle']);
        });
    }
};