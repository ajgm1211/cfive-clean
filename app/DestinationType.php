<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Watson\Rememberable\Rememberable;
class DestinationType extends Model
{
    use Rememberable;
    protected $fillable = ['name', 'code'];
}
