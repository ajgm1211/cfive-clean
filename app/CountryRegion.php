<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CountryRegion extends Model
{
    protected $table    = "countries_regions";
    protected $fillable = ['id',
                           'country_id',
                           'region_id',
                          ];

    public function region()
    {
        return $this->belongsTo('App\Region','region_id');
    }
    
    public function country(){
		return $this->belongsTo('App\Country','country_id');
	}
	

}
