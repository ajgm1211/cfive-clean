<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Http\Filters\InlandRangeFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class InlandRange extends Model
{
    public function inland()
    {
        return $this->belongsTo('App\Inland');
    }
    public function currency()
    {
        return $this->belongsTo('App\Currency');
    }

    public function gpContainer()
    {
        return $this->belongsTo('App\GroupContainer');
    }

    public function scopeFilter(Builder $builder, Request $request)
    {
        return (new InlandRangeFilter($request, $builder))->filter();
    }

    /**
    * Scope a query to only include rates by contract.
    *
    * @param  \Illuminate\Database\Eloquent\Builder $query
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function scopeFilterByInland( $query, $inland_id )
    {
        return $query->where( 'inland_id', '=', $inland_id );
    }
}
