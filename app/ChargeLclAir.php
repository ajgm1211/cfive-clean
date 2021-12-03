<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use App\Http\Filters\ChargeFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ChargeLclAir extends Model
{

    protected $fillable = [
        'automatic_rate_id', 'type_id', 'surcharge_id', 'calculation_type_id', 'units', 'price_per_unit',
        'currency_id', 'total', 'markup', 'minimum', 'provider_name'
    ];

    public function automatic_rate()
    {
        return $this->belongsTo('App\AutomaticRate', 'automatic_rate_id', 'id');
    }

    public function charge_sale_code_quote()
    {
        return $this->hasOne('App\ChargeLclSaleCodeQuote');
    }

    public function currency()
    {
        return $this->belongsTo('App\Currency');
    }

    public function type()
    {
        return $this->belongsTo('App\TypeDestiny');
    }

    public function surcharge()
    {
        return $this->hasOne('App\Surcharge', 'id', 'surcharge_id');
    }

    public function calculation_type()
    {
        return $this->hasOne('App\CalculationTypeLcl', 'id', 'calculation_type_id');
    }

    public function scopeFilter(Builder $builder, Request $request)
    {
        return (new ChargeFilter($request, $builder))->filter();
    }

    public function scopeFilterByAutorate($query, $automatic_rate_id)
    {
        return $query->where('automatic_rate_id', '=', $automatic_rate_id);
    }

    public function scopeSelectFields($query)
    {
        return $query->select('charge_lcl_airs.id', 'automatic_rate_id', 'charge_lcl_airs.type_id', 'charge_lcl_airs.surcharge_id', 'charge_lcl_airs.units', 'charge_lcl_airs.price_per_unit as price', 'charge_lcl_airs.total', 'charge_lcl_airs.markup as profit', 'charge_lcl_airs.calculation_type_id', 'charge_lcl_airs.minimum', 'charge_lcl_airs.currency_id');
    }
}
