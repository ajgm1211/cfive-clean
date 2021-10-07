<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PriceLevelDetail extends Model
{
    protected $table = 'price_level_details';

    protected $fillable = [
        'amount','currency_id','typedestiny_id','direction_id',
    ];

    public function price_level()
    {
        return $this->belongsTo('App\PriceLevel');
    }

    public function currency()
    {
        return $this->belongsTo('App\Currency');
    }

    public function price_level_apply()
    {
        return $this->belongsTo('App\PriceLevelApply');
    }

    public function direction()
    {
        return $this->belongsTo('App\Direction');
    }

    public function scopeFilterByPriceLevel($query, $price_level_id)
    {
        return $query->where('price_level_id', '=', $price_level_id);
    }

    public function scopeFilter(Builder $builder, Request $request)
    {
        return (new PriceLevelDetailFilter($request, $builder))->filter();
    }
}
