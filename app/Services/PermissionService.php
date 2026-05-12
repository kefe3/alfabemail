<?php

namespace App\Services;

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionService
{
    public static function getGroups(): array
    {
        return [
            'okul' => [
                'label' => 'Okul Yönetimi',
                'icon' => 'heroicon-o-building-school',
                'permissions' => ['view', 'create', 'edit', 'delete'],
            ],
            'ogretmen' => [
                'label' => 'Öğretmen Yönetimi',
                'icon' => 'heroicon-o-users',
                'permissions' => ['view', 'create', 'edit', 'delete'],
            ],
            'ogrenci' => [
                'label' => 'Öğrenci Yönetimi',
                'icon' => 'heroicon-o-academic-cap',
                'permissions' => ['view', 'create', 'edit', 'delete'],
            ],
            'mailbox' => [
                'label' => 'Mailbox İşlemleri',
                'icon' => 'heroicon-o-envelope',
                'permissions' => ['view', 'create', 'delete'],
            ],
            'rapor' => [
                'label' => 'Raporlar',
                'icon' => 'heroicon-o-chart-bar',
                'permissions' => ['view', 'export'],
            ],
            'sistem' => [
                'label' => 'Sistem',
                'icon' => 'heroicon-o-cog',
                'permissions' => ['send', 'print', 'view', 'manage'],
                'custom_names' => [
                    'aktivasyon.send' => 'Aktivasyon Gönder',
                    'yaka-karti.print' => 'Yaka Kartı Yazdır',
                    'kota.view' => 'Kota Görüntüle',
                    'ayar.manage' => 'Ayarları Yönet',
                ],
            ],
        ];
    }

    public static function getGroupedPermissions(): array
    {
        $groups = self::getGroups();
        $permissions = Permission::all();

        $grouped = [];
        foreach ($groups as $groupKey => $group) {
            $grouped[$groupKey] = [
                'label' => $group['label'],
                'icon' => $group['icon'],
                'permissions' => [],
            ];

            foreach ($group['permissions'] as $perm) {
                $permName = "{$groupKey}.{$perm}";
                $permission = $permissions->firstWhere('name', $permName);
                if ($permission) {
                    $grouped[$groupKey]['permissions'][] = $permission;
                }
            }
        }

        // Sistem özel izinleri
        $customNames = $groups['sistem']['custom_names'] ?? [];
        $grouped['sistem']['custom_permissions'] = [];
        foreach ($customNames as $permName => $label) {
            $permission = $permissions->firstWhere('name', $permName);
            if ($permission) {
                $grouped['sistem']['custom_permissions'][] = [
                    'permission' => $permission,
                    'label' => $label,
                ];
            }
        }

        return $grouped;
    }

    public static function getRolesWithPermissions(): array
    {
        $roles = Role::with('permissions')->get();
        $permissions = Permission::all();

        return $roles->map(function ($role) use ($permissions) {
            $rolePermissions = $role->permissions->pluck('name')->toArray();
            
            $groupedPerms = [];
            foreach (self::getGroups() as $groupKey => $group) {
                $groupPerms = array_filter($rolePermissions, fn($p) => str_starts_with($p, $groupKey));
                if (!empty($groupPerms)) {
                    $groupedPerms[$groupKey] = [
                        'label' => $group['label'],
                        'permissions' => $groupPerms,
                    ];
                }
            }

            return [
                'id' => $role->id,
                'name' => $role->name,
                'permissions' => $rolePermissions,
                'grouped_permissions' => $groupedPerms,
                'users_count' => $role->users()->count(),
            ];
        })->toArray();
    }
}