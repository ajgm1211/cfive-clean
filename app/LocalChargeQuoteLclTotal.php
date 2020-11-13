<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

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
}
