<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QuoteV2 extends Model
{

    protected $fillable = ['quote_id','company_user_id','custom_quote_id','type','quote_validity','origin_address','destination_address','company_id','origin_port_id','destination_port_id','price_id','contact_id','delivery_type','currency_id','user_id','equipment','incoterm_id','status','date_issued'];

    public function company()
    {
        return $this->belongsTo('App\Company');
    }

    public function contact()
    {
        return $this->belongsTo('App\Contact');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function currency()
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

    public function incoterm()
    {
        return $this->hasOne('App\Incoterm','id','incoterm_id');
    }

    public function price()
    {
        return $this->hasOne('App\Price','id','price_id');
    }
}
