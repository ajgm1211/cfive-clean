<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PdfQuoteStatus extends Model
{
    protected $table = 'pdf_quote_status';
    
    public function quote()
    {
        return $this->belongsTo('App\QuoteV2','quote_id');
    }
}
