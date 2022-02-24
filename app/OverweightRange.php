<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OverweightRange extends Model
{
    protected $casts = ['containers' => 'array'];

    protected $fillable = [
        'id', 'lower_limit', 'upper_limit', 'model_id', 'model_type', 'containers'
    ];

    
    public function model()
    {
        return $this->morphTo();
    }
}
