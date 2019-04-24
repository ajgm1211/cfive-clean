<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NewGlobalchargeRequestFcl extends Model
{
    protected $table = 'n_request_globalcharge';
    protected $fillable = ['name',
                           'numbercontract',
                           'validation',
                           'company_user_id',
                           'namefile',
                           'user_id',
                           'time_star',
                           'time_total',
                           'time_star_one',
                           'created',
                           'created_at',
                           'sentemail',
                           'type',
                           'data'];

    public function user(){
        return $this->belongsTo('App\User');
    }

    public function companyuser(){
        return $this->belongsTo('App\CompanyUser','company_user_id');
    }
}
