<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ContractCarrierLcl extends Model
{
    protected $table    = "contracts_carriers_lcl";     

    protected $fillable = ['id','carrier_id','contract_id'];
    
    public function carrier(){
        return $this->belongsTo('App\Carrier','carrier_id');
    }
}
