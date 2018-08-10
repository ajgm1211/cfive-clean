<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Airport extends Model
{
    protected $fillable = ['id','name','country_id'];

    public function country()
    {
        return $this->belongsTo('App\Country');
    }
}
