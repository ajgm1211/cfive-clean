<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FailRate extends Model
{
    protected $table    = "failes_rates";
    protected $fillable = [
        'origin_port',
        'destiny_port',
        'carrier_id',
        'contract_id',
        'twuenty',
        'forty',
        'fortyhc',
        'currency_id'
    ];
}
