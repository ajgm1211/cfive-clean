<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SurchargePerContract extends Model
{
    protected $fillable = ['charge', 'type', 'calculation_type', 'contract_id', 'currency', 'rates', 'origin_port', 'destination_port'];
}
