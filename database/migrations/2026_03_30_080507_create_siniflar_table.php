<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('siniflar', function (Blueprint $table) {
            $table->id();
            $table->foreignId('okul_id')->constrained('okullar')->cascadeOnDelete();
            $table->foreignId('ogretmen_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('ad'); // e.g. "3-A"
            $table->string('brans')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('siniflar');
    }
};
