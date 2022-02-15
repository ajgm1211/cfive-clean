<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IntegrationQuoteStatus extends Model
{
    public function quote()
    {
        return $this->belongsTo('App\QuoteV2', 'id', 'quote_id');
    }
}
