<?php

namespace App\Models;

use App\Traits\HasTenantScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bayi extends Model
{
    use HasTenantScope;
    protected $table = 'bayiler';
    protected $fillable = [
        'user_id',
        'il',
        'okul_kotasi',
        'onaylandi',
        'onay_tarihi',
        'aktif_at',
    ];

    protected $casts = [
        'onaylandi' => 'boolean',
        'onay_tarihi' => 'datetime',
        'aktif_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($bayi) {
            if (auth()->user()->hasRole('admin')) {
                $bayi->onaylandi = true;
                $bayi->onay_tarihi = now();
            } else {
                $bayi->onaylandi = false;
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function okullar(): HasMany
    {
        return $this->hasMany(Okul::class);
    }
}
