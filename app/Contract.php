<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    protected $table    = "contracts";
    
    
    protected $fillable = ['id', 'name','number','user_id','carrier_id','origin_country','destiny_country','validity','expire','status'];
    public function carrier(){

        return $this->belongsTo('App\Carrier');

    }
    public function country_origin(){
        return $this->belongsTo('App\Country','origin_country');


    }
    public function country_destiny(){
        return $this->belongsTo('App\Country','destiny_country');


    }
}
