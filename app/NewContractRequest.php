<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NewContractRequest extends Model
{
    protected $table = 'newcontractrequests';
    protected $fillable = ['namecontract',
                           'numbercontract',
                           'validation',
                           'company_user_id',
                           'namefile',
                           'user_id',
                           'created',
                           'created_at',
                           'time_star',
                           'time_total',
                           'time_star_one',
                           'type',
                           'data'];

    public function user(){
        return $this->belongsTo('App\User');
    }

    public function companyuser(){
        return $this->belongsTo('App\CompanyUser','company_user_id');
    }
}
