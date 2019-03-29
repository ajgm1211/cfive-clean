<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Charge extends Model
{
    protected $fillable = ['automatic_rate_id','type_id','surcharge_id','calculation_type_id','amount','markups','currency_id','total'];

    public function automatic_rate()
    {
        return $this->belongsTo('App\AutomaticRate','id','automatic_rate_id');
    }

    public function currency()
    {
        return $this->belongsTo('App\Currency');
    }

    public function type()
    {
        return $this->belongsTo('App\Type');
    }

    public function surcharge()
    {
        return $this->hasOne('App\Surcharge','id','surcharge_id');
    }

    public function calculation_type()
    {
        return $this->hasOne('App\CalculationType','id','calculation_type_id');
    }
}
