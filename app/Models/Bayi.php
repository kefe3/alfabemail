<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bayi extends Model
{
    protected $fillable = [
        'user_id',
        'il',
        'okul_kotasi',
        'aktif_at',
    ];

    protected $casts = [
        'aktif_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function okullar(): HasMany
    {
        return $this->hasMany(Okul::class);
    }
}
