<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HataBildirisi extends Model
{
    protected $table = 'hata_bildirisi';

    protected $fillable = [
        'ad_soyad',
        'email',
        'konu',
        'aciklama',
        'ekran_goruntusu',
        'sayfa',
        'tarayici',
        'cozuldu_mu',
    ];
}
