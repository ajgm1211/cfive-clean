<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LocalCharPort extends Model
{
    protected $table    = "localcharports";
    protected $fillable =   ['port','localcharge_id'];
    public $timestamps = false;
    public function localcharge()
    {
        return $this->belongsTo('App\LocalCharge');
    }
    public function ports(){
        return $this->belongsTo('App\Harbor','port');

    }
}
