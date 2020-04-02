<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Illuminate\Support\Facades\Auth;

class Contract extends Model implements HasMedia
{
  use HasMediaTrait;
  protected $guard = 'web';
  protected $table    = "contracts";     

  protected $fillable = ['id', 'name','number','company_user_id','account_id','direction_id','validity','expire','status','remarks'];

  public function rates(){
    return $this->hasMany('App\Rate');
  }
  public function addons(){
    return $this->hasMany('App\ContractAddons');
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

  public function FilesTmps(){
    return $thid->hasMany('App\FileTmp');  
  }

  public function carriers()
  {
    //return $this->belongsToMany('App\Carrier','contracts_carriers', 'carrier_id');
    return $this->hasMany('App\ContractCarrier','contract_id');
  }

  public function direction(){
    return $this->belongsTo('App\Direction','direction_id');
  }

  public function scopeCarrier($query, $carrier)
  {
    if ($carrier) {
      return $query->where('carrier', $carrier);
    }
    return $query;
  }

  public function scopeStatus($query, $status)
  {
    if ($status) {
      return $query->where('status', $status);
    }
    return $query;
  }

  public function scopeDestPort($query, $port_dest)
  {
    if ($port_dest) {
      return $query->where('port_dest', $port_dest);
    }
    return $query;
  }

  public function scopeOrigPort($query, $port_orig)
  {
    if ($port_orig) {
      return $query->where('port_orig', $port_orig);
    }
    return $query;
  }

    /**
     * Scope a query to only include contracts by authenticated users.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilterByCurrentCompany( $query )
    {
        $company_id = Auth::user('web')->company_user_id;
        return $query->where( 'company_user_id', '=', $company_id );
    }
}
