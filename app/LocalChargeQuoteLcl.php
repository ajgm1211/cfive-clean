<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

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

    public function totalLcl($index){
        if($index == 'units' || $index == 'price'  || $index == 'total'){
            $total = $this->price * $this->units;

            $this->update(['total' => $total]);
        }
    }
}
