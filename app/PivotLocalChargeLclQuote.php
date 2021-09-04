<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PivotLocalChargeLclQuote extends Model
{
    protected $fillable = ['charge_lcl_air_id', 'local_charge_quote_lcl_id', 'quote_id'];

    public $timestamps = false;

    public function quote()
    {
        return $this->belongsTo('App\QuoteV2', 'id', 'quote_id');
    }
}
