<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AccountImportationGlobalcharge extends Model
{
    protected $table    = "account_importation_globalcharge";
    protected $fillable = ['name', 'date','company_user_id'];


    public function FileTmp(){
		return $this->hasOne('App\FileTmpGlobalcharge','account_id');
	}

    
}
