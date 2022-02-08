<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InlandsProvince extends Model
{
    protected $fillable = ['id','name', 'region', 'country_id'];

    public function country()
    {
        return $this->belongsTo('App\Country', 'country_id');
    }
    public function location()
    {
        return $this->belongsTo('App\Location', 'province_id');
    }
}