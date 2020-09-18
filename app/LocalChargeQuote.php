<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LocalChargeQuote extends Model
{
    protected $fillable =   ['price', 'profit', 'surcharge_id', 'calculation_type_id', 'currency_id', 'port_id', 'quote_id', 'type_id'];

    protected $casts = [
        'price' => 'array',
        'profit' => 'array',
    ]; 

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
}
