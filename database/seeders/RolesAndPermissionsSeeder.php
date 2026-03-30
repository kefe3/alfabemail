<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'bayileri-yonet', 'okullari-yonet', 'ogretmenleri-yonet',
            'ogrencileri-yonet', 'mailbox-olustur', 'mailbox-sil',
            'kota-sor', 'rapor-gor', 'aktivasyon-gonder', 'yaka-karti-yazdir',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $superAdmin = Role::firstOrCreate(['name' => 'super_admin']);
        $superAdmin->syncPermissions(Permission::all());

        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->syncPermissions(['bayileri-yonet','okullari-yonet','ogretmenleri-yonet','aktivasyon-gonder','rapor-gor']);

        $bayi = Role::firstOrCreate(['name' => 'bayi']);
        $bayi->syncPermissions(['okullari-yonet','aktivasyon-gonder','rapor-gor']);

        $yonetici = Role::firstOrCreate(['name' => 'yonetici']);
        $yonetici->syncPermissions(['ogretmenleri-yonet','aktivasyon-gonder','rapor-gor']);

        $ogretmen = Role::firstOrCreate(['name' => 'ogretmen']);
        $ogretmen->syncPermissions(['ogrencileri-yonet','mailbox-olustur','mailbox-sil','kota-sor','yaka-karti-yazdir','rapor-gor']);

        $veli = Role::firstOrCreate(['name' => 'veli']);
        $veli->syncPermissions(['rapor-gor']);

        Role::firstOrCreate(['name' => 'ogrenci']);
    }
}
