<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class ContractLclUserRestriction extends Model implements Auditable
{
  use \OwenIt\Auditing\Auditable;
    protected $table    = "contractlcl_user_restrictions";    
  protected $fillable = ['user_id','contractlcl_id'];
}
