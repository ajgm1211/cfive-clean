<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AccountImportationGlobalcharge extends Model
{
    protected $table    = "account_importation_globalcharge";
    protected $fillable = ['name',
                           'date',
                           'data',
                           'requestgc_id',
                           'company_user_id'
                          ];


    public function FileTmp(){
		return $this->hasOne('App\FileTmpGlobalcharge','account_id');
	}
	
	public function companyuser(){
		return $this->belongsTo('App\CompanyUser','company_user_id');
	}
    
    
}
