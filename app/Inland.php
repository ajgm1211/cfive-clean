<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Inland extends Model
{
       protected $table    = "inlands";
    protected $fillable =   ['id','provider','type','validity','expire'];

    public function inlandports(){

        return $this->hasMany('App\InlandPort');

    }
    public function inlanddetails(){

        return $this->hasMany('App\InlandDetail');

    }
}
