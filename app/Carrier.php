<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Carrier extends Model
{
    protected $table    = "carriers";
    protected $fillable = ['id', 'name','image'];
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
}
