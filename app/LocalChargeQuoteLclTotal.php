<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class LocalChargeQuoteLclTotal extends Model
{
    protected $fillable = ['total', 'quote_id', 'port_id', 'currency_id', 'type_id'];

    public function currency()
    {
        return $this->belongsTo('App\Currency');
    }

    public function quote()
    {
        return $this->belongsTo('App\QuoteV2', 'quote_id');
    }

    public function quotev2()
    {
        return $this->belongsTo('App\QuoteV2', 'quote_id');
    }

    public function totalize()
    {
        $charges = LocalChargeQuoteLcl::where(['quote_id' => $this->quote_id, 'type_id' => $this->type_id, 'port_id' =>  $this->port_id])->get();

        $totals = 0;
        
        foreach ($charges as $charge) {
            if ($charge->total != null) {
                $exchange = ratesCurrencyFunction($charge->currency_id, $this->currency->alphacode);
                $total_w_exchange = $charge->total / $exchange;
                $totals += number_format((float)$total_w_exchange, 2, '.', '');
            }
        }

        $this->update([
            'total' => $totals,
        ]);
    }
}
