<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LocalChargeQuoteTotal extends Model
{
    protected $fillable = ['total', 'quote_id'];

    protected $casts = [
        'total' => 'array',
    ];
}
