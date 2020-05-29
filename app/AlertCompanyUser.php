<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AlertCompanyUser extends Model
{
    protected $table    = "alert_company_users";
    protected $fillable = ['id',
                           'company_user_id',
                           'alert_dp_id',
                           'n_global',
                           'n_group'
                          ];
    public function alert(){
        return $this->belongsTo('App\AlertDuplicateGcFcl','alert_dp_id');
    }
    
    public function company_user(){
        return $this->belongsTo('App\CompanyUser','company_user_id');
    }
}
