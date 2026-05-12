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
            'okul.view', 'okul.create', 'okul.edit', 'okul.delete',
            'sinif.view', 'sinif.create', 'sinif.edit', 'sinif.delete',
            'ogretmen.view', 'ogretmen.create', 'ogretmen.edit', 'ogretmen.delete',
            'ogrenci.view', 'ogrenci.create', 'ogrenci.edit', 'ogrenci.delete',
            'mailbox.view', 'mailbox-olustur', 'mailbox-sil',
            'rapor.view', 'rapor.export',
            'aktivasyon.send', 'yaka-karti.print', 'kota-sor', 'ayar.manage',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->syncPermissions([
            'okul.view', 'okul.create', 'okul.edit',
            'ogretmen.view', 'ogretmen.create', 'ogretmen.edit',
            'aktivasyon.send', 'rapor.view', 'rapor.export', 'ayar.manage',
        ]);

        $yonetici = Role::firstOrCreate(['name' => 'yonetici']);
        $yonetici->syncPermissions([
            'sinif.view', 'sinif.create', 'sinif.edit', 'sinif.delete',
            'ogretmen.view', 'ogretmen.create', 'ogretmen.edit',
            'aktivasyon.send', 'rapor.view',
        ]);

        $ogretmen = Role::firstOrCreate(['name' => 'ogretmen']);
        $ogretmen->syncPermissions([
            'sinif.view', 'sinif.create', 'sinif.edit', 'sinif.delete',
            'ogrenci.view', 'ogrenci.create', 'ogrenci.edit', 'ogrenci.delete',
            'mailbox-olustur', 'mailbox-sil', 'mailbox.view',
            'kota-sor', 'yaka-karti.print', 'rapor.view',
        ]);

        $veli = Role::firstOrCreate(['name' => 'veli']);
        $veli->syncPermissions(['rapor.view']);

        Role::firstOrCreate(['name' => 'ogrenci']);
    }
}
