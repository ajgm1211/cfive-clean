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
		'provider_id','inland_totals_id','charge'];

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
	
	public function inland_totals()
	{
		return $this->belongsTo('App\AutomaticInlandTotal','inland_totals_id','id');
	}

    public function scopeSelectFields($query)
    {
        return $query->select('id', 'provider_id', 'inland_address_id', 'contract', 'distance', 'port_id', 'type', 'distance', 'units', 'price_per_unit as price', 'markup as profit', 'total', 'currency_id', 'validity_start as valid_from', 'validity_start as valid_until');
    }

    public function scopeGetPortRelation($query)
    {
        $query->with(['port' => function ($q) {
            $q->select('id', 'display_name');
        }]);
    }

    public function providers()
    {
        return $this->hasOne('App\Provider', 'id', 'provider_id');
    }
}
