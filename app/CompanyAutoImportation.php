<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CompanyAutoImportation extends Model
{
    protected $table    = "companies_auto_importations";
    protected $fillable = ['id','status','company_user_id'];
    
    public function companyUser(){
        return $this->belongsTo('App\CompanyUser','company_user_id');
    }
}
