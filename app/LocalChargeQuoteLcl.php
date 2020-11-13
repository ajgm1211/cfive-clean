<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class LocalChargeQuoteLcl extends Model
{
    protected $fillable = ['charge', 'calculation_type_id', 'units', 'price', 'total', 'currency_id', 'port_id', 'quote_id', 'type_id'];

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

    public function totalLcl($index)
    {
        if ($index == 'units' || $index == 'price'  || $index == 'total') {
            $total = $this->price * $this->units;

            $this->update(['total' => $total]);
        }
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
                $exchange = ratesCurrencyFunction($charge->currency_id, $currency);
                $total_w_exchange = $charge->total / $exchange;
                $totals += number_format((float)$total_w_exchange, 2, '.', '');
            }
        }

        if (!empty($local_charge_quote_total)) {
            $local_charge_quote_total->delete();
        }

        LocalChargeQuoteLclTotal::create([
            'total' => $totals,
            'quote_id' => $quote->id,
            'port_id' => $this->port_id,
            'currency_id' => $currency_id,
            'type_id' => $this->type_id,
        ]);
    }
}
