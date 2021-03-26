<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SearchCarrier extends Model
{
    protected $fillable = ['id', 'search_rate_id', 'carrier_id'];

    public function search_rate()
    {
        return $this->belongsTo('App\SearchRate');
    }
}
