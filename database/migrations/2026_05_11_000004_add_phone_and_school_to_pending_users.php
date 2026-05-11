<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pending_users', function (Blueprint $table) {
            $table->string('phone', 20)->nullable()->after('password');
            $table->string('school', 255)->nullable()->after('phone');
        });
    }

    public function down(): void
    {
        Schema::table('pending_users', function (Blueprint $table) {
            $table->dropColumn(['phone', 'school']);
        });
    }
};
