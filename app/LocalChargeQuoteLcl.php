<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use OwenIt\Auditing\Contracts\Auditable;

class LocalChargeQuoteLcl extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    
    protected $fillable = ['charge', 'calculation_type_id', 'sale_term_code_id', 'units', 'price', 'total', 'currency_id', 'port_id', 'quote_id', 'type_id', 'provider_name', 'surcharge_id'];

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
        return $this->belongsTo('App\CalculationTypeLcl', 'calculation_type_id');
    }

    public function currency()
    {
        return $this->belongsTo('App\Currency');
    }

    public function port()
    {
        return $this->belongsTo('App\Harbor', 'port_id');
    }

    public function totalLcl($index)
    {
        if ($index == 'units' || $index == 'price'  || $index == 'total') {
            $total = $this->price * $this->units;

            $this->update(['total' => $total]);
        }
    }

    public function scopeGetPort($q)
    {
        return $q->with(['port' => function ($query) {
            $query->select('id', 'display_name');
        }]);
    }

    public function totalize()
    {
        $quote = $this->quotev2()->first();

        $local_charge_quote_total = LocalChargeQuoteLclTotal::where(['quote_id' => $quote->id, 'type_id' => $this->type_id, 'port_id' =>  $this->port_id])->first();

        $charges = $this->where(['quote_id' => $quote->id, 'type_id' => $this->type_id, 'port_id' =>  $this->port_id])->get();

        $totals = 0;

        $currency = @Auth::user()->companyUser->currency->alphacode;
        $currency_id = @Auth::user()->companyUser->currency_id;

        if (!empty($local_charge_quote_total)) {
            $currency = $local_charge_quote_total->currency->alphacode;
            $currency_id = $local_charge_quote_total->currency_id;
        }

        foreach ($charges as $charge) {
            if ($charge->total != null) {
                $exchange = ratesCurrencyQuote($charge->currency_id, $currency,$quote['pdf_options']['exchangeRates']);
                $total_w_exchange = $charge->total / $exchange;
                $totals += isDecimal($total_w_exchange);
            }
        }

        $local_charge_quote_total->total = $totals;
        $local_charge_quote_total->update();
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

    public function duplicate($quote)
    {
        $new_record = $this->replicate();
        $new_record->quote_id = $quote->id;
        $new_record->save();

        return $new_record;
    }

    public function groupingCharges($localcharge)
    {
        $current_total = $this->total;
        $total_to_add = ((float)$localcharge['price_per_unit'] * (float)$localcharge['units']) + (float)$localcharge['markup'];
        $localcharge['total'] = $total_to_add;
        $added_total = $current_total + $total_to_add;
        $this->price = $added_total;
        $this->total = $added_total;
        $this->units = 1;
        $this->calculation_type_id = 2;
        $this->update();
    }
}
