<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class ContractLcl extends Model implements Auditable
{
  use \OwenIt\Auditing\Auditable;
  protected $table    = "contracts_lcl";

  public function companyUser()
  {
    return $this->belongsTo('App\CompanyUser');
  }
}
