<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CalculationTypeLcl extends Model
{
  protected $table    = "calculationtypelcl";
  protected $fillable = ['id', 'name','code'];

}
