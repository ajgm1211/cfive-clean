<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Price extends Model
{
    protected $fillable = ['name','description'];

    public function company()
    {
        return $this->hasMany('App\Company');
    }

    public function company_price()
    {
        return $this->hasMany('App\CompanyPrice');
    }

    public function company_name()
    {
        return $this->hasManyThrough('App\Company','App\CompanyPrice','price_id','id','id','company_id');
    }

    public function freight_markup()
    {
        return $this->hasMany('App\FreightMarkup');
    }

    public function local_markup()
    {
        return $this->hasMany('App\LocalChargeMarkup');
    }

    public function inland_markup()
    {
        return $this->hasMany('App\InlandChargeMarkup');
    }
}
