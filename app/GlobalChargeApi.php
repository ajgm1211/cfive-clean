<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GlobalChargeApi extends Model
{

	protected $table    = "global_charges_api";
	protected $fillable = 
	['id','surcharge_id','typedestiny_id','calculationtype_id','amount','validity','expire','currency_id', 'provider_id'];

	public function currency(){
		return $this->belongsTo('App\Currency');
	}

	public function calculationtype(){
		return $this->belongsTo('App\CalculationType');
	}

	public function surcharge(){
		return $this->belongsTo('App\Surcharge');
	}

  	public function globalcharport(){
    	return $this->hasMany('App\GlobalChargePortApi','globalcharge_id');
  	}
	
	public function typedestiny(){
		return $this->belongsTo('App\TypeDestiny');
	}

	public function provider(){
		return $this->belongsTo('App\ApiProvider', 'provider_id');
	}

	public function globalchargeprovider(){
    	return $this->hasMany('App\GlobalChargeProvider','globalcharge_id');
  	}
}
