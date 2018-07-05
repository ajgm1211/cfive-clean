<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DestinationAmmount extends Model
{
    protected $fillable = ['charge','detail','units','price_per_unit','markup','currency_id','total_ammount','total_ammount_2','quote_id'];

    public function currency(){
        return $this->belongsTo('App\Currency');
    }
}
