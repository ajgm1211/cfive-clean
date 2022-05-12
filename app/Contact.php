<?php

namespace App;

use Illuminate\Http\Request;
use App\Http\Filters\ContactFilter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Builder;


class Contact extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    protected $fillable = ['id', 'first_name', 'last_name', 'phone', 'email', 'position', 'company_id', 'options', 'whitelabel', 'password_wl'];

    public function company()
    {
        return $this->belongsTo('App\Company', 'company_id');
    }

    public function scopeCompany($query)
    {
        return $query->with(['company' => function ($q) {
            $q->select('id', 'business_name', 'phone', 'address', 'tax_number', 'logo as url');
        }]);
    }

    public function getOptionsAttribute($value)
    {
        return json_decode($value);
    }

    public function getFullName()
    {
        if ($this->first_name && $this->last_name) {
            return $this->first_name . ' ' . $this->last_name;
        }else{
            return $this->first_name;
        }
    }

    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function scopeFilterByCurrentCompany($query)
    {
        return $query->whereHas('company', function ($query) {
            $query->select('id', 'business_name', 'phone', 'address', 'tax_number', 'logo as url')
                  ->where('company_user_id', '=', \Auth::user()->company_user_id);
        });
    }

    public function scopeFilterByCurrentEditingCompany($query, $company)
    {
        return $query->where('company_id', $company);
    }

    public function scopeFilter(Builder $builder, Request $request)
    {
        return (new ContactFilter($request, $builder))->filter();
    }
}
