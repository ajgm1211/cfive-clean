<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LocalChargeQuoteTotal extends Model
{
    protected $fillable = ['total', 'quote_id', 'port_id', 'currency_id'];

    protected $casts = [
        'total' => 'array',
    ];

    public function currency()
    {
        return $this->belongsTo('App\Currency');
    }

    public function scopeQuote($query, $quote)
    {
        return $query->where('quote_id', $quote);
    }

    public function scopePort($query, $port)
    {
        return $query->where('port_id', $port);
    }
}
