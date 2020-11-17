<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LocalCharCountry extends Model
{
   	protected $table    = "localcharcountry";
	protected $fillable =   ['country_orig','country_dest','localcharge_id'];
	public $timestamps = false;
	public function localcharge()
	{
		return $this->belongsTo('App\LocalCharge','localcharge_id');
	}
	public function countryOrig(){
		return $this->belongsTo('App\Country','country_orig');
	}
	public function countryDest(){
		return $this->belongsTo('App\Country','country_dest');

	}
}
