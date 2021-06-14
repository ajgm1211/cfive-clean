<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InlandsProvinces extends Model
{
    protected $table    = "inlands_provinces";
    protected $fillable = ['id','name', 'region', 'country_id'];

    public function country()
    {
        return $this->belongsTo('App\Country', 'country_id');
    }
    public function location()
    {
        return $this->belongsTo('App\location', 'province_id');
    }
}