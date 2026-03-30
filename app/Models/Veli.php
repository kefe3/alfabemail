<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Veli extends Model
{
    protected $fillable = [
        'user_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function ogrenciler(): BelongsToMany
    {
        return $this->belongsToMany(Ogrenci::class, 'ogrenci_veli', 'veli_id', 'ogrenci_id');
    }
}
