<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SurchargerForCompanyUser extends Model
{
    protected $table    = "surchargers_for_company_user";
    protected $fillable = ['id', 'name','company_auto_id'];
    
    public function companyAutoUser(){
        return $this->belongsTo('App\CompanyAutoImportation','company_auto_id');
    }
}
