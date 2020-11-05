<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Http\Filters\AutomaticRateFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

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

    public function totalize($new_currency_id)
    {
        //getting all data needed to calculate totals
        $quote = $this->quotev2()->first();

        if ($quote->type == 'FCL') {
            $equip = $quote->getContainerCodes($quote->equipment);

            $equip_array = explode(',', $equip);

            array_splice($equip_array, -1, 1);

            $charges = $this->charge()->where([['surcharge_id', '!=', null], ['type_id', 3]])->get();

            $ocean_freight = $this->charge()->where('surcharge_id', null)->first();

            $this->update(['currency_id' => $new_currency_id]);

            $currency = $this->currency()->first();

            $totals_usd = [];

            foreach ($equip_array as $eq) {
                $totals_usd['c' . $eq] = 0;
            }

            // adding all charges together
            foreach ($charges as $charge) {
                $amount_array = json_decode($charge->amount);
                $charge_currency = $charge->currency()->first();
                foreach ($amount_array as $key => $value) {
                    if ($charge_currency->alphacode != 'USD') {
                        $charge_conversion = $charge_currency->rates;
                        $value /= $charge_conversion;
                        $value = round($value, 2);
                    }
                    $totals_usd[$key] += $value;
                }
            }

            //converting to autorate currency
            if ($currency->alphacode != 'USD') {
                $conversion = $currency->rates;
                foreach ($totals_usd as $cont => $price) {
                    $conv_price = $price * $conversion;
                    $totals_usd[$cont] = round($conv_price, 2);
                }
            }

            //adding autorate markups
            if ($this->markups != null) {
                $markups = json_decode($this->markups);
                foreach ($markups as $mark => $profit) {
                    $clear_key = str_replace('m', 'c', $mark);
                    $totals_usd[$clear_key] += $profit;
                }
            }

            //adding ocean freight
            if ($ocean_freight->amount != null) {
                $freight_amount = json_decode($ocean_freight->amount);
                foreach ($freight_amount as $fr => $am) {
                    $totals_usd[$fr] += round($am, 2);
                    $totals_usd[$fr] = round($totals_usd[$fr], 2);
                }
            }

            $totals = json_encode($totals_usd);

            $this->update(['total' => $totals]);
        } else if ($quote->type == 'LCL') {

            $charges = $this->charge_lcl_air()->where([['surcharge_id', '!=', null], ['type_id', 3]])->get();

            $ocean_freight = $this->charge_lcl_air()->where('surcharge_id', null)->first();

            $this->update(['currency_id' => $new_currency_id]);

            $currency = $this->currency()->first();

            $totals_usd = [];
            $totals_usd['total'] = 0;
            $totals_usd['per_unit'] = 0;

            // adding all charges together
            foreach ($charges as $charge) {
                $charge_currency = $charge->currency()->first();
                $charge_units = $charge->units;
                if ($charge_currency->alphacode != 'USD') {
                    $charge_conversion = $charge_currency->rates;
                    $tots_value = $charge->total;
                    $tots_value /= $charge_conversion;
                    $tots_value = round($tots_value, 2);
                    $per_unit_value = $charge->price_per_unit;
                    $per_unit_value /= $charge_conversion;
                    $per_unit_value = round($per_unit_value, 2);
                } else {
                    $tots_value = $charge->total;
                    $per_unit_value = $charge->price_per_unit;
                }
                $totals_usd['total'] += $tots_value;
                $totals_usd['per_unit'] += $per_unit_value;
            }

            //converting to autorate currency
            if ($currency->alphacode != 'USD') {
                $conversion = $currency->rates;
                foreach ($totals_usd as $cont => $price) {
                    $conv_price = $price * $conversion;
                    $totals_usd[$cont] = round($conv_price, 2);
                }
            }

            //adding ocean freight
            $freight_amount_per_unit = $ocean_freight->price_per_unit;
            $freight_amount = $ocean_freight->total;
            $total_units = $ocean_freight->units;
            $totals_usd['total'] += $freight_amount;
            $totals_usd['per_unit'] += $freight_amount_per_unit;
            $totals_usd['total'] = round($totals_usd['total'], 2);
            $totals_usd['per_unit'] = round($totals_usd['per_unit'], 2);

            //adding autorate markups
            if ($this->markups != null) {
                $markups = json_decode($this->markups, true);
                $markups['total'] = $markups['per_unit'] * $total_units;
                $totals_usd['total'] += $markups['total'];
                $totals_usd['per_unit'] += $markups['per_unit'];
            } else {
                $markups = [];
                $markups['total'] = 0;
                $markups['per_unit'] = 0;
            }

            $markups_json = json_encode($markups);
            $totals = json_encode($totals_usd);

            $this->update(['total' => $totals, 'markups' => $markups_json]);
        }
    }

    public function scopeGetCharge($query, $type)
    {
        return $query->whereHas('charge', function ($query) use ($type) {
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
                'charge'
            );
        } else if ($quote->type == 'LCL') {
            $this->load(
                'charge_lcl_airs'
            );
        }

        $relations = $this->getRelations();

        foreach ($relations as $relation) {
            foreach ($relation as $relationRecord) {

                $newRelationship = $relationRecord->replicate();
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
                'markups as profit'
            );
        }]);
    }

    public function scopeSelectFields($query)
    {
        return $query->select('id', 'quote_id', 'contract', 'validity_start as valid_from', 'validity_end as valid_until', 'markups as profit', 'total', 'origin_port_id', 'destination_port_id', 'currency_id', 'carrier_id');
    }

    public function scopeCarrierRelation($query)
    {
        $query->with(['carrier' => function ($q) {
            $q->select('id', 'name', 'image as url');
        }]);
    }

    /*public function getProfitAttribute($value){
        
        return json_decode(json_decode($value));

    }

    public function getTotalAttribute($value){
        
        return json_decode(json_decode($value));

    }*/
}
