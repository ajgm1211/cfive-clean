<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Watson\Rememberable\Rememberable;

class Country extends Model
{
    use Rememberable;
    protected $table = 'countries';
    protected $fillable = ['id',
                           'name',
                           'code',
                           'continent',
                           'variation',
                          ];

    public function ports()
    {
        return $this->hasMany('App\Harbor', 'country_id');
    }
}
