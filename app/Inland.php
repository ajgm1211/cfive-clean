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
	protected $fillable =   [
		'id', 
		'provider', 
		'status', 
		'inland_type_id', 
		'validity', 
		'expire', 
		'company_user_id', 
		'gp_container_id', 
		'direction_id' 
	];

	public function inlandRange()
	{
		return $this->hasMany('App\InlandRange');
	}

	public function companyUser()
	{
		return $this->belongsTo('App\CompanyUser');
	}

	public function inland_type()
	{
		return $this->belongsTo('App\CompanyUser');
	}

	public function direction()
	{
		return $this->belongsTo('App\Direction');
	}

	public function inland_company_restriction()
	{
		return $this->HasMany('App\InlandCompanyRestriction');
	}

	public function scopeFilterByCurrentCompany( $query )
	{
		$company_id = Auth::user()->company_user_id;

		return $query->where('company_user_id', '=', $company_id);
	}

	public function gpContainer()
	{
		return $this->belongsTo('App\GroupContainer');
	}

	public function scopeFilter(Builder $builder, Request $request)
	{
		return (new InlandFilter($request, $builder))->filter();
	}

	/**
	* Sync Inland Company Restrictions
	*
	* @param  Array $companies
	* @return void
	*/
	public function InlandRestrictionsSync($companies)
	{
		DB::table('inlands_company_restrictions')->where('inland_id', '=', $this->id)->delete(); 

		foreach($companies as $company_id){
			InlandCompanyRestriction::create([
				'company_id'    => $company_id,
				'inland_id'   => $this->id
			]);
		}
	}
	public function inlandports()
    {
        return $this->hasMany('App\InlandPort');
	}
	public function inlandadditionalkms()
    {
        return $this->hasOne('App\InlandAdditionalKm');
    }
    public function inlanddetails()
    {
        return $this->hasMany('App\InlandDetail');
    }

}
