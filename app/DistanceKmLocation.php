<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DistanceKmLocation extends Model
{
    protected $table    = "distances_km_location";
    protected $fillable = ['id','distance', 'location_id', 'harbors_id'];

    public function location()
    {
        return $this->belongsTo('App\Location');
    }

    public function harbors()
    {
        return $this->belongsTo('App\Harbor');
    }
}

