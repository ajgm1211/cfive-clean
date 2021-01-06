<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GlobalChargeApiPortException extends Model
{
    protected $table    = "global_chargesapi_port_exception";
    protected $fillable =   ['id','port_orig','port_dest','globalchargeapi_id'];
    public $timestamps = false;
    public function globalchargeapi()
    {
        return $this->belongsTo('App\GlobalChargeApi','globalchargeapi_id');
    }
    public function portOrig(){
        return $this->belongsTo('App\Harbor','port_orig');
    }
    public function portDest(){
        return $this->belongsTo('App\Harbor','port_dest');

    }
}
