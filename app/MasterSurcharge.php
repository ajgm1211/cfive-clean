<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MasterSurcharge extends Model
{

    protected $fillable = ['id',
                           'name',
                           'carrier_id',
                           'typedestiny_id',
                           'calculationtype_id',
                           'direction_id'
                          ];
}
