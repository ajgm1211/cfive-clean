<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GroupGlobalsCompanyUser extends Model
{
    protected $table    = "group_globals_company_users";
    protected $fillable = ['id',
                           'alert_cmpuser_id',
                           'status_alert_id',
                           'n_global'
                          ];
    
    public function status(){
        return $this->belongsTo('App\StatusAlert','status_alert_id');
    }
}
