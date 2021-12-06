<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChargeSaleCodeQuote extends Model
{
    protected $fillable = ['charge_id', 'sale_term_code_id', 'local_charge_quote_id'];

    public function charge()
    {
        return $this->belongsTo('App\Charge');
    }
}
