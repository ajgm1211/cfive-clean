<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AutomaticRate extends Model
{   
    use SoftDeletes;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    protected $casts = [
        'amount' => 'array',
        'markups' => 'array',
        'total' => 'array',
    ];

    protected $fillable = ['quote_id','contract','validity_start','validity_end','origin_port_id','destination_port_id','carrier_id','rates','markups','currency_id','total','amount','markups','origin_airport_id','destination_airport_id','airline_id'];

    public function quote()
    {
        return $this->belongsTo('App\QuoteV2','id','quote_id');
    }

    public function inland()
    {
        return $this->hasMany('App\AutomaticInland','automatic_rate_id');
    }

    public function currency()
    {
        return $this->hasOne('App\Currency','id','currency_id');
    }

    public function carrier()
    {
        return $this->hasOne('App\Carrier','id','carrier_id');
    }

    public function origin_port()
    {
        return $this->hasOne('App\Harbor','id','origin_port_id');
    }

    public function destination_port()
    {
        return $this->hasOne('App\Harbor','id','destination_port_id');
    }
  
      public function origin_airport()
    {
        return $this->hasOne('App\Airline','id','origin_airport_id');
    }

    public function destination_airport()
    {
        return $this->hasOne('App\Airline','id','destination_airport_id');
    }

    public function country_code()
    {
        return $this->hasManyThrough('App\Country','App\Harbor','country_id','id');
    }

    public function charge()
    {
        return $this->hasMany('App\Charge','automatic_rate_id');
    }

    public function charge_lcl_air()
    {
        return $this->hasMany('App\ChargeLclAir','automatic_rate_id');
    }    
}
