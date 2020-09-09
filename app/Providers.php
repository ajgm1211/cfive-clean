<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Illuminate\Support\Facades\Auth;
use App\Http\Filters\ProvidersFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\ContractCarrier;
use App\ContractUserRestriction;
use App\ContractCompanyRestriction;
use Illuminate\Support\Facades\DB;
use App\Http\Traits\SearchTraitApi;
use App\Http\Traits\UtilTrait;
use Carbon\Carbon;
use Illuminate\Support\Collection as Collection;

class Providers extends Model
{
    protected $fillable = ['id','name', 'description'];


/**
     * Scope a query to only include contracts by authenticated users.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilterByCurrentCompany($query)
    {
        $company_id = Auth::user()->company_user_id;
        return $query->where('company_user_id', '=', $company_id);
    }

    /**
     * Scope a query filter
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  \Illuminate\Http\Request $request;
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilter(Builder $builder, Request $request)
    {
        return (new  ProvidersFilter($request, $builder))->filter();
    }


}
