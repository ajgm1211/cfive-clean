<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AccountImportationContractLcl extends Model
{
    protected $table    = "accounts_import_clcl";
    protected $fillable = ['id',
                           'name',
                           'date',
                           'namefile',
                           'requestlcl_id',
                           'company_user_id'
                          ];

    public function companyuser(){
        return $this->belongsTo('App\CompanyUser','company_user_id');
    }

    public function contractlcl(){
        return $this->hasOne('App\ContractLcl','account_id');
    }
}
