<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FailRateLcl extends Model
{
    use SoftDeletes;
    protected $dates    = ['deleted_at'];

    protected $table = 'failes_rate_lcl';
    protected $fillable = [
        'origin_port',
        'destiny_port',
        'carrier_id',
        'contractlcl_id',
        'uom',
        'minimum',
        'currency_id',
        'schedule_type',
        'transit_time',
        'via'
    ];
}
