<?php

namespace App;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use App\Http\Filters\FailedContactFilter;
use Illuminate\Database\Eloquent\Builder;

class FailedContact extends Model
{
    protected $table = 'failed_contacts';
    protected $fillable = [
        'first_name',
        'last_name',
        'phone',
        'email',
        'position',
        'company_id',
        'company_user_id',
    ];

    public function company_user()
    {
        return $this->belongsTo('App\CompanyUser');
    }

    public function scopeFilter(Builder $builder, Request $request)
    {
        return (new FailedContactFilter($request, $builder))->filter();
    }

    public function scopeFilterByCurrentCompanyUser($query)
    {
        $company_user_id = Auth::user()->company_user_id;
        return $query->where('company_user_id', '=', $company_user_id);
    }
}
