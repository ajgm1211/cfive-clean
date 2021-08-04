<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReferentialData extends Model
{
    protected $table = 'referential_data';
    
    protected $fillable = [
        'user_id',
        'company_user_id',
        'referential_type',
        'referential_id',
        'json_data'
    ];

    protected $attributes = [
        'json_data' => []
    ];

    public function referential()
    {
        return $this->morphTo();
    }
}
