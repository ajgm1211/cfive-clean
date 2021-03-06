<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

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
}
