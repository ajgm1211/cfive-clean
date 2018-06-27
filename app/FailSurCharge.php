<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FailSurCharge extends Model
{
    protected $table = 'failes_surcharges';
    protected $fillable = [
        'surcharge_id','port_orig','port_dest','typedestiny_id','contract_id','calculationtype_id','ammount','currency_id','carrier_id'];
}
