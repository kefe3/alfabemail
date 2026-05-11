<?php

namespace App\Filament\Portal\Resources\Concerns;

use Illuminate\Database\Eloquent\Model;

trait DisableCreate
{
    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }
}