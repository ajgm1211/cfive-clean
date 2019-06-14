<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FailSurCharge extends Model
{
    use SoftDeletes;
    protected $dates    = ['deleted_at'];

    protected $table = 'failes_surcharges';
    protected $fillable = [
        'surcharge_id',
        'port_orig',
        'port_dest',
        'typedestiny_id',
        'contract_id',
        'calculationtype_id',
        'ammount',
        'currency_id',
        'carrier_id',
        'differentiator'
    ];
    
    public function contract(){
        return $this->belongsTo('App\Contract','contract_id');
    }
}
