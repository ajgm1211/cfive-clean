<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InlandPort extends Model
{
      protected $table    = "inlandsports";
    protected $fillable =   ['port','ireland_id '];
    public $timestamps = false;
    public function inland()
    {
        return $this->belongsTo('App\Inland');
    }
    public function ports(){
        return $this->belongsTo('App\Harbor','port');

    }
}
