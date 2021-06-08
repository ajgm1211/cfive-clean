<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Http\Filters\AutomaticInlandFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use OwenIt\Auditing\Contracts\Auditable;

class AutomaticInland extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    protected $casts = [
        'markup' => 'array',
        'rate' => 'array'
    ];

    protected $fillable = ['quote_id', 'charge', 'automatic_rate_id', 'provider', 'provider_id', 'contract', 
        'validity_start', 'validity_end', 'port_id', 'type', 'distance', 'rate', 'markup', 'currency_id', 'inland_totals_id'];

    public function quote()
    {
        return $this->belongsTo('App\QuoteV2', 'quote_id', 'id');
    }

    public function rate()
    {
        return $this->belongsTo('App\AutomaticRate', 'automatic_rate_id', 'id');
    }

    public function currency()
    {
        return $this->hasOne('App\Currency', 'id', 'currency_id');
    }

    public function port()
    {
        return $this->hasOne('App\Harbor', 'id', 'port_id');
    }

    public function providers()
    {
        return $this->hasOne('App\Provider', 'id', 'provider_id');
    }

    public function inland_totals()
	{
		return $this->belongsTo('App\AutomaticInlandTotal','inland_totals_id','id');
	}

    public function country_code()
    {
        return $this->hasManyThrough('App\Country', 'App\Harbor', 'country_id', 'id');
    }

    public function getPriceAttribute($array)
    {
        $array = json_decode(json_decode($array));

        $value = [];

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
                    if ($k == 'c'.$container->code) {
                        $value['c'.$container->code] = $amount_value;
                    }
                }
            }
        }

        return $value;
    }

    public function getProfitAttribute($array)
    {
        $array = json_decode(json_decode($array));

        return $array;
    }

    public function scopeFilterByQuote($query, $quote_id)
    {
        return $query->where('quote_id', '=', $quote_id);
    }

    public function scopeFilter(Builder $builder, Request $request)
    {
        return (new AutomaticInlandFilter($request, $builder))->filter();
    }

    public function inland_address()
    {
        return $this->hasOne('App\InlandAddress', 'id', 'inland_address_id');
    }

    public function scopeSelectFields($query)
    {
        return $query->select('id', 'provider_id', 'provider', 'charge', 'contract', 'distance', 'port_id', 'type', 'distance', 'rate as price', 'markup as profit', 'currency_id', 'validity_start as valid_from', 'validity_start as valid_until');
    }

    public function scopeGetPortRelation($query)
    {
        $query->with(['port' => function ($q) {
            $q->select('id', 'display_name');
        }]);
    }
}
