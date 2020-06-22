<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Carrier extends Model
{
    protected $table    = "carriers";
    protected $fillable = ['id', 'name', 'image', 'varation'];
    public function rate()
    {
        return $this->hasOne('App\Rate');
    }

    public function automatic_rate()
    {
        return $this->hasOne('App\AutomaticRate');
    }

    public function globalcharge()
    {

        return $this->hasOne('App\GlobalCharge');
    }

    public function globalcharcarrier()
    {

        return $this->hasMany('App\GlobalCharPortCarrier');
    }

    public function globalcharport()
    {

        return $this->hasMany('App\GlobalCharPortCarrier');
    }

    public function getUrlAttribute($value)
    {
        return "https://cargofive-production.s3.eu-central-1.amazonaws.com/imgcarrier/".$value;
    }
}
