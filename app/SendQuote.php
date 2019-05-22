<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SendQuote extends Model
{
    protected $fillable =   ['pick_up_date','user_id'];
}
