<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Harbor extends Model
{
    protected $table    = "harbors";
    protected $fillable = ['id', 'name', 'code','country_id'];
    /*
    public function port_origin(){
          return $this->hasOne('App\Rate','origin_country');


    }
    public function port_destiny(){
         return $this->hasOne('App\Rate','destiny_country');

    }*/

    public function localcharge()
    {

        return $this->hasOne('App\LocalCharge');
    }
    public function rate()
    {

        return $this->hasOne('App\Rate');
    }
}
