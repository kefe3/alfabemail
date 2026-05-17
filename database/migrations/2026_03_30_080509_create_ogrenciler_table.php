<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ogrenciler', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sinif_id')->nullable()->constrained('siniflar')->nullOnDelete();
            $table->string('mailbox_local_part')->nullable();
            $table->integer('mailbox_quota_mb')->default(100);
            $table->string('qr_token')->nullable()->unique();
            $table->text('qr_svg')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ogrenciler');
    }
};
