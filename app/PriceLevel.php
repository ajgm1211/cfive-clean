<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PriceLevel extends Model
{
    protected $table = 'price_levels';

    protected $fillable = [
        'name','display_name','description','company_user_id','price_level_type_id',
    ];

    public function company_user()
    {
        return $this->belongsTo('App\CompanyUser');
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