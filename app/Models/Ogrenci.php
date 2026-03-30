<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Ogrenci extends Model
{
    protected $fillable = [
        'user_id',
        'sinif_id',
        'mailbox_local_part',
        'mailbox_quota_mb',
        'qr_token',
        'qr_svg',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sinif(): BelongsTo
    {
        return $this->belongsTo(Sinif::class);
    }

    public function veliler(): BelongsToMany
    {
        return $this->belongsToMany(Veli::class, 'ogrenci_veli', 'ogrenci_id', 'veli_id');
    }
}
