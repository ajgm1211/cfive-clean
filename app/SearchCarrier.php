<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SearchCarrier extends Model
{
    protected $fillable = ['id', 'search_rate_id', 'provider_id','provider_type'];

    public function search_rate()
    {
        return $this->belongsTo('App\SearchRate');
    }

    public function provider()
    {
        return $this->morphTo();
    }
}
