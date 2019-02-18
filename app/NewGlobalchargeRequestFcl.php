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
                          'created',
                          'created_at',
                          'type',
                          'data'];

	public function user(){
		return $this->belongsTo('App\User');
	}

	public function companyuser(){
		return $this->belongsTo('App\CompanyUser','company_user_id');
	}
}
