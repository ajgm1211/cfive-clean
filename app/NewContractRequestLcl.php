<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class NewContractRequestLcl extends Model
{
    
    protected $table = 'new_contract_request_lcl';
    protected $fillable = ['namecontract',
                           'numbercontract',
                           'validation',
                           'direction_id',
                           'company_user_id',
                           'namefile',
                           'user_id',
                           'updated',
                           'time_star',
                           'time_total',
                           'time_manager',
                           'time_star_one',
                           'created',
                           'created_at',
                           'sentemail',
                           'contract_id',
                           'type',
                           'data'];

    public function user(){
        return $this->belongsTo('App\User');
    }

    public function direction(){
        return $this->belongsTo('App\Direction');
    }

    public function Requestcarriers(){
        return $this->hasMany('App\RequetsCarrierLcl','request_id');
    }

    public function companyuser(){
        return $this->belongsTo('App\CompanyUser','company_user_id');
    }

}
