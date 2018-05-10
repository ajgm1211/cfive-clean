<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IrelandPort extends Model
{
    protected $table    = "irelandsports";
    protected $fillable =   ['port','ireland_id '];
    public $timestamps = false;
    public function ireland()
    {
        return $this->belongsTo('App\Ireland');
    }
    public function ports(){
        return $this->belongsTo('App\Harbor','port');

    }
}
