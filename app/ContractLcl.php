<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class ContractLcl extends Model implements Auditable
{
  use \OwenIt\Auditing\Auditable;
  protected $table    = "contracts_lcl";
  protected $fillable = ['id', 'name','number','company_user_id','validity','expire','status'];

  public function companyUser()
  {
    return $this->belongsTo('App\CompanyUser');
  }

  public function user()
  {
    return $this->belongsTo('App\User');
  }
}
