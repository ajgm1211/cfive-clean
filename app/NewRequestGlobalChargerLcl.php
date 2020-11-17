<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class NewRequestGlobalChargerLcl extends Model
{
    
    protected $table = 'new_request_global_charger_lcls';
    protected $fillable = ['id',
                           'name',
                           'validation',
                           'company_user_id',
                           'namefile',
                           'status',
                           'user_id',
                           'created',
                           'updated',
                           'time_star',
                           'time_total',
                           'username_load',
                           'time_star_one',
                           'created_at',
                           'sentemail'
                          ];

    public function user(){
        return $this->belongsTo('App\User');
    }

    public function companyuser(){
        return $this->belongsTo('App\CompanyUser','company_user_id');
    }
}
