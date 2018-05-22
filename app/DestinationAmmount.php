<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DestinationAmmount extends Model
{
    protected $fillable = ['cost','detail','ammount','total_ammount','currency_id','quote_id'];
}
