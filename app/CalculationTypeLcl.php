<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Watson\Rememberable\Rememberable;

class CalculationTypeLcl extends Model
{
    use Rememberable;
    protected $table = 'calculationtypelcl';
    protected $fillable = ['id', 'name', 'code'];
}
