<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Price extends Model
{
    protected $fillable = ['name','description','type_20','type_40','type_40_hc'];

    public function company()
    {
        return $this->hasMany('App\Company');
    }
}
