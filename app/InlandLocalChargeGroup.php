<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InlandLocalChargeGroup extends Model
{
    protected $fillable = ['automatic_inland_id', 'local_charge_quote_id'];
}
