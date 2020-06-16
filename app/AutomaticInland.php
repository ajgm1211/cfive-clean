<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AutomaticInland extends Model
{
	protected $casts = [
		'markup' => 'array',
		'rate' => 'array',
	];

	protected $fillable = ['quote_id','automatic_rate_id','provider','contract','validity_start','validity_end','port_id','type','distance','rate','markup','currency_id'];

	public function quote()
	{
		return $this->belongsTo('App\QuoteV2','id','quote_id');
	}

    public function rate()
    {
        return $this->belongsTo('App\AutomaticRate','automatic_rate_id','id');
    }

	public function currency()
	{
		return $this->hasOne('App\Currency','id','currency_id');
	}

	public function port()
	{
		return $this->hasOne('App\Harbor','id','port_id');
	}

	public function country_code()
	{
		return $this->hasManyThrough('App\Country','App\Harbor','country_id','id');
    }
    
    public function getPriceAttribute($array)
    {
        $array = json_decode(json_decode($array));

        $value = array();

        foreach ($array as $k => $amount_value) {
            if ($k == 'c20') {
                $value['c20DV'] = $amount_value;
            } elseif ($k == 'c40') {
                $value['c40DV'] = $amount_value;
            } elseif ($k == 'c40hc') {
                $value['c40HC'] = $amount_value;
            } elseif ($k == 'c40nor') {
                $value['c40NOR'] = $amount_value;
            } elseif ($k == 'c45hc') {
                $value['c45HC'] = $amount_value;
            } else {
                $containers = Container::all();
                foreach ($containers as $container) {
                    if ($k == 'c' . $container->code) {
                        $value['c' . $container->code] = $amount_value;
                    }
                }
            }
        }

        return $value;
    }
}
