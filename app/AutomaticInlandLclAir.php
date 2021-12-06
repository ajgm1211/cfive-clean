<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Http\Filters\AutomaticInlandFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use OwenIt\Auditing\Contracts\Auditable;

class AutomaticInlandLclAir extends Model implements Auditable

{	
	use \OwenIt\Auditing\Auditable;
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

    public function inland_local_group()
    {
        return $this->hasOne('App\InlandLocalChargeLclGroup', 'automatic_inland_lcl_id');
    }

    public function scopeSelectFields($query)
    {
        return $query->select('id', 'provider_id', 'contract', 'distance', 'port_id', 'type', 'distance', 'units', 'price_per_unit as price', 'markup as profit', 'total', 'currency_id', 'validity_start as valid_from', 'validity_start as valid_until');
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

	public function syncProviders($provider)
    {
		if($provider){
			InlandProvider::create([
				'provider_type' => $provider['model'],
				'provider_id' => $provider['id'],
				'automatic_inland_id' => $this->id,
			]);
		}
    }

	public function scopeQuotation($query, $quote)
    {
        return $query->where('quote_id', $quote);
    }

    public function scopePort($query, $port)
    {
        return $query->where('port_id', $port);
    }

    public function scopeType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeConditionalPort($q, $port)
    {
        return $q->when($port, function ($query, $port) {
            return $query->where('port_id', $port);
        });
    }

    public function getInlandAddress()
    {
        $result = AutomaticInlandTotal::find($this->inland_totals_id);
        return $result->inland_address->address ?? null;
    }
}
