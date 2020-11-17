<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class ContractCompanyRestriction extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
   
    protected $fillable = ['company_id','contract_id'];

    public function company(){
        return $this->belongsTo('App\Company','company_id');
    }
}
