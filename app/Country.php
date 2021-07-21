<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
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
