<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    public function up(): void
    {
        Role::whereIn('name', ['super_admin', 'bayi'])->delete();
    }

    public function down(): void
    {
        $admin = Role::where('name', 'admin')->first();
        if ($admin) {
            Role::create(['name' => 'super_admin', 'guard_name' => 'web']);
            Role::create(['name' => 'bayi', 'guard_name' => 'web']);
        }
    }
};