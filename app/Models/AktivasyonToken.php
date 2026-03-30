<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AktivasyonToken extends Model
{
    protected $fillable = [
        'user_id',
        'token',
        'tip',
        'expires_at',
        'kullanildi_at',
    ];

    protected $casts = [
        'expires_at'    => 'datetime',
        'kullanildi_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function isUsed(): bool
    {
        return $this->kullanildi_at !== null;
    }
}
