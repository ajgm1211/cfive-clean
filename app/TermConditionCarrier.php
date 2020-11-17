<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TermConditionCarrier extends Model
{
    protected $table = "termcondition_carriers";
    protected $fillable = ['carrier_id', 'termcondition_id'];

    public function carrier(){
        return $this->belongsTo('App\Carrier','carrier_id');
    }
}
