<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ContractCarrier extends Model
{
    protected $table    = "contracts_carriers";     

    protected $fillable = ['id','carrier_id','contract_id'];
    
    public function carrier(){
        return $this->belongsTo('App\Carrier','carrier_id');
    }
}
