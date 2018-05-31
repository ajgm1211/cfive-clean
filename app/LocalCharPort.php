<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LocalCharPort extends Model
{


    protected $table    = "localcharports";
    protected $fillable =   ['port_orig','port_dest','localcharge_id'];
    public $timestamps = false;
    public function localcharge()
    {
        return $this->belongsTo('App\LocalCharge');
    }
    public function portOrig(){
        return $this->belongsTo('App\Harbor','port_orig');
    }
    public function portDest(){
        return $this->belongsTo('App\Harbor','port_dest');

    }
}
