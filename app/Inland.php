<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
class Inland extends Model implements Auditable
{
  use \OwenIt\Auditing\Auditable;
  
  protected $table    = "inlands";
  protected $fillable =   ['id','provider','type','validity','expire'];

  public function inlandports(){

    return $this->hasMany('App\InlandPort');

  }
  public function inlanddetails(){

    return $this->hasMany('App\InlandDetail');
   
  }
  public function companyUser()
  {
    return $this->belongsTo('App\CompanyUser');
  }
  public function inland_company_restriction(){

    return $this->HasMany('App\InlandCompanyRestriction');

  }
  /*public function getRouteKey()
  {
    $hashids = new \Hashids\Hashids('MySecretSalt');

    return $hashids->encode($this->getKey());
  }*/


}
