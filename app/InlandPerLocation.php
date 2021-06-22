<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Http\Filters\InlandPerLocationFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;


class InlandPerLocation extends Model
{
    protected $table = 'inland_location';
    protected $fillable = ['id, json_container, currency_id, harbor_id, inland_id, location_id, type, service_id'];
    
    public function inland()
    {
        return $this->belongsTo('App\Inland');
    }

    public function harbor()
    {
        return $this->belongsTo('App\Harbor');
    }
    
    public function location()
    {
        return $this->belongsTo('App\location');
    }

    public function currency()
    {
        return $this->belongsTo('App\Currency');
    }

    public function gpContainer()
    {
        return $this->belongsTo('App\GroupContainer');
    }

    public function service()
    {
        return $this->belongsTo('App\inlandService');
    }

    public function scopeFilter(Builder $builder, Request $request)
    {
        return (new InlandPerLocationFilter($request, $builder))->filter();
    }

    public function scopeFilterByInland($query, $inland_id)
    {
        return $query->where('inland_id', '=', $inland_id);
    }
    public function duplicate()
    {
        $new_inland = $this->replicate();
        $new_inland->save();

        return $new_inland;
    }

}