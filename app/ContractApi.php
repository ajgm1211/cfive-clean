<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ContractApi extends Model
{
  protected $fillable = ['id', 'name','number','company_user_id','account_id','direction_id','validity','expire','status','remarks'];

  public function companyUser()
  {
    return $this->belongsTo('App\CompanyUser');
  }

  public function localchargesApi(){
    
    return $this->hasMany('App\LocalChargeApi');
  }


  public function user(){

    return $this->belongsTo('App\User');

  }


  public function carriers(){
    return $this->hasMany('App\ContractCarrier','contract_id');
  }

  public function direction(){
    return $this->belongsTo('App\Direction','direction_id');
  }
}
