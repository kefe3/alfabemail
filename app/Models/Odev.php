<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Odev extends Model
{
    protected $table = 'odevler';

    protected $fillable = [
        'ogretmen_id',
        'sinif_id',
        'baslik',
        'aciklama',
        'teslim_tarihi',
        'aktif',
    ];

    protected function casts(): array
    {
        return [
            'teslim_tarihi' => 'date',
            'aktif' => 'boolean',
        ];
    }

    public function ogretmen(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ogretmen_id');
    }

    public function sinif(): BelongsTo
    {
        return $this->belongsTo(Sinif::class);
    }

    public function ogrenciler(): BelongsToMany
    {
        return $this->belongsToMany(Ogrenci::class, 'odev_ogrenci', 'odev_id', 'ogrenci_id')
            ->withPivot('tamamlandi', 'tamamlanma_tarihi')
            ->withTimestamps();
    }

    public function scopeAktif($query)
    {
        return $query->where('aktif', true);
    }

    public function scopeGununOdevleri($query)
    {
        return $query->whereDate('teslim_tarihi', today());
    }

    public function ogrenciDurumu(Ogrenci $ogrenci): ?array
    {
        $pivot = $this->ogrenciler()->where('ogrenci_id', $ogrenci->id)->first();
        if (!$pivot) return null;

        return [
            'tamamlandi' => (bool) $pivot->pivot->tamamlandi,
            'tamamlanma_tarihi' => $pivot->pivot->tamamlanma_tarihi,
        ];
    }
}
