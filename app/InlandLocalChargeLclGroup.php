<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InlandLocalChargeLclGroup extends Model
{
    protected $fillable = ['automatic_inland_lcl_id', 'local_charge_quote_lcl_id'];
}
