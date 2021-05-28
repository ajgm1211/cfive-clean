<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HarborsLocationSearch extends Model
{
    protected $table    = "harbors_location_search";
    protected $fillable = ['id','location_id', 'harbors_id'];

    public function location()
    {
        return $this->belongsToMany('App\location', 'location_id');
    }

    public function harbors()
    {
        return $this->belongsTo('App\location', 'harbors_id');
    }
}