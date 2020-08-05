<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MasterSurcharge extends Model
{

    protected $fillable = ['id',
                           'surcharge_id',
                           'carrier_id',
                           'typedestiny_id',
                           'calculationtype_id',
                           'group_container_id',
                           'direction_id'
                          ];

    public function surcharge(){
        return $this->belongsTo('App\Surcharge');
    }
    
    public function direction(){
        return $this->belongsTo('App\Direction');
    }
    
    public function calculationtype(){
        return $this->belongsTo('App\CalculationType');
    }
    
    public function typedestiny(){
        return $this->belongsTo('App\TypeDestiny');
    }
}
