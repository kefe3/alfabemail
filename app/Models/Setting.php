<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
    ];

    // Belirli anahtarlar için şifreleme desteği
    // Aslında cast olarak 'encrypted' kullanılabilir ancak tüm ayarlar şifreli olmasın derseniz
    // 'value' alanını generic bırakıp sadece API KEY gibi hassas verileri şifreleyeceğiz.
    // Ancak Laravel 11'de Attribute üzerinden dinamik cast de yapabiliriz.
    
    // Basitleştirmek için: Hassas verileri tutan ayarları 'encrypted' cast ile saklayalım.
    protected $casts = [
        'value' => 'encrypted',
    ];
}
