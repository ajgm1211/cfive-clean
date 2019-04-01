<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AccountImportationContractFcl extends Model
{
    protected $table    = "accounts_import_cfcl";
    protected $fillable = ['id',
                           'name',
                           'date',
                           'namefile',
                           'company_user_id'
                          ];

    public function companyuser(){
        return $this->belongsTo('App\CompanyUser','company_user_id');
    }
    
    
    public function FilesTmps(){
        return $thid->hasMany('App\FileTmp');  
    }

    public function contract(){
        return $this->hasOne('App\Contract','account_id');
    }
}
