<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $fillable = ['id', 'name', 'province_id','identifier'];

    public function province()
    {
        return $this->belongsTo('App\InlandsProvince');
    }
}
