<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RequetsCarrierLcl extends Model
{
    protected $table = 'request_lcl_carriers';
    protected $fillable = ['carrier_id','request_id'];

    public function carrier(){
        return $this->belongsTo('App\Carrier','carrier_id');
    }
}
