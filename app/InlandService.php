<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InlandService extends Model
{
    protected $table = 'inland_service';
    protected $fillable = ['id', 'name'];

}
