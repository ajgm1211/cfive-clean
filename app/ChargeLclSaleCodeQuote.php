<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChargeLclSaleCodeQuote extends Model
{
    protected $fillable = ['charge_lcl_air_id', 'sale_term_code_id', 'local_charge_quote_lcl_id'];
}
