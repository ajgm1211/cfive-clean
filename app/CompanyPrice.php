<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CompanyPrice extends Model
{
    protected $fillable = ['company_id','price_id'];

    public function price()
    {
        return $this->hasOne('App\Price');
    }

    public function company()
    {
        return $this->hasOne('App\Company');
    }
}
