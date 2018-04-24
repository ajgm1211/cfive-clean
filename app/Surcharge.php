<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Surcharge extends Model
{
    protected $table    = "surcharge";
    protected $fillable = ['id', 'name', 'description'];
    public function user()
    {
        return $this->belongsTo('App\User');
    }
    
    public function localcharge()
    {

        return $this->hasOne('App\LocalCharge');
    }
    
    
}
