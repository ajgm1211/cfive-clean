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

    public function group_details()
    {
        return $this->hasMany('App\CompanyGroupDetail', 'company_group_id','id');
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

        $this->load(
            'group_details'
        );

        $new_model->push();

        $new_model->save();

        $relations = $this->getRelations();

        foreach ($relations as $relation) {
            if (!is_a($relation, 'Illuminate\Database\Eloquent\Collection')) {
                if ($relation != null) {
                    $relation->duplicate($new_model);
                }
            } else {
                foreach ($relation as $relationRecord) {
                    if ($relationRecord != null) {
                        $newRelationship = $relationRecord->duplicate($new_model);
                    }
                }
            }
        }

        return $new_model;
    }
}
