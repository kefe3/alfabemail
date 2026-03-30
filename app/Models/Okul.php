<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Okul extends Model
{
    protected $fillable = [
        'bayi_id',
        'yonetici_user_id',
        'ad',
        'adres',
        'telefon',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function bayi(): BelongsTo
    {
        return $this->belongsTo(Bayi::class);
    }

    public function yonetici(): BelongsTo
    {
        return $this->belongsTo(User::class, 'yonetici_user_id');
    }

    public function siniflar(): HasMany
    {
        return $this->hasMany(Sinif::class);
    }
}
