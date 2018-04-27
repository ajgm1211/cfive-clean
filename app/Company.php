<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = ['business_name','phone','address','email','associated_contacts','associated_quotes','associated_price_level'];

    public function contact()
    {

        return $this->hasMany('App\Contact');
    }
}
