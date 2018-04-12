<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    protected $table    = "contracts";

    protected $fillable = ['id', 'name', 'number','validity','expire','status'];
    public function courier(){

        return $this->belongsTo('App\Courier');

    }
    public function country_origin(){
        return $this->belongsTo('App\Country','origin_country');


    }
    public function country_destiny(){
        return $this->belongsTo('App\Country','destiny_country');


    }
}
