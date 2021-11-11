<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Harbor extends Model
{
    protected $table = "harbors";
    protected $fillable = ['id', 'name', 'code', 'display_name', 'coordinates', 'country_id', 'varation'];

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

    public function terms()
    {
        return $this->hasMany('App\TermAndCondition');
    }

    public function termport()
    {
        return $this->hasMany('App\TermsPort');
    }

    public function country()
    {
        return $this->belongsTo('App\Country');
    }

    public function getParentHarbor()
    {
        return $this->belongsTo('App\Habor', 'harbor_parent');
    }

    public function getCountryHarborIdAttribute()
    {
        return "{$this->country_id}-{$this->id}";
    }

    public function getIdCompleteAttribute()
    {
        return "{$this->id}-{$this->country_id}-{$this->harbor_parent}";
    }
}
