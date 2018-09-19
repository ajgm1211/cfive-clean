<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Harbor extends Model
{
    protected $table    = "harbors";
    protected $fillable = ['id', 'name', 'code','display_name','country_id','varation'];

    public function globalcharge()
    {

        return $this->hasOne('App\GlobalCharge');
    }

    public function rate()
    {

        return $this->hasOne('App\Rate');
    }
    public function globalcharport()
    {
        return $this->hasMany('App\GlobalCharPort');
    }

    public function terms(){
        return hasMany('App\TermAndCondition');
    }

    public function termport(){
        return hasMany('App\TermsPort');
    }
    
    public function country(){
        return $this->belongsTo('App\Country');
    }
}
