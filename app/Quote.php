<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Quote extends Model
{
    protected $fillable = ['owner','incoterm','validity','origin_address','destination_address','company_id','origin_harbor_id',
        'destination_harbor_id','price_id','qty_20','qty_40','qty_40_hc','status_id','pick_up_date','delivery_type','type'];

    public function company()
    {
        return $this->belongsTo('App\Company');
    }

    public function user()
    {
        return $this->belongsTo('App\User','owner','id');
    }

    public function price()
    {
        return $this->belongsTo('App\Price');
    }

    public function origin_harbor()
    {
        return $this->hasOne('App\Harbor','id','origin_harbor_id');
    }

    public function destination_harbor()
    {
        return $this->hasOne('App\Harbor','id','destination_harbor_id');
    }
}
