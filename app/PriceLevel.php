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
        'name','display_name','description','company_user_id','type','options'
    ];

    protected $casts = [
        'options' => 'array',
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

    public function price_level_details()
    {
        return $this->hasMany('App\PriceLevelDetail', 'price_level_id', 'id');
    }

    public function price_level_groups()
    {
        return $this->hasMany('App\PriceLevelGroup', 'price_level_id', 'id');
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

        $this->load(
            'price_level_details',
            'price_level_groups'
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