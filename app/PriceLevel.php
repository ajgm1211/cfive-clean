<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\Http\Filters\PriceLevelFilter;

class PriceLevel extends Model
{
    protected $table = 'price_levels';

    protected $fillable = [
        'name','display_name','description','company_user_id','type',
    ];

    public function company_user()
    {
        return $this->belongsTo('App\CompanyUser');
    }

    public function companies()
    {
        return $this->hasManyThrough('App\Company', 'App\PriceLevelGroups', 'price_level_id', 'id', 'id', 'group_id')
        ->where('group_type', 'App\Company');
    }

    public function company_groups()
    {
        return $this->hasManyThrough('App\CompanyGroup', 'App\PriceLevelGroups', 'price_level_id', 'id', 'id', 'group_id')
        ->where('group_type', 'App\CompanyGroup');
    }

    public function scopeFilterByCurrentCompany($query)
    {
        $company_id = Auth::user()->company_user_id;

        return $query->where('company_user_id', '=', $company_id);
    }

    public function scopeFilter(Builder $builder, Request $request)
    {
        return (new PriceLevelFilter($request, $builder))->filter();
    }

    public function duplicate()
    {
        $new_model = $this->replicate();

        $new_model->push();

        $new_model->save();

        return $new_model;
    }
}