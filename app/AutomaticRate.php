<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Http\Filters\AutomaticRateFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use OwenIt\Auditing\Contracts\Auditable;

class AutomaticRate extends Model implements Auditable
{
    use SoftDeletes;
    use \OwenIt\Auditing\Auditable;
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

    protected $fillable = [
        'id', 'quote_id', 'contract', 'validity_start', 'validity_end', 'origin_port_id',
        'destination_port_id', 'carrier_id', 'rates', 'markups', 'currency_id', 'total', 'amount', 'origin_airport_id',
        'destination_airport_id', 'airline_id', 'remarks', 'remarks_english', 'remarks_spanish', 'remarks_portuguese',
        'schedule_type', 'transit_time', 'via'
    ];

    public function quote()
    {
        return $this->belongsTo('App\QuoteV2', 'id', 'quote_id');
    }

    public function quotev2()
    {
        return $this->belongsTo('App\QuoteV2', 'quote_id');
    }

    public function inland()
    {
        return $this->hasMany('App\AutomaticInland', 'automatic_rate_id');
    }

    public function automaticInlandLclAir()
    {
        return $this->hasMany('App\AutomaticInlandLclAir', 'automatic_rate_id');
    }

    public function inland_lcl()
    {
        return $this->hasMany('App\AutomaticInlandLclAir', 'automatic_rate_id');
    }

    public function currency()
    {
        return $this->hasOne('App\Currency', 'id', 'currency_id');
    }

    public function carrier()
    {
        return $this->hasOne('App\Carrier', 'id', 'carrier_id');
    }

    public function airline()
    {
        return $this->hasOne('App\Airline', 'id', 'airline_id');
    }

    public function origin_port()
    {
        return $this->hasOne('App\Harbor', 'id', 'origin_port_id');
    }

    public function destination_port()
    {
        return $this->hasOne('App\Harbor', 'id', 'destination_port_id');
    }

    public function origin_airport()
    {
        return $this->hasOne('App\Airport', 'id', 'origin_airport_id');
    }

    public function destination_airport()
    {
        return $this->hasOne('App\Airport', 'id', 'destination_airport_id');
    }

    public function country_code()
    {
        return $this->hasManyThrough('App\Country', 'App\Harbor', 'country_id', 'id');
    }

    public function charge()
    {
        return $this->hasMany('App\Charge', 'automatic_rate_id');
    }

    public function charge_lcl_air()
    {
        return $this->hasMany('App\ChargeLclAir', 'automatic_rate_id');
    }

    public function totals()
    {
        return $this->hasMany('App\AutomaticRateTotal', 'automatic_rate_id');
    }

    public function total_rate()
    {
        return $this->hasOne('App\AutomaticRateTotal', 'automatic_rate_id');
    }

    public function scopeCharge($query, $type_id, $type)
    {
        $query->whereHas('charge', function ($query) use ($type_id) {
            $query->where('type_id', $type_id);
        })->orWhereHas('inland', function ($query) use ($type) {
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
        return $query->whereHas('charge_lcl_air', function ($query) use ($type_id) {
            $query->where('type_id', $type_id);
        })->orWhereHas('automaticInlandLclAir', function ($query) use ($type) {
            $query->where('type', $type);
        });
    }

    public function scopeFilterByQuote($query, $quote_id)
    {
        return $query->where('quote_id', '=', $quote_id);
    }

    public function scopeFilter(Builder $builder, Request $request)
    {
        return (new AutomaticRateFilter($request, $builder))->filter();
    }

    public function scopeGetCharge($query, $type)
    {
        return $query->whereHas('charge', function ($query) use ($type) {
            $query->where('type_id', $type);
        });
    }

    public function scopeGetChargeLcl($query, $type)
    {
        return $query->whereHas('charge_lcl_air', function ($query) use ($type) {
            $query->where('type_id', $type);
        });
    }

    public function scopeGetQuote($query, $id)
    {
        return $query->where('quote_id', $id);
    }

    public function duplicate($quote)
    {

        $new_rate = $this->replicate();
        $new_rate->quote_id = $quote->id;
        $new_rate->save();

        if ($quote->type == 'FCL') {
            $this->load(
                'charge',
                'totals'
            );
        } else if ($quote->type == 'LCL') {
            $this->load(
                'charge_lcl_air',
                'totals'
            );
        }

        $relations = $this->getRelations();

        foreach ($relations as $relation) {
            foreach ($relation as $relationRecord) {

                $newRelationship = $relationRecord->replicate();
                if($newRelationship->quote_id){
                    $newRelationship->quote_id = $quote->id;
                }
                $newRelationship->automatic_rate_id = $new_rate->id;
                $newRelationship->save();
            }
        }

        return $new_rate;
    }

    public function scopeSelectCharge($q)
    {
        return $q->with(['charge' => function ($query) {
            $query->where('type_id', 3);
            $query->select(
                'id',
                'automatic_rate_id',
                'amount as price',
                'markups as profit',
                'surcharge_id',
                'calculation_type_id',
                'currency_id'
            );
        }]);
    }

    public function scopeSelectChargeApi($q, $type)
    {
        if($type == 'FCL'){
            return $q->with(['charge' => function ($query) {
                $query->where('type_id', 3);
                $query->select(
                    'id',
                    'automatic_rate_id',
                    'amount as price',
                    'markups as profit',
                    'surcharge_id',
                    'calculation_type_id',
                    'currency_id'
                );
            }]);
        }else{
            return $q->with(['charge_lcl_air' => function ($query) {
                $query->where('type_id', 3);
                $query->select(
                    'id',
                    'automatic_rate_id',
                    'price_per_unit as price',
                    'units',
                    'minimum',
                    'markup as profit',
                    'total',
                    'surcharge_id',
                    'calculation_type_id',
                    'currency_id'
                );
            }]);
        }
    }

    public function scopeSelectFields($query)
    {
        return $query->select('id', 'quote_id', 'contract', 'validity_start as valid_from', 'validity_end as valid_until', 'markups as profit', 'total', 'schedule_type', 'transit_time', 'via', 'origin_port_id', 'destination_port_id', 'currency_id', 'carrier_id');
    }

    public function scopeCarrierRelation($query)
    {
        $query->with(['carrier' => function ($q) {
            $q->select('id', 'name', 'image as url');
        }]);
    }

    public function getProfitAttribute($value)
    {
        $json = json_decode($value);

        if (!is_object($json)) {
            return json_decode($json);
        }else{
            return $json;
        }
        
    }

    /*public function getTotalAttribute($value)
    {

        return json_decode(json_decode($value));
    }*/
}
