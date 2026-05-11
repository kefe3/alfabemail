<?php

namespace App\Models;

use App\Traits\HasTenantScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Okul extends Model
{
    use HasTenantScope;
    protected $table = 'okullar';
    protected $fillable = [
        'bayi_id',
        'yonetici_user_id',
        'ad',
        'adres',
        'telefon',
        'is_active',
        'yonetici_ad_soyad',
        'yonetici_email',
        'ulke',
        'sehir',
        'ilce',
        'mahalle',
        'durum',
        'red_nedeni',
    ];

    protected $attributes = [
        'bayi_id' => null,
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
