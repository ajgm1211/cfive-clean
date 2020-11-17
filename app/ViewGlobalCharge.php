<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Carrier;

class ViewGlobalCharge extends Model
{
    protected $table = 'views_globalcharges';

    public function contract()
    {
        return $this->belongsTo('App\Contract');
    }

    public function origin_harbor()
    {
        return $this->hasOne('App\Harbor','id','orig_port');
    }

    public function destination_harbor()
    {
        return $this->hasOne('App\Harbor','id','dest_port');
    }

    public function scopeCarrier($query, $carrier)
    {
        if ($carrier != '') {
            $carrier = Carrier::find($carrier);
            return $query->where('carrier','like','%'.$carrier->name.'%');
        }
    }

    public function scopeCompanyUser($query, $companyUser)
    {
        if ($companyUser != '') {
            return $query->where('company_user_id', $companyUser);
        }
    }    
}
