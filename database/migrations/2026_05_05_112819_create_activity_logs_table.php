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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('user_name')->nullable();
            $table->string('user_role')->nullable();
            $table->string('action'); // created, updated, deleted, approved, etc.
            $table->string('module'); // ogrenci, sinif, okul, bayi, user, etc.
            $table->string('target_type')->nullable(); // App\Models\Ogrenci
            $table->unsignedBigInteger('target_id')->nullable();
            $table->string('target_name')->nullable(); // Öğrenci adı, okul adı, etc.
            $table->string('parent_type')->nullable(); // Kimin altında eklendi
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('parent_name')->nullable();
            $table->text('description')->nullable();
            $table->json('extra_data')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();
            
            $table->index(['module', 'action']);
            $table->index('user_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
