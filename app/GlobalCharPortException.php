<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GlobalCharPortException extends Model
{
    protected $table    = "global_char_port_exceptions";
    protected $fillable =   ['id','port_orig','port_dest','globalcharge_id'];
    public $timestamps = false;
    public function globalcharge()
    {
        return $this->belongsTo('App\GlobalCharge','globalcharge_id');
    }
    public function portOrig(){
        return $this->belongsTo('App\Harbor','port_orig');
    }
    public function portDest(){
        return $this->belongsTo('App\Harbor','port_dest');

    }
    public function harbor1(){
        return $this->belongsTo('App\Harbor','port_dest','id');
    }
    public function harbor(){
        return $this->belongsTo('App\Harbor','port_orig','id');
    }
}
