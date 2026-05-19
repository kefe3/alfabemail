<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\Widget;

class OnlineAdminsWidget extends Widget
{
    protected string $view = 'filament.admin.widgets.online-admins';

    protected static ?int $sort = 3;

    public static function canView(): bool
    {
        return auth()->user()?->hasRole('admin') ?? false;
    }

    public function getViewData(): array
    {
        $threshold = now()->subMinutes(5);

        $onlineAdmins = User::whereHas('roles', fn($q) => $q->where('name', 'admin'))
            ->where('is_active', true)
            ->where('last_active_at', '>=', $threshold)
            ->get(['id', 'name']);

        $totalAdmins = User::whereHas('roles', fn($q) => $q->where('name', 'admin'))
            ->where('is_active', true)
            ->count();

        return [
            'onlineAdmins' => $onlineAdmins,
            'totalAdmins' => $totalAdmins,
        ];
    }
}
