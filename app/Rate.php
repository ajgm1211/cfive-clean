<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Rate extends Model
{
    use SoftDeletes;
    protected $dates    = ['deleted_at'];
    
    protected $table    = "rates";
    protected $fillable = ['id', 'origin_port','destiny_port','carrier_id','contract_id','twuenty','forty','fortyhc', 'currency_id'];
    public function contract()
    {
        return $this->belongsTo('App\Contract');
    }

    public function port_origin(){
        return $this->belongsTo('App\Harbor','origin_port');

    }
    public function port_destiny(){
        return $this->belongsTo('App\Harbor','destiny_port');

    }
    public function carrier(){

        return $this->belongsTo('App\Carrier');

    }
    public function currency(){

        return $this->belongsTo('App\Currency');

    }
    
    public function contract_company_restriction(){

        return $this->HasManyTrougth('App\Contract','App\ContractCompanyRestriction');
        
    }
}
