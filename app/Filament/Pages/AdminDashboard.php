<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard;

class AdminDashboard extends Dashboard
{
    protected static ?string $navigationLabel = 'Yönetim Paneli';

    public function getHeading(): string
    {
        $user = auth()->user();

        if (!$user) {
            return 'Admin Paneli';
        }

        if ($user->hasRole('admin')) {
            return 'Admin Paneli';
        }

        return 'Yönetim Paneli';
    }

    public function getSubheading(): ?string
    {
        return 'Alfabe Mail sistem yönetim paneline hoş geldiniz.';
    }
}
