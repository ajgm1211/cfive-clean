<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    protected $fillable = ['name','region','country_id'];
    public $timestamps = false;

    public function country()
    {
      return $this->belongsTo('App\Country','country_id');
    }
}
