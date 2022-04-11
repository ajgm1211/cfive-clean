<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Http\Filters\ProviderFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;


class Provider extends Model
{
    protected $table    = "providers";
    protected $fillable = ['id','name','options','description','company_user_id'];
    protected $casts = [
        'options' => 'array',
    ];

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
        return (new  ProviderFilter($request, $builder))->filter();
    }

    /* Duplicate Contract Model instance with relations */
    
    public function duplicate()
    {

        $new_provider = $this->replicate();
        $new_provider->name .= ' copy';
        $new_provider->description.=' copy';
        $new_provider->save();

        return $new_provider;
    }

    public function referentialData()
    {
        return $this->morphOne('App\ReferentialData', 'referential')
            ->where('company_user_id', $this->company_user_id)
            ->first();
    }

}
