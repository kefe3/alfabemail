<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('okullar', function (Blueprint $table) {
            $table->dropForeign(['bayi_id']);
            $table->dropColumn('bayi_id');
        });

        Schema::dropIfExists('bayiler');
    }

    public function down(): void
    {
        Schema::create('bayiler', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('il')->nullable();
            $table->integer('okul_kotasi')->default(0);
            $table->boolean('onaylandi')->default(false);
            $table->timestamp('onay_tarihi')->nullable();
            $table->timestamp('aktif_at')->nullable();
            $table->timestamps();
        });

        Schema::table('okullar', function (Blueprint $table) {
            $table->foreignId('bayi_id')->nullable()->constrained('bayiler')->cascadeOnDelete();
        });
    }
};
