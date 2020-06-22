<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TransitTimeFail extends Model
{
    //transit_time_fails
    protected $fillable = [ 
    	'origin', 
    	'destiny',
    	'carrier',
    	'destination_type',
    	'transit_time',
    	'via'
    ];
}
