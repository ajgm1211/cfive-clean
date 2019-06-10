<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GlobalCharPortCarrier extends Model
{
    protected $table    = "globalcharcarrier";

    protected $fillable = ['carrier_id','globalcharge_id'];
}
