<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\Http\Filters\PriceLevelDetailFilter;

class PriceLevelDetail extends Model
{
    protected $table = 'price_level_details';

    protected $fillable = [
        'amount','currency_id','direction_id', 'price_level_id', 'price_level_apply_id',
    ];
    
    protected $casts = [
        'amount' => 'array',
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

    public function duplicate($price_level)
    {
        $new_model = $this->replicate();

        $new_model->price_level_id = $price_level->id;

        $new_model->push();

        $new_model->save();

        return $new_model;
    }
}
