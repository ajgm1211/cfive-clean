<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FailedGlobalcharge extends Model
{

	protected $table    = "failed_globalchargers";
	
	protected $fillable = ['surcharge',
								  'origin',
								  'destiny',
								  'typedestiny',
								  'calculationtype',
								  'ammount',
								  'currency',
								  'carrier',
								  'validityto',
								  'validityfrom',
								  'port',
								  'country',
								  'company_user_id',
								  'account_id'
								 ];

}
