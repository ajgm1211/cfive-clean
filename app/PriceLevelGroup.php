<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PriceLevelGroup extends Model
{
    protected $fillable = ['id','price_level_id', 'group_id','group_type'];
    public $timestamps = false;

    public function price_level()
    {
        return $this->belongsTo('App\PriceLevel');
    }

    public function group()
    {
        return $this->morphTo();
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
