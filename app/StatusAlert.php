<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StatusAlert extends Model
{
    protected $table    = "status_alerts";
    protected $fillable = ['id',
                           'name'
                          ];


}
