<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DistanceKmLocation extends Model
{
    protected $table    = "distances_km_location";
    protected $fillable = ['id','distance', 'location_id', 'harbor_id'];

    public function location()
    {
        return $this->belongsToMany('App\Location', 'location_id');
    }

    public function harbors()
    {
        return $this->belongsToMany('App\Harbor', 'harbors_id');
    }
}

