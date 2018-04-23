<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    protected $table    = "contracts";


    protected $fillable = ['id', 'name','number','user_id','validity','expire','status'];

    public function rates(){

        return $this->hasMany('App\Rate');

    }
    public function localcharges(){

        return $this->hasMany('App\LocalCharge');

    }
}
