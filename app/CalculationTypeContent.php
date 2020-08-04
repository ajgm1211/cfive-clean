<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CalculationTypeContent extends Model
{
    protected $table = 'calculation_types_contents';
    protected $fillable = ['id',
                           'calculationtype_base_id',
                           'calculationtype_content_id'
                          ];
}

