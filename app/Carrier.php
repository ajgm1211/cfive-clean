<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Carrier extends Model
{
    protected $table    = "carriers";
    protected $fillable = ['id', 'name'];
    public function rate()
    {
        return $this->hasOne('App\Rate');
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
