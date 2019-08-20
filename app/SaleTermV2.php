<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SaleTermV2 extends Model
{
    protected $fillable = ['quote_id', 'port_id', 'airport_id', 'type'];

    public function port()
    {
        return $this->hasOne('App\Harbor','id','port_id');
    }

    public function airport()
    {
        return $this->hasOne('App\Airport','id','airport_id');
    }

    public function charge()
    {
        return $this->hasMany('App\SaleTermV2Charge','sale_term_id','id');
    }
}
