<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SaleTermV2Charge extends Model
{
    public function currency()
    {
        return $this->hasOne('App\Currency','id','currency_id');
    }
}
