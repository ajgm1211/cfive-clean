<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ViewContractLclRates extends Model
{
    protected $table = 'views_contractlcl_rates';

    public function scopeCarrier($query, $carrier)
    {
        if ($carrier != "null") {
            return $query->where('carrier', $carrier);
        }
    }

    public function scopeStatus($query, $status)
    {   

        if ($status != "null") {
            return $query->where('status', $status);
        }
    }

    public function scopeDestPort($query, $port_dest)
    {
        if ($port_dest != "null") {
            return $query->where('port_dest', $port_dest);
        }
    }

    public function scopeOrigPort($query, $port_orig)
    {
        if ($port_orig != "null") {
            return $query->where('port_orig', $port_orig);
        }
    }
}
