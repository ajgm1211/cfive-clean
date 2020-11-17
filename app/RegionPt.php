<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RegionPt extends Model
{
    protected $table    = "region_pts";
    protected $fillable = ['id',
                           'name'
                          ];

    public function PortRegions()
    {
        return $this->hasMany('App\PortRegion','region_pts_id');
    }
    
    public function harbor(){
		return $this->belongsTo('App\Harbor','harbor_id');
	}
}
