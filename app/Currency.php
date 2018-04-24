<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{    
    protected $table    = "currency";
    protected $fillable = ['id', 'alphacode'];
    public function rate()
    {

        return $this->hasOne('App\Rate');
    }
}
