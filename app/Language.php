<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Watson\Rememberable\Rememberable;
class Language extends Model
{
    use Rememberable;
    protected $table = 'languages';
    protected $fillable = ['name'];
}
