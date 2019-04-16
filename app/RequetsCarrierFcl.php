<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RequetsCarrierFcl extends Model
{
    protected $table = 'request_fcl_carriers';
    protected $fillable = ['carrier_id','request_id'];

    public function carrier(){
        return $this->belongsTo('App\Carrier','carrier_id');
    }

}
