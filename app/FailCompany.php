<?php

namespace App;

use Illuminate\Http\Request;
use App\Http\Filters\FailCompanyFilter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Builder;

class FailCompany extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    protected $table = 'fail_companies';
    protected $fillable = ['business_name',
                           'phone',
                           'address',
                           'email',
                           'tax_number',
                           'associated_quotes',
                           'company_user_id',
                           'owner',
                          ];

    public function owner()
    {
        return $this->belongsTo('App\user', 'owner');
    }
    
    public function company_user()
    {
        return $this->belongsTo('App\CompanyUser');
    }

    public function scopeFilter(Builder $builder, Request $request)
    {
        return (new FailCompanyFilter($request, $builder))->filter();
    }
    
    public function scopeFilterByCurrentUser($query)
    {
        $user_id = Auth::user()->id;
        return $query->where('owner', '=', $user_id);
    }
}
