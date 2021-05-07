<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EndpointTable extends Model
{
    protected $fillable = ['id', 'name', 'url', 'status'];
}
