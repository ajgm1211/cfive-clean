<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GlobalChargeProvider extends Model
{
    protected $table    = "global_charge_provider";
    protected $fillable =   ['id','provider_id','globalcharge_id'];
    public $timestamps = false;

    public function globalcharge()
    {
        return $this->belongsTo('App\GlobalCharge','globalcharge_id');
    }

    public function provider()
    {
        return $this->belongsTo('App\ApiProvider');
    }
}
