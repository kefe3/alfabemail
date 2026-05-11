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
        Schema::table('okullar', function (Blueprint $table) {
            $table->enum('durum', ['beklemede', 'onayli', 'reddet'])->default('beklemede')->after('is_active');
            $table->text('red_nedeni')->nullable()->after('durum');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('okullar', function (Blueprint $table) {
            //
        });
    }
};
