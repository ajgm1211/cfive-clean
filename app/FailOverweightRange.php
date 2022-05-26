<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FailOverweightRange extends Model
{
    protected $fillable = [
        'id', 'lower_limit', 'upper_limit','model_id', 'model_type'
    ];

    
    public function model()
    {
        return $this->morphTo();
    }
}
