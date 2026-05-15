<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('bayiler', 'onaylandi')) {
            Schema::table('bayiler', function (Blueprint $table) {
                $table->boolean('onaylandi')->default(false)->after('okul_kotasi');
                $table->timestamp('onay_tarihi')->nullable()->after('onaylandi');
            });
        }
    }

    public function down(): void
    {
        Schema::table('bayiler', function (Blueprint $table) {
            $table->dropColumn(['onaylandi', 'onay_tarihi']);
        });
    }
};