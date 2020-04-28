<?php

namespace App;


use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Illuminate\Support\Facades\Auth;
use App\Http\Filters\InlandFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;



class Inland extends Model implements Auditable
{
  use \OwenIt\Auditing\Auditable;

  protected $table    = "inlands";
  protected $fillable =   ['id','provider','type','validity','expire'];

  public function inlandadditionalkms(){

    return $this->hasOne('App\InlandAdditionalKm');

  }
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

  public function scopeFilterByCurrentCompany( $query )
  {

    $company_id = Auth::user('web')->company_user_id;
  
    return $query->where('company_user_id', '=',$company_id);
  }

  public function gpContainer()
	{
		return $this->belongsTo('App\GroupContainer');
  }
  public function scopeFilter(Builder $builder, Request $request)
	{
		return (new InlandFilter($request, $builder))->filter();
	}


}
