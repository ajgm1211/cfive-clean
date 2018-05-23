<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = ['business_name','phone','address','email','associated_contacts','associated_quotes','price_id'];

    public function contact()
    {
        return $this->hasMany('App\Contact');
    }

    public function price()
    {
        return $this->belongsTo('App\Price');
    }

    public function company_price()
    {
        return $this->hasMany('App\CompanyPrice');
    }

    public function price_name()
    {
        return $this->hasManyThrough('App\Price','App\CompanyPrice','company_id','id','id','price_id');
    }
}
