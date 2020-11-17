<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Http\Filters\AutomaticInlandFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class AutomaticInlandLclAir extends Model

{
	protected $fillable = [
		'quote_id','automatic_rate_id','provider','contract','validity_start','validity_end','port_id','type','distance','units','price_per_unit','markup','total','currency_id',
		'provider_id','inland_address_id','charge'];

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

	public function provider()
	{
		return $this->hasOne('App\Provider','id','provider_id');
	}
	
	public function scopeFilterByQuote($query,$quote_id){
        return $query->where( 'quote_id', '=', $quote_id );
    }

    public function scopeFilter(Builder $builder, Request $request)
    {
        return (new AutomaticInlandFilter($request, $builder))->filter();
    }

    public function inland_address()
    {
        return $this->hasOne('App\InlandAddress','id','inland_address_id');
    }
}
