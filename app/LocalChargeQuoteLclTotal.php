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

    public function get_port()
    {
        return $this->hasOne('App\Harbor', 'id', 'port_id');
    }

    public function get_type()
    {
        return $this->hasOne('App\TypeDestiny', 'id', 'type_id');
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
        return $query->where('type_id', $type);
    }

    public function totalize()
    {
        $charges = LocalChargeQuoteLcl::where(['quote_id' => $this->quote_id, 'type_id' => $this->type_id, 'port_id' =>  $this->port_id])->get();

        $totals = 0;
        
        foreach ($charges as $charge) {
            if ($charge->total != null) {
                $quote = $this->quote()->first();
                $exchange = ratesCurrencyQuote($charge->currency_id, $this->currency->alphacode,$quote['pdf_options']['exchangeRates']);
                // $total_w_exchange = $charge->total / $exchange;
                $total_w_exchange == 0 ? 0 : ($charge->total /  $exchange);
                $totals += number_format((float)$total_w_exchange, 2, '.', '');
            }
        }

        $this->update([
            'total' => $totals,
        ]);

        $quote = $this->quote()->first();

        $quote->updatePdfOptions('exchangeRates');
    }

    public function duplicate($quote)
    {
        $new_record = $this->replicate();
        $new_record->quote_id = $quote->id;
        $new_record->save();

        return $new_record;
    }
}
