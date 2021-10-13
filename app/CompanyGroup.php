<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\Http\Filters\CompanyGroupFilter;

class CompanyGroup extends Model
{
    protected $table = 'company_groups';

    protected $fillable = ['name','status','company_user_id'];

    public $timestamps = false;

    public function company_user()
    {
        return $this->belongsTo('App\CompanyUser');
    }

    public function companies()
    {
        return $this->hasManyThrough('App\Company', 'App\CompanyGroupDetail', 'company_group_id','id','id','company_id');
    }

    public function scopeFilterByCurrentCompany($query)
    {
        $company_id = Auth::user()->company_user_id;

        return $query->where('company_user_id', '=', $company_id);
    }

    public function scopeFilter(Builder $builder, Request $request)
    {
        return (new CompanyGroupFilter($request, $builder))->filter();
    }

    public function duplicate()
    {
        $new_model = $this->replicate();

        $new_model->push();

        $new_model->save();

        return $new_model;
    }
}
