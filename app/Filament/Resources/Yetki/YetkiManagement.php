<?php

namespace App\Filament\Resources\Yetki;

use Filament\Resources\Resource;
use Spatie\Permission\Models\Role;

class YetkiManagement extends Resource
{
    protected static ?string $model = Role::class;
    protected static ?string $navigationLabel = 'Yetki Yönetimi';
    protected static ?int $navigationSort = 100;

    public static function canAccess(): bool
    {
        $user = auth()->user();
        return $user && $user->hasRole('admin');
    }

    public static function getPages(): array
    {
        return [
            'roller' => \App\Filament\Resources\Yetki\Pages\ListRoles::route('/roller'),
            'kullanicilar' => \App\Filament\Resources\Yetki\Pages\ListUserRoles::route('/kullanicilar'),
        ];
    }

    public static function getNavigationSubItems(): array
    {
        return [
            \Filament\Tables\Navigation\NavigationItem::make('Roller')
                ->icon('heroicon-o-user-group')
                ->url('/admin/yetki/roller')
                ->isActive(),
            \Filament\Tables\Navigation\NavigationItem::make('Kullanıcılar')
                ->icon('heroicon-o-users')
                ->url('/admin/yetki/kullanicilar'),
        ];
    }
}