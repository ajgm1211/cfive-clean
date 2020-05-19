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

    protected $fillable = ['quote_id','contract','validity_start','validity_end','origin_port_id','destination_port_id','carrier_id','rates','markups','currency_id','total','amount','markups','origin_airport_id','destination_airport_id','airline_id','remarks','schedule_type','transit_time','via'];

    public function quote()
    {
        return $this->belongsTo('App\QuoteV2','id','quote_id');
    }

    public function quotev2()
    {
        return $this->belongsTo('App\QuoteV2','quote_id');
    }

    public function inland()
    {
        return $this->hasMany('App\AutomaticInland','automatic_rate_id');
    }

    public function automaticInlandLclAir()
    {
        return $this->hasMany('App\AutomaticInlandLclAir','automatic_rate_id');
    }

    public function currency()
    {
        return $this->hasOne('App\Currency','id','currency_id');
    }

    public function carrier()
    {
        return $this->hasOne('App\Carrier','id','carrier_id');
    }

    public function airline()
    {
        return $this->hasOne('App\Airline','id','airline_id');
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
        return $this->hasOne('App\Airport','id','origin_airport_id');
    }

    public function destination_airport()
    {
        return $this->hasOne('App\Airport','id','destination_airport_id');
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

    public function scopeCharge($query, $type_id, $type)
    {
        $query->whereHas('charge', function ($query) use($type_id) {
            $query->where('type_id', $type_id);
        })->orWhereHas('inland', function($query) use($type)  {
            $query->where('type', $type);
        });

        return $query;
    }

    public function scopeChargeNotSale($query)
    {
        return $query->whereHas('charge', function ($query) {
            $query->where('saleterm', 0);
        });
    }

    public function scopeChargeLclAir($query, $type_id, $type)
    {
        return $query->whereHas('charge_lcl_air', function ($query) use($type_id) {
            $query->where('type_id', $type_id);
        })->orWhereHas('automaticInlandLclAir', function($query) use($type) {
            $query->where('type', $type);
        });
    }
}
