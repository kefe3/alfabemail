<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sinif extends Model
{
    protected $fillable = [
        'okul_id',
        'ogretmen_user_id',
        'ad',
        'brans',
    ];

    public function okul(): BelongsTo
    {
        return $this->belongsTo(Okul::class);
    }

    public function ogretmen(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ogretmen_user_id');
    }

    public function ogrenciler(): HasMany
    {
        return $this->hasMany(Ogrenci::class);
    }
}
