<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LocalCharCountryLcl extends Model
{
 	protected $table    = "localcharcountry_lcl";
	protected $fillable =   ['country_orig','country_dest','localchargelcl_id'];
	public $timestamps = false;
	public function localchargelcl()
	{
		return $this->belongsTo('App\LocalCharge','localchargelcl_id');
	}
	public function countryOrig(){
		return $this->belongsTo('App\Country','country_orig');
	}
	public function countryDest(){
		return $this->belongsTo('App\Country','country_dest');

	}
}
