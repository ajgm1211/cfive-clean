<?php

namespace App;

use App\Http\Filters\SaleTermFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SaleTermV3 extends Model
{

    protected $fillable = ['id', 'name', 'company_user_id', 'type_id', 'port_id', 'group_container_id'];

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
     * scopeFilter
     *
     * @param  mixed $builder
     * @param  mixed $request
     * @return void
     */
    public function scopeFilter(Builder $builder, Request $request)
    {
        return (new SaleTermFilter($request, $builder))->filter();
    }

    public function port()
    {
        return $this->belongsTo('App\Harbor');
    }

    public function type()
    {
        return $this->belongsTo('App\SaleTermType');
    }

    public function group_container()
    {
        return $this->belongsTo('App\GroupContainer');
    }

    public function company_user()
    {
        return $this->belongsTo('App\CompanyUser');
    }

    /* Duplicate Inland Model instance with relations */
    public function duplicate()
    {
        $new_saleterm = $this->replicate();
        $new_saleterm->name .= ' copy';
        $new_saleterm->save();

        return $new_saleterm;
    }
}
