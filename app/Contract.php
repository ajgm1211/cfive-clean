<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
  protected $table    = "contracts";            

  protected $fillable = ['id', 'name','number','company_user_id','validity','expire','status'];

  public function rates(){
    return $this->hasMany('App\Rate');
  }
  public function companyUser()
  {
    return $this->belongsTo('App\CompanyUser');
  }
  
  public function localcharges(){
        //return $this->hasManyThrough('App\LocalCharCarrier', 'App\LocalCharge');
    return $this->hasMany('App\LocalCharge');
  }

  public function contract_company_restriction(){

    return $this->HasMany('App\ContractCompanyRestriction');
    
  }

  public function contract_user_restriction(){

    return $this->HasMany('App\ContractUserRestriction');
    
  }

  public function user(){

    return $this->belongsTo('App\User');
    
  }
  
}
