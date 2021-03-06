<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ViewGlobalchargeLcl extends Model
{
    protected $table = 'views_globalcharges_lcl';

    public function scopeCarrier($query, $carrier)
    {
        if ($carrier != 0) {
            $carrier = Carrier::find($carrier);

            return $query->where('carrier', 'like', '%'.$carrier->name.'%');
        }
    }

    public function scopeCompanyUser($query, $companyUser)
    {
        if ($companyUser != 0) {
            return $query->where('company_user_id', $companyUser);
        }
    }
}
