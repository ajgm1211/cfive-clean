<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $table = 'location';
    protected $fillable = ['id', 'name', 'province_id','identifier'];

    public function provinces()
    {
        return $this->hasOne('App\InlandsProvinces', 'province_id');
    }
}
