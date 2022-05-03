<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DistanceKmLocation extends Model
{
    protected $table    = "distances_km_location";
    protected $fillable = ['id','distance', 'location_id', 'harbor_id'];

    public function location()
    {
        return $this->belongsTo('App\Location');
    }

    public function harbor()
    {
        return $this->belongsTo('App\Harbor');
    }
}

