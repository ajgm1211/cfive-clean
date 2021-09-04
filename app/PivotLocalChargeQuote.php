<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PivotLocalChargeQuote extends Model
{
    protected $fillable = ['charge_id', 'local_charge_quote_id', 'quote_id'];

    public $timestamps = false;

    public function quote()
    {
        return $this->belongsTo('App\QuoteV2', 'id', 'quote_id');
    }
}
