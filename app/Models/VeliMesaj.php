<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VeliMesaj extends Model
{
    protected $table = 'veli_mesajlari';

    protected $fillable = [
        'veli_id',
        'ogretmen_user_id',
        'ogrenci_id',
        'konu',
        'mesaj',
        'okundu_mu',
        'okundu_at',
    ];

    protected $casts = [
        'okundu_mu' => 'boolean',
        'okundu_at' => 'datetime',
    ];

    public function veli()
    {
        return $this->belongsTo(Veli::class);
    }

    public function ogretmen()
    {
        return $this->belongsTo(User::class, 'ogretmen_user_id');
    }

    public function ogrenci()
    {
        return $this->belongsTo(Ogrenci::class);
    }
}
