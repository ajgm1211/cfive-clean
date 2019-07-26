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
}
