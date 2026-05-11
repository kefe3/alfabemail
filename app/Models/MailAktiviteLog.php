<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MailAktiviteLog extends Model
{
    protected $table = 'mail_aktivite_loglari';

    protected $fillable = [
        'ogrenci_id',
        'tip', // 'gonderilen' | 'alinan'
        'konu',
        'kime',
        'kimden',
        'tarih',
    ];

    protected $casts = [
        'tarih' => 'datetime',
    ];

    public function ogrenci()
    {
        return $this->belongsTo(Ogrenci::class);
    }
}