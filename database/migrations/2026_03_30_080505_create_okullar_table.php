<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('okullar', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bayi_id')->constrained('bayiler')->cascadeOnDelete();
            $table->foreignId('yonetici_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('ad');
            $table->string('adres')->nullable();
            $table->string('telefon')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('okullar');
    }
};
