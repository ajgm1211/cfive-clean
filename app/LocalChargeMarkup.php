<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LocalChargeMarkup extends Model
{
    protected $fillable = ['percent_markup_import', 'fixed_markup_import','currency_import','currency_export','percent_markup_export','fixed_markup_export','price_type_id','price_id'];

    public function price()
    {
        return $this->belongsTo('App\Price');
    }
}
