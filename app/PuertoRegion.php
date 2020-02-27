<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PuertoRegion extends Model
{
    protected $table    = "puerto_regions";
    protected $fillable = ['id',
                           'harbor_id',
                           'region_pts_id',
                          ];

    public function region_pt()
    {
        return $this->belongsTo('App\RegionPt','region_pts_id');
    }
    
    public function harbor(){
		return $this->belongsTo('App\Harbor','harbor_id');
	}
}
