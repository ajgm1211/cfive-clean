<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GlobalCharPort extends Model
{

    protected $table    = "globalcharport";
    protected $fillable =   ['id','port','globalcharge_id'];
    public function globalcharge()
    {
        return $this->belongsTo('App\GlobalCharge','globalcharge_id');
    }
    public function ports(){
        return $this->belongsTo('App\Harbor','port');

    }
}
