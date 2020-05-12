<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class ChargeLclAir extends Model
{
    
    protected $fillable = ['automatic_rate_id','type_id','surcharge_id','calculation_type_id','units','price_per_unit','currency_id','total','markup'];

    public function automatic_rate()
    {
        return $this->belongsTo('App\AutomaticRate','automatic_rate_id','id');
    }

    public function currency()
    {
        return $this->belongsTo('App\Currency');
    }

    public function type()
    {
        return $this->belongsTo('App\TypeDestiny');
    }

    public function surcharge()
    {
        return $this->hasOne('App\Surcharge','id','surcharge_id');
    }

    public function calculation_type()
    {
        return $this->hasOne('App\CalculationTypeLcl','id','calculation_type_id');
    }    
}
