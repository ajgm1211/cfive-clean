<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Quote extends Model
{
    protected $fillable = ['owner','incoterm','modality','validity','origin_address','destination_address','company_id','origin_harbor_id',
    'destination_harbor_id','price_id','contact_id','qty_20','qty_40','qty_40_hc','status_quote_id','pick_up_date',
    'delivery_type','currency_id','type','sub_total_origin','sub_total_freight','sub_total_destination'];

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
        return $this->belongsTo('App\User','owner','id');
    }

    public function price()
    {
        return $this->belongsTo('App\Price');
    }

    public function status()
    {
        return $this->hasOne('App\StatusQuote','id','status_quote_id');
    }

    public function origin_harbor()
    {
        return $this->hasOne('App\Harbor','id','origin_harbor_id');
    }

    public function destination_harbor()
    {
        return $this->hasOne('App\Harbor','id','destination_harbor_id');
    }

    public function company_name()
    {
        return $this->hasManyThrough('App\Company','App\CompanyPrice','price_id','id','id','company_id');
    }
    public function schedules()
    {
        return $this->hasMany('App\Schedule');
    }
    public function currencies()
    {
        return $this->hasOne('App\Currency','id','currency_id');
    }
}
