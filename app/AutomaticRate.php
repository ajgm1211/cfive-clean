<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AutomaticRate extends Model
{
    protected $fillable = ['quote_id','contract','validity_start','validity_end','origin_port_id','destination_port_id','carrier_id','rates','markups','currency_id','total'];

    public function quote()
    {
        return $this->belongsTo('App\QuoteV2','id','quote_id');
    }

    public function currency()
    {
        return $this->belongsTo('App\Currency');
    }

    public function carrier()
    {
        return $this->belongsTo('App\Currency');
    }

    public function origin_port()
    {
        return $this->hasOne('App\Harbor','id','origin_port_id');
    }

    public function destination_port()
    {
        return $this->hasOne('App\Harbor','id','destination_port_id');
    }
}
