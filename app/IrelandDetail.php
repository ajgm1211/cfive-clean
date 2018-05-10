<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IrelandDetail extends Model
{
    
    protected $table    = "irelandsdetails";
    protected $fillable =   ['lower','upper','ammount','type','currency_id','ireland_id'];
    public $timestamps = false;
    public function ireland()
    {
        return $this->belongsTo('App\Ireland');
    }
    public function ports(){
        return $this->belongsTo('App\Harbor','port');

    }
}
