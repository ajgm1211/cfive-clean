<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FreightMarkup extends Model
{
    protected $fillable = ['percent_markup', 'fixed_markup','currency','price_type_id','price_id'];

    public function price()
    {
        return $this->belongsTo('App\Price');
    }
}
