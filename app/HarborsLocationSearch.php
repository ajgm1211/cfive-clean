<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HarborsLocationSearch extends Model
{
    protected $table    = "harbors_location_search";
    protected $fillable = ['id','location_id', 'harbor_id'];

    public function location()
    {
        return $this->belongsTo('App\Location');
    }

    public function harbor()
    {
        return $this->belongsTo('App\Harbor');
    }
}