<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PaymentCondition extends Model
{
    protected $fillable = ['quote_id', 'content'];

    public function quote()
    {
        return $this->belongsTo('App\QuoteV2', 'id', 'quote_id');
    }
}
