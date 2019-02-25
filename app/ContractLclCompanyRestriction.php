<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class ContractLclCompanyRestriction extends Model implements Auditable
{
  use \OwenIt\Auditing\Auditable;
   protected $table    = "contractlcl_company_restrictions";    
  protected $fillable = ['company_id','contractlcl_id'];
}
