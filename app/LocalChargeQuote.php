<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use OwenIt\Auditing\Contracts\Auditable;
use SebastianBergmann\CodeCoverage\Report\Xml\Totals;

class LocalChargeQuote extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $fillable = ['price', 'profit', 'total', 'charge', 'surcharge_id', 'calculation_type_id', 'currency_id', 'port_id', 'quote_id', 'type_id', 'sale_term_v3_id', 'provider_name', 'sale_term_code_id', 'source'];

    protected $casts = [
        'price' => 'array',
        'profit' => 'array',
        'total' => 'array',
    ];

    public function quotev2()
    {
        return $this->belongsTo('App\QuoteV2', 'quote_id');
    }

    public function surcharge()
    {
        return $this->belongsTo('App\Surcharge');
    }

    public function calculation_type()
    {
        return $this->belongsTo('App\CalculationType');
    }

    public function currency()
    {
        return $this->belongsTo('App\Currency');
    }

    public function port()
    {
        return $this->belongsTo('App\Harbor', 'port_id');
    }

    public function type()
    {
        return $this->belongsTo('App\TypeDestiny', 'type_id');
    }

    /**
     * sumarize
     *
     * @return void
     */
    public function sumarize()
    {
        $quote = $this->quotev2()->first();

        $equip = $quote->getContainerCodes($quote->equipment);

        $equip_array = explode(',', $equip);

        array_splice($equip_array, -1, 1);

        $totals = [];

        foreach ($equip_array as $eq) {
            $totals['c' . $eq] = 0;
        }

        if ($this->price != null) {
            foreach ($equip_array as $eq) {
                foreach ($this->price as $key => $price) {
                    if ($key == 'c' . $eq) {
                        $totals[$key] += $price;
                    }
                }
            }
        }

        if ($this->profit != null) {
            foreach ($equip_array as $eq) {
                foreach ($this->profit as $k => $profit) {
                    if ($k == 'm' . $eq) {
                        $clear_key = str_replace('m', 'c', $k);
                        $totals[$clear_key] += $profit;
                    }
                }
            }
        }

        $this->update(['total' => $totals]);
    }

    /**
     * totalize
     *
     * @return void
     */
    public function totalize()
    {
        $quote = $this->quotev2()->first();

        $local_charge_quote_total = LocalChargeQuoteTotal::where(['quote_id' => $quote->id, 'type_id' => $this->type_id, 'port_id' =>  $this->port_id])->first();

        $charges = $this->where(['quote_id' => $quote->id, 'type_id' => $this->type_id, 'port_id' =>  $this->port_id])->get();

        $equip = $quote->getContainerCodes($quote->equipment);

        $equip_array = explode(',', $equip);

        array_splice($equip_array, -1, 1);

        $totals = [];

        foreach ($equip_array as $eq) {
            $totals['c' . $eq] = 0;
        }

        $currency = @Auth::user()->companyUser->currency->alphacode;
        $currency_id = @Auth::user()->companyUser->currency_id;

        if (!empty($local_charge_quote_total)) {
            $currency = $local_charge_quote_total->currency->alphacode;
            $currency_id = $local_charge_quote_total->currency_id;
        }

        foreach ($charges as $charge) {
            if ($charge->total != null) {
                foreach ($equip_array as $eq) {
                    foreach ($charge->total as $key => $total) {
                        if ($key == 'c' . $eq) {
                            $quote = $this->quotev2()->first();
                            $exchange = ratesCurrencyQuote($charge->currency_id, $currency,$quote['pdf_options']['exchangeRates']);
                            $total_w_exchange = $total / $exchange;
                            $totals[$key] += isDecimal($total_w_exchange);
                        }
                    }
                }
            }
        }

        if(empty($local_charge_quote_total)){
            $local_charge = LocalChargeQuoteTotal::create([
                'quote_id' => $quote->id, 
                'type_id' => $this->type_id, 
                'port_id' =>  $this->port_id,
                'currency_id' => $currency_id,
                'totals'=>$totals
            ]);
        }else{
            $local_charge_quote_total->total = $totals;
            
            $local_charge_quote_total->update();
        }

        
    }

    /**
     * Grouping charges by sale code
     *
     * @param  mixed $localcharge
     * @return void
     */
    public function groupingCharges($localcharge)
    {
        $price_keys = [];

        foreach ($this->price as $key => $price) {
            ${'price_' . $key} = 'price->' . $key;
            array_push($price_keys,$key);
            foreach ($localcharge['price'] as $k => $new_price) {
                if ($key == $k) {
                    $new_price = floatvalue($new_price);
                    $price += $new_price;
                    $this->${'price_' . $key} = $price;
                    $this->update();
                }
            }
        }

        if(empty($localcharge['markup'])){
            return;
        }

        $new_profit = [];

        foreach ($price_keys as $price_key) {
            $profit_key = 'm' . substr($price_key,1);
            $new_profit[$profit_key] = 0;
            
            if(isset($this->profit[$profit_key])){
                $current_profit = $this->profit[$profit_key];
            }else{
                $current_profit = 0;
            }

            if(isset($localcharge['markup'][$profit_key])){
                $incoming_profit = $localcharge['markup'][$profit_key];
            }else{
                $incoming_profit = 0;
            }

            $new_profit[$profit_key] = $current_profit + $incoming_profit;

        }

        $this->profit = $new_profit;
        $this->update();

    }

    /**
     * scopeQuote
     *
     * @param  mixed $query
     * @param  mixed $id
     * @return void
     */
    public function scopeQuote($query, $id)
    {
        return $query->where('quote_id', $id);
    }

    /**
     * scopeType
     *
     * @param  mixed $query
     * @param  mixed $type
     * @return void
     */
    public function scopeType($query, $type)
    {
        return $query->where('type_id', $type);
    }

    public function scopeGetPort($q)
    {
        return $q->with(['port' => function ($query) {
            $query->select('id', 'display_name');
        }]);
    }

    public function duplicate($quote)
    {
        $new_record = $this->replicate();
        $new_record->quote_id = $quote->id;
        $new_record->save();

        return $new_record;
    }

    public function setPriceAttribute($array)
    {
        $this->attributes['price'] = $this->removeCommas($array);
    }

    public function setProfitAttribute($array)
    {
        $this->attributes['profit'] = $this->removeCommas($array);
    }

    public function removeCommas($array)
    {
        $containers = Container::all();

        if ($array != null || $array != '') {
            foreach ($array as $k => $amount) {
                foreach ($containers as $container) {
                    if ($k == 'c' . $container->code) {
                        $array['c' . $container->code] = floatvalue($amount);
                    } else if ($k == 'm' . $container->code) {
                        $array['m' . $container->code] = floatvalue($amount);
                    }
                }
            }
        }

        return json_encode($array);
    }
}
