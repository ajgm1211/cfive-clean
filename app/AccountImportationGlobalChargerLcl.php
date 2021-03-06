<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AccountImportationGlobalChargerLcl extends Model
{
    protected $table = 'account_importation_global_charger_lcls';
    protected $fillable = ['id',
                           'name',
                           'date',
                           'requestgclcl_id',
                           'company_user_id',
                           'status',
                          ];

    public function companyuser()
    {
        return $this->belongsTo('App\CompanyUser', 'company_user_id');
    }
}
