<?php

namespace App;

use DB;
use Illuminate\Http\Request;
use App\Http\Filters\CompanyFilter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Builder;


class Company extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    protected $fillable = ['business_name', 'phone', 'address', 'email', 'associated_contacts', 'associated_quotes', 'currency_id', 'company_user_id', 'owner', 'tax_number', 'logo', 'pdf_language', 'payment_conditions', 'options', 'api_id', 'api_status', 'options->vf_code', 'options->vs_code','unique_code', 'whitelabel','url_wl'];

    public function contact()
    {
        return $this->hasMany('App\Contact');
    }

    public function groupUserCompanies()
    {
        return $this->hasMany('App\GroupUserCompany');
    }

    public function quote()
    {
        return $this->hasMany('App\Quote');
    }

    public function currency()
    {
        return $this->belongsTo('App\Currency');
    }

    public function user()
    {
        return $this->belongsTo('App\user', 'owner');
    }

    public function owner()
    {
        return $this->belongsTo('App\user', 'owner');
    }

    public function company_price()
    {
        return $this->hasOne('App\CompanyPrice');
    }

    public function price_name()
    {
        return $this->hasManyThrough('App\Price', 'App\CompanyPrice', 'company_id', 'id', 'id', 'price_id');
    }

    public function company_user()
    {
        return $this->belongsTo('App\CompanyUser');
    }

    public function scopeFilter(Builder $builder, Request $request)
    {
        return (new CompanyFilter($request, $builder))->filter();
    }

    public function scopeFilterByCurrentCompany($query)
    {
        $company_id = Auth::user()->company_user_id;
        return $query->where('company_user_id', '=', $company_id);
    }
    public function scopeFilterByCurrentUser($query)
    {
        $user_id = Auth::user()->id;
        return $query->where('owner', '=', $user_id);
    }

    public function scopeUser($query)
    {
        $query->with(['user' => function ($q) {
            $q->select('id', 'name', 'lastname', 'email', 'phone');
        }]);
    }

    public function scopeCompanyUser($query)
    {
        $query->with(['company_user' => function ($q) {
            $q->select('id', 'name', 'address', 'phone');
        }]);
    }

    public function getOptionsAttribute($value)
    {
        return json_decode($value);
    }

    public function duplicate()
    {
        $new_model = $this->replicate();
        $new_model->push();
        $new_model->save();
        $relations = $this->getRelations();

        foreach ($relations as $relation) {
            if (!is_a($relation, 'Illuminate\Database\Eloquent\Collection')) {
                if ($relation != null) {
                    $relation->duplicate($new_model);
                }
            } else {
                foreach ($relation as $relationRecord) {
                    if ($relationRecord != null) {
                        $newRelationship = $relationRecord->duplicate($new_model);
                    }
                }
            }
        }

        return $new_model;
    }

    public static function getCompaniesExport(){
        $records = DB::table('companies')->select('id','business_name', 'phone', 'email', 'address', 'tax_number', 'created_at')->get()->toArray();
        return $records;
    }
}
