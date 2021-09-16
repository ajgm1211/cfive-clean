<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SearchLocation extends Model
{
    protected $fillable = ['id', 'search_rates_id', 'location_orig', 'location_dest'];
    public $timestamps = false;

    public function search_rate()
    {
        return $this->belongsTo('App\SearchRate');
    }

    public function locationOrigin()
    {
        return $this->belongsTo('App\location', 'location_orig');
    }

    public function locationDest()
    {
        return $this->belongsTo('App\location', 'location_dest');
    }
}
