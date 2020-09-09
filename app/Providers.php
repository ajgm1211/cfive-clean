<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Http\Filters\ProvidersFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;


class Providers extends Model
{
    protected $fillable = ['id','name', 'description','company_user_id'];


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
