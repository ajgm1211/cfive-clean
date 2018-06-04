<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MergeTag extends Model
{
    protected $table = "mergeTags";
    protected $fillable = [
        'id', 
        'company_name', 
        'client_name', 
        'client_phone', 
        'client_email', 
        'quote_number', 
        'quote_total', 
        'destination', 
        'origin', 
        'carrier', 
        'user_name', 
        'user_email'
    ];
}
