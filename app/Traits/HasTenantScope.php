<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

trait HasTenantScope
{
    /**
     * Scope queries to only show records owned by the current user's tenant hierarchy
     */
    public function scopeForCurrentUser(Builder $query): Builder
    {
        $user = auth()->user();
        
        if (!$user) {
            return $query;
        }

        // Admin sees everything
        if ($user->hasRole('admin')) {
            return $query;
        }

        // Yonetici sees only their own school
        if ($user->hasRole('yonetici')) {
            return $this->scopeForYonetici($query, $user);
        }

        // Ogretmen sees only their own students
        if ($user->hasRole('ogretmen')) {
            return $this->scopeForOgretmen($query, $user);
        }

        // Veli sees only their own students
        if ($user->hasRole('veli')) {
            return $this->scopeForVeli($query, $user);
        }

        // Ogrenci sees only their own data
        if ($user->hasRole('ogrenci')) {
            return $this->scopeForOgrenci($query, $user);
        }

        return $query;
    }

    /**
     * Scope for Yonetici - sees only their own school
     */
    protected function scopeForYonetici(Builder $query, User $user): Builder
    {
        $okul = $user->okul;
        
        if (!$okul) {
            return $query->whereRaw('1 = 0'); // No access
        }

        return match(static::class) {
            \App\Models\Okul::class => $query->where('id', $okul->id),
            \App\Models\Ogrenci::class => $query->whereHas('sinif.okul', fn($q) => $q->where('id', $okul->id)),
            \App\Models\Ogretmen::class => $query->whereHas('siniflar.okul', fn($q) => $q->where('id', $okul->id)),
            \App\Models\Sinif::class => $query->where('okul_id', $okul->id),
            default => $query->whereRaw('1 = 0'),
        };
    }

    /**
     * Scope for Ogretmen - sees only their own students
     */
    protected function scopeForOgretmen(Builder $query, User $user): Builder
    {
        // This would require a teacher model or relationship
        // For now, teachers see students in their classes
        return match(static::class) {
            \App\Models\Ogrenci::class => $query->whereHas('sinif', function($q) use ($user) {
                // Assuming teacher is assigned to classes through some relationship
                // This would need to be implemented based on the actual teacher-class relationship
                $q->where('ogretmen_user_id', $user->id);
            }),
            default => $query->whereRaw('1 = 0'),
        };
    }

    /**
     * Scope for Veli - sees only their own students
     */
    protected function scopeForVeli(Builder $query, User $user): Builder
    {
        $veli = $user->veli;
        
        if (!$veli) {
            return $query->whereRaw('1 = 0'); // No access
        }

        return match(static::class) {
            \App\Models\Ogrenci::class => $query->whereHas('veliler', fn($q) => $q->where('veli_id', $veli->id)),
            default => $query->whereRaw('1 = 0'),
        };
    }

    /**
     * Scope for Ogrenci - sees only their own data
     */
    protected function scopeForOgrenci(Builder $query, User $user): Builder
    {
        $ogrenci = $user->ogrenci;
        
        if (!$ogrenci) {
            return $query->whereRaw('1 = 0'); // No access
        }

        return match(static::class) {
            \App\Models\Ogrenci::class => $query->where('id', $ogrenci->id),
            default => $query->whereRaw('1 = 0'),
        };
    }
}
