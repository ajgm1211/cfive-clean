<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InlandDetail extends Model
{
    protected $table    = "inlandsdetails";
    protected $fillable =   ['lower','upper','ammount','type','currency_id','ireland_id'];
    public $timestamps = false;
    public function inland()
    {
        return $this->belongsTo('App\Inland');
    }
    public function currency(){
        return $this->belongsTo('App\Currency');

    }
}
