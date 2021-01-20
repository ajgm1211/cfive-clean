<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GlobalCharCountryException extends Model
{
    protected $table    = "global_char_country_exceptions";
	protected $fillable =   ['country_orig','country_dest','globalcharge_id'];
    public $timestamps = false;
	public function globalcharge()
	{
		return $this->belongsTo('App\GlobalCharge','globalcharge_id');
	}
	public function countryOrig(){
		return $this->belongsTo('App\Country','country_orig');
	}
	public function countryDest(){
		return $this->belongsTo('App\Country','country_dest');

	}
	public function contryorigin(){
        return $this->belongsTo('App\Country','country_orig','id');
    }
    public function countrydestiny(){
        return $this->belongsTo('App\Country','country_dest','id');
    }
}
