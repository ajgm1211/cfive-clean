<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{    
    protected $table    = "currency";
    protected $fillable = ['id', 'alphacode','name','rates'];
    public function rate()
    {

        return $this->hasOne('App\Rate');
    }
    public function globalcharge()
    {

        return $this->hasOne('App\GlobalCharge');
    }
}
