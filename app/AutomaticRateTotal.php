<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\Http\Traits\QuoteV2Trait;

class AutomaticRateTotal extends Model
{
    use QuoteV2Trait;

    protected $casts = [
        'markups' => 'json',
        'total' => 'json'
    ];

    protected $fillable = [
        'id', 'quote_id', 'markups', 'currency_id', 'totals', 'automatic_rate_id','origin_port_id', 'destination_port_id'];

    public function quote()
    {
        return $this->belongsTo('App\QuoteV2', 'quote_id');
    }

    public function currency()
    {
        return $this->hasOne('App\Currency', 'id', 'currency_id');
    }

    public function rate()
    {
        return $this->belongsTo('App\AutomaticRate', 'automatic_rate_id');
    }

    public function origin_port()
    {
        return $this->hasOne('App\Harbor', 'id', 'origin_port_id');
    }

    public function destination_port()
    {
        return $this->hasOne('App\Harbor', 'id', 'destination_port_id');
    }

    public function scopeGetQuote($query, $id)
    {
        return $query->where('quote_id', $id);
    }

    public function totalize($new_currency_id)
    {
        //getting all data needed to calculate totals
        $quote = $this->quote()->first();

        $rate = $this->rate()->first();

        if ($quote->type == 'FCL') {
            $equip = $quote->getContainerCodes($quote->equipment);

            $equip_array = explode(',', $equip);

            array_splice($equip_array, -1, 1);

            $charges = $rate->charge()->where([['surcharge_id', '!=', null], ['type_id', 3]])->get();

            $ocean_freight = $rate->charge()->where('surcharge_id', null)->first();

            $this->update(['currency_id' => $new_currency_id]);

            $currency = $this->currency()->first();
            
            $usd = Currency::where('alphacode','USD')->first();

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
            $totals_rate = $this->convertToCurrency($usd,$currency,$totals_usd);

            //adding autorate markups
            if ($this->markups != null) {
                $markups = $this->markups;
                foreach ($markups as $mark => $profit) {
                    $clear_key = str_replace('m', 'c', $mark);
                    $totals_rate[$clear_key] += $profit;
                }
            }

            //adding ocean freight
            if ($ocean_freight->amount != null) {
                $freight_amount = json_decode($ocean_freight->amount);
                foreach ($freight_amount as $fr => $am) {
                    $totals_rate[$fr] += round($am, 2);
                    $totals_rate[$fr] = isDecimal($totals_rate[$fr], true);
                }
            }

            $totals_json = json_encode($totals_rate);

            $this->update(['totals' => $totals_json]);
            $rate->update(['total' => $totals_json]);

        } else if ($quote->type == 'LCL') {

            $charges = $rate->charge_lcl_air()->where([['surcharge_id', '!=', null], ['type_id', 3]])->get();

            $ocean_freight = $rate->charge_lcl_air()->where('surcharge_id', null)->first();

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
                $markups = $this->markups;
                $markups['total'] = $markups['per_unit'] * $total_units;
                $markups['total'] = isDecimal($markups['total'],true);
                $totals_usd['total'] += $markups['total'];
                $totals_usd['total'] = isDecimal($totals_usd['total'],true);
                $totals_usd['per_unit'] += $markups['per_unit'];
                $totals_usd['per_unit'] = isDecimal($totals_usd['per_unit'],true);
            } else {
                $markups = [];
                $markups['total'] = isDecimal(0,true);
                $markups['per_unit'] = isDecimal(0,true);
            }

            $totals = json_encode($totals_usd);

            $this->update(['totals' => $totals, 'markups' => $markups]);
            $rate->update(['total' => $totals]);
        }
    }
}
