<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FailRate extends Model
{
    use SoftDeletes;
    protected $dates    = ['deleted_at'];

    protected $table    = "failes_rates";
    protected $fillable = [
        'origin_port',
        'destiny_port',
        'carrier_id',
        'contract_id',
        'twuenty',
        'forty',
        'fortyhc',
        'currency_id',
        'fortynor',
        'fortyfive',
        'containers',
        'schedule_type',
        'transit_time',
        'via'
    ];
}
