<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SurchergerForCarrier extends Model
{
    protected $table    = "surchargers_for_carrier";
    protected $fillable = ['id','name'];
}
