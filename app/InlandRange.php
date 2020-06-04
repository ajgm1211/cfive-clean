<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Http\Filters\InlandRangeFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class InlandRange extends Model
{
    protected $fillable = [
                            'id',
                            'lower',
                            'upper',
                            'currency_id',
                            'inland_id',
                            'json_containers',
                            'status'
                          ];

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

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'json_containers' => 'array',
    ];


    public function per_container(){
        $first = true;

        foreach ($this->json_containers as $key => $value) {
            
            if($first)
            {
                $first_value = $value;
                $first = false;

            } else 
                if($value != $first_value) return '-';
        }
        
        return $value;
    }
}
